<?php
// DOM型XSS漏洞演示
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOM型XSS漏洞</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
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
        .demo-box {
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
        .output-box {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            margin-top: 15px;
            min-height: 50px;
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
        .payload-list {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            margin: 15px 0;
        }
        .payload-list code {
            color: #7ee787;
            cursor: pointer;
            display: block;
            padding: 5px 0;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #4facfe;
            text-decoration: none;
            font-weight: 600;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="alert-danger">
                <h4>🚨 DOM型XSS漏洞</h4>
                <p>本页面存在DOM型XSS漏洞，通过JavaScript操作DOM时使用了不安全的函数。</p>
            </div>
            
            <h2>3. 🌲 DOM型XSS</h2>
            
            <div class="info-section">
                <strong>💡 漏洞说明：</strong><br>
                DOM型XSS是指通过修改页面的DOM结构来执行恶意脚本，整个过程在客户端完成，不经过服务器。<br>
                攻击者通过构造特殊的URL片段（如#后面的内容）来触发漏洞。<br>
                <br>
                <strong>特点：</strong>服务器无法检测，WAF难以防御
            </div>
        </div>

        <div class="card">
            <h3>🎯 漏洞演示 1：innerHTML</h3>
            <div class="demo-box">
                <p>使用 innerHTML 直接插入用户输入（危险）：</p>
                <div class="input-group">
                    <input type="text" id="input1" placeholder="输入内容...">
                    <button class="btn" onclick="demoInnerHTML()">插入</button>
                </div>
                <div id="output1" class="output-box">输出将显示在这里...</div>
            </div>
            
            <div class="code-block">// 漏洞代码
function demoInnerHTML() {
    const input = document.getElementById('input1').value;
    document.getElementById('output1').innerHTML = input; // 危险！
}</div>
        </div>

        <div class="card">
            <h3>🎯 漏洞演示 2：document.write</h3>
            <div class="demo-box">
                <p>从URL参数读取并直接写入页面：</p>
                <a href="?name=<script>alert('DOM XSS')</script>" class="btn" style="text-decoration: none; display: inline-block;">点击触发漏洞链接</a>
                <div id="output2" class="output-box" style="margin-top: 15px;"></div>
            </div>
            
            <div class="code-block">// 漏洞代码
const params = new URLSearchParams(window.location.search);
const name = params.get('name');
if (name) {
    document.write('欢迎：' + name); // 危险！
}</div>
        </div>

        <div class="card">
            <h3>🎯 漏洞演示 3：location.hash</h3>
            <div class="demo-box">
                <p>从URL hash读取内容（#后面的部分）：</p>
                <p style="margin: 10px 0;">尝试访问：<code style="background: #f0f0f0; padding: 5px; border-radius: 5px;">03_dom.php#&lt;img src=x onerror=alert(1)&gt;</code></p>
                <div id="output3" class="output-box">Hash内容将显示在这里...</div>
            </div>
        </div>

        <div class="card">
            <h3>🎯 攻击Payload</h3>
            <div class="payload-list">
                <code onclick="setInput1('&lt;img src=x onerror=alert(1)&gt;')">&lt;img src=x onerror=alert(1)&gt;</code>
                <code onclick="setInput1('&lt;svg onload=alert(1)&gt;')">&lt;svg onload=alert(1)&gt;</code>
                <code onclick="setInput1('&lt;iframe src=javascript:alert(1)&gt;')">&lt;iframe src=javascript:alert(1)&gt;</code>
                <code onclick="setInput1('&lt;a href=javascript:alert(1)&gt;点击我&lt;/a&gt;')">&lt;a href=javascript:alert(1)&gt;点击我&lt;/a&gt;</code>
            </div>
            
            <div id="flagBox" class="flag-box">
                🚩 FLAG{DOM_Based_XSS_Vulnerability}<br>
                <span style="font-size: 0.9rem;">你成功触发了DOM型XSS！</span>
            </div>
        </div>

        <div class="card">
            <h3>🛡️ 防御方法</h3>
            <div class="info-section">
                1. <strong>使用textContent代替innerHTML：</strong>textContent不会解析HTML标签<br>
                2. <strong>使用DOMPurify库：</strong>对用户输入进行净化处理<br>
                3. <strong>避免使用危险函数：</strong>如document.write、eval等<br>
                4. <strong>CSP策略：</strong>限制内联脚本的执行
            </div>
            <a href="index.php" class="back-link">← 返回XSS模块首页</a>
        </div>
    </div>

    <script>
        // 漏洞演示1：innerHTML
        function demoInnerHTML() {
            const input = document.getElementById('input1').value;
            document.getElementById('output1').innerHTML = input;
            
            // 检测是否触发了XSS
            if (input.includes('<script>') || input.includes('onerror=') || 
                input.includes('onload=') || input.includes('javascript:')) {
                document.getElementById('flagBox').classList.add('show');
            }
        }
        
        // 漏洞演示2：document.write
        const params = new URLSearchParams(window.location.search);
        const name = params.get('name');
        if (name) {
            document.getElementById('output2').innerHTML = '欢迎：' + name;
            if (name.includes('<script>') || name.includes('onerror=')) {
                document.getElementById('flagBox').classList.add('show');
            }
        }
        
        // 漏洞演示3：location.hash
        if (window.location.hash) {
            const hash = decodeURIComponent(window.location.hash.substring(1));
            document.getElementById('output3').innerHTML = 'Hash内容：' + hash;
            if (hash.includes('<') && (hash.includes('onerror=') || hash.includes('onload='))) {
                document.getElementById('flagBox').classList.add('show');
            }
        }
        
        function setInput1(value) {
            document.getElementById('input1').value = value;
        }
    </script>
</body>
</html>
