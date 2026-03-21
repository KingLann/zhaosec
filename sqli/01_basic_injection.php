<?php
// SQL注入基础场景
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
    ('user1', 'password1', 'user1@example.com'),
    ('user2', 'password2', 'user2@example.com'),
    ('user3', 'password3', 'user3@example.com')";
    $conn->query($insert_users_sql);
}

// 数据库已在init_db.php中初始化

// 模拟数据库查询函数
function query($sql, $conn) {
    // 执行查询
    $result = $conn->query($sql);
    
    if (!$result) {
        return [];
    }
    
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    
    // 检查是否是注入查询
    $sql_lower = strtolower($sql);
    if (strpos($sql_lower, 'union') !== false || strpos($sql_lower, 'or') !== false || strpos($sql_lower, 'and') !== false) {
        // 添加FLAG
        $rows[] = [
            'id' => 999,
            'username' => 'FLAG{SQL_INJECTION_SUCCESS}',
            'password' => 'SQL_INJECTION',
            'email' => 'flag@example.com'
        ];
        $rows[] = [
            'id' => 1000,
            'username' => '数据库信息',
            'password' => 'version: ' . $conn->server_info,
            'email' => 'database: zhao'
        ];
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
    <title>SQL注入基础 - SQL注入漏洞</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            border-left: 5px solid #667eea;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: #667eea;
            color: white;
            font-weight: 600;
        }
        .results-table tr:hover {
            background: #f8f9fa;
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
        .payload-list code:hover {
            text-decoration: underline;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="alert-danger">
                <h4>🚨 SQL注入基础漏洞</h4>
                <p>本页面存在SQL注入漏洞，用户输入直接拼接到SQL语句中，未进行任何过滤。</p>
            </div>
            
            <h2>1. 💉 SQL注入基础</h2>
            
            <div class="info-section">
                <strong>💡 漏洞说明：</strong><br>
                SQL注入是指攻击者通过在用户输入中插入恶意SQL代码，从而操纵数据库查询。<br>
                当应用程序直接将用户输入拼接到SQL语句中时，就会产生SQL注入漏洞。<br>
                <br>
                <strong>危害：</strong>获取敏感数据、绕过登录验证、执行任意SQL语句、甚至控制服务器
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
                <div style="color: #e74c3c; margin: 15px 0;">
                    <strong>错误：</strong><?php echo htmlspecialchars($error); ?>
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
                <?php else:
                    if ($id): ?>
                    <p style="color: #666; margin: 15px 0;">未找到用户</p>
                    <?php endif;
                endif; ?>
            </div>
        </div>

        <div class="card">
            <h3>🎯 常用注入Payload</h3>
            <div class="payload-list">
                <h4>1. 基础注入</h4>
                <code onclick="setId('1 OR 1=1')">1 OR 1=1</code>
                <code onclick="setId('1 --')">1 --</code>
                <code onclick="setId('1 #')">1 #</code>
                
                <h4>2. UNION注入</h4>
                <code onclick="setId('1 UNION SELECT 1,2,3,4')">1 UNION SELECT 1,2,3,4</code>
                <code onclick="setId('0 UNION SELECT id,username,password,email FROM users')">0 UNION SELECT id,username,password,email FROM users</code>
                
                <h4>3. 布尔注入</h4>
                <code onclick="setId('1 AND 1=1')">1 AND 1=1</code>
                <code onclick="setId('1 AND 1=2')">1 AND 1=2</code>
                
                <h4>4. 时间注入</h4>
                <code onclick="setId('1 AND SLEEP(5)')">1 AND SLEEP(5)</code>
            </div>
            
            <div id="flagBox" class="flag-box">
                🚩 FLAG{Basic_SQL_Injection_Success}<br>
                <span style="font-size: 0.9rem;">你成功完成了SQL注入！</span>
            </div>
        </div>

        <div class="card">
            <h3>📚 SQL基础命令</h3>
            <div class="info-section">
                <strong>常见SQL增删改查命令：</strong>
            </div>
            <div class="sql-code">
-- 1. 查询数据（SELECT）<br>
SELECT * FROM users;  -- 查询所有用户<br>
SELECT id, username FROM users WHERE id > 5;  -- 带条件查询<br>
<br>
-- 2. 插入数据（INSERT）<br>
INSERT INTO users (username, password, email) VALUES ('newuser', 'pass123', 'new@example.com');<br>
<br>
-- 3. 更新数据（UPDATE）<br>
UPDATE users SET password = 'newpass' WHERE id = 1;<br>
<br>
-- 4. 删除数据（DELETE）<br>
DELETE FROM users WHERE id = 2;<br>
<br>
-- 5. 创建表（CREATE TABLE）<br>
CREATE TABLE users (<br>
    id INT AUTO_INCREMENT PRIMARY KEY,<br>
    username VARCHAR(50) NOT NULL,<br>
    password VARCHAR(50) NOT NULL<br>
);<br>
<br>
-- 6. 删除表（DROP TABLE）<br>
DROP TABLE users;<br>
            </div>
        </div>

        <div class="card">
            <h3>🗃️ MySQL元数据库</h3>
            <div class="info-section">
                <strong>MySQL系统数据库：</strong><br>
                <ul>
                    <li><code>information_schema</code> - 存储数据库结构信息</li>
                    <li><code>mysql</code> - 存储MySQL系统信息</li>
                    <li><code>performance_schema</code> - 存储性能数据</li>
                    <li><code>sys</code> - 系统视图</li>
                </ul>
            </div>
            <div class="sql-code">
-- 关键元数据表查询<br>
<br>
-- 1. 查询所有数据库<br>
SELECT schema_name FROM information_schema.schemata;<br>
<br>
-- 2. 查询当前数据库的所有表<br>
SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE();<br>
<br>
-- 3. 查询表的列信息<br>
SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'users';
<br>
-- 4. 查询MySQL版本<br>
SELECT version();<br>
<br>
-- 5. 查询用户权限<br>
SELECT user, host FROM mysql.user;<br>
            </div>
        </div>

        <div class="card">
            <h3>🛡️ 防御方法</h3>
            <div class="info-section">
                1. <strong>参数化查询：</strong>使用预处理语句和绑定参数<br>
                2. <strong>输入验证：</strong>对用户输入进行类型检查和白名单过滤<br>
                3. <strong>最小权限原则：</strong>数据库用户只授予必要的权限<br>
                4. <strong>WAF防护：</strong>部署Web应用防火墙<br>
                5. <strong>错误处理：</strong>不向用户显示详细的错误信息
            </div>
            
            <div class="sql-code">
// 安全的参数化查询（PHP PDO示例）
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
</div>
            
            <a href="index.php" class="back-link">← 返回SQL注入模块首页</a>
        </div>
    </div>

    <script>
        function setId(payload) {
            document.querySelector('input[name="id"]').value = payload;
        }
        
        // 检测是否成功注入
        window.onload = function() {
            const url = window.location.href;
            if (url.includes('id=') && 
                (url.includes('OR') || url.includes('UNION') || 
                 url.includes('AND') || url.includes('--') || 
                 url.includes('#'))) {
                document.getElementById('flagBox').classList.add('show');
            }
        };
    </script>
</body>
</html>
