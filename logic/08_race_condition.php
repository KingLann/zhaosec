<?php
// 条件竞争漏洞场景（超卖/重复领取）
include 'common.php';

$conn = getDBConnection();
$user = getCurrentUser();
$message = '';
$flag = null;

// 获取限量商品信息
$sql = "SELECT * FROM products WHERE stock <= 10 AND status = 'active' LIMIT 1";
$result = $conn->query($sql);
$limitedProduct = $result->fetch_assoc();

if (!$limitedProduct) {
    // 如果没有限量商品，创建一个
    $sql = "INSERT INTO products (name, description, price, stock) VALUES ('限量款手办', '绝版收藏手办', 999.00, 5)";
    $conn->query($sql);
    $limitedProduct = ['id' => $conn->insert_id, 'name' => '限量款手办', 'price' => 999.00, 'stock' => 5];
}

// 处理抢购请求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rush'])) {
    $productId = $limitedProduct['id'];
    
    // 漏洞点：先检查库存，再扣减库存（存在时间窗口）
    // 检查库存
    $sql = "SELECT stock FROM products WHERE id = ? FOR UPDATE";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    
    if ($product && $product['stock'] > 0) {
        // 模拟处理延迟（放大竞争窗口）
        usleep(100000); // 100ms延迟
        
        // 扣减库存
        $newStock = $product['stock'] - 1;
        $sql = "UPDATE products SET stock = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $newStock, $productId);
        $stmt->execute();
        $stmt->close();
        
        // 创建订单
        $orderNo = generateOrderNo();
        $userId = $user['id'];
        $price = $limitedProduct['price'];
        $sql = "INSERT INTO orders (user_id, order_no, total_amount, pay_amount, status) VALUES (?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isdd", $userId, $orderNo, $price, $price);
        $stmt->execute();
        $orderId = $stmt->insert_id;
        $stmt->close();
        
        // 插入订单商品
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, unit_price, total_price) VALUES (?, ?, 1, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iidd", $orderId, $productId, $price, $price);
        $stmt->execute();
        $stmt->close();
        
        $message = showSuccess("抢购成功！订单号：{$orderNo}");
        logAction($userId, 'rush_buy', "抢购限量商品 {$limitedProduct['name']}");
        
        // 刷新商品信息
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $limitedProduct = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    } else {
        $message = showError("商品已售罄！");
    }
}

// 检查是否超卖（库存为负数）
$sql = "SELECT stock FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $limitedProduct['id']);
$stmt->execute();
$result = $stmt->get_result();
$currentStock = $result->fetch_assoc()['stock'];
$stmt->close();

if ($currentStock < 0) {
    $flag = getFlag('race');
    $message .= showWarning("🎉 检测到超卖！库存为负数：{$currentStock}");
}

// 获取抢购统计
$sql = "SELECT COUNT(*) as total_buy FROM order_items WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $limitedProduct['id']);
$stmt->execute();
$buyCount = $stmt->get_result()->fetch_assoc()['total_buy'];
$stmt->close();

$conn->close();

echo getHeader('条件竞争漏洞');
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">⚡ 条件竞争漏洞演示（超卖）</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>💡 漏洞说明：</strong><br>
                    条件竞争是指多个请求同时处理时，由于时间窗口导致的数据不一致。<br>
                    本场景中，库存检查和扣减之间存在时间窗口，可能导致超卖。
                </div>
                
                <?php echo $message; ?>
                
                <?php if ($flag): ?>
                <div class="flag-box">
                    🚩 Flag: <?php echo $flag; ?>
                </div>
                <?php endif; ?>
                
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <h4><?php echo $limitedProduct['name']; ?></h4>
                        <p class="text-danger h3">¥<?php echo $limitedProduct['price']; ?></p>
                        <p>库存：<span class="badge bg-<?php echo $currentStock > 0 ? 'success' : 'danger'; ?> fs-5"><?php echo $currentStock; ?></span></p>
                        <p class="text-muted">已售出：<?php echo $buyCount; ?> 件</p>
                        
                        <?php if ($currentStock > 0): ?>
                        <form method="POST">
                            <button type="submit" name="rush" class="btn btn-danger btn-lg">立即抢购</button>
                        </form>
                        <?php else: ?>
                        <button class="btn btn-secondary btn-lg" disabled>已售罄</button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <strong>测试方法：</strong><br>
                    使用Burp Suite的Intruder功能，同时发送多个抢购请求，观察是否会出现超卖现象。
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">📊 抢购统计</h6>
            </div>
            <div class="card-body">
                <p>初始库存：5</p>
                <p>当前库存：<?php echo $currentStock; ?></p>
                <p>已售出：<?php echo $buyCount; ?></p>
                <?php if ($buyCount > 5): ?>
                <div class="alert alert-danger">
                    超卖 <?php echo $buyCount - 5; ?> 件！
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">🎯 攻击提示</h6>
            </div>
            <div class="card-body">
                <p class="small">1. 使用Burp Suite拦截抢购请求</p>
                <p class="small">2. 发送到Intruder，设置高并发</p>
                <p class="small">3. 同时发送50-100个请求</p>
                <p class="small">4. 观察是否出现超卖</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">🛡️ 防御建议</h6>
            </div>
            <div class="card-body">
                <ol class="small">
                    <li>使用数据库事务和行锁</li>
                    <li>使用原子操作扣减库存</li>
                    <li>使用队列处理高并发请求</li>
                    <li>添加唯一索引防止重复订单</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php echo getFooter(); ?>
