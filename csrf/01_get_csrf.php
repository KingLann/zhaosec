<?php
// GET型CSRF漏洞场景
$module_name = 'GET型CSRF';
$module_icon = '🔗';
$module_desc = '演示通过GET请求进行CSRF攻击的漏洞场景。';

// 初始化数据
session_start();
if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 10000;
    $_SESSION['transactions'] = [];
}

$message = '';
$error = '';

// 漏洞代码：使用GET请求执行转账操作
if (isset($_GET['to']) && isset($_GET['amount'])) {
    $to = $_GET['to'];
    $amount = intval($_GET['amount']);
    
    // 漏洞：没有验证CSRF Token，也没有验证请求方法
    if ($amount > 0 && $amount <= $_SESSION['balance']) {
        $_SESSION['balance'] -= $amount;
        $_SESSION['transactions'][] = [
            'to' => $to,
            'amount' => $amount,
            'time' => date('Y-m-d H:i:s')
        ];
        $message = "转账成功！向 {$to} 转账 {$amount} 元";
    } else {
        $error = '转账失败：余额不足或金额无效';
    }
}

// 构建交易记录HTML
$transactions_html = '';
if (!empty($_SESSION['transactions'])) {
    $transactions_html = '<table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>时间</th>
                                    <th>收款人</th>
                                    <th>金额</th>
                                </tr>
                            </thead>
                            <tbody>';
    foreach ($_SESSION['transactions'] as $t) {
        $transactions_html .= '<tr>
                                    <td>' . htmlspecialchars($t['time']) . '</td>
                                    <td>' . htmlspecialchars($t['to']) . '</td>
                                    <td>' . htmlspecialchars($t['amount']) . '</td>
                                </tr>';
    }
    $transactions_html .= '</tbody></table>';
} else {
    $transactions_html = '<p class="text-muted">暂无交易记录</p>';
}

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🔗 GET型CSRF</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示GET型CSRF漏洞。<br>
                服务器使用GET请求执行敏感操作（转账），没有验证CSRF Token，攻击者可以通过构造恶意链接诱导用户点击，从而执行非预期的转账操作。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_GET["to"]) && isset($_GET["amount"])) {
    $to = $_GET["to"];
    $amount = intval($_GET["amount"]);
    
    // 漏洞：没有验证CSRF Token，也没有验证请求方法
    if ($amount > 0 && $amount <= $_SESSION["balance"]) {
        $_SESSION["balance"] -= $amount;
        $_SESSION["transactions"][] = [
            "to" => $to,
            "amount" => $amount,
            "time" => date("Y-m-d H:i:s")
        ];
        $message = "转账成功！";
    }
}</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">本场景演示GET型CSRF漏洞，尝试以下攻击：</p>

                    <h5 class="mb-2">1. 正常转账</h5>
                    <p>点击以下链接向张三转账100元：</p>
                    <a href="?to=张三&amount=100" class="btn btn-primary">向张三转账100元</a>

                    <h5 class="mb-2 mt-4">2. CSRF攻击</h5>
                    <p>攻击者构造的恶意链接（用户点击后会向攻击者转账）：</p>
                    <div class="input-group mb-3">
                        <span class="input-group-text">恶意URL</span>
                        <input type="text" class="form-control" value="http://localhost/zhaosec/csrf/01_get_csrf.php?to=攻击者&amount=5000" readonly>
                        <button class="btn btn-danger" onclick="window.location.href=&#39;?to=攻击者&amount=5000&#39;">模拟点击</button>
                    </div>

                    <h5 class="mb-2 mt-4">3. 通过图片标签触发</h5>
                    <p>攻击者可以在网页中嵌入以下图片标签：</p>
                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;img src="http://localhost/zhaosec/csrf/01_get_csrf.php?to=攻击者&amount=1000" width="0" height="0"&gt;</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 账户信息</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>当前余额</h5>
                            <p class="display-4 text-success">¥' . $_SESSION['balance'] . '</p>
                        </div>
                        <div class="col-md-6">
                            <h5>交易记录</h5>
                            ' . $transactions_html . '
                        </div>
                    </div>

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
                        <li><strong>使用POST请求：</strong>敏感操作应该使用POST而不是GET</li>
                        <li><strong>CSRF Token：</strong>为每个表单生成唯一的Token</li>
                        <li><strong>Referer检查：</strong>验证请求的来源是否合法</li>
                        <li><strong>SameSite Cookie：</strong>设置Cookie的SameSite属性</li>
                    </ol>

                    <h5 class="mb-3 mt-4">修复后的代码</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 修复后的代码
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 验证CSRF Token
    if (!hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])) {
        die("CSRF验证失败");
    }
    
    $to = $_POST["to"];
    $amount = intval($_POST["amount"]);
    
    if ($amount > 0 && $amount <= $_SESSION["balance"]) {
        $_SESSION["balance"] -= $amount;
        // ...
    }
}</code></pre>
                </div>
            </div>
        </div>
    </div>';

include '../template/module_template.php';
?>