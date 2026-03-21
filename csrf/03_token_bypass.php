<?php
// CSRF Token绕过漏洞场景
$module_name = 'Token绕过';
$module_icon = '🔓';
$module_desc = '演示CSRF Token验证缺陷导致的绕过漏洞。';

// 初始化数据
session_start();
if (!isset($_SESSION['settings'])) {
    $_SESSION['settings'] = [
        'notifications' => true,
        'privacy' => 'public'
    ];
}

// 生成CSRF Token（存在漏洞的实现）
if (!isset($_SESSION['csrf_token'])) {
    // 漏洞1：Token过于简单，容易被猜测
    $_SESSION['csrf_token'] = md5(session_id());
}

$message = '';
$error = '';

// 漏洞代码：Token验证存在缺陷
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    
    // 漏洞2：使用弱比较（==）而不是强比较（===）
    // 漏洞3：没有验证Token是否为空
    if ($token == $_SESSION['csrf_token']) {
        $_SESSION['settings']['notifications'] = isset($_POST['notifications']);
        $_SESSION['settings']['privacy'] = $_POST['privacy'] ?? $_SESSION['settings']['privacy'];
        $message = '设置修改成功！';
    } else {
        $error = 'CSRF Token验证失败！';
    }
}

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🔓 Token绕过</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示CSRF Token验证缺陷导致的绕过漏洞。<br>
                虽然使用了CSRF Token，但由于Token生成方式不安全、验证逻辑存在缺陷（如使用弱比较），攻击者可以绕过Token验证。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>// 漏洞1：Token过于简单
if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = md5(session_id());  // 容易被猜测
}

// 漏洞2：使用弱比较（==）而不是强比较（===）
// 漏洞3：没有验证Token是否为空
if ($token == $_SESSION["csrf_token"]) {
    // 执行操作
} else {
    $error = "CSRF Token验证失败！";
}</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">本场景演示Token验证缺陷，尝试以下攻击：</p>

                    <h5 class="mb-2">1. 正常修改设置</h5>
                    <form method="POST" class="mb-3">
                        <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="notifications" id="notifications" class="form-check-input" ' . ($_SESSION['settings']['notifications'] ? 'checked' : '') . '>
                            <label for="notifications" class="form-check-label">开启通知</label>
                        </div>
                        <div class="mb-3">
                            <label for="privacy" class="form-label">隐私设置</label>
                            <select name="privacy" id="privacy" class="form-control">
                                <option value="public" ' . ($_SESSION['settings']['privacy'] == 'public' ? 'selected' : '') . '>公开</option>
                                <option value="friends" ' . ($_SESSION['settings']['privacy'] == 'friends' ? 'selected' : '') . '>仅好友</option>
                                <option value="private" ' . ($_SESSION['settings']['privacy'] == 'private' ? 'selected' : '') . '>私密</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">保存设置</button>
                    </form>

                    <h5 class="mb-2 mt-4">2. Token猜测攻击</h5>
                    <p>由于Token是session_id的MD5值，攻击者可以猜测Token：</p>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 攻击者可以通过社会工程学获取session_id
// 然后计算MD5值作为Token
$guessed_token = md5($session_id);</code></pre>

                    <h5 class="mb-2 mt-4">3. PHP弱类型比较绕过</h5>
                    <p>由于使用==进行比较，可以尝试以下绕过方式：</p>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 如果Token是"0e123456789..."（科学计数法格式）
// 传入0会被认为是相等的
// 或者传入true也会被认为是相等的</code></pre>
                    <form method="POST" id="bypass-form">
                        <input type="hidden" name="csrf_token" value="true">
                        <input type="hidden" name="privacy" value="public">
                    </form>
                    <button class="btn btn-danger" onclick="document.getElementById(&#39;bypass-form&#39;).submit()">尝试绕过Token验证</button>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 当前设置</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>通知设置</th>
                            <td>' . ($_SESSION['settings']['notifications'] ? '开启' : '关闭') . '</td>
                        </tr>
                        <tr>
                            <th>隐私设置</th>
                            <td>' . htmlspecialchars($_SESSION['settings']['privacy']) . '</td>
                        </tr>
                        <tr>
                            <th>当前Token</th>
                            <td><code>' . substr($_SESSION['csrf_token'], 0, 16) . '...</code></td>
                        </tr>
                    </table>
                    ';

if ($message) {
    $content .= '<div class="alert alert-success mt-3">
                        <strong>成功：</strong>
                        <p>' . htmlspecialchars($message) . '</p>
                    </div>';
}

if ($error) {
    $content .= '<div class="alert alert-danger mt-3">
                        <strong>错误：</strong>
                        <p>' . htmlspecialchars($error) . '</p>
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
                        <li><strong>使用强随机Token：</strong>使用random_bytes()生成不可预测的Token</li>
                        <li><strong>使用强比较：</strong>使用===而不是==进行Token验证</li>
                        <li><strong>验证Token非空：</strong>确保Token不为空</li>
                        <li><strong>Token时效性：</strong>设置Token的有效期</li>
                        <li><strong>单次使用：</strong>每个Token只能使用一次</li>
                    </ol>

                    <h5 class="mb-3 mt-4">修复后的代码</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 生成强随机Token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// 验证Token
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["csrf_token"] ?? "";
    
    // 使用强比较，并验证Token非空
    if (empty($token) || !hash_equals($_SESSION["csrf_token"], $token)) {
        die("CSRF Token验证失败");
    }
    
    // 处理表单数据
}</code></pre>
                </div>
            </div>
        </div>
    </div>';

include '../template/module_template.php';
?>