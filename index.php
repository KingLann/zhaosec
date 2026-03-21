<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web安全漏洞靶场 - Zhaosec</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hero-section {
            background: var(--primary-gradient);
            color: white;
            padding: 80px 0;
            margin-bottom: 60px;
            text-align: center;
        }
        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .hero-section p {
            font-size: 1.3rem;
            opacity: 0.9;
        }
        .vuln-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            margin-bottom: 60px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .vuln-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
            border-radius: 20px 20px 0 0;
        }
        .row {
            margin-left: -30px;
            margin-right: -30px;
        }
        .col-lg-4,
        .col-md-6 {
            padding-left: 30px;
            padding-right: 30px;
        }
        .footer {
            text-align: center;
            padding: 40px 0;
            color: #666;
            margin-top: 60px;
        }
        .vuln-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.18);
            border-color: rgba(102, 126, 234, 0.3);
        }
        .vuln-card .icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
        }
        .vuln-card h5 {
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        .vuln-card p {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 15px;
        }
        .vuln-card .btn {
            border-radius: 25px;
            padding: 8px 25px;
            font-weight: 500;
        }
        .icon-auth { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .icon-xss { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .icon-logic { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .icon-upload { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .icon-rce { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .icon-sqli { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
        .icon-lfi { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); }
        .icon-xxe { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); }
        .icon-ssrf { background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%); }
        .icon-csrf { background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); }
        .icon-unserialize { background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%); }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .badge-level {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 15px;
        }
        .level-low { background: #d4edda; color: #155724; }
        .level-medium { background: #fff3cd; color: #856404; }
        .level-high { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="container">
            <h1>🛡️ Web安全漏洞靶场</h1>
            <p>Zhaosec Vulnerability Range - 学习Web安全，掌握漏洞原理</p>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="vuln-card">
                    <div class="icon icon-auth text-white">🔐</div>
                    <h5>身份认证漏洞 <span class="badge badge-level level-medium">中级</span></h5>
                    <p>包含弱密码、暴力破解、认证绕过、会话管理等身份认证相关漏洞场景。</p>
                    <a href="auth/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="vuln-card">
                    <div class="icon icon-xss text-white">📜</div>
                    <h5>XSS跨站脚本 <span class="badge badge-level level-medium">中级</span></h5>
                    <p>反射型XSS、存储型XSS、DOM型XSS等多种跨站脚本攻击场景。</p>
                    <a href="xss/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="vuln-card">
                    <div class="icon icon-logic text-white">🧩</div>
                    <h5>逻辑漏洞 <span class="badge badge-level level-high">高级</span></h5>
                    <p>越权访问、条件竞争、业务逻辑缺陷等逻辑层面的安全漏洞。</p>
                    <a href="logic/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="vuln-card">
                    <div class="icon icon-upload text-white">📁</div>
                    <h5>文件上传漏洞 <span class="badge badge-level level-high">高级</span></h5>
                    <p>绕过前端验证、MIME类型验证、扩展名验证等文件上传攻击场景。</p>
                    <a href="upload/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="vuln-card">
                    <div class="icon icon-rce text-white">⚡</div>
                    <h5>命令/代码执行 <span class="badge badge-level level-high">高级</span></h5>
                    <p>远程命令执行(RCE)、代码注入、eval注入等高危漏洞场景。</p>
                    <a href="rce/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="vuln-card">
                    <div class="icon icon-sqli text-white">💉</div>
                    <h5>SQL注入漏洞 <span class="badge badge-level level-high">高级</span></h5>
                    <p>联合注入、报错注入、盲注、二次注入等数据库攻击技术。</p>
                    <a href="sqli/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="vuln-card">
                    <div class="icon icon-lfi text-white">📂</div>
                    <h5>文件包含漏洞 <span class="badge badge-level level-medium">中级</span></h5>
                    <p>本地文件包含(LFI)、远程文件包含(RFI)、伪协议利用等场景。</p>
                    <a href="lfi/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="vuln-card">
                    <div class="icon icon-xxe text-white">📄</div>
                    <h5>XXE漏洞 <span class="badge badge-level level-high">高级</span></h5>
                    <p>XML外部实体注入，包含文件读取、SSRF、RCE等利用场景。</p>
                    <a href="xxe/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="vuln-card">
                    <div class="icon icon-ssrf text-white">🌐</div>
                    <h5>SSRF漏洞 <span class="badge badge-level level-high">高级</span></h5>
                    <p>服务端请求伪造，内网探测、云元数据获取、协议利用等场景。</p>
                    <a href="ssrf/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="vuln-card">
                    <div class="icon icon-csrf text-white">🔄</div>
                    <h5>CSRF漏洞 <span class="badge badge-level level-medium">中级</span></h5>
                    <p>跨站请求伪造，包含GET/POST型CSRF、JSONP劫持等场景。</p>
                    <a href="csrf/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="vuln-card">
                    <div class="icon icon-unserialize text-white">📦</div>
                    <h5>PHP反序列化 <span class="badge badge-level level-high">高级</span></h5>
                    <p>PHP对象注入、POP链构造、phar反序列化等高级利用技术。</p>
                    <a href="unserialize/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>⚠️ 本靶场仅供安全学习和研究使用，请勿用于非法用途</p>
            <p>Zhaosec Web Security Lab &copy; 2024</p>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
