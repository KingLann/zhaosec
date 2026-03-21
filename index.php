<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web安全漏洞演示导航页</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Web安全漏洞演示导航页</h1>
        <p>本页面仅用于安全学习，禁止用于非法用途</p>
    </header>

    <div class="container">
        <!-- SQL注入漏洞 -->
        <div class="vulnerability-card">
            <h2 class="card-title">SQL注入 (SQL Injection)</h2>
            <p class="card-desc">通过在输入框中插入SQL语句，绕过验证或获取/篡改数据库数据，是最常见的Web安全漏洞之一。</p>
            <a href="./sql-injection.html" class="btn">演示入口</a>
            <a href="https://owasp.org/www-community/attacks/SQL_Injection" target="_blank" class="btn">官方文档</a>
            <div class="defense-tips">
                <strong>防御建议：</strong><br>
                1. 使用预编译语句（Prepared Statement）<br>
                2. 实施输入验证和过滤<br>
                3. 最小权限原则配置数据库账号<br>
                4. 使用ORM框架减少手动SQL编写
            </div>
        </div>

        <!-- XSS跨站脚本漏洞 -->
        <div class="vulnerability-card">
            <h2 class="card-title">XSS跨站脚本 (Cross-Site Scripting)</h2>
            <p class="card-desc">攻击者注入恶意JavaScript代码，当用户访问页面时执行，可窃取Cookie、伪造操作、钓鱼等。</p>
            <a href="./xss-demo.html" class="btn">演示入口</a>
            <a href="https://owasp.org/www-community/attacks/xss/" target="_blank" class="btn">官方文档</a>
            <div class="defense-tips">
                <strong>防御建议：</strong><br>
                1. 对输出内容进行HTML实体编码<br>
                2. 使用CSP（内容安全策略）<br>
                3. 设置Cookie的HttpOnly属性<br>
                4. 输入验证和白名单过滤
            </div>
        </div>

        <!-- CSRF跨站请求伪造 -->
        <div class="vulnerability-card">
            <h2 class="card-title">CSRF跨站请求伪造</h2>
            <p class="card-desc">利用用户已登录的身份，诱导用户点击恶意链接或访问恶意页面，执行非本意的操作（如转账、改密码）。</p>
            <a href="./csrf-demo.html" class="btn">演示入口</a>
            <a href="https://owasp.org/www-community/attacks/csrf" target="_blank" class="btn">官方文档</a>
            <div class="defense-tips">
                <strong>防御建议：</strong><br>
                1. 使用CSRF Token验证<br>
                2. 验证Referer/Origin请求头<br>
                3. 重要操作增加二次验证<br>
                4. 设置SameSite Cookie属性
            </div>
        </div>

        <!-- 文件上传漏洞 -->
        <div class="vulnerability-card">
            <h2 class="card-title">文件上传漏洞</h2>
            <p class="card-desc">未对上传文件的类型、大小、内容进行严格验证，导致攻击者上传恶意脚本文件并执行。</p>
            <a href="./file-upload.html" class="btn">演示入口</a>
            <a href="https://owasp.org/www-community/vulnerabilities/Unrestricted_File_Upload" target="_blank" class="btn">官方文档</a>
            <div class="defense-tips">
                <strong>防御建议：</strong><br>
                1. 验证文件类型（后缀+MIME+内容）<br>
                2. 上传文件重命名，存储在非Web访问目录<br>
                3. 限制文件大小和上传频率<br>
                4. 使用单独的域名存储上传文件
            </div>
        </div>

        <!-- 弱口令/暴力破解 -->
        <div class="vulnerability-card">
            <h2 class="card-title">弱口令/暴力破解</h2>
            <p class="card-desc">用户使用简单密码（如123456、admin），或系统未限制登录尝试次数，导致账号被破解。</p>
            <a href="./weak-password.html" class="btn">演示入口</a>
            <a href="https://owasp.org/www-community/attacks/Brute_force_attack" target="_blank" class="btn">官方文档</a>
            <div class="defense-tips">
                <strong>防御建议：</strong><br>
                1. 强制密码复杂度要求<br>
                2. 限制登录失败次数（如5次后锁定）<br>
                3. 使用验证码/短信验证<br>
                4. 实施账号异常登录检测
            </div>
        </div>

        <!-- 路径遍历漏洞 -->
        <div class="vulnerability-card">
            <h2 class="card-title">路径遍历 (Path Traversal)</h2>
            <p class="card-desc">通过输入特殊路径（如../），绕过访问限制，读取服务器上的敏感文件（如/etc/passwd、config.php）。</p>
            <a href="./path-traversal.html" class="btn">演示入口</a>
            <a href="https://owasp.org/www-community/attacks/Path_Traversal" target="_blank" class="btn">官方文档</a>
            <div class="defense-tips">
                <strong>防御建议：</strong><br>
                1. 验证和规范化用户输入的路径<br>
                2. 将访问限制在指定目录（白名单）<br>
                3. 避免使用用户输入直接拼接文件路径<br>
                4. 配置服务器禁止目录遍历
            </div>
        </div>
    </div>

    <footer>
        <p>© 2026 Web安全学习导航页 | 仅用于安全研究与学习</p>
    </footer>
</body>
</html>