<?php
// 联合查询注入场景
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
    
    // 检查是否是联合查询注入
    $sql_lower = strtolower($sql);
    if (strpos($sql_lower, 'union') !== false && strpos($sql_lower, 'select') !== false) {
        // 从flags表中查询flag
        $flag_result = $conn->query("SELECT flag FROM flags WHERE description LIKE '%SQL注入%' LIMIT 1");
        if ($flag_result && $flag_row = $flag_result->fetch_assoc()) {
            $flag = $flag_row['flag'];
        } else {
            $flag = 'FLAG{UNION_INJECTION_SUCCESS}';
        }
        
        // 添加FLAG
        $rows[] = [
            'id' => 999,
            'username' => $flag,
            'password' => 'UNION_INJECTION',
            'email' => 'flag@example.com',
            'created_at' => date('Y-m-d H:i:s')
        ];
    }
    
    return $rows;
}

$id = $_GET['id'] ?? 1;
$error = '';
$results = [];

// 执行查询（存在SQL注入漏洞）
$sql = "SELECT id, username, password, email, created_at FROM users WHERE id=$id";
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
    <title>联合查询注入 - SQL注入漏洞</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
                <h4>🚨 联合查询注入漏洞</h4>
                <p>本页面存在联合查询注入漏洞，攻击者可以通过UNION关键字合并查询结果，获取数据库中的敏感信息。</p>
            </div>
            
            <h2>1. 🔗 联合查询注入</h2>
            
            <div class="info-section">
                <strong>💡 漏洞说明：</strong><br>
                联合查询注入是一种常见的SQL注入技术，通过使用UNION关键字将两个或多个SELECT语句的结果组合在一起。<br>
                当应用程序直接将用户输入拼接到SQL语句中时，攻击者可以构造恶意UNION查询来获取数据库中的敏感信息。<br>
                <br>
                <strong>危害：</strong>获取敏感数据、查询数据库结构、获取系统信息
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
                            <th>创建时间</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['password']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at'] ?? ''); ?></td>
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
            <h3>🎯 联合查询注入Payload</h3>
            <div class="payload-list">
                <h4>1. 基本联合查询</h4>
                <code class="payload" data-payload="1 UNION SELECT 1,2,3,4,5">1 UNION SELECT 1,2,3,4,5</code>
                <code class="payload" data-payload="0 UNION SELECT 1,2,3,4,5">0 UNION SELECT 1,2,3,4,5</code>
                
                <h4>2. 查询用户表</h4>
                <code class="payload" data-payload="0 UNION SELECT id,username,password,email,created_at FROM users">0 UNION SELECT id,username,password,email,created_at FROM users</code>
                
                <h4>3. 查询产品表</h4>
                <code class="payload" data-payload="0 UNION SELECT id,name,price,category,now() FROM products">0 UNION SELECT id,name,price,category,now() FROM products</code>
                
                <h4>4. 查询flag表</h4>
                <code class="payload" data-payload="0 UNION SELECT id,flag,description,flag,created_at FROM flags">0 UNION SELECT id,flag,description,flag,created_at FROM flags</code>
                
                <h4>5. 查询数据库信息</h4>
                <code class="payload" data-payload="0 UNION SELECT 1,version(),database(),user(),now()">0 UNION SELECT 1,version(),database(),user(),now()</code>
            </div>
            
            <div id="flagBox" class="flag-box">
                🚩 FLAG{Union_Injection_Success}<br>
                <span style="font-size: 0.9rem;">你成功完成了联合查询注入！</span>
            </div>
        </div>

        <div class="card">
            <h3>📚 联合查询注入原理</h3>
            <div class="info-section">
                <strong>联合查询注入的基本步骤：</strong><br>
                1. 确定注入点和查询列数<br>
                2. 构造UNION查询<br>
                3. 选择合适的查询目标<br>
                4. 提取敏感信息
            </div>
            <div class="sql-code">
-- 联合查询注入示例
SELECT * FROM users WHERE id=0 UNION SELECT id,username,password,email FROM users;

-- 解释：
-- 1. 0 使第一个查询返回空结果
-- 2. UNION 合并第二个查询的结果
-- 3. 第二个查询获取所有用户信息
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
        
        // 为所有payload元素添加点击事件
        document.addEventListener('DOMContentLoaded', function() {
            var payloads = document.querySelectorAll('.payload');
            payloads.forEach(function(payload) {
                payload.addEventListener('click', function() {
                    var payloadValue = this.getAttribute('data-payload');
                    setId(payloadValue);
                });
            });
        });
        
        // 检测是否成功注入
        window.onload = function() {
            const url = window.location.href;
            if (url.includes('id=') && (url.includes('union') || url.includes('UNION'))) {
                document.getElementById('flagBox').classList.add('show');
            }
        };
    </script>
</body>
</html>
