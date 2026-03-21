<?php
session_start();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $users = [
        'admin' => base64_encode('123456'),
        'test' => base64_encode('password'),
        'user' => base64_encode('123456789')
    ];
    
    if (isset($users[$username]) && $users[$username] === base64_encode($password)) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        header('Location: success.php');
        exit;
    } else {
        $error = '用户名或密码错误';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base64编码爆破</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 400px;
            width: 100%;
        }
        .login-container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
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
            border-color: #f5576c;
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
        .error {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #fee;
            border-radius: 8px;
        }
        .info {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #856404;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #f5576c;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>2. 🔐 Base64编码登录</h2>
        <div class="info">
            <strong>漏洞说明：</strong><br>
            密码使用Base64编码传输，虽然不是明文，但编码不是加密，可解码后进行暴力破解。<br>
            <br>
            <strong>测试账号：</strong><br>
            - admin / 123456<br>
            - test / password<br>
            - user / 123456789
        </div>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="username">用户名</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">密码（将自动Base64编码）</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">登录</button>
        </form>
        <a href="../index.php" class="back-link">← 返回首页</a>
    </div>
</body>
</html>
