<?php
session_start();
$error = '';
$token = '';
$secret_key = 'weak_secret_key_123';

function generateJWT($payload, $secret) {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload_encoded = base64url_encode(json_encode($payload));
    $header_encoded = base64url_encode($header);
    $signature = hash_hmac('sha256', $header_encoded . '.' . $payload_encoded, $secret, true);
    $signature_encoded = base64url_encode($signature);
    return $header_encoded . '.' . $payload_encoded . '.' . $signature_encoded;
}

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function verifyJWT($token, $secret) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;
    
    $header = $parts[0];
    $payload = $parts[1];
    $signature = $parts[2];
    
    $expected_signature = hash_hmac('sha256', $header . '.' . $payload, $secret, true);
    $expected_signature_encoded = base64url_encode($expected_signature);
    
    return hash_equals($signature, $expected_signature_encoded);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    
    if ($username === 'admin') {
        $payload = [
            'user' => 'admin',
            'role' => 'administrator',
            'exp' => time() + 3600
        ];
        $token = generateJWT($payload, $secret_key);
        $_SESSION['token'] = $token;
        $_SESSION['logged_in'] = true;
        header('Location: jwt_dashboard.php');
        exit;
    } else {
        $error = '用户名不存在';
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
            max-width: 500px;
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
            border-color: #667eea;
        }
        .btn {
            width: 100%;
            padding: 14px;
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
        .error {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #fee;
            border-radius: 8px;
        }
        .info {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #2c3e50;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>6. 🔐 JWT弱密钥漏洞</h2>
        <div class="info">
            <strong>漏洞说明：</strong><br>
            JWT使用弱密钥进行签名，攻击者可以破解密钥后伪造任意token。<br>
            <br>
            <strong>测试账号：</strong> admin<br>
            <br>
            <strong>利用方式：</strong><br>
            1. 登录获取JWT token<br>
            2. 使用工具（如jwt_tool）破解密钥<br>
            3. 伪造管理员token提升权限
        </div>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="username">用户名</label>
                <input type="text" id="username" name="username" value="admin" required>
            </div>
            <button type="submit" class="btn">登录获取JWT</button>
        </form>
        <a href="../index.php" class="back-link">← 返回首页</a>
    </div>
</body>
</html>
