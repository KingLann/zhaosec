<?php
// 价格篡改漏洞场景
include 'common.php';

$conn = getDBConnection();
$user = getCurrentUser();
$message = '';
$flag = null;

// 获取商品列表
$sql = "SELECT * FROM products WHERE status = 'active'";
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);

// 处理购买请求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy'])) {
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    // 漏洞点：从前端获取价格，未验证
    $price = floatval($_POST['price']);
    
    // 获取商品真实信息
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($product) {
        $totalAmount = $price * $quantity;
        
        // 检查用户余额（负数金额时跳过余额检查）
        if ($user['balance'] >= $totalAmount || $totalAmount < 0) {
            // 创建订单
            $orderNo = generateOrderNo();
            $userId = $user['id'];
            $sql = "INSERT INTO orders (user_id, order_no, total_amount, pay_amount, status) VALUES (?, ?, ?, ?, 'pending')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isdd", $userId, $orderNo, $totalAmount, $totalAmount);
            
            if ($stmt->execute()) {
                $orderId = $stmt->insert_id;
                
                // 插入订单商品
                $sql = "INSERT INTO order_items (order_id, product_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)";
                $stmt2 = $conn->prepare($sql);
                $stmt2->bind_param("iiidd", $orderId, $productId, $quantity, $price, $totalAmount);
                $stmt2->execute();
                $stmt2->close();
                
                // 扣除/增加余额
                $newBalance = $user['balance'] - $totalAmount;
                $sql = "UPDATE logic_users SET balance = ? WHERE id = ?";
                $stmt2 = $conn->prepare($sql);
                if ($stmt2) {
                    $stmt2->bind_param("di", $newBalance, $userId);
                    $stmt2->execute();
                    $stmt2->close();
                }
                
                // 检查是否为价格篡改或反向充值
                if ($price < $product['price'] * 0.5 || $totalAmount < 0) {
                    $flag = getFlag('payment');
                    if ($totalAmount < 0) {
                        $message = showSuccess("🎉 操作成功！检测到反向充值漏洞！<br>订单号：{$orderNo}<br>充值金额：¥" . abs($totalAmount) . "<br>当前余额：¥{$newBalance}");
                    } else {
                        $message = showSuccess("🎉 购买成功！检测到价格篡改漏洞！<br>订单号：{$orderNo}<br>支付金额：¥{$totalAmount}<br>实际应付：¥" . ($product['price'] * $quantity));
                    }
                } else {
                    $message = showSuccess("购买成功！<br>订单号：{$orderNo}<br>支付金额：¥{$totalAmount}");
                }
                
                logAction($userId, 'buy', "购买商品 {$product['name']}, 支付 {$totalAmount}");
            }
            $stmt->close();
        } else {
            $message = showError("余额不足！当前余额：¥{$user['balance']}");
        }
    }
}

$conn->close();

echo getHeader('价格篡改漏洞');
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">💰 价格篡改漏洞演示</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>💡 漏洞说明：</strong><br>
                    本场景存在价格篡改漏洞。系统从前端接收价格参数，但未与后端数据库中的真实价格进行校验。<br>
                    攻击者可以通过修改前端提交的价格参数，以极低的价格购买商品。
                </div>
                
                <?php echo $message; ?>
                
                <?php if ($flag): ?>
                <div class="flag-box">
                    🚩 Flag: <?php echo $flag; ?>
                </div>
                <?php endif; ?>
                
                <h6 class="mt-4">商品列表</h6>
                <div class="row">
                    <?php foreach ($products as $product): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6><?php echo htmlspecialchars($product['name']); ?></h6>
                                <p class="text-muted small"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="text-danger fw-bold">¥<?php echo $product['price']; ?></p>
                                <p class="small text-muted">库存: <?php echo $product['stock']; ?></p>
                                
                                <form method="POST" class="mt-3">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="price" value="<?php echo $product['price']; ?>" id="price_<?php echo $product['id']; ?>">
                                    <div class="mb-2">
                                        <label class="form-label small">数量</label>
                                        <input type="number" name="quantity" class="form-control form-control-sm" value="1" min="1">
                                    </div>
                                    <button type="submit" name="buy" class="btn btn-primary btn-sm w-100">立即购买</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
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
                <p><strong>积分：</strong><?php echo $user['points']; ?></p>
                <p><strong>角色：</strong><?php echo $user['role']; ?></p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">🎯 攻击提示</h6>
            </div>
            <div class="card-body">
                <p class="small">尝试使用浏览器开发者工具（F12）修改表单中的价格参数，然后提交订单。</p>
                <p class="small text-muted">例如：将价格从7999.00改为1.00</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">🛡️ 防御建议</h6>
            </div>
            <div class="card-body">
                <ol class="small">
                    <li>价格必须从后端数据库获取，不能信任前端传来的价格</li>
                    <li>对订单金额进行二次校验</li>
                    <li>记录异常价格操作日志</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php echo getFooter(); ?>
