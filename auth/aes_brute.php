<?php
session_start();
$error = '';

function aes_encrypt($data, $key) {
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $encrypted);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $users = [
        'admin' => aes_encrypt('123456', 'mysecretkey123'),
        'test' => aes_encrypt('password', 'mysecretkey123'),
        'user' => aes_encrypt('123456789', 'mysecretkey123')
    ];
    
    if (isset($users[$username])) {
        $encrypted_input = aes_encrypt($password, 'mysecretkey123');
        if ($users[$username] === $encrypted_input) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            header('Location: success.php');
            exit;
        }
    }
    $error = '用户名或密码错误';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>前端AES加密爆破</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
            max-width: 450px;
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
            border-color: #4facfe;
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
            background: #d1ecf1;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #0c5460;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #4facfe;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>🔐 AES加密登录</h2>
        <div class="info">
            <strong>漏洞说明：</strong><br>
            密码使用AES加密传输，但密钥硬编码在前端，攻击者可以获取密钥后进行加密爆破。<br>
            <br>
            <strong>测试账号：</strong><br>
            - admin / 123456<br>
            - test / password<br>
            - user / 123456789
        </div>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" id="loginForm">
            <div class="form-group">
                <label for="username">用户名</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">密码</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">登录</button>
        </form>
        <a href="../index.php" class="back-link">← 返回首页</a>
    </div>
    <script>
        const secretKey = 'mysecretkey123';
        
        function aesEncrypt(text, key) {
            const iv = CryptoJS.lib.WordArray.random(16);
            const encrypted = CryptoJS.AES.encrypt(text, CryptoJS.enc.Utf8.parse(key), {
                iv: iv,
                mode: CryptoJS.mode.CBC,
                padding: CryptoJS.pad.Pkcs7
            });
            const combined = iv.concat(encrypted.ciphertext);
            return CryptoJS.enc.Base64.stringify(combined);
        }
        
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const password = document.getElementById('password').value;
            const encryptedPassword = aesEncrypt(password, secretKey);
            
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'password';
            hiddenInput.value = encryptedPassword;
            this.appendChild(hiddenInput);
            
            const originalPassword = document.getElementById('password');
            originalPassword.name = '';
            
            this.submit();
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
</body>
</html>
