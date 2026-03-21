<?php
// 基础IDOR漏洞场景
$module_name = '基础IDOR';
$module_icon = '🔓';
$module_desc = '演示通过修改用户ID访问其他用户信息的IDOR漏洞。';

// 模拟用户数据
$users = [
    ['id' => 1, 'name' => '张三', 'email' => 'zhangsan@example.com', 'phone' => '13800138001', 'address' => '北京市朝阳区'],
    ['id' => 2, 'name' => '李四', 'email' => 'lisi@example.com', 'phone' => '13900139002', 'address' => '上海市浦东新区'],
    ['id' => 3, 'name' => '王五', 'email' => 'wangwu@example.com', 'phone' => '13700137003', 'address' => '广州市天河区'],
    ['id' => 4, 'name' => '赵六', 'email' => 'zhaoliu@example.com', 'phone' => '13600136004', 'address' => '深圳市南山区'],
];

// 漏洞代码
$user = null;
$error = '';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // 漏洞：直接使用用户提供的ID，没有进行访问控制检查
    foreach ($users as $u) {
        if ($u['id'] == $user_id) {
            $user = $u;
            break;
        }
    }
    
    if (!$user) {
        $error = '用户不存在';
    }
}

// 页面内容
$content = <<<'EOT'
<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🔓 基础IDOR</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示基础的IDOR漏洞。<br>
                服务器直接使用用户提供的ID参数查询用户信息，没有进行任何访问控制检查，攻击者可以通过修改URL中的ID参数访问其他用户的信息。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // 漏洞：直接使用用户提供的ID，没有进行访问控制检查
    foreach ($users as $u) {
        if ($u['id'] == $user_id) {
            $user = $u;
            break;
        }
    }
    
    if (!$user) {
        $error = '用户不存在';
    }
}</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">本场景演示基础IDOR漏洞，尝试以下攻击：</p>

                    <h5 class="mb-2">1. 访问自己的信息</h5>
                    <p>点击以下链接访问用户1的信息：</p>
                    <a href="?id=1" class="btn btn-primary">访问用户1的信息</a>

                    <h5 class="mb-2 mt-4">2. 访问其他用户的信息</h5>
                    <p>尝试修改URL中的id参数，访问其他用户的信息：</p>
                    <div class="input-group mb-3">
                        <span class="input-group-text">URL</span>
                        <input type="text" class="form-control" value="http://localhost/zhaosec/idor/01_basic_idor.php?id=2">
                        <button class="btn btn-danger" onclick="window.location.href='?id=2'">访问</button>
                    </div>

                    <h5 class="mb-2 mt-4">3. 尝试访问不存在的用户</h5>
                    <a href="?id=999" class="btn btn-warning">访问不存在的用户</a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 实际测试</h6>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">用户ID</label>
                            <input type="number" name="id" id="user_id" class="form-control" placeholder="输入用户ID" value="1">
                        </div>
                        <button type="submit" class="btn btn-danger">查看用户信息</button>
                    </form>

                    ';

if ($error) {
    $content .= '<div class="alert alert-danger">
                        <strong>错误：</strong>
                        <p>' . htmlspecialchars($error) . '</p>
                    </div>';
}

if ($user) {
    $content .= '<div class="card">
                        <div class="card-header">
                            <h6>用户信息</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th>ID</th>
                                    <td>' . htmlspecialchars($user['id']) . '</td>
                                </tr>
                                <tr>
                                    <th>姓名</th>
                                    <td>' . htmlspecialchars($user['name']) . '</td>
                                </tr>
                                <tr>
                                    <th>邮箱</th>
                                    <td>' . htmlspecialchars($user['email']) . '</td>
                                </tr>
                                <tr>
                                    <th>电话</th>
                                    <td>' . htmlspecialchars($user['phone']) . '</td>
                                </tr>
                                <tr>
                                    <th>地址</th>
                                    <td>' . htmlspecialchars($user['address']) . '</td>
                                </tr>
                            </table>
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
                        <li><strong>实施访问控制检查：</strong>在访问用户信息前，验证当前用户是否有权限访问该用户的信息</li>
                        <li><strong>使用会话ID：</strong>基于当前登录用户的会话ID获取用户信息，而不是通过URL参数</li>
                        <li><strong>输入验证：</strong>对用户输入的ID参数进行验证，确保它是有效的用户ID</li>
                        <li><strong>使用间接引用：</strong>不直接使用数据库ID，而是使用映射表或随机标识符</li>
                    </ol>

                    <h5 class="mb-3 mt-4">修复后的代码</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 修复后的代码
if (isset($_GET["id"])) {
    $user_id = $_GET["id"];
    
    // 获取当前登录用户的ID
    $current_user_id = $_SESSION["user_id"];
    
    // 检查当前用户是否有权限访问该用户的信息
    // 这里简化处理，只允许用户访问自己的信息
    if ($user_id != $current_user_id) {
        die("Access denied");
    }
    
    foreach ($users as $u) {
        if ($u["id"] == $user_id) {
            $user = $u;
            break;
        }
    }
    
    if (!$user) {
        $error = "用户不存在";
    }
}</code></pre>
                </div>
            </div>
        </div>
    </div>';

include '../template/module_template.php';
?>