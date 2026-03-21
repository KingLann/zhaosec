<?php
// 支付状态绕过漏洞场景
include 'common.php';

$conn = getDBConnection();
$user = getCurrentUser();
$message = '';
$flag = null;

// 获取用户的订单列表
$sql = "SELECT o.*, oi.product_id, p.name as product_name 
        FROM orders o 
        LEFT JOIN order_items oi ON o.id = oi.order_id 
        LEFT JOIN products p ON oi.product_id = p.id 
        WHERE o.user_id = ? 
        ORDER BY o.created_at DESC";
$stmt = $conn->prepare($sql);
$userId = $user['id'];
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 处理支付请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['pay'])) {
        $orderId = intval($_POST['order_id']);
        
        // 获取订单信息
        $sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $orderId, $userId);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($order && $order['status'] === 'pending') {
            // 模拟支付流程
            if ($user['balance'] >= $order['pay_amount']) {
                // 扣除余额并更新订单状态
                $newBalance = $user['balance'] - $order['pay_amount'];
                $sql = "UPDATE logic_users SET balance = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("di", $newBalance, $userId);
                    $stmt->execute();
                    $stmt->close();
                }
                
                $sql = "UPDATE orders SET status = 'paid', pay_time = NOW() WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $orderId);
                $stmt->execute();
                $stmt->close();
                
                $message = showSuccess("支付成功！订单已支付。");
                logAction($userId, 'pay', "支付订单 {$order['order_no']}");
            } else {
                $message = showError("余额不足！");
            }
        }
    }
    
    // 漏洞点：通过隐藏参数修改订单状态（抓包修改）
    // 攻击者可以抓包添加 status 参数直接修改订单状态
    if (isset($_POST['status'])) {
        $orderId = intval($_POST['order_id'] ?? 0);
        $newStatus = $_POST['status'];
        
        // 漏洞：没有验证状态转换的合法性，允许任意修改
        $allowedStatus = ['pending', 'paid', 'shipped', 'completed', 'cancelled'];
        if (in_array($newStatus, $allowedStatus)) {
            $sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $orderId, $userId);
            $stmt->execute();
            $order = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            if ($order) {
                $sql = "UPDATE orders SET status = ? WHERE id = ? AND user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sii", $newStatus, $orderId, $userId);
                
                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    $flag = getFlag('payment');
                    $message = showWarning("🎉 通过修改status参数成功！<br>订单金额：¥{$order['pay_amount']}<br>当前余额：¥{$user['balance']}<br>修改状态：{$order['status']} → {$newStatus}<br><strong>说明：</strong>您通过抓包修改status参数，绕过了支付验证！");
                    logAction($userId, 'bypass_status_param', "通过status参数修改订单 {$orderId} 状态为 {$newStatus}");
                }
                $stmt->close();
            }
        }
    }
    
    // 漏洞点2：通过URL参数直接修改订单状态
    if (isset($_GET['bypass']) && isset($_GET['order_id'])) {
        $bypassOrderId = intval($_GET['order_id']);
        
        // 严重漏洞：通过URL直接修改订单状态，无需认证
        $sql = "UPDATE orders SET status = 'completed' WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $bypassOrderId, $userId);
        
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $flag = getFlag('payment');
            $message = showWarning("🎉 通过URL直接修改订单状态成功！检测到严重的支付状态绕过漏洞！");
            logAction($userId, 'bypass_url', "通过URL绕过支付直接完成订单 {$bypassOrderId}");
        }
        $stmt->close();
    }
}

// 重新获取订单列表
$sql = "SELECT o.*, oi.product_id, p.name as product_name 
        FROM orders o 
        LEFT JOIN order_items oi ON o.id = oi.order_id 
        LEFT JOIN products p ON oi.product_id = p.id 
        WHERE o.user_id = ? 
        ORDER BY o.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();

echo getHeader('支付状态绕过');
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">🔓 支付状态绕过漏洞演示</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>💡 漏洞说明：</strong><br>
                    本场景存在支付状态绕过漏洞。系统允许用户直接修改订单状态，而未验证支付是否真正完成。<br>
                    攻击者可以通过直接调用状态更新接口，绕过支付流程获取商品。
                </div>
                
                <?php if ($flag): ?>
                <div class="flag-box">
                    🚩 Flag: <?php echo $flag; ?>
                </div>
                <?php endif; ?>
                
                <?php echo $message; ?>
                
                <h6 class="mt-4">我的订单</h6>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>订单号</th>
                            <th>商品</th>
                            <th>金额</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo $order['order_no']; ?></td>
                            <td><?php echo $order['product_name'] ?? '未知商品'; ?></td>
                            <td>¥<?php echo $order['pay_amount']; ?></td>
                            <td>
                                <?php 
                                $statusClass = [
                                    'pending' => 'badge bg-warning',
                                    'paid' => 'badge bg-success',
                                    'completed' => 'badge bg-primary',
                                    'cancelled' => 'badge bg-secondary'
                                ][$order['status']] ?? 'badge bg-secondary';
                                $statusText = [
                                    'pending' => '待支付',
                                    'paid' => '已支付',
                                    'completed' => '已完成',
                                    'cancelled' => '已取消'
                                ][$order['status']] ?? $order['status'];
                                ?>
                                <span class="<?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                            </td>
                            <td>
                                <?php if ($order['status'] === 'pending'): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <button type="submit" name="pay" class="btn btn-success btn-sm">支付</button>
                                </form>
                                <?php endif; ?>
                                
                                <?php if ($order['status'] === 'paid'): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn btn-primary btn-sm">确认收货</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if (empty($orders)): ?>
                <p class="text-muted text-center">暂无订单，请先购买商品</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">👤 用户信息</h6>
            </div>
            <div class="card-body">
                <p><strong>用户名：</strong><?php echo $user['username']; ?></p>
                <p><strong>余额：</strong>¥<?php echo $user['balance']; ?></p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">🎯 攻击提示</h6>
            </div>
            <div class="card-body">
                <p class="small">本场景有多个漏洞利用点：</p>
                <ol class="small">
                    <li><strong>抓包修改绕过：</strong>
                        <ul>
                            <li>使用Burp Suite拦截任意POST请求</li>
                            <li>添加参数：<code>status=completed</code></li>
                            <li>添加参数：<code>order_id=订单ID</code></li>
                        </ul>
                    </li>
                    <li><strong>URL绕过：</strong>直接在浏览器地址栏输入：<br>
                        <code>02_payment_bypass.php?bypass=1&order_id=订单ID</code><br>
                        例如：<code>02_payment_bypass.php?bypass=1&order_id=1</code></li>
                </ol>
                <p class="small text-danger mt-2">提示：创建订单后，使用抓包修改status参数绕过支付</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">🛡️ 防御建议</h6>
            </div>
            <div class="card-body">
                <ol class="small">
                    <li><strong>状态流转控制：</strong>建立严格的状态流转规则，只允许从特定状态转换到另一个状态</li>
                    <li><strong>支付验证：</strong>订单状态更新前必须验证支付状态，未支付的订单不能直接标记为完成</li>
                    <li><strong>接口保护：</strong>状态更新接口必须进行身份验证和权限检查</li>
                    <li><strong>参数验证：</strong>对所有输入参数进行严格验证，防止恶意参数注入</li>
                    <li><strong>日志审计：</strong>记录所有状态变更操作，便于事后审计和异常检测</li>
                    <li><strong>防重放：</strong>添加请求唯一标识，防止重复提交</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php echo getFooter(); ?>
