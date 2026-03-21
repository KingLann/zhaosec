<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: 01_plaintext_brute.php');
    exit;
}
$username = $_SESSION['username'] ?? 'Unknown';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录成功</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .success-container {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .success-icon {
            font-size: 80px;
            color: #11998e;
            margin-bottom: 20px;
        }
        .success-container h1 {
            color: #11998e;
            margin-bottom: 20px;
        }
        .user-info {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #11998e;
            text-decoration: none;
            font-weight: 600;
        }
        .logout-btn {
            display: inline-block;
            margin-left: 20px;
            padding: 10px 20px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✅</div>
        <h1>登录成功！</h1>
        <div class="user-info">
            <strong>欢迎，<?php echo htmlspecialchars($username); ?>！</strong>
        </div>
        <div>
            <a href="../index.php" class="back-link">← 返回首页</a>
            <a href="logout.php" class="logout-btn">退出登录</a>
        </div>
    </div>
</body>
</html>
