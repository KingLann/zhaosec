<?php
// 反射型XSS漏洞演示
$search = $_GET['search'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>反射型XSS漏洞</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            border-left: 5px solid #667eea;
            padding: 20px;
            border-radius: 0 10px 10px 0;
            margin-bottom: 25px;
        }
        .search-box {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }
        .search-box input {
            flex: 1;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
        }
        .search-box button {
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        .result-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            border: 1px solid #dee2e6;
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
        .payload-list code:hover {
            text-decoration: underline;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
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
                <h4>🚨 反射型XSS漏洞</h4>
                <p>本页面存在反射型XSS漏洞，用户输入直接输出到页面，未进行任何过滤。</p>
            </div>
            
            <h2>1. 💉 反射型XSS</h2>
            
            <div class="info-section">
                <strong>💡 漏洞说明：</strong><br>
                反射型XSS是指恶意脚本通过URL参数传入，服务器将参数内容直接返回到页面中执行。<br>
                攻击者需要诱导用户点击包含恶意代码的链接。<br>
                <br>
                <strong>攻击场景：</strong>钓鱼邮件、恶意链接、论坛帖子等
            </div>
        </div>

        <div class="card">
            <h3>🔍 搜索功能（存在漏洞）</h3>
            <form method="GET">
                <div class="search-box">
                    <input type="text" name="search" placeholder="输入搜索内容..." value="<?php echo $search; ?>">
                    <button type="submit">搜索</button>
                </div>
            </form>
            
            <?php if ($search): ?>
            <div class="result-box">
                <strong>搜索结果：</strong>
                <div style="margin-top: 10px; padding: 10px; background: white; border-radius: 5px;">
                    <!-- 漏洞点：直接输出用户输入，未转义 -->
                    <?php echo $search; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3>🎯 常用攻击Payload</h3>
            <p>点击Payload自动填充到搜索框：</p>
            <div class="payload-list">
                <code onclick="setPayload('&lt;script&gt;alert(1)&lt;/script&gt;')">&lt;script&gt;alert(1)&lt;/script&gt;</code>
                <code onclick="setPayload('&lt;img src=x onerror=alert(1)&gt;')">&lt;img src=x onerror=alert(1)&gt;</code>
                <code onclick="setPayload('&lt;svg onload=alert(1)&gt;')">&lt;svg onload=alert(1)&gt;</code>
                <code onclick="setPayload('&lt;body onload=alert(1)&gt;')">&lt;body onload=alert(1)&gt;</code>
                <code onclick="setPayload('&lt;iframe src=javascript:alert(1)&gt;')">&lt;iframe src=javascript:alert(1)&gt;</code>
            </div>
            
            <div id="flagBox" class="flag-box">
                🚩 FLAG{Reflected_XSS_Vulnerability_Exploited}<br>
                <span style="font-size: 0.9rem;">你成功触发了反射型XSS！</span>
            </div>
        </div>

        <div class="card">
            <h3>🛡️ 防御方法</h3>
            <div class="info-section">
                1. <strong>输出编码：</strong>使用 htmlspecialchars() 函数对输出进行转义<br>
                2. <strong>输入验证：</strong>对用户输入进行白名单过滤<br>
                3. <strong>CSP策略：</strong>设置Content-Security-Policy头限制脚本执行<br>
                4. <strong>HttpOnly Cookie：</strong>防止XSS窃取Cookie
            </div>
            <a href="index.php" class="back-link">← 返回XSS模块首页</a>
        </div>
    </div>

    <script>
        function setPayload(payload) {
            document.querySelector('input[name="search"]').value = payload;
        }
        
        // 检测是否触发了XSS（检查URL中的多种XSS特征）
        const url = window.location.href;
        if (url.includes('search=')) {
            const searchParam = decodeURIComponent(url.split('search=')[1].split('&')[0]);
            const xssPatterns = [
                /<script[^>]*>/i,
                /<img[^>]+onerror=/i,
                /<svg[^>]+onload=/i,
                /<body[^>]+onload=/i,
                /<iframe[^>]+src=javascript:/i,
                /javascript:/i,
                /onerror=/i,
                /onload=/i,
                /onclick=/i,
                /onmouseover=/i
            ];
            
            const hasXSS = xssPatterns.some(pattern => pattern.test(searchParam));
            if (hasXSS) {
                document.getElementById('flagBox').classList.add('show');
            }
        }
    </script>
</body>
</html>
