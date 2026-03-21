<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: 01_plaintext_brute.php');
    exit;
}
$username = $_SESSION['username'] ?? 'Unknown';
$flag = $_SESSION['flag'] ?? 'FLAG{Default_Success_Flag}';
$vuln_name = $_SESSION['vuln_name'] ?? '身份认证';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录成功 - <?php echo htmlspecialchars($vuln_name); ?></title>
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
            max-width: 550px;
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
        .flag-box {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin: 25px 0;
            position: relative;
            overflow: hidden;
        }
        .flag-box::before {
            content: '🏆';
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 30px;
            opacity: 0.3;
        }
        .flag-box h3 {
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        .flag-code {
            background: rgba(255,255,255,0.2);
            padding: 15px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            word-break: break-all;
            margin-top: 10px;
        }
        .congrats-text {
            font-size: 1.1rem;
            margin-bottom: 10px;
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
        .vuln-badge {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✅</div>
        <div class="vuln-badge"><?php echo htmlspecialchars($vuln_name); ?></div>
        <h1>登录成功！</h1>
        <div class="user-info">
            <strong>欢迎，<?php echo htmlspecialchars($username); ?>！</strong>
        </div>
        
        <div class="flag-box">
            <div class="congrats-text">🎉 恭喜你成功利用漏洞！</div>
            <h3>🏁 获取到 FLAG</h3>
            <div class="flag-code"><?php echo htmlspecialchars($flag); ?></div>
        </div>
        
        <div>
            <a href="index.php" class="back-link">← 返回身份认证首页</a>
            <a href="logout.php?redirect=01_plaintext_brute.php" class="logout-btn">退出登录</a>
        </div>
    </div>
</body>
</html>
