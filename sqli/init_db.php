<?php
// SQL注入数据库初始化脚本
// 使用真实MySQL数据库

$servername = "127.0.0.1";
$username = "root";
$password = "123456";
$dbname = "zhao";

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

echo "连接成功<br>";

// 创建users表
$create_users_sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    password VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($create_users_sql) === TRUE) {
    echo "创建users表成功<br>";
} else {
    echo "创建users表失败: " . $conn->error . "<br>";
}

// 插入模拟数据
$insert_users_sql = "INSERT IGNORE INTO users (username, password, email) VALUES
('admin', 'admin123', 'admin@example.com'),
('user1', 'password1', 'user1@example.com'),
('user2', 'password2', 'user2@example.com'),
('user3', 'password3', 'user3@example.com')";

if ($conn->query($insert_users_sql) === TRUE) {
    echo "插入用户数据成功<br>";
} else {
    echo "插入用户数据失败: " . $conn->error . "<br>";
}

// 创建products表（用于UNION注入演示）
$create_products_sql = "CREATE TABLE IF NOT EXISTS products (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(30) NOT NULL
)";

if ($conn->query($create_products_sql) === TRUE) {
    echo "创建products表成功<br>";
} else {
    echo "创建products表失败: " . $conn->error . "<br>";
}

// 插入产品数据
$insert_products_sql = "INSERT IGNORE INTO products (name, price, category) VALUES
('笔记本电脑', 5999.99, '电子产品'),
('手机', 3999.99, '电子产品'),
('键盘', 199.99, '电脑配件'),
('鼠标', 99.99, '电脑配件')";

if ($conn->query($insert_products_sql) === TRUE) {
    echo "插入产品数据成功<br>";
} else {
    echo "插入产品数据失败: " . $conn->error . "<br>";
}

// 关闭连接
$conn->close();

echo "数据库初始化完成！";
?>
