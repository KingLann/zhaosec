<?php
// POST型CSRF漏洞场景
$module_name = 'POST型CSRF';
$module_icon = '📝';
$module_desc = '演示通过POST请求进行CSRF攻击的漏洞场景。';

// 初始化数据
session_start();
if (!isset($_SESSION['profile'])) {
    $_SESSION['profile'] = [
        'username' => 'user001',
        'email' => 'user@example.com',
        'phone' => '13800138000'
    ];
}

$message = '';

// 漏洞代码：使用POST请求修改用户信息，但没有验证CSRF Token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 漏洞：没有验证CSRF Token
    $_SESSION['profile']['email'] = $_POST['email'] ?? $_SESSION['profile']['email'];
    $_SESSION['profile']['phone'] = $_POST['phone'] ?? $_SESSION['profile']['phone'];
    $message = '个人信息修改成功！';
}

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">📝 POST型CSRF</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示POST型CSRF漏洞。<br>
                服务器使用POST请求修改用户信息，但没有验证CSRF Token，攻击者可以通过构造恶意表单诱导用户提交，从而修改用户的个人信息。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 漏洞：没有验证CSRF Token
    $_SESSION["profile"]["email"] = $_POST["email"];
    $_SESSION["profile"]["phone"] = $_POST["phone"];
    $message = "个人信息修改成功！";
}</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">本场景演示POST型CSRF漏洞，攻击者可以构造恶意表单：</p>

                    <h5 class="mb-2">1. 正常修改信息</h5>
                    <form method="POST" class="mb-3">
                        <div class="mb-3">
                            <label for="email" class="form-label">邮箱</label>
                            <input type="email" name="email" id="email" class="form-control" value="' . htmlspecialchars($_SESSION['profile']['email']) . '">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">手机号</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="' . htmlspecialchars($_SESSION['profile']['phone']) . '">
                        </div>
                        <button type="submit" class="btn btn-primary">修改信息</button>
                    </form>

                    <h5 class="mb-2 mt-4">2. CSRF攻击代码</h5>
                    <p>攻击者构造的恶意网页代码：</p>
                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;!-- 攻击者的恶意网页 --&gt;
&lt;body onload="document.forms[0].submit()"&gt;
&lt;form action="http://localhost/zhaosec/csrf/02_post_csrf.php" method="POST"&gt;
    &lt;input type="hidden" name="email" value="attacker@evil.com"&gt;
    &lt;input type="hidden" name="phone" value="13999999999"&gt;
&lt;/form&gt;
&lt;/body&gt;</code></pre>

                    <h5 class="mb-2 mt-4">3. 模拟攻击</h5>
                    <p>点击按钮模拟攻击者修改你的邮箱和手机号：</p>
                    <form method="POST" id="csrf-form">
                        <input type="hidden" name="email" value="attacker@evil.com">
                        <input type="hidden" name="phone" value="13999999999">
                    </form>
                    <button class="btn btn-danger" onclick="document.getElementById(&#39;csrf-form&#39;).submit()">模拟CSRF攻击</button>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 当前信息</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>用户名</th>
                            <td>' . htmlspecialchars($_SESSION['profile']['username']) . '</td>
                        </tr>
                        <tr>
                            <th>邮箱</th>
                            <td>' . htmlspecialchars($_SESSION['profile']['email']) . '</td>
                        </tr>
                        <tr>
                            <th>手机号</th>
                            <td>' . htmlspecialchars($_SESSION['profile']['phone']) . '</td>
                        </tr>
                    </table>
                    ';

if ($message) {
    $content .= '<div class="alert alert-success mt-3">
                        <strong>提示：</strong>
                        <p>' . htmlspecialchars($message) . '</p>
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
                        <li><strong>CSRF Token：</strong>为每个表单生成唯一的Token</li>
                        <li><strong>验证Referer：</strong>检查请求的来源</li>
                        <li><strong>SameSite Cookie：</strong>设置Cookie的SameSite属性为Strict或Lax</li>
                        <li><strong>二次确认：</strong>敏感操作需要用户再次确认</li>
                    </ol>

                    <h5 class="mb-3 mt-4">修复后的代码</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 生成CSRF Token
session_start();
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// 表单中嵌入Token
&lt;input type="hidden" name="csrf_token" value="&lt;?php echo $_SESSION["csrf_token"]; ?&gt;"&gt;

// 验证Token
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])) {
        die("CSRF验证失败");
    }
    // 处理表单数据
}</code></pre>
                </div>
            </div>
        </div>
    </div>';

include '../template/module_template.php';
?>