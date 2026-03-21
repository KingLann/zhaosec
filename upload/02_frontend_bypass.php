<?php
// 前端验证绕过漏洞
$module_name = '前端验证绕过';
$module_icon = '🔒';
$module_desc = '仅在前端使用JavaScript验证文件类型，可通过禁用JS或直接发送请求绕过。';

// 漏洞代码
$message = '';
$uploaded_file = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        
        // 漏洞：仅在前端进行验证，后端没有验证
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
            <h5 class="mb-0">🔒 前端验证绕过</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示前端验证绕过漏洞。<br>
                服务器仅在前端使用JavaScript验证文件类型，后端没有任何验证，攻击者可以通过禁用JS或直接发送请求绕过验证。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-2">前端验证代码：</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;script&gt;
function validateFile() {
    var fileInput = document.getElementById('file');
    var file = fileInput.files[0];
    var allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    
    if (!file) {
        alert('请选择文件');
        return false;
    }
    
    if (!allowedTypes.includes(file.type)) {
        alert('仅允许上传图片文件');
        return false;
    }
    
    return true;
}
&lt;/script&gt;</code></pre>
                    
                    <h5 class="mb-2 mt-4">后端代码：</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // 漏洞：仅在前端进行验证，后端没有验证
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
                    <p class="mb-3">本场景演示前端验证绕过漏洞，尝试以下攻击方法：</p>

                    <h5 class="mb-2">方法1：禁用JavaScript</h5>
                    <ol>
                        <li>在浏览器中禁用JavaScript</li>
                        <li>刷新本页面</li>
                        <li>上传任意类型的文件，包括PHP文件</li>
                    </ol>

                    <h5 class="mb-2 mt-4">方法2：使用Burp Suite拦截修改</h5>
                    <ol>
                        <li>使用Burp Suite拦截文件上传请求</li>
                        <li>修改文件类型和扩展名</li>
                        <li>放行请求，文件将被成功上传</li>
                    </ol>

                    <h5 class="mb-2 mt-4">方法3：直接发送POST请求</h5>
                    <ol>
                        <li>使用curl或其他工具直接发送POST请求</li>
                        <li>示例命令：</li>
                        <pre class="bg-dark text-light p-3 rounded"><code>curl -X POST \
  http://localhost/zhaosec/upload/frontend_bypass.php \
  -F "file=@webshell.php"
</code></pre>
                    </ol>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 实际测试</h6>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="mb-3" onsubmit="return validateFile();">
                        <div class="mb-3">
                            <label for="file" class="form-label">选择文件</label>
                            <input type="file" name="file" id="file" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-danger">上传文件</button>
                    </form>

                    <script>
                    function validateFile() {
                        var fileInput = document.getElementById('file');
                        var file = fileInput.files[0];
                        var allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                        
                        if (!file) {
                            alert('请选择文件');
                            return false;
                        }
                        
                        if (!allowedTypes.includes(file.type)) {
                            alert('仅允许上传图片文件');
                            return false;
                        }
                        
                        return true;
                    }
                    </script>

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
                        <li><strong>后端验证：</strong>在服务器端进行完整的文件验证，不要依赖前端验证</li>
                        <li><strong>文件类型验证：</strong>使用白名单验证文件扩展名和MIME类型</li>
                        <li><strong>文件内容验证：</strong>检查文件头部特征，确保文件类型与扩展名匹配</li>
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