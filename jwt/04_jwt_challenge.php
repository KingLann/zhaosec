<?php
$module_name = 'JWT实战挑战';
$module_icon = '🏆';
$module_desc = '综合JWT漏洞利用挑战场景。';

session_start();

$message = '';
$success = false;

if (isset($_GET['reset'])) {
    $_SESSION['jwt_challenge'] = 0;
    $_SESSION['jwt_token'] = null;
    header('Location: 04_jwt_challenge.php');
    exit;
}

if (!isset($_SESSION['jwt_challenge'])) {
    $_SESSION['jwt_challenge'] = 0;
}

$level = $_SESSION['jwt_challenge'];
$secret = 'super_secret_key_12345';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
    $token = $_POST['token'];
    
    $parts = explode('.', $token);
    if (count($parts) === 3) {
        $header = json_decode(base64_decode($parts[0]), true);
        $payload = json_decode(base64_decode($parts[1]), true);
        
        if ($level === 0) {
            if (isset($header['alg']) && strtolower($header['alg']) === 'none') {
                if (isset($payload['role']) && $payload['role'] === 'admin') {
                    $_SESSION['jwt_challenge'] = 1;
                    $level = 1;
                    $message = '🎉 第1关通过！None算法攻击成功！';
                    $success = true;
                }
            }
            if (!$success && !$message) {
                $message = '❌ 验证失败，请尝试使用None算法伪造管理员令牌。';
            }
        } elseif ($level === 1) {
            $expected_sig = base64_encode(hash_hmac('sha256', $parts[0].'.'.$parts[1], $secret, true));
            if (hash_equals($expected_sig, base64_decode($parts[2]))) {
                if (isset($payload['role']) && $payload['role'] === 'superadmin') {
                    $_SESSION['jwt_challenge'] = 2;
                    $level = 2;
                    $message = '🎉 第2关通过！密钥破解成功！Flag: flag{jwt_master_2024}';
                    $success = true;
                }
            }
            if (!$success && !$message) {
                $message = '❌ 验证失败，请尝试破解密钥并伪造superadmin令牌。';
            }
        }
    } else {
        $message = '❌ 无效的JWT格式。';
    }
}

$content = '<div class="card">
    <div class="card-header">
        <h5 class="mb-0">🏆 JWT实战挑战</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>💡 挑战说明：</strong><br>
            本挑战包含多个关卡，需要你利用JWT漏洞伪造令牌获取管理员权限。
        </div>

        <div class="mb-3 text-center">
            <a href="?reset" class="btn btn-warning">重置挑战</a>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🚩 挑战状态</h6>
            </div>
            <div class="card-body">
                <div class="alert ' . ($success ? 'alert-success' : 'alert-warning') . '">
                    <strong>当前关卡：' . $level . '/2</strong><br>
                    ' . ($message ? $message : '') . '
                </div>

                <form method="POST" class="mt-3">
                    <div class="mb-3">
                        <label for="token" class="form-label">输入你的JWT令牌：</label>
                        <textarea name="token" id="token" class="form-control" rows="3" placeholder="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">验证令牌</button>
                </form>
            </div>
        </div>';

if ($level === 0) {
    $content .= '<div class="card mb-3">
        <div class="card-header">
            <h6>📖 第1关：None算法攻击</h6>
        </div>
        <div class="card-body">
            <p>原始令牌（普通用户）：</p>
            <pre class="bg-dark text-light p-3 rounded"><code>eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJ1c2VyMTIzIiwicm9sZSI6InVzZXIiLCJpYXQiOjE1MTYyMzkwMjJ9</code></pre>
            <p class="mt-3">目标：伪造一个管理员令牌，role设置为"admin"，使用None算法绕过签名验证。</p>
        </div>
    </div>';
} elseif ($level === 1) {
    $content .= '<div class="card mb-3">
        <div class="card-header">
            <h6>📖 第2关：密钥破解</h6>
        </div>
        <div class="card-body">
            <p>服务器使用HS256算法签名，密钥是一个常见的弱密钥。</p>
            <p>目标：破解密钥并伪造一个superadmin令牌。</p>
            <p class="mt-3">提示：密钥格式为 snake_case，包含"secret"。</p>
        </div>
    </div>';
} else {
    $content .= '<div class="card mb-3">
        <div class="card-header">
            <h6>🏆 恭喜通关！</h6>
        </div>
        <div class="card-body">
            <p>你已经掌握了JWT漏洞利用技术：</p>
            <ul>
                <li>None算法攻击</li>
                <li>弱密钥破解</li>
                <li>令牌伪造</li>
            </ul>
        </div>
    </div>';
}

$content .= '<div class="card">
    <div class="card-header">
        <h6>💡 提示工具</h6>
    </div>
    <div class="card-body">
        <p>推荐使用以下工具：</p>
        <ul>
            <li><a href="https://jwt.io" target="_blank">jwt.io</a> - JWT解码和编码</li>
            <li><a href="https://github.com/ticarpi/jwt_tool" target="_blank">jwt_tool</a> - JWT安全测试工具</li>
        </ul>
    </div>
</div>
    </div>
</div>';

include '../template/module_template.php';
?>
