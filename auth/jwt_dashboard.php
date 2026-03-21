<?php
session_start();
$secret_key = 'weak_secret_key_123';

function verifyJWT($token, $secret) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;
    
    $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
    $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
    $signature = $parts[2];
    
    // 支持 alg:none 算法（空签名）
    if (isset($header['alg']) && $header['alg'] === 'none') {
        return $payload;
    }
    
    // 标准HS256验证
    $expected_signature = hash_hmac('sha256', $parts[0] . '.' . $parts[1], $secret, true);
    $expected_signature_encoded = rtrim(strtr(base64_encode($expected_signature), '+/', '-_'), '=');
    
    if (!hash_equals($signature, $expected_signature_encoded)) {
        return false;
    }
    
    return $payload;
}

// 从session或cookie获取token
$token = $_SESSION['token'] ?? $_COOKIE['jwt_token'] ?? '';
$payload = verifyJWT($token, $secret_key);

if (!$payload) {
    header('Location: 06_jwt_weak_key.php');
    exit;
}

$is_admin = ($payload['role'] ?? '') === 'admin';
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
        .success {
            background: #d4edda;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            color: #155724;
            border: 2px solid #28a745;
        }
        .admin-panel {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        .admin-panel h3 {
            margin-bottom: 15px;
            font-size: 1.5rem;
        }
        .admin-panel .secret-flag {
            background: rgba(255,255,255,0.2);
            padding: 15px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            text-align: center;
            margin-top: 15px;
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
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .role-user {
            background: #e9ecef;
            color: #495057;
        }
        .role-admin {
            background: #dc3545;
            color: white;
        }
        .forge-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .forge-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
        }
        .forge-form button {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>🎉 JWT认证成功</h2>
            
            <?php if ($is_admin): ?>
            <div class="success">
                <strong>🎊 恭喜！</strong><br>
                你已成功伪造admin权限token，完成了JWT权限提升攻击！
            </div>
            
            <div class="admin-panel">
                <h3>👑 管理员专属区域</h3>
                <p>只有admin角色才能看到此内容</p>
                <div class="secret-flag">
                    🚩 FLAG{JWT_Weak_Key_Exploited_Successfully}
                </div>
            </div>
            <?php else: ?>
            <div class="warning">
                <strong>⚠️ 普通用户权限</strong><br>
                当前token角色为：<?php echo htmlspecialchars($payload['role'] ?? 'unknown'); ?><br>
                你需要伪造admin权限token才能访问管理员功能。
            </div>
            <?php endif; ?>
            
            <div class="info">
                <strong>当前Token信息：</strong>
                <table>
                    <tr><th>用户名</th><td><?php echo htmlspecialchars($payload['user'] ?? 'unknown'); ?></td></tr>
                    <tr><th>角色</th><td><span class="role-badge <?php echo $is_admin ? 'role-admin' : 'role-user'; ?>"><?php echo htmlspecialchars($payload['role'] ?? 'unknown'); ?></span></td></tr>
                    <tr><th>过期时间</th><td><?php echo isset($payload['exp']) ? date('Y-m-d H:i:s', $payload['exp']) : 'unknown'; ?></td></tr>
                    <tr><th>算法</th><td><?php echo htmlspecialchars($payload['alg'] ?? 'HS256'); ?></td></tr>
                </table>
            </div>
            
            <?php if (!$is_admin): ?>
            <div class="warning">
                <strong>💡 攻击提示：</strong><br>
                尝试以下方法提升权限：<br>
                1. <strong>破解密钥：</strong>使用jwt_tool或hashcat破解密钥 'weak_secret_key_123'<br>
                2. <strong>算法混淆：</strong>将header中的alg改为"none"，并清空签名<br>
                3. <strong>伪造token：</strong>修改payload中的role为"admin"并重新签名<br>
                <br>
                伪造成功后，将新token设置到cookie名为 'jwt_token' 或替换session中的token
            </div>
            <?php endif; ?>
            
            <div class="payload-display">
                <strong>📋 JWT Token：</strong>
                <div class="token-display"><?php echo htmlspecialchars($token); ?></div>
            </div>
            
            <div class="payload-display">
                <strong>📦 Payload内容：</strong>
                <div class="token-display"><?php echo htmlspecialchars(json_encode($payload, JSON_PRETTY_PRINT)); ?></div>
            </div>
            
            <br>
            <a href="index.php" class="back-link">← 返回身份认证首页</a>
            <a href="logout.php?redirect=06_jwt_weak_key.php" style="margin-left: 20px; color: #dc3545; text-decoration: none; font-weight: 600;">退出登录</a>
        </div>
    </div>
</body>
</html>
