<?php
// 密码重置缺陷漏洞场景
include 'common.php';

session_start();
$conn = getDBConnection();
$message = '';
$flag = null;
$resetStep = 1;
$targetUser = null;

// 步骤1：输入用户名/邮箱
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step1'])) {
    $username = $_POST['username'] ?? '';
    
    // 漏洞：没有验证码，可以暴力枚举用户名
    $sql = "SELECT id, username, email FROM logic_users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $targetUser = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($targetUser) {
        $resetStep = 2;
        $_SESSION['reset_user_id'] = $targetUser['id'];
        $_SESSION['reset_code'] = rand(1000, 9999); // 4位验证码，容易被爆破
        
        // 漏洞：验证码显示在页面上（应该发送到邮箱）
        $message = showInfo("验证码已生成：<strong>" . $_SESSION['reset_code'] . "</strong>（实际场景会发送到邮箱）");
    } else {
        $message = showError("用户不存在");
    }
}

// 步骤2：输入验证码
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step2'])) {
    $inputCode = $_POST['code'] ?? '';
    
    // 漏洞1：没有限制验证次数，可以暴力破解
    // 漏洞2：验证码可以绕过（空值或特定值）
    if ($inputCode == $_SESSION['reset_code'] || $inputCode == '0000' || $inputCode === '') {
        $resetStep = 3;
        $message = showSuccess("验证码验证通过");
    } else {
        $message = showError("验证码错误");
        $resetStep = 2;
    }
}

// 步骤3：设置新密码
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step3'])) {
    $newPassword = $_POST['new_password'] ?? '';
    $userId = $_SESSION['reset_user_id'] ?? 0;
    
    if ($userId && $newPassword) {
        // 漏洞：没有验证当前用户是否是本人
        $hashedPassword = md5($newPassword);
        $sql = "UPDATE logic_users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashedPassword, $userId);
        
        if ($stmt->execute()) {
            $flag = getFlag('auth');
            $message = showSuccess("🎉 密码重置成功！检测到密码重置缺陷漏洞！");
            logAction($userId, 'password_reset', "密码被重置");
            $resetStep = 4;
        }
        $stmt->close();
    }
}

// 漏洞：通过URL参数直接重置（没有验证）
if (isset($_GET['direct_reset']) && isset($_GET['user_id'])) {
    $directUserId = intval($_GET['user_id']);
    $newPass = $_GET['new_pass'] ?? 'hacked123';
    
    // 严重漏洞：没有任何验证，直接重置任意用户密码
    $hashedPassword = md5($newPass);
    $sql = "UPDATE logic_users SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hashedPassword, $directUserId);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $flag = getFlag('auth');
        $message = showWarning("🎉 通过URL直接重置密码成功！用户ID: {$directUserId}");
        logAction($directUserId, 'password_reset', "密码被通过URL重置");
    }
    $stmt->close();
}

$conn->close();

echo getHeader('密码重置缺陷');
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">🔑 密码重置缺陷漏洞演示</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>💡 漏洞说明：</strong><br>
                    密码重置功能存在多处缺陷：验证码可爆破、可通过URL直接重置、没有身份验证等。
                </div>
                
                <?php echo $message; ?>
                
                <?php if ($flag): ?>
                <div class="flag-box">
                    🚩 Flag: <?php echo $flag; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($resetStep == 1): ?>
                <!-- 步骤1：输入用户名 -->
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">用户名或邮箱</label>
                        <input type="text" name="username" class="form-control" placeholder="输入用户名或邮箱" required>
                        <div class="form-text">尝试：admin, user1, user2</div>
                    </div>
                    <button type="submit" name="step1" class="btn btn-primary">下一步</button>
                </form>
                <?php endif; ?>
                
                <?php if ($resetStep == 2): ?>
                <!-- 步骤2：输入验证码 -->
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">验证码</label>
                        <input type="text" name="code" class="form-control" placeholder="输入4位验证码" maxlength="4">
                        <div class="form-text text-danger">提示：尝试暴力破解0000-9999，或直接留空</div>
                    </div>
                    <button type="submit" name="step2" class="btn btn-primary">验证</button>
                </form>
                <?php endif; ?>
                
                <?php if ($resetStep == 3): ?>
                <!-- 步骤3：设置新密码 -->
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">新密码</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <button type="submit" name="step3" class="btn btn-success">重置密码</button>
                </form>
                <?php endif; ?>
                
                <?php if ($resetStep == 4): ?>
                <!-- 完成 -->
                <div class="text-center">
                    <p class="text-success">密码重置成功！</p>
                    <a href="07_password_reset.php" class="btn btn-primary">返回</a>
                </div>
                <?php endif; ?>
                
                <hr>
                <h6>直接重置漏洞（URL参数）</h6>
                <div class="alert alert-danger">
                    <p class="small">通过URL直接重置任意用户密码：</p>
                    <code>?direct_reset=1&user_id=1&new_pass=hack123</code>
                    <br><br>
                    <a href="?direct_reset=1&user_id=2&new_pass=hacked" class="btn btn-sm btn-danger">尝试重置user1密码</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">🎯 漏洞列表</h6>
            </div>
            <div class="card-body">
                <ul class="small">
                    <li>没有验证码防护，可枚举用户名</li>
                    <li>验证码为4位数字，可暴力破解</li>
                    <li>验证码可通过空值或0000绕过</li>
                    <li>可通过URL参数直接重置密码</li>
                    <li>没有验证用户身份</li>
                </ul>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">🛡️ 防御建议</h6>
            </div>
            <div class="card-body">
                <ol class="small">
                    <li>使用复杂验证码（6位以上）</li>
                    <li>限制验证次数</li>
                    <li>验证Token机制</li>
                    <li>发送验证链接到注册邮箱</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php echo getFooter(); ?>
