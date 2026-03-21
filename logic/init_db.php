<?php
// 逻辑漏洞场景 - 数据库初始化
// 使用真实MySQL数据库

// 数据库连接信息
$servername = "127.0.0.1";
$username = "root";
$password = "123456";
$dbname = "zhao";

// 创建连接
$conn = new mysqli($servername, $username, $password);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

echo "<h2>🧩 逻辑漏洞场景 - 数据库初始化</h2>";

// 检查并创建数据库
$create_db_sql = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($create_db_sql) === TRUE) {
    echo "✅ 数据库 $dbname 检查/创建成功<br>";
} else {
    echo "❌ 数据库 $dbname 操作失败: " . $conn->error . "<br>";
}

// 选择数据库
$conn->select_db($dbname);

// 检查是否需要重置
$reset = isset($_GET['reset']) && $_GET['reset'] == '1';

// 无论是否重置，都清除Session和Cookie中的用户信息
session_start();
unset($_SESSION['user_id']);
session_destroy();

if (isset($_COOKIE['visitor_id'])) {
    setcookie('visitor_id', '', time() - 3600, '/');
}

if ($reset) {
    echo "<div style='background: #fff3cd; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
    echo "<strong>⚠️ 重置模式：</strong>正在清空数据库...<br>";
    echo "</div>";
    
    // 删除表（按照依赖关系顺序）
    $tables = ['lottery_records', 'coupons', 'order_items', 'orders', 'products', 'user_sessions', 'password_resets', 'user_logs', 'logic_users', 'logic_flags'];
    foreach ($tables as $table) {
        $drop_sql = "DROP TABLE IF EXISTS $table";
        if ($conn->query($drop_sql) === TRUE) {
            echo "删除表 $table 成功<br>";
        } else {
            echo "删除表 $table 失败: " . $conn->error . "<br>";
        }
    }
}

// 清除用户信息后提示
echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 8px;'>";
echo "<strong>✅ 已清除用户会话信息</strong><br>";
echo "访问其他页面时将自动创建新的访客用户";
echo "</div>";

// 先删除旧表（确保表结构完整）
$tables = ['lottery_records', 'coupons', 'order_items', 'orders', 'products', 'user_sessions', 'password_resets', 'user_logs', 'logic_users', 'logic_flags'];
foreach ($tables as $table) {
    $drop_sql = "DROP TABLE IF EXISTS $table";
    if ($conn->query($drop_sql) === TRUE) {
        echo "删除旧表 $table 成功<br>";
    }
}

// 创建逻辑漏洞flags表
$create_flags_sql = "CREATE TABLE logic_flags (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    flag VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL,
    vuln_type VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($create_flags_sql) === TRUE) {
    echo "✅ 表 logic_flags 创建成功<br>";
} else {
    echo "❌ 表 logic_flags 创建失败: " . $conn->error . "<br>";
}

// 创建用户表（扩展版）
$create_users_sql = "CREATE TABLE logic_users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(50) NOT NULL,
    role ENUM('user', 'admin', 'vip') DEFAULT 'user',
    balance DECIMAL(10,2) DEFAULT 0.00,
    points INT DEFAULT 0,
    phone VARCHAR(20),
    reset_token VARCHAR(64),
    reset_expires DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($create_users_sql) === TRUE) {
    echo "✅ 表 logic_users 创建成功<br>";
} else {
    echo "❌ 表 logic_users 创建失败: " . $conn->error . "<br>";
}

// 创建商品表
$create_products_sql = "CREATE TABLE products (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($create_products_sql) === TRUE) {
    echo "✅ 表 products 创建成功<br>";
} else {
    echo "❌ 表 products 创建失败: " . $conn->error . "<br>";
}

// 创建订单表
$create_orders_sql = "CREATE TABLE orders (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED NOT NULL,
    order_no VARCHAR(32) NOT NULL UNIQUE,
    total_amount DECIMAL(10,2) NOT NULL,
    pay_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'paid', 'shipped', 'completed', 'cancelled') DEFAULT 'pending',
    pay_time DATETIME,
    is_replay TINYINT(1) DEFAULT 0,
    replay_source_id INT(6) UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES logic_users(id)
)";
if ($conn->query($create_orders_sql) === TRUE) {
    echo "✅ 表 orders 创建成功<br>";
} else {
    echo "❌ 表 orders 创建失败: " . $conn->error . "<br>";
}

// 创建订单商品表
$create_order_items_sql = "CREATE TABLE order_items (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT(6) UNSIGNED NOT NULL,
    product_id INT(6) UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)";
if ($conn->query($create_order_items_sql) === TRUE) {
    echo "✅ 表 order_items 创建成功<br>";
} else {
    echo "❌ 表 order_items 创建失败: " . $conn->error . "<br>";
}

// 创建优惠券表
$create_coupons_sql = "CREATE TABLE coupons (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(32) NOT NULL UNIQUE,
    discount DECIMAL(10,2) NOT NULL,
    min_amount DECIMAL(10,2) DEFAULT 0,
    user_id INT(6) UNSIGNED,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES logic_users(id)
)";
if ($conn->query($create_coupons_sql) === TRUE) {
    echo "✅ 表 coupons 创建成功<br>";
} else {
    echo "❌ 表 coupons 创建失败: " . $conn->error . "<br>";
}

// 创建抽奖记录表
$create_lottery_sql = "CREATE TABLE lottery_records (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED NOT NULL,
    prize VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES logic_users(id)
)";
if ($conn->query($create_lottery_sql) === TRUE) {
    echo "✅ 表 lottery_records 创建成功<br>";
} else {
    echo "❌ 表 lottery_records 创建失败: " . $conn->error . "<br>";
}

// 创建会话表（用于会话固定漏洞）
$create_sessions_sql = "CREATE TABLE user_sessions (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED,
    session_id VARCHAR(64) NOT NULL UNIQUE,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES logic_users(id)
)";
if ($conn->query($create_sessions_sql) === TRUE) {
    echo "✅ 表 user_sessions 创建成功<br>";
} else {
    echo "❌ 表 user_sessions 创建失败: " . $conn->error . "<br>";
}

// 创建密码重置表
$create_password_resets_sql = "CREATE TABLE password_resets (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES logic_users(id)
)";
if ($conn->query($create_password_resets_sql) === TRUE) {
    echo "✅ 表 password_resets 创建成功<br>";
} else {
    echo "❌ 表 password_resets 创建失败: " . $conn->error . "<br>";
}

// 创建用户日志表
$create_logs_sql = "CREATE TABLE user_logs (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED,
    action VARCHAR(50) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES logic_users(id)
)";
if ($conn->query($create_logs_sql) === TRUE) {
    echo "✅ 表 user_logs 创建成功<br>";
} else {
    echo "❌ 表 user_logs 创建失败: " . $conn->error . "<br>";
}

// 插入测试数据
echo "<hr><h3>插入测试数据</h3>";

// 插入用户数据
// 只保留admin用户用于水平越权测试，其他用户由访客自动创建
$insert_users = "INSERT IGNORE INTO logic_users (id, username, password, email, role, balance, points, phone) VALUES
(1, 'admin', MD5('admin123'), 'admin@example.com', 'admin', 9999.99, 10000, '13800138000'),
(2, 'testuser', MD5('test123'), 'testuser@example.com', 'user', 1000.00, 100, '13800138001')";
if ($conn->query($insert_users) === TRUE) {
    echo "✅ 用户数据插入成功<br>";
    echo "<small style='color: #666;'>说明：admin用户用于水平越权测试，其他用户由访客自动创建</small><br>";
} else {
    echo "❌ 用户数据插入失败: " . $conn->error . "<br>";
}

// 插入商品数据
$insert_products = "INSERT IGNORE INTO products (id, name, description, price, stock) VALUES
(1, 'iPhone 15 Pro', '最新款苹果手机', 7999.00, 100),
(2, 'MacBook Pro', '专业级笔记本电脑', 14999.00, 50),
(3, 'AirPods Pro', '无线降噪耳机', 1999.00, 200),
(4, '限量款手办', '绝版收藏手办', 999.00, 5),
(5, '优惠券大礼包', '价值100元的优惠券', 0.01, 1000)";
if ($conn->query($insert_products) === TRUE) {
    echo "✅ 商品数据插入成功<br>";
} else {
    echo "❌ 商品数据插入失败: " . $conn->error . "<br>";
}

// 插入Flag数据
$insert_flags = "INSERT IGNORE INTO logic_flags (flag, description, vuln_type) VALUES
('FLAG{PRICE_TAMPERING_SUCCESS}', '价格篡改漏洞 - 通过修改价格参数完成支付', 'payment'),
('FLAG{PAYMENT_STATUS_BYPASS}', '支付状态绕过 - 直接修改订单状态完成支付', 'payment'),
('FLAG{ORDER_REPLAY_ATTACK}', '订单重放攻击 - 重复提交订单获取多次商品', 'payment'),
('FLAG{HORIZONTAL_PRIVILEGE_ESCALATION}', '水平越权 - 访问其他用户的数据', 'privilege'),
('FLAG{VERTICAL_PRIVILEGE_ESCALATION}', '垂直越权 - 普通用户访问管理员功能', 'privilege'),
('FLAG{BUSINESS_STEP_SKIP}', '业务流程绕过 - 跳过必要步骤完成操作', 'business'),
('FLAG{STATUS_TAMPERING}', '状态篡改 - 修改业务状态绕过限制', 'business'),
('FLAG{PASSWORD_RESET_VULN}', '密码重置缺陷 - 绕过验证重置他人密码', 'auth'),
('FLAG{MFA_BYPASS_SUCCESS}', 'MFA绕过 - 绕过双因素认证', 'auth'),
('FLAG{SESSION_FIXATION}', '会话固定 - 利用固定会话ID劫持用户', 'auth'),
('FLAG{RACE_CONDITION_WIN}', '条件竞争 - 利用并发漏洞超卖商品', 'race'),
('FLAG{LOTTERY_ABUSE}', '抽奖滥用 - 绕过限制重复抽奖', 'race'),
('FLAG{PARAMETER_BINDING}', '参数绑定覆盖 - 覆盖敏感参数', 'validation'),
('FLAG{LIMIT_BYPASS}', '限额绕过 - 绕过业务限额限制', 'validation'),
('FLAG{POINTS_ABUSE}', '积分滥刷 - 利用漏洞刷取积分', 'abuse'),
('FLAG{SMS_BOMBING}', '短信轰炸 - 利用接口发送大量短信', 'abuse')";
if ($conn->query($insert_flags) === TRUE) {
    echo "✅ Flag数据插入成功<br>";
} else {
    echo "❌ Flag数据插入失败: " . $conn->error . "<br>";
}

// 关闭连接
$conn->close();

echo "<hr>";
echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin-top: 20px;'>";
echo "<h3>✅ 数据库初始化完成！</h3>";
echo "<p>所有逻辑漏洞场景所需的表和数据已创建完成。</p>";
echo "<a href='index.php' style='display: inline-block; margin-top: 10px; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px;'>返回逻辑漏洞首页</a>";
echo "</div>";
?>
