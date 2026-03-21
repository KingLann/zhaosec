<?php
session_start();
$secret_key = 'weak_secret_key_123';

function verifyJWT($token, $secret) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;
    
    $header = $parts[0];
    $payload = $parts[1];
    $signature = $parts[2];
    
    $expected_signature = hash_hmac('sha256', $header . '.' . $payload, $secret, true);
    $expected_signature_encoded = rtrim(strtr(base64_encode($expected_signature), '+/', '-_'), '=');
    
    return hash_equals($signature, $expected_signature_encoded);
}

if (!isset($_SESSION['token']) || !verifyJWT($_SESSION['token'], $secret_key)) {
    header('Location: jwt_weak_key.php');
    exit;
}

$token = $_SESSION['token'];
$parts = explode('.', $token);
$payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JWT管理后台</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
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
        .token-display {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            word-break: break-all;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        .info {
            background: #d1ecf1;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            color: #0c5460;
        }
        .warning {
            background: #fff3cd;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            color: #856404;
        }
        .payload-display {
            background: #e8f4fd;
            padding: 20px;
            border-radius: 10px;
            color: #2c3e50;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        table th {
            background: #f8f9fa;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>🎉 JWT认证成功</h2>
            
            <div class="info">
                <strong>当前用户信息：</strong>
                <table>
                    <tr><th>用户名</th><td><?php echo htmlspecialchars($payload['user']); ?></td></tr>
                    <tr><th>角色</th><td><?php echo htmlspecialchars($payload['role']); ?></td></tr>
                    <tr><th>过期时间</th><td><?php echo date('Y-m-d H:i:s', $payload['exp']); ?></td></tr>
                </table>
            </div>
            
            <div class="warning">
                <strong>⚠️ 安全警告：</strong><br>
                当前JWT使用弱密钥签名，攻击者可以：<br>
                1. 破解密钥获取签名能力<br>
                2. 伪造任意payload（如将role改为admin）<br>
                3. 使用伪造的token访问管理员功能
            </div>
            
            <div class="payload-display">
                <strong>📋 JWT Token：</strong>
                <div class="token-display"><?php echo htmlspecialchars($token); ?></div>
            </div>
            
            <div class="payload-display">
                <strong>📦 Payload内容：</strong>
                <div class="token-display"><?php echo htmlspecialchars(json_encode($payload, JSON_PRETTY_PRINT)); ?></div>
            </div>
            
            <br>
            <a href="../index.php" class="back-link">← 返回首页</a>
        </div>
    </div>
</body>
</html>
