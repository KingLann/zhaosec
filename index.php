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
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
            height: 100%;
            position: relative;
        }
        .vuln-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
            border-color: #667eea;
        }
        .row {
            margin-left: -15px;
            margin-right: -15px;
        }
        .col-lg-3,
        .col-md-4,
        .col-sm-6 {
            padding-left: 15px;
            padding-right: 15px;
        }
        .footer {
            text-align: center;
            padding: 40px 0;
            color: #666;
            margin-top: 60px;
        }
        .vuln-card .icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 15px;
            background: #667eea;
            color: white;
        }
        .vuln-card h5 {
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
            font-size: 1.1rem;
        }
        .vuln-card p {
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 15px;
            line-height: 1.4;
        }
        .vuln-card .btn {
            border-radius: 4px;
            padding: 6px 20px;
            font-weight: 500;
            font-size: 0.85rem;
            background: #667eea;
            border: none;
            color: white;
        }
        .vuln-card .btn:hover {
            background: #5a6fd8;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }

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
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="vuln-card">
                    <div class="icon icon-logic text-white">🧩</div>
                    <h5>逻辑漏洞</h5>
                    <p>包含水平/垂直越权、并发漏洞、支付篡改、短信轰炸、任意密码重置、未授权访问等逻辑漏洞演示。</p>
                    <a href="logic/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="vuln-card">
                    <div class="icon icon-xss text-white">📜</div>
                    <h5>XSS跨站脚本</h5>
                    <p>演示反射型、存储型、DOM型XSS及常见绕过技术。</p>
                    <a href="xss/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="vuln-card">
                    <div class="icon icon-auth text-white">🔐</div>
                    <h5>身份认证漏洞</h5>
                    <p>演示弱密码、未授权访问、越权操作、JWT攻击等认证安全问题。</p>
                    <a href="auth/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="vuln-card">
                    <div class="icon icon-upload text-white">📁</div>
                    <h5>文件上传漏洞</h5>
                    <p>演示不安全的文件上传功能如何导致服务器入侵。</p>
                    <a href="upload/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="vuln-card">
                    <div class="icon icon-rce text-white">⚡</div>
                    <h5>命令/代码执行</h5>
                    <p>演示系统命令执行、PHP代码执行及常见绕过技术。</p>
                    <a href="rce/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="vuln-card">
                    <div class="icon icon-sqli text-white">💉</div>
                    <h5>SQL注入漏洞</h5>
                    <p>演示过滤的SQL查询如何泄露或修改数据库信息。</p>
                    <a href="sqli/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="vuln-card">
                    <div class="icon icon-lfi text-white">📂</div>
                    <h5>文件包含漏洞</h5>
                    <p>演示本地文件包含、远程文件包含及PHP伪协议利用。</p>
                    <a href="lfi/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="vuln-card">
                    <div class="icon icon-xxe text-white">📄</div>
                    <h5>XXE漏洞</h5>
                    <p>演示XML外部实体注入、Blind XXE及内网攻击。</p>
                    <a href="xxe/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="vuln-card">
                    <div class="icon icon-ssrf text-white">🌐</div>
                    <h5>SSRF漏洞</h5>
                    <p>演示服务端请求伪造漏洞及利用技术。</p>
                    <a href="ssrf/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="vuln-card">
                    <div class="icon icon-csrf text-white">🔄</div>
                    <h5>CSRF漏洞</h5>
                    <p>演示跨站请求伪造漏洞及防护技术。</p>
                    <a href="csrf/" class="btn btn-outline-primary">进入演练</a>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="vuln-card">
                    <div class="icon icon-unserialize text-white">📦</div>
                    <h5>PHP反序列化</h5>
                    <p>演示PHP反序列化漏洞及利用技术。</p>
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
