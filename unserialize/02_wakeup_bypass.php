<?php
$module_name = '__wakeup绕过';
$module_icon = '🛡️';
$module_desc = '利用CVE-2016-7124漏洞，通过修改属性数量绕过__wakeup()安全检查。';

$flag = getenv('FLAG') ?: 'flag{deserialization_success}';

class FileHandler {
    public $filename = "/tmp/test.txt";
    public $content = "default";

    public function __wakeup() {
        $this->filename = "/tmp/safe.txt";
    }

    public function __destruct() {
        if ($this->filename) {
            // 模拟文件写入
        }
    }
}

$challenge_result = '';
if (isset($_GET['data'])) {
    $data = $_GET['data'];
    $challenge_result .= '<div class="card mb-3"><div class="card-header"><h6>📥 输入数据</h6></div><div class="card-body"><pre class="bg-dark text-light p-3 rounded">' . htmlspecialchars($data) . '</pre></div></div>';

    $obj = @unserialize($data);
    if ($obj && $obj instanceof FileHandler) {
        $challenge_result .= '<div class="alert alert-info"><strong>🔍 反序列化结果：</strong><br>filename: ' . htmlspecialchars($obj->filename) . '</div>';
        if ($obj->filename !== '/tmp/safe.txt') {
            $challenge_result .= '<div class="alert alert-success"><strong>🚩 Flag：</strong>' . htmlspecialchars($flag) . '</div>';
        } else {
            $challenge_result .= '<div class="alert alert-warning">__wakeup() 被调用，文件名被重置。尝试绕过它！</div>';
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
                    <strong>什么是__wakeup？</strong><br>
                    <code>__wakeup()</code> 是PHP的魔术方法，在 <code>unserialize()</code> 时自动调用。开发者常在其中进行安全检查或重置属性。
                </div>
                <div class="alert alert-danger">
                    <strong>CVE-2016-7124漏洞：</strong><br>
                    当序列化数据中<strong>声明的属性数量大于实际属性数量</strong>时，<code>__wakeup()</code> 方法<strong>不会被调用</strong>！<br>
                    <strong>影响版本：</strong>PHP 5 < 5.6.25, PHP 7 < 7.0.10
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">💻 漏洞代码</h5></div>
            <div class="card-body">
                <pre class="bg-dark text-light p-3 rounded"><code>class FileHandler {
    public $filename;
    public $content;

    public function __wakeup() {
        $this->filename = "/tmp/safe.txt";
    }

    public function __destruct() {
        file_put_contents($this->filename, $this->content);
    }
}

// 漏洞：属性数量大于实际数量时，__wakeup不会被调用
// O:11:"FileHandler":3:{...}  (实际只有2个属性)</code></pre>
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
                    <li><strong>正常数据（__wakeup会被调用）：</strong><br><pre class="bg-dark text-light p-2 rounded">O:11:"FileHandler":2:{s:8:"filename";s:14:"/tmp/test.txt";s:7:"content";s:4:"test";}</pre></li>
                    <li><strong>绕过数据（修改属性数量为3）：</strong><br><pre class="bg-dark text-light p-2 rounded">O:11:"FileHandler":3:{s:8:"filename";s:14:"/tmp/test.txt";s:7:"content";s:4:"test";}</pre></li>
                    <li><strong>关键：</strong>将 <code>:2:</code> 改为 <code>:3:</code>（属性数量大于实际数量）</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">🔍 漏洞原理图解</h5></div>
            <div class="card-body">
                <pre class="bg-dark text-light p-3 rounded">正常反序列化流程：
1. 创建对象
2. 调用 __wakeup()  ← 安全检查在这里
3. 对象可用

属性数量不匹配时：
1. 创建对象
2. __wakeup() 被跳过！ ← 安全检查被绕过
3. 对象可用</pre>
            </div>
        </div>
EOT;

include '../template/module_template.php';
