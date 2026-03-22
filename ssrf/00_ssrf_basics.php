<?php
$module_name = 'SSRF漏洞基础';
$module_icon = '📚';
$module_desc = '学习服务端请求伪造(SSRF)漏洞的基本概念、类型、原理和防御方法。';

$content = <<<'EOT'
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">📚 SSRF漏洞基础</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>💡 学习目标：</strong><br>
            理解服务端请求伪造(SSRF)漏洞的基本概念、攻击原理、常见类型和防御方法。
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🌐 什么是SSRF？</h6>
            </div>
            <div class="card-body">
                <p><strong>SSRF（Server-Side Request Forgery，服务端请求伪造）</strong>是一种攻击，攻击者诱导服务器向其控制的目标发送请求，利用服务器作为跳板来访问内部网络资源或执行其他操作。</p>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card bg-light h-100">
                            <div class="card-body">
                                <h6 class="card-title">🔍 核心原理</h6>
                                <p class="card-text">应用程序允许用户提供URL参数，服务器根据该参数发起请求，但未对请求目标进行充分验证。</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light h-100">
                            <div class="card-body">
                                <h6 class="card-title">🎯 攻击特点</h6>
                                <ul class="mb-0">
                                    <li>利用服务器作为代理</li>
                                    <li>访问内部网络资源</li>
                                    <li>绕过网络访问控制</li>
                                    <li>执行未授权操作</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>📊 SSRF攻击流程图</h6>
            </div>
            <div class="card-body">
                <div class="mermaid-container" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                    <pre class="mermaid">
flowchart TD
    A[攻击者] -->|构造恶意URL| B[Web应用]
    B -->|发起请求| C[内部资源]
    C -->|返回数据| B
    B -->|返回响应| A
    
    style A fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
    style C fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
                    </pre>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🏷️ SSRF攻击类型</h6>
            </div>
            <div class="card-body">
                <div class="accordion" id="ssrfTypes">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                🔴 基础SSRF
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#ssrfTypes">
                            <div class="accordion-body">
                                <p>最基本的SSRF攻击，通过控制URL参数来诱导服务器访问指定目标。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击示例</h6>
                                                <pre class="mb-0"><code>// 前端URL
http://example.com/fetch?url=http://internal-service/api

// 后端代码
$url = $_GET['url'];
$response = file_get_contents($url);
echo $response;</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>常见触发点</h6>
                                                <ul class="mb-0">
                                                    <li>URL预览功能</li>
                                                    <li>图片加载服务</li>
                                                    <li>API调用代理</li>
                                                    <li>文件下载服务</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                🟠 内网探测
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#ssrfTypes">
                            <div class="accordion-body">
                                <p>利用SSRF探测内网服务和端口，发现内部网络结构。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击示例</h6>
                                                <pre class="mb-0"><code>// 探测内网服务
http://example.com/fetch?url=http://127.0.0.1:3306

// 探测内网端口
http://example.com/fetch?url=http://192.168.1.1:22</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击目的</h6>
                                                <ul class="mb-0">
                                                    <li>发现内部服务</li>
                                                    <li>识别内网拓扑</li>
                                                    <li>寻找未暴露的服务</li>
                                                    <li>探测敏感端口</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mermaid-container" style="background: #fff; padding: 15px; border-radius: 10px; margin: 15px 0; border: 1px solid #ddd;">
                                    <pre class="mermaid">
sequenceDiagram
        participant A as 攻击者
        participant S as 服务器
        participant I as 内网服务
        
        A->>S: 请求: http://127.0.0.1:3306
        S->>I: 访问内网MySQL
        I->>S: 返回MySQL响应
        S->>A: 显示响应信息
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                🟡 云元数据攻击
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#ssrfTypes">
                            <div class="accordion-body">
                                <p>针对云服务的元数据API进行攻击，获取云服务器凭证和配置信息。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击示例</h6>
                                                <pre class="mb-0"><code>// AWS元数据
http://example.com/fetch?url=http://169.254.169.254/latest/meta-data/

// GCP元数据
http://example.com/fetch?url=http://metadata.google.internal/computeMetadata/v1/</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击危害</h6>
                                                <ul class="mb-0">
                                                    <li>获取临时凭证</li>
                                                    <li>访问云存储</li>
                                                    <li>执行云API操作</li>
                                                    <li>完全控制云资源</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
                                🟢 协议利用
                            </button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#ssrfTypes">
                            <div class="accordion-body">
                                <p>利用各种协议进行SSRF攻击，如file://、dict://、gopher://等。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击示例</h6>
                                                <pre class="mb-0"><code>// 读取本地文件
http://example.com/fetch?url=file:///etc/passwd

// 访问Redis
http://example.com/fetch?url=gopher://127.0.0.1:6379/_SET%20exploit%201</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>常用协议</h6>
                                                <ul class="mb-0">
                                                    <li>file:// - 读取本地文件</li>
                                                    <li>dict:// - 访问字典服务</li>
                                                    <li>gopher:// - 访问Gopher服务</li>
                                                    <li>ldap:// - 访问LDAP服务</li>
                                                    <li>ftp:// - 访问FTP服务</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading5">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5">
                                🔵 SSRF绕过
                            </button>
                        </h2>
                        <div id="collapse5" class="accordion-collapse collapse" data-bs-parent="#ssrfTypes">
                            <div class="accordion-body">
                                <p>各种绕过SSRF防护的技术和方法。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>绕过技术</h6>
                                                <ul class="mb-0">
                                                    <li>IP地址变换（127.0.0.1 → 0x7f000001）</li>
                                                    <li>DNS重绑定</li>
                                                    <li>协议混淆</li>
                                                    <li>利用URL编码</li>
                                                    <li>利用@符号</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>示例</h6>
                                                <pre class="mb-0"><code>// IP地址变换
http://example.com/fetch?url=http://0x7f000001

// 利用@符号
http://example.com/fetch?url=http://example.com@127.0.0.1</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>⚠️ SSRF漏洞的危害</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0">🔴 数据泄露</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li>读取本地敏感文件</li>
                                    <li>获取数据库凭证</li>
                                    <li>访问内网服务</li>
                                    <li>获取云服务凭证</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">🟠 服务攻击</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li>DoS攻击内部服务</li>
                                    <li>端口扫描</li>
                                    <li>攻击内网应用</li>
                                    <li>利用内部服务漏洞</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">🔵 横向移动</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li>内网渗透</li>
                                    <li>权限提升</li>
                                    <li>访问其他服务器</li>
                                    <li>绕过网络隔离</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🔧 SSRF漏洞防御方法</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card h-100 border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">✅ 核心防御措施</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li><strong>白名单验证：</strong>只允许访问特定域名和IP</li>
                                    <li><strong>URL解析：</strong>正确解析URL，避免混淆</li>
                                    <li><strong>协议限制：</strong>只允许HTTP/HTTPS协议</li>
                                    <li><strong>网络隔离：</strong>限制服务器网络访问</li>
                                    <li><strong>超时设置：</strong>防止DoS攻击</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">🔵 辅助防御措施</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li><strong>请求监控：</strong>记录和分析请求</li>
                                    <li><strong>云服务配置：</strong>限制元数据访问</li>
                                    <li><strong>使用代理：</strong>通过安全代理转发请求</li>
                                    <li><strong>网络ACL：</strong>配置防火墙规则</li>
                                    <li><strong>定期审计：</strong>检查SSRF防护措施</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mermaid-container" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                    <pre class="mermaid">
flowchart TD
    A[用户输入URL] --> B{URL验证}
    B -->|不在白名单| C[拒绝请求]
    B -->|在白名单| D{协议检查}
    D -->|非HTTP/HTTPS| C
    D -->|HTTP/HTTPS| E{网络检查}
    E -->|内网地址| C
    E -->|合法地址| F[发起请求]
    F --> G[返回响应]
    
    style B fill:#ffd43b,stroke:#333,stroke-width:2px
    style D fill:#ffd43b,stroke:#333,stroke-width:2px
    style E fill:#ffd43b,stroke:#333,stroke-width:2px
    style F fill:#51cf66,stroke:#333,stroke-width:2px,color:#fff
    style C fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
                    </pre>
                </div>

                <div class="card bg-light mt-3">
                    <div class="card-body">
                        <h6>💡 防御代码示例</h6>
                        <pre class="mb-0"><code>// 白名单验证
$allowed_domains = ['example.com', 'api.example.com'];
$url = $_GET['url'];
$parsed = parse_url($url);

if (!in_array($parsed['host'], $allowed_domains)) {
    die('访问被拒绝');
}

// 协议限制
if (!in_array($parsed['scheme'], ['http', 'https'])) {
    die('不支持的协议');
}

// 防止访问内网
$ip = gethostbyname($parsed['host']);
if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
    die('不能访问内网');
}

// 发起请求
$response = file_get_contents($url);
echo $response;</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/mermaid.min.js"></script>
<script>
// 确保mermaid加载完成后再初始化
if (typeof mermaid !== 'undefined') {
    mermaid.initialize({
        startOnLoad: true,
        theme: 'default',
        flowchart: {
            useMaxWidth: true,
            htmlLabels: true,
            curve: 'basis'
        },
        sequence: {
            useMaxWidth: true,
            wrap: true
        }
    });
}

// 监听折叠面板展开事件
document.addEventListener('DOMContentLoaded', function() {
    const accordionItems = document.querySelectorAll('.accordion-collapse');
    accordionItems.forEach(collapse => {
        collapse.addEventListener('shown.bs.collapse', function() {
            // 重新渲染mermaid
            if (typeof mermaid !== 'undefined') {
                mermaid.run();
            }
        });
    });
});
</script>
EOT;

include '../template/module_template.php';
?>
