<?php
session_start();
$secret_data = '这是敏感数据，只有管理员才能访问';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>未授权访问</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            margin-bottom: 30px;
        }
        .card h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .info {
            background: #d1ecf1;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            color: #0c5460;
        }
        .secret {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #f5c6cb;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #a18cd1;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>🔓 未授权访问漏洞</h2>
            <div class="info">
                <strong>漏洞说明：</strong><br>
                本页面存在未授权访问漏洞，攻击者无需登录即可访问敏感数据。<br>
                <br>
                <strong>漏洞利用：</strong><br>
                直接访问本页面即可获取敏感数据，无需任何认证。
            </div>
            
            <div class="secret">
                <strong>🚨 敏感数据：</strong><br>
                <?php echo htmlspecialchars($secret_data); ?>
            </div>
            
            <br>
            <a href="../index.php" class="back-link">← 返回首页</a>
        </div>
    </div>
</body>
</html>
