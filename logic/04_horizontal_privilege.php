<?php
// 水平越权漏洞场景
include 'common.php';

$conn = getDBConnection();
$user = getCurrentUser();
$message = '';
$flag = null;
$viewUser = null;

// 漏洞点：只检查是否登录，不检查是否是本人
if (isset($_GET['user_id'])) {
    $targetUserId = intval($_GET['user_id']);
    
    // 漏洞：没有验证当前用户是否有权限查看该用户信息
    $sql = "SELECT id, username, email, phone, balance, points, role FROM logic_users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $targetUserId);
    $stmt->execute();
    $viewUser = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($viewUser) {
        logAction($user['id'], 'view_profile', "查看用户 {$targetUserId} 的信息");
        
        // 如果查看的是其他用户，触发flag
        if ($targetUserId != $user['id']) {
            $flag = getFlag('privilege');
            $message = showWarning("🎉 你正在查看其他用户的信息！检测到水平越权漏洞！");
        }
    }
}

// 获取所有用户列表（用于演示）
$sql = "SELECT id, username, role FROM logic_users WHERE id != ? LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$otherUsers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();

echo getHeader('水平越权漏洞');
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">↔️ 水平越权漏洞演示</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>💡 漏洞说明：</strong><br>
                    水平越权是指同一权限级别的用户之间可以互相访问数据。<br>
                    本场景中，系统只检查用户是否登录，但未验证用户只能访问自己的数据。
                </div>
                
                <?php echo $message; ?>
                
                <?php if ($flag): ?>
                <div class="flag-box">
                    🚩 Flag: <?php echo $flag; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($viewUser): ?>
                <div class="card mt-3">
                    <div class="card-header bg-info text-white">
                        用户信息详情
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>用户ID：</strong></td>
                                <td><?php echo $viewUser['id']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>用户名：</strong></td>
                                <td><?php echo htmlspecialchars($viewUser['username']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>邮箱：</strong></td>
                                <td><?php echo htmlspecialchars($viewUser['email']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>手机号：</strong></td>
                                <td><?php echo htmlspecialchars($viewUser['phone'] ?? '未设置'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>余额：</strong></td>
                                <td class="text-danger">¥<?php echo $viewUser['balance']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>积分：</strong></td>
                                <td><?php echo $viewUser['points']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>角色：</strong></td>
                                <td><?php echo $viewUser['role']; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <a href="04_horizontal_privilege.php" class="btn btn-secondary mt-3">返回</a>
                <?php else: ?>
                
                <h6 class="mt-4">我的信息</h6>
                <div class="card">
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>用户ID：</strong></td>
                                <td><?php echo $user['id']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>用户名：</strong></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>邮箱：</strong></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>余额：</strong></td>
                                <td>¥<?php echo $user['balance']; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <h6 class="mt-4">尝试访问其他用户</h6>
                <div class="list-group">
                    <?php foreach ($otherUsers as $other): ?>
                    <a href="?user_id=<?php echo $other['id']; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        查看用户: <?php echo htmlspecialchars($other['username']); ?>
                        <span class="badge bg-primary rounded-pill"><?php echo $other['role']; ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-4">
                    <h6>手动测试</h6>
                    <div class="input-group">
                        <span class="input-group-text">user_id=</span>
                        <input type="number" class="form-control" id="manualUserId" placeholder="输入用户ID">
                        <button class="btn btn-outline-primary" onclick="location.href='?user_id='+document.getElementById('manualUserId').value">访问</button>
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
                <p class="small">1. 点击列表中的其他用户链接</p>
                <p class="small">2. 或手动修改URL中的user_id参数</p>
                <p class="small">3. 尝试访问id为1的用户（管理员）</p>
                <p class="small text-muted">例如：?user_id=1</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">🛡️ 防御建议</h6>
            </div>
            <div class="card-body">
                <ol class="small">
                    <li>从Session获取当前用户ID，而不是从URL参数</li>
                    <li>验证用户只能访问自己的数据</li>
                    <li>使用间接引用映射（IDOR防护）</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php echo getFooter(); ?>
