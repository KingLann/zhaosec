<?php
session_start();
$error = '';
$found_users = [];
$show_flag = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $action = $_POST['action'] ?? 'check';
    
    $users = ['admin', 'test', 'user', 'root', 'guest'];
    
    if ($action === 'verify') {
        // 验证是否成功枚举了所有用户
        $submitted_users = explode(',', strtolower(str_replace(' ', '', $username)));
        $found_count = 0;
        foreach ($submitted_users as $u) {
            if (in_array($u, $users)) {
                $found_count++;
            }
        }
        if ($found_count >= count($users)) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = 'security_tester';
            $_SESSION['flag'] = 'FLAG{Account_Enumeration_Vulnerability_Found}';
            $_SESSION['vuln_name'] = '账户枚举';
            header('Location: success.php');
            exit;
        } else {
            $error = "验证失败！你只找到了 {$found_count}/" . count($users) . " 个用户，请继续枚举！";
        }
    } else {
        if (in_array($username, $users)) {
            $error = "用户 '{$username}' 存在，但密码错误";
        } else {
            $error = "用户 '{$username}' 不存在";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>账户枚举</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
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
            border-color: #fa709a;
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
            margin-bottom: 10px;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
            color: #fa709a;
            text-decoration: none;
        }
        .flag-section {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 2px solid #28a745;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            text-align: center;
        }
        .flag-section h4 {
            color: #155724;
            margin-bottom: 10px;
        }
        .divider {
            border-top: 1px solid #e0e0e0;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>4. 🔐 账户枚举</h2>
        <div class="info">
            <strong>漏洞说明：</strong><br>
            根据不同的错误信息可以判断用户是否存在，从而枚举出系统中的有效用户名。<br>
            <br>
            <strong>利用方法：</strong><br>
            1. 输入不同用户名观察错误提示<br>
            2. "用户存在，但密码错误" = 用户存在<br>
            3. "用户不存在" = 用户不存在<br>
            4. 枚举出所有用户后提交获取FLAG
        </div>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="action" value="check">
            <div class="form-group">
                <label for="username">用户名（用于枚举测试）</label>
                <input type="text" id="username" name="username" placeholder="输入用户名测试是否存在" required>
            </div>
            <div class="form-group">
                <label for="password">密码</label>
                <input type="password" id="password" name="password" placeholder="任意密码">
            </div>
            <button type="submit" class="btn">测试用户是否存在</button>
        </form>
        
        <div class="divider"></div>
        
        <form method="POST">
            <input type="hidden" name="action" value="verify">
            <div class="form-group">
                <label for="found_users">提交枚举到的所有用户（用逗号分隔）</label>
                <input type="text" id="found_users" name="username" placeholder="例如: admin, test, user, root, guest" required>
            </div>
            <button type="submit" class="btn btn-success">🚩 验证并获取FLAG</button>
        </form>
        
        <a href="index.php" class="back-link">← 返回身份认证首页</a>
    </div>
</body>
</html>
