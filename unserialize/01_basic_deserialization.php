<?php
$module_name = '基本反序列化';
$module_icon = '🎯';
$module_desc = '通过直接利用unserialize()修改对象属性，了解反序列化漏洞的基本原理。';

$flag = getenv('FLAG') ?: 'flag{deserialization_success}';

class User {
    public $username = "guest";
    public $role = "user";

    public function __construct($username = "guest", $role = "user") {
        $this->username = $username;
        $this->role = $role;
    }

    public function getInfo() {
        return "用户名: {$this->username}, 角色: {$this->role}";
    }
}

$challenge_result = '';
if (isset($_GET['data'])) {
    $data = $_GET['data'];
    $challenge_result .= '<div class="card mb-3"><div class="card-header"><h6>📥 输入数据</h6></div><div class="card-body"><pre class="bg-dark text-light p-3 rounded">' . htmlspecialchars($data) . '</pre></div></div>';

    $user = @unserialize($data);
    if ($user && $user instanceof User) {
        $challenge_result .= '<div class="alert alert-info"><strong>🔍 反序列化结果：</strong><br>' . htmlspecialchars($user->getInfo()) . '</div>';
        if ($user->role === 'admin') {
            $challenge_result .= '<div class="alert alert-success"><strong>🚩 Flag：</strong>' . htmlspecialchars($flag) . '</div>';
        }
    } else {
        $challenge_result .= '<div class="alert alert-danger">反序列化失败！请检查数据格式。</div>';
    }
}

$data_attr = isset($_GET['data']) ? htmlspecialchars($_GET['data']) : '';

$content = <<<'EOT'
<div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">💡 漏洞原理</h5></div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>什么是反序列化漏洞？</strong><br>
                    PHP的 <code>unserialize()</code> 函数可以将序列化的字符串还原为对象。如果攻击者能够控制序列化数据，就可以构造恶意对象来修改对象属性，甚至执行任意代码。
                </div>
                <h6>📐 序列化格式说明</h6>
                <ul class="mb-0">
                    <li><code>O:4:"User":2:{...}</code> - 对象类型</li>
                    <li><code>s:8:"username"</code> - 字符串类型</li>
                    <li><code>i:123</code> - 整数类型</li>
                    <li><code>b:1</code> - 布尔值类型</li>
                </ul>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">💻 漏洞代码</h5></div>
            <div class="card-body">
                <pre class="bg-dark text-light p-3 rounded"><code>class User {
    public $username = "guest";
    public $role = "user";
}

$data = $_GET['data'];
$user = unserialize($data);
echo $user->getInfo();</code></pre>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">🎯 尝试挑战</h5></div>
            <div class="card-body">
                <form method="GET">
                    <div class="mb-3">
                        <label class="form-label">序列化数据：</label>
EOT;
$content .= '                        <input type="text" name="data" class="form-control" value="' . $data_attr . '" placeholder="输入序列化数据">';
$content .= <<<'EOT'
                    </div>
                    <button type="submit" class="btn btn-primary">反序列化</button>
                </form>

EOT;
$content .= $challenge_result;
$content .= <<<'EOT'
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">💡 解题提示</h5></div>
            <div class="card-body">
                <ul>
                    <li><strong>正常数据：</strong><br><code>O:4:"User":2:{s:8:"username";s:5:"guest";s:4:"role";s:4:"user";}</code></li>
                    <li><strong>修改role为admin：</strong><br><code>O:4:"User":2:{s:8:"username";s:5:"admin";s:4:"role";s:5:"admin";}</code></li>
                    <li><strong>PHP生成Payload：</strong><br><code>echo urlencode(serialize(new User("admin", "admin")));</code></li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">🛡️ 防御措施</h5></div>
            <div class="card-body">
                <ul>
                    <li>不要对用户可控的数据使用 <code>unserialize()</code></li>
                    <li>使用 <code>json_decode()</code> 替代 <code>unserialize()</code></li>
                    <li>如果必须使用，使用 <code>allowed_classes</code> 参数限制允许的类</li>
                </ul>
                <pre class="bg-dark text-light p-3 rounded"><code>// 限制允许的类
$data = unserialize($input, ['allowed_classes' => ['User']]);

// 或禁止所有类
$data = unserialize($input, ['allowed_classes' => false]);</code></pre>
            </div>
        </div>
EOT;

include '../template/module_template.php';
