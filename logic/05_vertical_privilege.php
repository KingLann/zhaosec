<?php
// 垂直越权漏洞场景
include 'common.php';

$conn = getDBConnection();
$user = getCurrentUser();
$message = '';
$flag = null;
$adminView = false;

// 漏洞点：只检查是否登录，不检查角色权限
if (isset($_POST['access_admin'])) {
    $userRole = $_POST['role'] ?? 'user';
    
    // 漏洞：从前端获取角色参数，没有验证当前用户的真实角色
    // 攻击者可以通过抓包修改role参数为admin来访问管理员面板
    if ($userRole === 'admin') {
        $adminView = true;
        
        // 获取所有用户信息（管理员功能）
        $sql = "SELECT id, username, email, role, balance, points FROM logic_users";
        $result = $conn->query($sql);
        $allUsers = $result->fetch_all(MYSQLI_ASSOC);
        
        // 获取系统统计
        $sql = "SELECT COUNT(*) as total_orders, SUM(pay_amount) as total_revenue FROM orders WHERE status = 'paid'";
        $result = $conn->query($sql);
        $stats = $result->fetch_assoc();
        
        logAction($user['id'], 'admin_access', "访问管理员面板");
        
        // 如果不是真正的管理员，触发flag
        if ($user['role'] !== 'admin') {
            $flag = getFlag('privilege');
            $message = showWarning("🎉 你正在访问管理员功能！检测到垂直越权漏洞！<br><strong>说明：</strong>通过修改role参数绕过了权限验证！");
        }
    } else {
        $message = showError("权限不足！只有管理员才能访问此页面。");
    }
}

// 处理删除用户请求（管理员功能）
if (isset($_POST['delete_user'])) {
    // 漏洞：没有验证是否为管理员
    $deleteId = intval($_POST['user_id']);
    
    if ($deleteId !== $user['id']) {
        $sql = "DELETE FROM logic_users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $deleteId);
        $stmt->execute();
        $stmt->close();
        
        logAction($user['id'], 'delete_user', "删除用户 {$deleteId}");
        $message = showSuccess("用户已删除！");
        
        // 刷新用户列表
        $sql = "SELECT id, username, email, role, balance, points FROM logic_users";
        $result = $conn->query($sql);
        $allUsers = $result->fetch_all(MYSQLI_ASSOC);
    }
}

// 处理修改用户角色（管理员功能）
if (isset($_POST['change_role'])) {
    // 漏洞：没有验证是否为管理员
    $targetId = intval($_POST['user_id']);
    $newRole = $_POST['new_role'];
    
    $sql = "UPDATE logic_users SET role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $newRole, $targetId);
    $stmt->execute();
    $stmt->close();
    
    logAction($user['id'], 'change_role', "修改用户 {$targetId} 角色为 {$newRole}");
    $message = showSuccess("用户角色已修改！");
    
    // 刷新用户列表
    $sql = "SELECT id, username, email, role, balance, points FROM logic_users";
    $result = $conn->query($sql);
    $allUsers = $result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();

echo getHeader('垂直越权漏洞');
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">⬆️ 垂直越权漏洞演示</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>💡 漏洞说明：</strong><br>
                    垂直越权是指低权限用户可以访问高权限用户的功能。<br>
                    本场景中，系统只检查用户是否登录，但未验证用户角色权限。
                </div>
                
                <?php echo $message; ?>
                
                <?php if ($flag): ?>
                <div class="flag-box">
                    🚩 Flag: <?php echo $flag; ?>
                </div>
                <?php endif; ?>
                
                <?php if (!$adminView): ?>
                <div class="card">
                    <div class="card-body">
                        <h6>普通用户面板</h6>
                        <p>当前角色：<span class="badge bg-info"><?php echo $user['role']; ?></span></p>
                        <p>欢迎，<?php echo htmlspecialchars($user['username']); ?>！</p>
                        
                        <div class="alert alert-info">
                            <strong>提示：</strong>尝试访问管理员面板（需要管理员权限）
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="role" value="<?php echo $user['role']; ?>">
                            <button type="submit" name="access_admin" class="btn btn-danger">访问管理员面板</button>
                        </form>
                        <div class="form-text text-danger mt-2">提示：抓包修改 role 参数值为 admin 可以绕过权限验证</div>
                    </div>
                </div>
                <?php else: ?>
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        🔴 管理员面板
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h4><?php echo $stats['total_orders']; ?></h4>
                                        <p class="text-muted">总订单数</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h4>¥<?php echo number_format($stats['total_revenue'], 2); ?></h4>
                                        <p class="text-muted">总收入</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h6>用户管理</h6>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>用户名</th>
                                    <th>邮箱</th>
                                    <th>角色</th>
                                    <th>余额</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allUsers as $u): ?>
                                <tr>
                                    <td><?php echo $u['id']; ?></td>
                                    <td><?php echo htmlspecialchars($u['username']); ?></td>
                                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $u['role'] === 'admin' ? 'danger' : ($u['role'] === 'vip' ? 'warning' : 'info'); ?>">
                                            <?php echo $u['role']; ?>
                                        </span>
                                    </td>
                                    <td>¥<?php echo $u['balance']; ?></td>
                                    <td>
                                        <?php if ($u['id'] !== $user['id']): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                            <button type="submit" name="delete_user" class="btn btn-danger btn-sm" onclick="return confirm('确定删除该用户？')">删除</button>
                                        </form>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                            <select name="new_role" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                                <option value="">修改角色</option>
                                                <option value="user">user</option>
                                                <option value="vip">vip</option>
                                                <option value="admin">admin</option>
                                            </select>
                                            <input type="hidden" name="change_role">
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <a href="05_vertical_privilege.php" class="btn btn-secondary mt-3">返回普通面板</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">👤 当前用户</h6>
            </div>
            <div class="card-body">
                <p><strong>用户名：</strong><?php echo $user['username']; ?></p>
                <p><strong>角色：</strong><span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'info'; ?>"><?php echo $user['role']; ?></span></p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">🎯 攻击提示</h6>
            </div>
            <div class="card-body">
                <p class="small"><strong>步骤1：</strong>点击"访问管理员面板"按钮</p>
                <p class="small"><strong>步骤2：</strong>使用Burp Suite拦截请求</p>
                <p class="small"><strong>步骤3：</strong>找到 <code>role=user</code> 参数</p>
                <p class="small"><strong>步骤4：</strong>修改为 <code>role=admin</code></p>
                <p class="small"><strong>步骤5：</strong>发送请求获得管理员权限</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">🛡️ 防御建议</h6>
            </div>
            <div class="card-body">
                <ol class="small">
                    <li>在每个管理员功能前检查用户角色</li>
                    <li>使用RBAC（基于角色的访问控制）</li>
                    <li>在服务端进行权限校验</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php echo getFooter(); ?>
