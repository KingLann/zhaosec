<?php
// 不安全的文件下载IDOR漏洞场景
$module_name = '不安全的文件下载';
$module_icon = '📁';
$module_desc = '演示通过IDOR漏洞下载任意文件的场景。';

$error = '';
$file_content = '';
$file_name = '';

// 处理文件下载请求
if (isset($_GET['file'])) {
    $file = $_GET['file'];
    
    // 危险：直接使用用户输入作为文件路径
    // 没有任何路径验证和过滤
    if (file_exists($file)) {
        // 直接输出文件内容
        $file_name = basename($file);
        $file_content = file_get_contents($file);
    } else {
        $error = '文件不存在！';
    }
}

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">📁 不安全的文件下载</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示不安全的文件下载漏洞，攻击者可以通过直接指定文件路径来下载服务器上的任意文件。<br>
                这是一种典型的IDOR（不安全直接对象引用）漏洞，当应用程序直接使用用户输入作为文件路径而没有进行适当的验证和过滤时发生。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞原理</h6>
                </div>
                <div class="card-body">
                    <p>不安全的文件下载漏洞的核心在于：</p>
                    <ol>
                        <li>应用程序允许用户通过参数指定要下载的文件</li>
                        <li>没有对文件路径进行验证和过滤</li>
                        <li>没有限制可访问的文件范围</li>
                        <li>攻击者可以使用路径遍历技术（如 ../）访问系统文件</li>
                    </ol>

                    <h5 class="mb-3 mt-4">攻击流程</h5>
                    <div class="bg-light p-3 rounded border">
                        <script src="../assets/js/mermaid.min.js"></script>
                        <div class="mermaid">
                            flowchart TD
                                A[攻击者] --> B[构造恶意文件路径]
                                B --> C[发送请求: ?file=../敏感文件]
                                C --> D[服务器直接读取文件]
                                D --> E[返回文件内容给攻击者]
                            
                            style A fill:#f9f,stroke:#333,stroke-width:2px
                            style E fill:#f99,stroke:#333,stroke-width:2px
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">尝试下载以下文件：</p>
                    
                    <div class="btn-group mb-3" role="group">
                        <a href="?file=../README.md" class="btn btn-primary">下载README.md</a>
                        <a href="?file=../.git/config" class="btn btn-danger">下载.git/config</a>
                        <a href="?file=../../../../windows/win.ini" class="btn btn-warning">下载win.ini</a>
                    </div>

                    <div class="form-group mb-3">
                        <label for="filePath">自定义文件路径：</label>
                        <div class="input-group">
                            <input type="text" id="filePath" class="form-control" placeholder="例如：../config.php">
                            <div class="input-group-append">
                                <button id="downloadBtn" class="btn btn-primary">下载</button>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.getElementById('downloadBtn').addEventListener('click', function() {
                            var filePath = document.getElementById('filePath').value;
                            if (filePath) {
                                window.location.href = '?file=' + encodeURIComponent(filePath);
                            }
                        });
                    </script>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>📄 文件内容</h6>
                </div>
                <div class="card-body">
                    ';

if ($error) {
    $content .= '<div class="alert alert-danger">
                        <strong>错误：</strong>
                        <p>' . htmlspecialchars($error) . '</p>
                    </div>';
}

if ($file_content) {
    $content .= '<div class="alert alert-success">
                        <strong>文件：</strong> ' . htmlspecialchars($file_name) . '<br>
                        <strong>内容：</strong>
                        <pre class="bg-dark text-light p-3 rounded mt-2"><code>' . htmlspecialchars($file_content) . '</code></pre>
                    </div>';
}

$content .= '                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>// 危险的文件下载代码
if (isset($_GET[&#39;file&#39;])) {
    $file = $_GET[&#39;file&#39;];
    
    // 漏洞：直接使用用户输入作为文件路径
    // 没有任何路径验证和过滤
    if (file_exists($file)) {
        // 直接输出文件内容
        $file_name = basename($file);
        $file_content = file_get_contents($file);
    } else {
        $error = &#39;文件不存在！&#39;;
    }
}</code></pre>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 防御建议</h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li><strong>使用白名单验证</strong> - 只允许下载预定义的文件列表</li>
                        <li><strong>限制文件路径</strong> - 将文件路径限制在特定目录内</li>
                        <li><strong>使用文件ID映射</strong> - 使用文件ID而不是直接路径</li>
                        <li><strong>验证文件类型</strong> - 只允许下载特定类型的文件</li>
                        <li><strong>使用安全的文件读取函数</strong> - 避免直接使用file_get_contents</li>
                        <li><strong>设置正确的Content-Type</strong> - 防止浏览器执行恶意文件</li>
                    </ol>

                    <h5 class="mb-3 mt-4">修复后的代码</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 安全的文件下载代码
if (isset($_GET[&#39;file_id&#39;])) {
    $file_id = $_GET[&#39;file_id&#39;];
    
    // 白名单映射
    $allowed_files = [
        1 => &#39;docs/readme.md&#39;,
        2 => &#39;docs/manual.pdf&#39;,
        3 => &#39;docs/example.txt&#39;
    ];
    
    if (isset($allowed_files[$file_id])) {
        $file = $allowed_files[$file_id];
        $file_path = realpath(&#39;./files/&#39; . $file);
        
        // 验证文件路径是否在允许的目录内
        $base_dir = realpath(&#39;./files/&#39;);
        if (strpos($file_path, $base_dir) === 0 && file_exists($file_path)) {
            // 安全地输出文件
            $file_name = basename($file_path);
            header(&#39;Content-Disposition: attachment; filename="&#39; . $file_name . &#39;"&#39;);
            readfile($file_path);
            exit;
        }
    }
    
    $error = &#39;无效的文件请求！&#39;;
}</code></pre>
                </div>
            </div>
        </div>
    </div>';

include '../template/module_template.php';
?>