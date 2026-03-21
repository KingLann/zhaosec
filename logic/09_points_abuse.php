<?php
// 积分滥刷漏洞场景
include 'common.php';

// 启动session（用于记录分享次数）
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = getDBConnection();
$user = getCurrentUser();
$message = '';
$flag = null;

// 获取今日签到状态
$today = date('Y-m-d');
$sql = "SELECT * FROM user_logs WHERE user_id = ? AND action = 'checkin' AND DATE(created_at) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user['id'], $today);
$stmt->execute();
$hasCheckedIn = $stmt->get_result()->num_rows > 0;
$stmt->close();

// 处理签到
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkin'])) {
    if (!$hasCheckedIn) {
        // 正常签到逻辑
        $points = 10;
        $newPoints = $user['points'] + $points;
        
        $sql = "UPDATE logic_users SET points = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ii", $newPoints, $user['id']);
            $stmt->execute();
            $stmt->close();
        }
        
        logAction($user['id'], 'checkin', "签到获得 {$points} 积分");
        
        $message = showSuccess("签到成功！获得 {$points} 积分");
        $hasCheckedIn = true;
        $user['points'] = $newPoints;
    } else {
        $message = showError("今天已经签到过了！");
    }
}

// 漏洞1：前端传入积分值
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_complete'])) {
    // 漏洞：从前端获取积分值，未验证
    $taskPoints = intval($_POST['task_points'] ?? 0);
    
    // 正常应该根据task_id查询任务积分
    // 但这里直接使用前端传入的值
    $newPoints = $user['points'] + $taskPoints;
    
    $sql = "UPDATE logic_users SET points = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $newPoints, $user['id']);
    $stmt->execute();
    $stmt->close();
    
    logAction($user['id'], 'task', "完成任务获得 {$taskPoints} 积分");
    
    // 如果积分异常高，触发flag
    if ($taskPoints > 100) {
        $flag = getFlag('abuse');
        $message = showWarning("🎉 检测到积分滥刷！获得 {$taskPoints} 积分！");
    } else {
        $message = showSuccess("任务完成！获得 {$taskPoints} 积分");
    }
    
    $user['points'] = $newPoints;
}

// 漏洞2：重复完成任务（没有唯一性校验）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['share'])) {
    $sharePoints = 5;
    
    // 漏洞：虽然有次数限制，但没有防重放机制
    // 使用会话变量记录今日分享次数（重放攻击时不会更新会话）
    if (!isset($_SESSION['today_share_count'])) {
        $_SESSION['today_share_count'] = 0;
    }
    
    // 限制每日分享次数为3次（仅在前端显示限制）
    // 漏洞：后端没有严格限制，可以通过重放绕过
    $canShare = true;
    
    if ($canShare) {
        // 漏洞：没有防重放机制，可以重放数据包
        // 重放攻击时，会话变量不会更新，所以可以绕过限制
        $newPoints = $user['points'] + $sharePoints;
        
        $sql = "UPDATE logic_users SET points = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $newPoints, $user['id']);
        $stmt->execute();
        $stmt->close();
        
        logAction($user['id'], 'share', "分享获得 {$sharePoints} 积分");
        
        // 检查短时间内积分增长是否异常（用于触发flag）
        $sql = "SELECT COUNT(*) as count FROM user_logs WHERE user_id = ? AND action = 'share' AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $shareCount = $stmt->get_result()->fetch_assoc()['count'];
        $stmt->close();
        
        // 更新会话变量（重放攻击时不会更新会话，所以可以绕过3次限制）
        $_SESSION['today_share_count']++;
        
        if ($shareCount >= 5) {
            $flag = getFlag('abuse');
            $message = showWarning("🎉 检测到积分滥刷！1分钟内分享 {$shareCount} 次！<br><strong>说明：</strong>虽然分享显示限制3次，但可以通过重放数据包无限获取积分！");
        } else {
            $todayShareCountDisplay = $_SESSION['today_share_count'];
            $message = showSuccess("分享成功！获得 {$sharePoints} 积分（今日已分享 {$todayShareCountDisplay}/3 次）<br><small class='text-muted'>提示：使用Burp重放此请求可绕过限制</small>");
        }
        
        $user['points'] = $newPoints;
    }
}

// 兑换商品
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['redeem'])) {
    $cost = intval($_POST['cost'] ?? 0);
    $itemName = $_POST['item_name'] ?? '商品';
    
    // 漏洞：没有验证cost是否与商品实际价格匹配
    if ($user['points'] >= $cost) {
        $newPoints = $user['points'] - $cost;
        
        $sql = "UPDATE logic_users SET points = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ii", $newPoints, $user['id']);
            $stmt->execute();
            $stmt->close();
        }
        
        logAction($user['id'], 'redeem', "兑换 {$itemName} 花费 {$cost} 积分");
        
        $message = showSuccess("兑换成功！花费 {$cost} 积分兑换 {$itemName}");
        $user['points'] = $newPoints;
    } else {
        $message = showError("积分不足！");
    }
}

$conn->close();

echo getHeader('积分滥刷漏洞');
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">💰 积分滥刷漏洞演示</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>💡 漏洞说明：</strong><br>
                    积分系统存在多处缺陷：前端可传入积分值、没有频率限制、兑换价格未校验等，导致可以刷取大量积分。
                </div>
                
                <?php echo $message; ?>
                
                <?php if ($flag): ?>
                <div class="flag-box">
                    🚩 Flag: <?php echo $flag; ?>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <!-- 签到 -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6>📅 每日签到</h6>
                                <p class="text-muted">每日签到可获得10积分</p>
                                <form method="POST">
                                    <button type="submit" name="checkin" class="btn btn-primary" <?php echo $hasCheckedIn ? 'disabled' : ''; ?>>
                                        <?php echo $hasCheckedIn ? '已签到' : '签到'; ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 任务 -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6>📋 完成任务</h6>
                                <p class="text-muted">完成任务获得积分</p>
                                <form method="POST">
                                    <input type="hidden" name="task_id" value="1">
                                    <input type="hidden" name="task_points" value="20" id="taskPoints">
                                    <button type="submit" name="task_complete" class="btn btn-success">完成任务</button>
                                </form>
                                <div class="form-text text-danger">提示：尝试修改task_points值</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 分享 -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6>📤 分享奖励</h6>
                                <p class="text-muted">分享可获得5积分，每日限3次</p>
                                <form method="POST" id="shareForm">
                                    <button type="submit" name="share" class="btn btn-info" id="shareBtn">
                                        分享 (<span id="shareCount">0</span>/3)
                                    </button>
                                </form>
                                <div class="form-text text-danger">提示：可重放数据包绕过</div>
                            </div>
                        </div>
                    </div>
                    
                    <script>
                    // 前端限制分享次数（但后端不限制，可通过重放绕过）
                    (function() {
                        let shareCount = parseInt(localStorage.getItem('shareCount_<?php echo $user['id']; ?>') || '0');
                        const shareBtn = document.getElementById('shareBtn');
                        const shareCountSpan = document.getElementById('shareCount');
                        
                        function updateShareButton() {
                            shareCountSpan.textContent = shareCount;
                            if (shareCount >= 3) {
                                shareBtn.disabled = true;
                                shareBtn.classList.remove('btn-info');
                                shareBtn.classList.add('btn-secondary');
                                shareBtn.innerHTML = '已达上限 (3/3)';
                            }
                        }
                        
                        updateShareButton();
                        
                        document.getElementById('shareForm').addEventListener('submit', function(e) {
                            shareCount++;
                            localStorage.setItem('shareCount_<?php echo $user['id']; ?>', shareCount);
                            updateShareButton();
                        });
                    })();
                    </script>
                    
                    <!-- 兑换 -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6>🎁 积分兑换</h6>
                                <p class="text-muted">兑换虚拟商品</p>
                                <form method="POST">
                                    <input type="hidden" name="item_name" value="优惠券">
                                    <input type="hidden" name="cost" value="100" id="redeemCost">
                                    <button type="submit" name="redeem" class="btn btn-warning">兑换优惠券（100积分）</button>
                                </form>
                                <div class="form-text text-danger">提示：尝试修改cost值</div>
                            </div>
                        </div>
                    </div>
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
                <p><strong>当前积分：</strong><span class="badge bg-warning fs-5"><?php echo $user['points']; ?></span></p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">🎯 攻击提示</h6>
            </div>
            <div class="card-body">
                <p class="small">1. 使用浏览器开发者工具修改task_points值</p>
                <p class="small">2. 快速连续点击分享按钮</p>
                <p class="small">3. 修改兑换时的cost值为负数或0</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">🛡️ 防御建议</h6>
            </div>
            <div class="card-body">
                <ol class="small">
                    <li>积分值必须从服务端计算</li>
                    <li>添加操作频率限制</li>
                    <li>验证兑换价格与商品匹配</li>
                    <li>添加操作日志和异常检测</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php echo getFooter(); ?>
