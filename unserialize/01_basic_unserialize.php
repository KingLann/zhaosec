<?php
// 基础反序列化漏洞场景
$module_name = '基础反序列化';
$module_icon = '🔓';
$module_desc = '演示PHP反序列化漏洞的基本原理和利用方法。';

// 有漏洞的类
class Test {
    public $name;
    public $cmd;
    
    // 漏洞：在__wakeup方法中执行命令
    public function __wakeup() {
        if (isset($this->cmd)) {
            // 危险操作：执行命令
            system($this->cmd);
        }
    }
    
    // 漏洞：在__destruct方法中也有危险操作
    public function __destruct() {
        if (isset($this->name)) {
            // 危险操作：写入文件
            file_put_contents('test.txt', $this->name);
        }
    }
}

$output = '';
$error = '';

// 处理反序列化
if (isset($_GET['data'])) {
    $data = $_GET['data'];
    try {
        $object = unserialize($data);
        $output = '反序列化成功！';
    } catch (Exception $e) {
        $error = '反序列化失败：' . $e->getMessage();
    }
}

// 生成正常的序列化字符串
$normal_test = new Test();
$normal_test->name = '正常数据';
$normal_serialized = serialize($normal_test);

// 生成恶意的序列化字符串
$malicious_test = new Test();
$malicious_test->cmd = 'echo "<h3 style=\"color:red\">命令执行成功！当前用户：$(whoami)</h3>"';
$malicious_test->name = '恶意数据';
$malicious_serialized = serialize($malicious_test);

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🔓 基础反序列化</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示基础的PHP反序列化漏洞。<br>
                当 unserialize() 函数处理用户可控的数据时，如果目标类中存在危险的魔术方法（如 __wakeup、__destruct 等），攻击者可以构造恶意的序列化字符串来执行任意命令。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>class Test {
    public $name;
    public $cmd;
    
    // 漏洞：在__wakeup方法中执行命令
    public function __wakeup() {
        if (isset($this->cmd)) {
            // 危险操作：执行命令
            system($this->cmd);
        }
    }
    
    // 漏洞：在__destruct方法中也有危险操作
    public function __destruct() {
        if (isset($this->name)) {
            // 危险操作：写入文件
            file_put_contents("test.txt", $this->name);
        }
    }
}

// 漏洞：直接反序列化用户输入
if (isset($_GET[&#39;data&#39;])) {
    $data = $_GET[&#39;data&#39;];
    $object = unserialize($data); // 这里可能执行恶意代码
}</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">本场景演示基础的反序列化漏洞，尝试以下攻击：</p>

                    <h5 class="mb-2">1. 正常反序列化</h5>
                    <p>正常的序列化字符串：</p>
                    <div class="input-group mb-3">
                        <span class="input-group-text">正常数据</span>
                        <input type="text" class="form-control" value="' . htmlspecialchars($normal_serialized) . '" readonly>
                        <a href="?data=' . urlencode($normal_serialized) . '" class="btn btn-primary">测试</a>
                    </div>

                    <h5 class="mb-2 mt-4">2. 命令执行攻击</h5>
                    <p>构造恶意的序列化字符串，执行系统命令：</p>
                    <div class="input-group mb-3">
                        <span class="input-group-text">恶意数据</span>
                        <input type="text" class="form-control" value="' . htmlspecialchars($malicious_serialized) . '" readonly>
                        <a href="?data=' . urlencode($malicious_serialized) . '" class="btn btn-danger">执行命令</a>
                    </div>

                    <h5 class="mb-2 mt-4">3. 自定义攻击</h5>
                    <p>输入自定义的序列化字符串：</p>
                    <form method="GET" class="mb-3">
                        <div class="mb-3">
                            <label for="data" class="form-label">序列化字符串</label>
                            <textarea name="data" id="data" class="form-control" rows="3" placeholder="输入序列化字符串"></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning">提交</button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 执行结果</h6>
                </div>
                <div class="card-body">
                    ';

if ($output) {
    $content .= '<div class="alert alert-success">
                        <strong>成功：</strong>
                        <p>' . htmlspecialchars($output) . '</p>
                    </div>';
}

if ($error) {
    $content .= '<div class="alert alert-danger">
                        <strong>错误：</strong>
                        <p>' . htmlspecialchars($error) . '</p>
                    </div>';
}

// 检查是否生成了test.txt文件
if (file_exists('test.txt')) {
    $file_content = file_get_contents('test.txt');
    $content .= '<div class="alert alert-info">
                        <strong>文件内容：</strong>
                        <p><code>' . htmlspecialchars($file_content) . '</code></p>
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
                        <li><strong>避免反序列化用户输入</strong> - 不要对用户可控的数据使用 unserialize()</li>
                        <li><strong>使用安全的序列化格式</strong> - 如 JSON 或 msgpack</li>
                        <li><strong>验证反序列化的对象类型</strong> - 使用白名单机制</li>
                        <li><strong>避免在魔术方法中执行危险操作</strong> - 不要在 __wakeup、__destruct 等方法中执行命令</li>
                        <li><strong>加密序列化数据</strong> - 防止攻击者篡改</li>
                    </ol>

                    <h5 class="mb-3 mt-4">修复后的代码</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 修复1：使用JSON代替序列化
$json = json_encode($data);
$decoded = json_decode($json);

// 修复2：验证对象类型
function safe_unserialize($data) {
    $object = unserialize($data);
    if ($object instanceof SafeClass) {
        return $object;
    }
    return null;
}

// 修复3：移除危险的魔术方法
class SafeClass {
    public $name;
    // 移除危险的__wakeup和__destruct方法
}</code></pre>
                </div>
            </div>
        </div>
    </div>';

include '../template/module_template.php';
?>