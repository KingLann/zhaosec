<?php
// 文件下载IDOR漏洞场景
$module_name = '文件下载IDOR';
$module_icon = '📁';
$module_desc = '演示通过修改文件ID下载其他用户文件的IDOR漏洞。';

// 模拟文件数据
$files = [
    ['id' => 1, 'user_id' => 1, 'name' => '个人简历.pdf', 'path' => 'files/user1_resume.pdf', 'size' => '2.5MB', 'upload_date' => '2024-01-01'],
    ['id' => 2, 'user_id' => 1, 'name' => '项目方案.docx', 'path' => 'files/user1_project.docx', 'size' => '1.8MB', 'upload_date' => '2024-01-02'],
    ['id' => 3, 'user_id' => 2, 'name' => '财务报表.xlsx', 'path' => 'files/user2_finance.xlsx', 'size' => '3.2MB', 'upload_date' => '2024-01-03'],
    ['id' => 4, 'user_id' => 2, 'name' => '客户资料.csv', 'path' => 'files/user2_customers.csv', 'size' => '0.5MB', 'upload_date' => '2024-01-04'],
    ['id' => 5, 'user_id' => 3, 'name' => '产品设计图.png', 'path' => 'files/user3_design.png', 'size' => '4.7MB', 'upload_date' => '2024-01-05'],
];

// 漏洞代码
$file = null;
$error = '';

if (isset($_GET['id'])) {
    $file_id = $_GET['id'];
    
    // 漏洞：直接使用用户提供的ID，没有进行访问控制检查
    foreach ($files as $f) {
        if ($f['id'] == $file_id) {
            $file = $f;
            break;
        }
    }
    
    if (!$file) {
        $error = '文件不存在';
    }
}

// 页面内容
$content = <<<'EOT'
<div class="card">
        <div class="card-header">
            <h5 class="mb-0">📁 文件下载IDOR</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示文件下载的IDOR漏洞。<br>
                服务器直接使用用户提供的文件ID查询文件信息，没有进行任何访问控制检查，攻击者可以通过修改URL中的文件ID参数下载其他用户的文件。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_GET['id'])) {
    $file_id = $_GET['id'];
    
    // 漏洞：直接使用用户提供的ID，没有进行访问控制检查
    foreach ($files as $f) {
        if ($f['id'] == $file_id) {
            $file = $f;
            break;
        }
    }
    
    if (!$file) {
        $error = '文件不存在';
    }
}</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">本场景演示文件下载的IDOR漏洞，尝试以下攻击：</p>

                    <h5 class="mb-2">1. 下载自己的文件</h5>
                    <p>点击以下链接下载文件1：</p>
                    <a href="?id=1" class="btn btn-primary">下载文件1</a>

                    <h5 class="mb-2 mt-4">2. 下载其他用户的文件</h5>
                    <p>尝试修改URL中的id参数，下载其他用户的文件：</p>
                    <div class="input-group mb-3">
                        <span class="input-group-text">URL</span>
                        <input type="text" class="form-control" value="http://localhost/zhaosec/idor/03_file_idor.php?id=3">
                        <button class="btn btn-danger" onclick="window.location.href='?id=3'">访问</button>
                    </div>

                    <h5 class="mb-2 mt-4">3. 尝试下载不存在的文件</h5>
                    <a href="?id=999" class="btn btn-warning">访问不存在的文件</a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 实际测试</h6>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="mb-3">
                            <label for="file_id" class="form-label">文件ID</label>
                            <input type="number" name="id" id="file_id" class="form-control" placeholder="输入文件ID" value="1">
                        </div>
                        <button type="submit" class="btn btn-danger">查看文件信息</button>
                    </form>

                    ';

if ($error) {
    $content .= '<div class="alert alert-danger">
                        <strong>错误：</strong>
                        <p>' . htmlspecialchars($error) . '</p>
                    </div>';
}

if ($file) {
    $content .= '<div class="card">
                        <div class="card-header">
                            <h6>文件信息</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th>文件ID</th>
                                    <td>' . htmlspecialchars($file['id']) . '</td>
                                </tr>
                                <tr>
                                    <th>用户ID</th>
                                    <td>' . htmlspecialchars($file['user_id']) . '</td>
                                </tr>
                                <tr>
                                    <th>文件名</th>
                                    <td>' . htmlspecialchars($file['name']) . '</td>
                                </tr>
                                <tr>
                                    <th>文件路径</th>
                                    <td>' . htmlspecialchars($file['path']) . '</td>
                                </tr>
                                <tr>
                                    <th>文件大小</th>
                                    <td>' . htmlspecialchars($file['size']) . '</td>
                                </tr>
                                <tr>
                                    <th>上传日期</th>
                                    <td>' . htmlspecialchars($file['upload_date']) . '</td>
                                </tr>
                            </table>
                            <div class="mt-3">
                                <a href="#' . htmlspecialchars($file['path']) . '" class="btn btn-success">下载文件</a>
                            </div>
                        </div>
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
                        <li><strong>实施访问控制检查：</strong>在下载文件前，验证当前用户是否有权限下载该文件</li>
                        <li><strong>关联用户ID：</strong>在查询文件时，同时检查文件的用户ID与当前登录用户的ID是否匹配</li>
                        <li><strong>输入验证：</strong>对用户输入的文件ID参数进行验证，确保它是有效的文件ID</li>
                        <li><strong>使用间接引用：</strong>不直接使用数据库ID，而是使用映射表或随机标识符</li>
                        <li><strong>文件路径保护：</strong>避免在URL中暴露真实的文件路径</li>
                    </ol>

                    <h5 class="mb-3 mt-4">修复后的代码</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 修复后的代码
if (isset($_GET['id'])) {
    $file_id = $_GET['id'];
    
    // 获取当前登录用户的ID
    $current_user_id = $_SESSION['user_id'];
    
    // 查找文件并检查权限
    foreach ($files as $f) {
        if ($f['id'] == $file_id) {
            // 检查当前用户是否有权限下载该文件
            if ($f['user_id'] != $current_user_id) {
                die('Access denied');
            }
            $file = $f;
            break;
        }
    }
    
    if (!$file) {
        $error = '文件不存在';
    }
}</code></pre>
                </div>
            </div>
        </div>
    </div>';

// 包含模板
include '../template/module_template.php';
?>