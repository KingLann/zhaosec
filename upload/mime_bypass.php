<?php
// MIME类型绕过漏洞
$module_name = '03. MIME类型绕过';
$module_icon = '🔍';
$module_desc = '仅验证Content-Type请求头，可通过修改请求头绕过验证。';

// 漏洞代码
$message = '';
$uploaded_file = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        
        // 漏洞：仅验证Content-Type，可被绕过
        $allowed_mime = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowed_mime)) {
            $message = '仅允许上传图片文件！';
        } else {
            $upload_dir = 'uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $target_file = $upload_dir . basename($file['name']);
            
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $message = '文件上传成功！';
                $uploaded_file = $target_file;
            } else {
                $message = '文件上传失败！';
            }
        }
    }
}

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🔍 MIME类型绕过</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示MIME类型绕过漏洞。<br>
                服务器仅验证Content-Type请求头，攻击者可以通过修改请求头中的Content-Type值来绕过验证，上传非图片文件。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // 漏洞：仅验证Content-Type，可被绕过
    $allowed_mime = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_mime)) {
        $message = '仅允许上传图片文件！';
    } else {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $target_file = $upload_dir . basename($file['name']);
        
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            $message = '文件上传成功！';
        } else {
            $message = '文件上传失败！';
        }
    }
}</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">本场景演示MIME类型绕过漏洞，尝试以下攻击方法：</p>

                    <h5 class="mb-2">方法：使用Burp Suite修改Content-Type</h5>
                    <ol>
                        <li>准备一个PHP文件，例如：</li>
                        <pre class="bg-dark text-light p-3 rounded"><code>&lt;?php
// webshell.php
system($_GET[\'cmd\']);
?&gt;</code></pre>
                        <li>使用Burp Suite拦截文件上传请求</li>
                        <li>修改Content-Type为图片类型，例如：</li>
                        <pre class="bg-dark text-light p-3 rounded"><code>Content-Type: image/jpeg</code></pre>
                        <li>保持文件名不变（如webshell.php）</li>
                        <li>放行请求，文件将被成功上传</li>
                        <li>访问上传后的文件，例如：<code>http://localhost/zhaosec/upload/uploads/webshell.php?cmd=whoami</code></li>
                    </ol>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 实际测试</h6>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="mb-3">
                        <div class="mb-3">
                            <label for="file" class="form-label">选择文件</label>
                            <input type="file" name="file" id="file" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-danger">上传文件</button>
                    </form>

                    ';

if ($message) {
    if (strpos($message, '成功') !== false) {
        $content .= '<div class="alert alert-success">
                        <strong>操作结果：</strong>
                        <p>' . htmlspecialchars($message) . '</p>';
        if ($uploaded_file) {
            $content .= '<p>上传文件路径：<code>' . htmlspecialchars($uploaded_file) . '</code></p>';
            $content .= '<p>访问链接：<a href="' . htmlspecialchars($uploaded_file) . '" target="_blank">' . htmlspecialchars($uploaded_file) . '</a></p>';
        }
        $content .= '</div>';
    } else {
        $content .= '<div class="alert alert-danger">
                        <strong>操作结果：</strong>
                        <p>' . htmlspecialchars($message) . '</p>
                    </div>';
    }
}

$content .= '                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 防御建议</h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li><strong>验证文件扩展名：</strong>同时验证文件扩展名，不要仅依赖MIME类型</li>
                        <li><strong>检查文件内容：</strong>验证文件头部特征，确保文件类型与声明一致</li>
                        <li><strong>使用白名单：</strong>只允许特定的文件扩展名和MIME类型</li>
                        <li><strong>重命名文件：</strong>使用随机文件名，避免路径遍历攻击</li>
                        <li><strong>存储位置：</strong>将上传文件存储在Web根目录外，或使用不可执行的目录</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>';

// 包含模板
include '../template/module_template.php';
?>