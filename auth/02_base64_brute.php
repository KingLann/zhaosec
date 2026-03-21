<?php
session_start();
$error = '';

// 用户数据库
$users = [
    'admin' => '123456',
    'test' => 'password',
    'user' => '123456789'
];

// 检查是否有HTTP基础认证头
if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
    $username = $_SERVER['PHP_AUTH_USER'];
    $password = $_SERVER['PHP_AUTH_PW'];
    
    // 验证用户名密码
    if (isset($users[$username]) && $users[$username] === $password) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['flag'] = 'FLAG{Base64_Encoding_Bypass_Success}';
        $_SESSION['vuln_name'] = 'Base64编码爆破';
        header('Location: success.php');
        exit;
    } else {
        $error = '认证失败';
    }
}

// 如果没有认证或认证失败，发送401响应要求认证
if (!isset($_SESSION['logged_in']) || $error) {
    header('WWW-Authenticate: Basic realm="Secure Area"');
    header('HTTP/1.0 401 Unauthorized');
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base64编码爆破 - HTTP基础认证</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            margin-bottom: 25px;
        }
        .card h2 {
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .alert-info h4 {
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        .code-block {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin: 15px 0;
        }
        .info-section {
            background: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 20px;
            border-radius: 0 10px 10px 0;
            margin-bottom: 25px;
        }
        .demo-form {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin: 20px 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #f5576c;
        }
        .btn {
            padding: 14px 30px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .encoded-display {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            word-break: break-all;
            margin-top: 10px;
            border: 1px solid #bee5eb;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #f5576c;
            text-decoration: none;
            font-weight: 600;
        }
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .tab-btn {
            padding: 10px 20px;
            border: none;
            background: #e9ecef;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        .tab-btn.active {
            background: #f5576c;
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .highlight {
            background: #ffeb3b;
            padding: 2px 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="alert-info">
                <h4>🔐 HTTP基础认证 (Basic Auth)</h4>
                <p>本场景使用HTTP基础认证，用户名密码会以Base64编码形式在Authorization头中传输。</p>
            </div>
            
            <h2>2. 🔐 Base64编码爆破</h2>
            
            <div class="info-section">
                <strong>💡 漏洞说明：</strong><br>
                HTTP基础认证使用Base64编码传输凭证，格式为：<code>username:password</code><br>
                虽然进行了编码，但Base64是可逆的，攻击者可以拦截请求后解码获取明文，或直接进行暴力破解。<br>
                <br>
                <strong>测试账号：</strong> admin / 123456, test / password, user / 123456789
            </div>
        </div>

        <div class="card">
            <h3>🎯 在线编码演示</h3>
            <div class="demo-form">
                <div class="form-group">
                    <label>用户名</label>
                    <input type="text" id="demo_username" placeholder="输入用户名" value="admin">
                </div>
                <div class="form-group">
                    <label>密码</label>
                    <input type="text" id="demo_password" placeholder="输入密码" value="123456">
                </div>
                <button class="btn" onclick="encodeCredentials()">生成Base64编码</button>
                
                <div style="margin-top: 20px;">
                    <strong>编码结果：</strong>
                    <div class="encoded-display" id="encoded_result">
                        点击按钮查看Base64编码结果
                    </div>
                </div>
                
                <div style="margin-top: 15px;">
                    <strong>HTTP请求头：</strong>
                    <div class="code-block" id="http_header">
Authorization: Basic <span class="highlight">等待生成...</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>🔧 漏洞利用方法</h3>
            <div class="tabs">
                <button class="tab-btn active" onclick="showTab('method1')">方法1: 浏览器认证</button>
                <button class="tab-btn" onclick="showTab('method2')">方法2: 手动构造</button>
                <button class="tab-btn" onclick="showTab('method3')">方法3: Burp爆破</button>
            </div>
            
            <div id="method1" class="tab-content active">
                <p>点击下面按钮，浏览器会弹出认证对话框：</p>
                <button class="btn" onclick="triggerAuth()">触发HTTP基础认证</button>
                <p style="margin-top: 15px; color: #666;">
                    在弹出的对话框中输入：<code>admin</code> / <code>123456</code>
                </p>
            </div>
            
            <div id="method2" class="tab-content">
                <p>使用JavaScript手动构造Authorization头：</p>
                <div class="code-block">// 构造Base64凭证
const credentials = btoa('admin:123456');
// 结果: YWRtaW46MTIzNDU2

// 发送请求
fetch('/auth/02_base64_brute.php', {
    headers: {
        'Authorization': 'Basic ' + credentials
    }
});</div>
                <button class="btn" onclick="manualAuth()">使用编码登录</button>
            </div>
            
            <div id="method3" class="tab-content">
                <p>使用Burp Suite进行暴力破解：</p>
                <ol style="line-height: 2;">
                    <li>拦截请求，找到Authorization头的Basic值</li>
                    <li>发送到Intruder，选择Base64编码的payload位置</li>
                    <li>配置Payload Processing：Base64解码 → 修改 → Base64编码</li>
                    <li>或使用自定义迭代器：username:password 格式</li>
                </ol>
            </div>
            
            <a href="index.php" class="back-link">← 返回身份认证首页</a>
        </div>
    </div>

    <script>
        function encodeCredentials() {
            const username = document.getElementById('demo_username').value;
            const password = document.getElementById('demo_password').value;
            const combined = username + ':' + password;
            const encoded = btoa(combined);
            
            document.getElementById('encoded_result').innerHTML = 
                `<strong>明文：</strong>${combined}<br><strong>Base64：</strong>${encoded}`;
            document.getElementById('http_header').innerHTML = 
                `Authorization: Basic <span class="highlight">${encoded}</span>`;
        }
        
        function triggerAuth() {
            // 使用错误的凭据触发401，然后浏览器会弹出认证框
            fetch('02_base64_brute.php', {
                headers: {
                    'Authorization': 'Basic ' + btoa('trigger:trigger')
                }
            }).then(() => {
                location.reload();
            });
        }
        
        function manualAuth() {
            const username = document.getElementById('demo_username').value;
            const password = document.getElementById('demo_password').value;
            const encoded = btoa(username + ':' + password);
            
            fetch('02_base64_brute.php', {
                headers: {
                    'Authorization': 'Basic ' + encoded
                }
            }).then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    location.reload();
                }
            });
        }
        
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
        
        // 页面加载时自动编码默认凭据
        window.onload = function() {
            encodeCredentials();
        };
    </script>
</body>
</html>
