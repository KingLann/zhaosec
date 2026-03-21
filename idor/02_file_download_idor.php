<?php
// 不安全的文件下载IDOR漏洞场景
$module_name = '不安全的文件下载';
$module_icon = '📁';
$module_desc = '演示通过IDOR漏洞下载任意文件的场景。';

$error = '';

// 处理文件下载请求
if (isset($_GET['file'])) {
    $file = $_GET['file'];
    
    // 危险：直接使用用户输入作为文件路径
    // 没有任何路径验证和过滤
    if (file_exists($file)) {
        // 直接下载文件
        $file_name = basename($file);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
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
                    <p class="mb-3">本场景模拟文件下载功能，点击以下文件图标尝试下载：</p>
                    
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="display-4 mb-3">📄</div>
                                    <h6 class="card-title">README.md</h6>
                                    <p class="text-muted small">项目说明文件</p>
                                    <a href="?file=../README.md" class="btn btn-primary mt-2">下载</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="display-4 mb-3">⚙️</div>
                                    <h6 class="card-title">.git/config</h6>
                                    <p class="text-muted small">Git配置文件</p>
                                    <a href="?file=../.git/config" class="btn btn-danger mt-2">下载</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="display-4 mb-3">💻</div>
                                    <h6 class="card-title">win.ini</h6>
                                    <p class="text-muted small">Windows配置文件</p>
                                    <a href="?file=../../../../windows/win.ini" class="btn btn-warning mt-2">下载</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mb-4">
                        <strong>🔧 攻击方法：</strong>
                        <ol>
                            <li>打开浏览器开发者工具（F12）</li>
                            <li>点击上方的下载按钮</li>
                            <li>在网络（Network）标签中查看请求</li>
                            <li>右键点击请求，选择"复制" → "复制请求URL"</li>
                            <li>修改URL中的 <code>file</code> 参数值，例如：<code>?file=../config.php</code></li>
                            <li>在浏览器地址栏中粘贴修改后的URL并访问</li>
                            <li>服务器将直接下载指定的文件</li>
                        </ol>
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
                        document.getElementById("downloadBtn").addEventListener("click", function() {
                            var filePath = document.getElementById("filePath").value;
                            if (filePath) {
                                window.location.href = "?file=" + encodeURIComponent(filePath);
                            }
                        });
                    </script>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>// 危险的文件下载代码
if (isset($_GET["file"])) {
    $file = $_GET["file"];
    
    // 漏洞：直接使用用户输入作为文件路径
    // 没有任何路径验证和过滤
    if (file_exists($file)) {
        // 直接下载文件
        $file_name = basename($file);
        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Pragma: public");
        header("Content-Length: " . filesize($file));
        readfile($file);
        exit;
    } else {
        $error = "文件不存在！";
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
                        <li><strong>使用安全的文件读取函数</strong> - 避免直接使用readfile</li>
                        <li><strong>设置正确的Content-Type</strong> - 防止浏览器执行恶意文件</li>
                    </ol>

                    <h5 class="mb-3 mt-4">修复后的代码</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 安全的文件下载代码
if (isset($_GET["file_id"])) {
    $file_id = $_GET["file_id"];
    
    // 白名单映射
    $allowed_files = [
        1 => "docs/readme.md",
        2 => "docs/manual.pdf",
        3 => "docs/example.txt"
    ];
    
    if (isset($allowed_files[$file_id])) {
        $file = $allowed_files[$file_id];
        $file_path = realpath("./files/" . $file);
        
        // 验证文件路径是否在允许的目录内
        $base_dir = realpath("./files/");
        if (strpos($file_path, $base_dir) === 0 && file_exists($file_path)) {
            // 安全地下载文件
            $file_name = basename($file_path);
            header("Content-Description: File Transfer");
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
            header("Expires: 0");
            header("Cache-Control: must-revalidate");
            header("Pragma: public");
            header("Content-Length: " . filesize($file_path));
            readfile($file_path);
            exit;
        }
    }
    
    $error = "无效的文件请求！";
}</code></pre>
                </div>
            </div>
        </div>
    </div>';

include '../template/module_template.php';
?>