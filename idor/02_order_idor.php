<?php
// 订单信息IDOR漏洞场景
$module_name = '订单信息IDOR';
$module_icon = '📋';
$module_desc = '演示通过修改订单ID访问其他用户订单信息的IDOR漏洞。';

// 模拟订单数据
$orders = [
    ['id' => 101, 'user_id' => 1, 'product' => 'iPhone 13', 'amount' => 5999, 'status' => '已支付'],
    ['id' => 102, 'user_id' => 1, 'product' => 'AirPods Pro', 'amount' => 1999, 'status' => '已支付'],
    ['id' => 103, 'user_id' => 2, 'product' => 'MacBook Pro', 'amount' => 12999, 'status' => '已支付'],
    ['id' => 104, 'user_id' => 2, 'product' => 'iPad Pro', 'amount' => 6999, 'status' => '待支付'],
    ['id' => 105, 'user_id' => 3, 'product' => 'Apple Watch', 'amount' => 2999, 'status' => '已支付'],
];

// 漏洞代码
$order = null;
$error = '';

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    
    // 漏洞：直接使用用户提供的ID，没有进行访问控制检查
    foreach ($orders as $o) {
        if ($o['id'] == $order_id) {
            $order = $o;
            break;
        }
    }
    
    if (!$order) {
        $error = '订单不存在';
    }
}

// 页面内容
$content = <<<'EOT'
<div class="card">
        <div class="card-header">
            <h5 class="mb-0">📋 订单信息IDOR</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示订单信息的IDOR漏洞。<br>
                服务器直接使用用户提供的订单ID查询订单信息，没有进行任何访问控制检查，攻击者可以通过修改URL中的订单ID参数访问其他用户的订单信息。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    
    // 漏洞：直接使用用户提供的ID，没有进行访问控制检查
    foreach ($orders as $o) {
        if ($o['id'] == $order_id) {
            $order = $o;
            break;
        }
    }
    
    if (!$order) {
        $error = '订单不存在';
    }
}</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">本场景演示订单信息的IDOR漏洞，尝试以下攻击：</p>

                    <h5 class="mb-2">1. 访问自己的订单</h5>
                    <p>点击以下链接访问订单101的信息：</p>
                    <a href="?id=101" class="btn btn-primary">访问订单101</a>

                    <h5 class="mb-2 mt-4">2. 访问其他用户的订单</h5>
                    <p>尝试修改URL中的id参数，访问其他用户的订单：</p>
                    <div class="input-group mb-3">
                        <span class="input-group-text">URL</span>
                        <input type="text" class="form-control" value="http://localhost/zhaosec/idor/02_order_idor.php?id=103">
                        <button class="btn btn-danger" onclick="window.location.href='?id=103'">访问</button>
                    </div>

                    <h5 class="mb-2 mt-4">3. 尝试访问不存在的订单</h5>
                    <a href="?id=999" class="btn btn-warning">访问不存在的订单</a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 实际测试</h6>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="mb-3">
                            <label for="order_id" class="form-label">订单ID</label>
                            <input type="number" name="id" id="order_id" class="form-control" placeholder="输入订单ID" value="101">
                        </div>
                        <button type="submit" class="btn btn-danger">查看订单信息</button>
                    </form>

                    ';

if ($error) {
    $content .= '<div class="alert alert-danger">
                        <strong>错误：</strong>
                        <p>' . htmlspecialchars($error) . '</p>
                    </div>';
}

if ($order) {
    $content .= '<div class="card">
                        <div class="card-header">
                            <h6>订单信息</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th>订单ID</th>
                                    <td>' . htmlspecialchars($order['id']) . '</td>
                                </tr>
                                <tr>
                                    <th>用户ID</th>
                                    <td>' . htmlspecialchars($order['user_id']) . '</td>
                                </tr>
                                <tr>
                                    <th>商品</th>
                                    <td>' . htmlspecialchars($order['product']) . '</td>
                                </tr>
                                <tr>
                                    <th>金额</th>
                                    <td>¥' . htmlspecialchars($order['amount']) . '</td>
                                </tr>
                                <tr>
                                    <th>状态</th>
                                    <td>' . htmlspecialchars($order['status']) . '</td>
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
                        <li><strong>实施访问控制检查：</strong>在访问订单信息前，验证当前用户是否有权限访问该订单</li>
                        <li><strong>关联用户ID：</strong>在查询订单时，同时检查订单的用户ID与当前登录用户的ID是否匹配</li>
                        <li><strong>输入验证：</strong>对用户输入的订单ID参数进行验证，确保它是有效的订单ID</li>
                        <li><strong>使用间接引用：</strong>不直接使用数据库ID，而是使用映射表或随机标识符</li>
                    </ol>

                    <h5 class="mb-3 mt-4">修复后的代码</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 修复后的代码
if (isset($_GET["id"])) {
    $order_id = $_GET["id"];
    
    // 获取当前登录用户的ID
    $current_user_id = $_SESSION["user_id"];
    
    // 查找订单并检查权限
    foreach ($orders as $o) {
        if ($o["id"] == $order_id) {
            // 检查当前用户是否有权限访问该订单
            if ($o["user_id"] != $current_user_id) {
                die("Access denied");
            }
            $order = $o;
            break;
        }
    }
    
    if (!$order) {
        $error = "订单不存在";
    }
}</code></pre>
                </div>
            </div>
        </div>
    </div>';

// 包含模板
include '../template/module_template.php';
?>