<?php
// 短信轰炸漏洞场景
include 'common.php';

$conn = getDBConnection();
$user = getCurrentUser();
$message = '';
$flag = null;
$smsLog = [];

// 获取短信发送记录
$sql = "SELECT * FROM user_logs WHERE action = 'send_sms' ORDER BY created_at DESC LIMIT 20";
$result = $conn->query($sql);
$smsLog = $result->fetch_all(MYSQLI_ASSOC);

// 处理发送短信请求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_sms'])) {
    $phone = $_POST['phone'] ?? '';
    
    // 简单验证手机号格式
    if (preg_match('/^1[3-9]\d{9}$/', $phone)) {
        // 漏洞1：没有频率限制
        // 漏洞2：没有图形验证码
        // 漏洞3：没有同一手机号限制
        
        // 模拟发送短信
        $code = rand(100000, 999999);
        
        logAction($user['id'], 'send_sms', "发送短信到 {$phone}, 验证码: {$code}");
        
        // 检查短时间内发送次数
        $sql = "SELECT COUNT(*) as count FROM user_logs WHERE action = 'send_sms' AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
        $result = $conn->query($sql);
        $recentCount = $result->fetch_assoc()['count'];
        
        if ($recentCount >= 10) {
            $flag = getFlag('abuse');
            $message = showWarning("🎉 1分钟内发送 {$recentCount} 条短信！检测到短信轰炸漏洞！");
        } else {
            $message = showSuccess("短信已发送到 {$phone}，验证码：{$code}（实际场景不会显示）");
        }
    } else {
        $message = showError("手机号格式错误！");
    }
}

// 漏洞：通过不同接口发送
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_voice'])) {
    $phone = $_POST['phone'] ?? '';
    
    if (preg_match('/^1[3-9]\d{9}$/', $phone)) {
        // 语音验证码接口同样没有限制
        $code = rand(100000, 999999);
        
        logAction($user['id'], 'send_sms', "发送语音验证码到 {$phone}, 验证码: {$code}");
        
        $message = showInfo("语音验证码已发送到 {$phone}");
    }
}

// 漏洞：批量发送
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['batch_send'])) {
    $phones = $_POST['phones'] ?? '';
    $phoneList = array_filter(array_map('trim', explode("\n", $phones)));
    
    $sentCount = 0;
    foreach ($phoneList as $phone) {
        if (preg_match('/^1[3-9]\d{9}$/', $phone)) {
            $code = rand(100000, 999999);
            logAction($user['id'], 'send_sms', "批量发送短信到 {$phone}");
            $sentCount++;
        }
    }
    
    if ($sentCount > 5) {
        $flag = getFlag('abuse');
        $message = showWarning("🎉 批量发送 {$sentCount} 条短信！检测到短信轰炸漏洞！");
    } else {
        $message = showSuccess("成功发送 {$sentCount} 条短信");
    }
}

// 重新获取短信记录
$sql = "SELECT * FROM user_logs WHERE action = 'send_sms' ORDER BY created_at DESC LIMIT 20";
$result = $conn->query($sql);
$smsLog = $result->fetch_all(MYSQLI_ASSOC);

// 获取统计
$sql = "SELECT COUNT(*) as total FROM user_logs WHERE action = 'send_sms' AND DATE(created_at) = CURDATE()";
$result = $conn->query($sql);
$todayCount = $result->fetch_assoc()['total'];

$conn->close();

echo getHeader('短信轰炸漏洞');
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">📱 短信轰炸漏洞演示</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>💡 漏洞说明：</strong><br>
                    短信接口存在多处缺陷：没有频率限制、没有图形验证码、可批量发送等，导致可被用于短信轰炸攻击。
                </div>
                
                <?php echo $message; ?>
                
                <?php if ($flag): ?>
                <div class="flag-box">
                    🚩 Flag: <?php echo $flag; ?>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">发送短信验证码</div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">手机号</label>
                                        <input type="tel" name="phone" class="form-control" placeholder="13800138000" maxlength="11">
                                    </div>
                                    <button type="submit" name="send_sms" class="btn btn-primary">发送短信</button>
                                </form>
                                <div class="form-text text-danger">提示：没有频率限制，可连续点击</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">发送语音验证码</div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">手机号</label>
                                        <input type="tel" name="phone" class="form-control" placeholder="13800138000" maxlength="11">
                                    </div>
                                    <button type="submit" name="send_voice" class="btn btn-info">发送语音</button>
                                </form>
                                <div class="form-text text-danger">提示：另一个没有限制的接口</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">批量发送</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">手机号列表（每行一个）</label>
                                <textarea name="phones" class="form-control" rows="4" placeholder="13800138000&#10;13800138001&#10;13800138002"></textarea>
                            </div>
                            <button type="submit" name="batch_send" class="btn btn-danger">批量发送</button>
                        </form>
                    </div>
                </div>
                
                <h6 class="mt-4">最近发送记录</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>时间</th>
                            <th>详情</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($smsLog as $log): ?>
                        <tr>
                            <td><?php echo $log['created_at']; ?></td>
                            <td><?php echo htmlspecialchars($log['details']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">📊 今日统计</h6>
            </div>
            <div class="card-body">
                <h4 class="text-center"><?php echo $todayCount; ?></h4>
                <p class="text-center text-muted">今日发送短信数</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">🎯 攻击提示</h6>
            </div>
            <div class="card-body">
                <p class="small">1. 连续点击发送按钮</p>
                <p class="small">2. 使用Burp Suite重放请求</p>
                <p class="small">3. 批量输入多个手机号</p>
                <p class="small">4. 切换短信和语音接口</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">🛡️ 防御建议</h6>
            </div>
            <div class="card-body">
                <ol class="small">
                    <li>添加图形验证码</li>
                    <li>限制同一手机号发送频率（60秒一次）</li>
                    <li>限制同一IP发送次数</li>
                    <li>使用短信服务商的防轰炸服务</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php echo getFooter(); ?>
