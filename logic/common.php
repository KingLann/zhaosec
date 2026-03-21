<?php
// 逻辑漏洞场景 - 公共函数库

// 数据库连接信息
$servername = "127.0.0.1";
$username = "root";
$password = "123456";
$dbname = "zhao";

// 创建数据库连接
function getDBConnection() {
    global $servername, $username, $password, $dbname;
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }
    return $conn;
}

// 创建访客用户
function createVisitorUser() {
    $conn = getDBConnection();
    
    // 生成唯一的访客用户名
    $visitorId = substr(md5(uniqid('visitor_', true)), 0, 8);
    $username = 'visitor_' . $visitorId;
    $email = $visitorId . '@test.com';
    $password = md5('visitor123');
    $role = 'user';
    $balance = 1000.00;
    $points = 100;
    
    $sql = "INSERT INTO logic_users (username, email, password, role, balance, points) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("创建访客用户失败: " . $conn->error);
        $conn->close();
        return false;
    }
    $stmt->bind_param("ssssdi", $username, $email, $password, $role, $balance, $points);
    if (!$stmt->execute()) {
        error_log("创建访客用户执行失败: " . $stmt->error);
        $stmt->close();
        $conn->close();
        return false;
    }
    $userId = $conn->insert_id;
    $stmt->close();
    $conn->close();
    
    return $userId;
}

// 获取或创建访客用户ID
function getOrCreateVisitorId() {
    // 启动session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // 1. 首先检查session中是否有user_id
    if (isset($_SESSION['user_id'])) {
        return $_SESSION['user_id'];
    }
    
    // 2. 检查cookie中是否有visitor_id
    if (isset($_COOKIE['visitor_id'])) {
        $visitorId = intval($_COOKIE['visitor_id']);
        // 验证用户是否存在
        $conn = getDBConnection();
        $sql = "SELECT id FROM logic_users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $visitorId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $_SESSION['user_id'] = $visitorId;
            $stmt->close();
            $conn->close();
            return $visitorId;
        }
        $stmt->close();
        $conn->close();
    }
    
    // 3. 创建新的访客用户
    $newUserId = createVisitorUser();
    
    if ($newUserId === false) {
        error_log("创建访客用户失败，返回默认用户ID 2");
        return 2; // 返回testuser作为默认用户
    }
    
    // 设置session和cookie
    $_SESSION['user_id'] = $newUserId;
    setcookie('visitor_id', $newUserId, time() + (86400 * 30), '/'); // 30天有效期
    
    return $newUserId;
}

// 获取当前登录用户ID（从session或cookie）
function getCurrentUserId() {
    return getOrCreateVisitorId();
}

// 获取当前用户信息
function getCurrentUser() {
    $conn = getDBConnection();
    $userId = getCurrentUserId();
    $sql = "SELECT * FROM logic_users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $user;
}

// 检查用户是否登录
function isLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_id']) || isset($_COOKIE['visitor_id']);
}

// 检查是否为管理员
function isAdmin() {
    $user = getCurrentUser();
    return $user && $user['role'] === 'admin';
}

// 生成订单号
function generateOrderNo() {
    return date('Ymd') . strtoupper(substr(uniqid(), -8));
}

// 记录用户操作日志
function logAction($userId, $action, $details = '') {
    $conn = getDBConnection();
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $sql = "INSERT INTO user_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $userId, $action, $details, $ip);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

// 获取Flag
function getFlag($vulnType) {
    $conn = getDBConnection();
    $sql = "SELECT flag FROM logic_flags WHERE vuln_type = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $vulnType);
    $stmt->execute();
    $result = $stmt->get_result();
    $flag = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $flag ? $flag['flag'] : null;
}

// 显示成功消息
function showSuccess($message) {
    return "<div class='alert alert-success'>{$message}</div>";
}

// 显示错误消息
function showError($message) {
    return "<div class='alert alert-danger'>{$message}</div>";
}

// 显示警告消息
function showWarning($message) {
    return "<div class='alert alert-warning'>{$message}</div>";
}

// 显示信息消息
function showInfo($message) {
    return "<div class='alert alert-info'>{$message}</div>";
}

// 页面头部
function getHeader($title) {
    $user = getCurrentUser();
    $userInfo = '';
    if ($user) {
        $userInfo = '<span class="navbar-text text-light me-3">👤 ' . htmlspecialchars($user['username']) . '</span>';
    }
    
    return '<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . $title . ' - 逻辑漏洞靶场</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .navbar {
            background: var(--primary-gradient);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
        }
        .btn-primary:hover {
            opacity: 0.9;
        }
        .vuln-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .flag-box {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            font-family: monospace;
            font-size: 1.2rem;
            margin: 20px 0;
        }
        .visitor-info {
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">🧩 逻辑漏洞靶场</a>
            <div class="navbar-nav flex-row align-items-center">
                ' . $userInfo . '
                <a class="nav-link me-3" href="index.php">首页</a>
                <a class="nav-link" href="../index.php">返回主站</a>
            </div>
        </div>
    </nav>
    <div class="container py-4">';
}

// 页面底部
function getFooter() {
    return '</div>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
}
?>
