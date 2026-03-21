<?php
// 业务流程步骤跳跃漏洞场景
include 'common.php';

session_start();
$conn = getDBConnection();
$user = getCurrentUser();
$message = '';
$flag = null;

// 初始化流程状态
if (!isset($_SESSION['process_step'])) {
    $_SESSION['process_step'] = 1;
}

$currentStep = $_SESSION['process_step'];

// 处理流程步骤
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stepJumped = false;
    
    // 漏洞点：接受前端传入的步骤参数，没有验证是否可以跳转
    if (isset($_POST['step'])) {
        $targetStep = intval($_POST['step']);
        // 漏洞：没有验证是否可以跳转到该步骤，直接允许跳转
        if ($targetStep >= 1 && $targetStep <= 4) {
            // 检测是否跳过了必要步骤
            if ($targetStep > $currentStep) {
                // 如果跳过了审核步骤（步骤3）
                if ($targetStep >= 4 && $currentStep < 3) {
                    $flag = getFlag('business');
                    $message = showWarning("🎉 检测到步骤跳跃！从步骤 {$currentStep} 直接跳到步骤 {$targetStep}，跳过了必要的审核步骤！");
                    // 漏洞：直接设置为审核通过，显示审核通过的页面
                    $_SESSION['is_reviewed'] = true;
                    $_SESSION['has_documents'] = true;
                }
                $_SESSION['process_step'] = $targetStep;
                $currentStep = $targetStep;
                $stepJumped = true;
            }
        }
    }
    
    // 步骤1：填写信息
    if (!$stepJumped && isset($_POST['step1'])) {
        $_SESSION['apply_name'] = $_POST['name'] ?? '';
        $_SESSION['apply_phone'] = $_POST['phone'] ?? '';
        $_SESSION['apply_reason'] = $_POST['reason'] ?? '';
        $_SESSION['process_step'] = 2;
        $currentStep = 2;
    }
    
    // 步骤2：上传资料
    if (!$stepJumped && isset($_POST['step2'])) {
        $_SESSION['has_documents'] = true;
        $_SESSION['process_step'] = 3;
        $currentStep = 3;
    }
    
    // 步骤3：审核（模拟）
    if (!$stepJumped && isset($_POST['step3'])) {
        $_SESSION['is_reviewed'] = true;
        $_SESSION['process_step'] = 4;
        $currentStep = 4;
    }
    
    // 上一步功能
    if (isset($_POST['prev_step'])) {
        $prevStep = intval($_POST['prev_step']);
        if ($prevStep >= 1 && $prevStep < $currentStep) {
            $_SESSION['process_step'] = $prevStep;
            $currentStep = $prevStep;
        }
    }
    
    // 漏洞点：直接提交完成，跳过前面步骤
    if (isset($_POST['complete'])) {
        // 漏洞：没有验证前面步骤是否完成
        $step = intval($_POST['direct_step'] ?? 4);
        
        // 如果直接跳到完成步骤（跳过必要步骤）
        if ($step >= 4 && (!isset($_SESSION['is_reviewed']) || !$_SESSION['is_reviewed'])) {
            $flag = getFlag('business');
            $message = showWarning("🎉 你跳过了必要的审核步骤！检测到业务流程绕过漏洞！");
        }
        
        // 完成申请
        $sql = "INSERT INTO user_logs (user_id, action, details) VALUES (?, 'apply_complete', '完成申请流程')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $stmt->close();
        
        $message .= showSuccess("申请已提交！");
        
        // 重置流程
        $_SESSION['process_step'] = 1;
        unset($_SESSION['apply_name'], $_SESSION['apply_phone'], $_SESSION['apply_reason'], $_SESSION['has_documents'], $_SESSION['is_reviewed']);
        $currentStep = 1;
    }
}

// 漏洞：通过URL参数直接跳转到指定步骤
if (isset($_GET['jump_to'])) {
    $jumpStep = intval($_GET['jump_to']);
    // 漏洞：没有验证是否可以跳转到该步骤
    $_SESSION['process_step'] = $jumpStep;
    $currentStep = $jumpStep;
    $message = showInfo("已跳转到步骤 {$jumpStep}");
}

$conn->close();

echo getHeader('业务流程绕过');
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">⏭️ 业务流程步骤跳跃漏洞演示</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>💡 漏洞说明：</strong><br>
                    业务流程步骤跳跃是指攻击者绕过必要的业务步骤直接完成操作。<br>
                    本场景中，申请流程需要经过多个必要步骤，但系统未严格验证步骤顺序。
                </div>
                
                <?php echo $message; ?>
                
                <?php if ($flag): ?>
                <div class="flag-box">
                    🚩 Flag: <?php echo $flag; ?>
                </div>
                <?php endif; ?>
                
                <!-- 进度指示器 -->
                <div class="progress mb-4">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo ($currentStep / 4) * 100; ?>%">
                        步骤 <?php echo $currentStep; ?> / 4
                    </div>
                </div>
                
                <!-- 步骤1：填写信息 -->
                <?php if ($currentStep == 1): ?>
                <div class="card">
                    <div class="card-header">步骤 1：填写申请信息</div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="step" value="2">
                            <div class="mb-3">
                                <label class="form-label">姓名</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">手机号</label>
                                <input type="tel" name="phone" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">申请理由</label>
                                <textarea name="reason" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-text text-danger">提示：抓包修改 step 参数值可以跳过步骤</div>
                                <button type="submit" name="step1" class="btn btn-primary">下一步</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- 步骤2：上传资料 -->
                <?php if ($currentStep == 2): ?>
                <div class="card">
                    <div class="card-header">步骤 2：上传证明材料</div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="step" value="3">
                            <div class="mb-3">
                                <label class="form-label">身份证照片</label>
                                <input type="file" class="form-control" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">工作证明</label>
                                <input type="file" class="form-control" accept=".pdf,.doc,.docx">
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-text text-danger">提示：抓包修改 step 参数值可以跳过步骤</div>
                                <div>
                                    <button type="button" class="btn btn-secondary me-2" onclick="document.getElementById('prevForm').submit();">上一步</button>
                                    <button type="submit" name="step2" class="btn btn-primary">下一步</button>
                                </div>
                            </div>
                        </form>
                        <form id="prevForm" method="POST" style="display:none;">
                            <input type="hidden" name="prev_step" value="1">
                        </form>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- 步骤3：等待审核 -->
                <?php if ($currentStep == 3): ?>
                <div class="card">
                    <div class="card-header">步骤 3：资料审核</div>
                    <div class="card-body">
                        <p>您的申请正在审核中...</p>
                        <p class="text-muted">审核通常需要1-3个工作日</p>
                        
                        <div class="alert alert-info">
                            <strong>审核信息：</strong><br>
                            姓名：<?php echo $_SESSION['apply_name'] ?? ''; ?><br>
                            手机号：<?php echo $_SESSION['apply_phone'] ?? ''; ?><br>
                            材料状态：已上传
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="step" value="4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-text text-danger">提示：抓包修改 step 参数值可以跳过步骤</div>
                                <div>
                                    <button type="button" class="btn btn-secondary me-2" onclick="document.getElementById('prevForm3').submit();">上一步</button>
                                    <button type="submit" name="step3" class="btn btn-primary">模拟审核通过</button>
                                </div>
                            </div>
                        </form>
                        <form id="prevForm3" method="POST" style="display:none;">
                            <input type="hidden" name="prev_step" value="2">
                        </form>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- 步骤4：完成 -->
                <?php if ($currentStep == 4): ?>
                <div class="card">
                    <div class="card-header">步骤 4：确认提交</div>
                    <div class="card-body">
                        <p>请确认您的申请信息：</p>
                        <ul>
                            <li>姓名：<?php echo $_SESSION['apply_name'] ?? ''; ?></li>
                            <li>手机号：<?php echo $_SESSION['apply_phone'] ?? ''; ?></li>
                            <li>申请理由：<?php echo $_SESSION['apply_reason'] ?? ''; ?></li>
                            <li>材料状态：<?php echo isset($_SESSION['has_documents']) ? '已上传' : '未上传'; ?></li>
                            <li>审核状态：<?php echo isset($_SESSION['is_reviewed']) ? '已通过' : '未审核'; ?></li>
                        </ul>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('prevForm4').submit();">上一步</button>
                            <form method="POST">
                                <button type="submit" name="complete" class="btn btn-success">确认提交申请</button>
                            </form>
                        </div>
                        <form id="prevForm4" method="POST" style="display:none;">
                            <input type="hidden" name="prev_step" value="3">
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">🎯 攻击提示</h6>
            </div>
            <div class="card-body">
                <p class="small"><strong>方法1：抓包修改step参数</strong></p>
                <ol class="small">
                    <li>在步骤1点击"下一步"时抓包</li>
                    <li>找到 <code>step=2</code> 参数</li>
                    <li>修改为 <code>step=4</code> 跳过审核</li>
                    <li>发送请求获得Flag</li>
                </ol>
                <p class="small mt-2"><strong>方法2：URL参数跳转</strong></p>
                <code class="d-block bg-light p-2 mb-2">?jump_to=4</code>
                <p class="small"><strong>方法3：直接提交complete请求</strong></p>
                <p class="small">添加参数：<code>complete=1&direct_step=4</code></p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">📋 正常流程</h6>
            </div>
            <div class="card-body">
                <ol class="small">
                    <li class="<?php echo $currentStep >= 1 ? 'text-success' : ''; ?>">填写申请信息</li>
                    <li class="<?php echo $currentStep >= 2 ? 'text-success' : ''; ?>">上传证明材料</li>
                    <li class="<?php echo $currentStep >= 3 ? 'text-success' : ''; ?>">等待资料审核</li>
                    <li class="<?php echo $currentStep >= 4 ? 'text-success' : ''; ?>">确认提交申请</li>
                </ol>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">🛡️ 防御建议</h6>
            </div>
            <div class="card-body">
                <ol class="small">
                    <li>在服务端维护流程状态机</li>
                    <li>验证每个步骤的前置条件</li>
                    <li>使用Token验证流程完整性</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php echo getFooter(); ?>
