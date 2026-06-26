<?php
$module_name = 'POP链构造 - 困难';
$module_icon = '🔗';
$module_desc = '涉及三个类的复杂POP链，通过多层对象嵌套和方法调用实现命令执行。';

$flag = getenv('FLAG') ?: 'flag{deserialization_success}';

class CommandExecutor {
    public $cmd;

    public function execute() {
        echo "<div>执行命令: " . htmlspecialchars($this->cmd) . "</div>";
        if (preg_match('/(ls|cat|whoami|id|pwd|uname)/i', $this->cmd)) {
            echo "<div>模拟输出: 命令执行成功</div>";
            return true;
        }
        return false;
    }

    public function log($data) {
        echo "<div>记录日志: " . htmlspecialchars($data) . "</div>";
        $this->execute();
    }
}

class LogProcessor {
    public $logger;
    public $logData;

    public function process() {
        if ($this->logger instanceof CommandExecutor) {
            $this->logger->log($this->logData);
        }
    }
}

class Application {
    public $processor;
    public $config;

    public function __destruct() {
        if (isset($this->config['autoLog']) && $this->config['autoLog']) {
            if ($this->processor instanceof LogProcessor) {
                $this->processor->process();
            }
        }
    }

    public function __wakeup() {
        if (isset($this->config['init'])) {
            echo "<div>检测到 __wakeup() 中的 eval() 调用！</div>";
        }
    }
}

$challenge_result = '';
if (isset($_GET['data'])) {
    $data = $_GET['data'];
    $challenge_result .= '<div class="card mb-3"><div class="card-header"><h6>📥 输入数据</h6></div><div class="card-body"><pre class="bg-dark text-light p-3 rounded">' . htmlspecialchars($data) . '</pre></div></div>';

    $result = @unserialize($data);
    if ($result === false) {
        $challenge_result .= '<div class="alert alert-danger"><h6>反序列化失败</h6><p>数据格式错误，请检查序列化数据格式。</p></div>';
    } else {
        $challenge_result .= '<div class="card mb-3"><div class="card-header"><h6>🔍 反序列化结果</h6></div><div class="card-body"><pre class="bg-dark text-light p-3 rounded">' . htmlspecialchars(print_r($result, true)) . '</pre></div></div>';

        if (is_object($result) && $result instanceof Application) {
            $cmdExecuted = false;
            if (isset($result->config['autoLog']) &&
                $result->config['autoLog'] &&
                $result->processor instanceof LogProcessor &&
                $result->processor->logger instanceof CommandExecutor &&
                !empty($result->processor->logger->cmd)) {
                $cmdExecuted = true;
            }
            if (isset($result->config['init']) && !empty($result->config['init'])) {
                $cmdExecuted = true;
            }
            if ($cmdExecuted) {
                $challenge_result .= '<div class="alert alert-success"><h5>🎉 恭喜你，挑战成功！</h5><p>你成功构造了困难难度的POP链！</p><strong>🚩 Flag：</strong>' . htmlspecialchars($flag) . '</div>';
            } else {
                $challenge_result .= '<div class="alert alert-warning">POP链结构不完整，请检查属性设置。</div>';
            }
        }
    }
}

$content = <<<'EOT'

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">💡 漏洞原理</h5></div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>困难难度POP链：</strong>涉及三个类，通过多层对象嵌套和方法调用，形成复杂的攻击链。
                </div>
                <div class="alert alert-warning">
                    <strong>攻击思路：</strong>利用多个类的魔术方法和普通方法，层层传递参数，最终实现命令执行。
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">💻 漏洞代码</h5></div>
            <div class="card-body">
                <pre class="bg-dark text-light p-3 rounded"><code>class CommandExecutor {
    public $cmd;
    public function execute() { return system($this->cmd); }
    public function log($data) { $this->execute(); }
}

class LogProcessor {
    public $logger;   // CommandExecutor对象
    public $logData;
    public function process() {
        if ($this->logger instanceof CommandExecutor) {
            $this->logger->log($this->logData);
        }
    }
}

class Application {
    public $processor;  // LogProcessor对象
    public $config;     // 配置数组
    public function __destruct() {
        if ($this->config['autoLog']) {
            $this->processor->process();
        }
    }
    public function __wakeup() {
        eval($this->config['init']);
    }
}</code></pre>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">🔗 POP链分析</h5></div>
            <div class="card-body">
                <div class="alert alert-info"><strong>攻击流程：</strong><br>
                <code>Application → __destruct() → LogProcessor->process() → CommandExecutor->log() → execute() → system()</code></div>
                <h6>类结构图</h6>
                <div class="alert alert-light border">
                    <strong>Application</strong><br>
                    属性: $processor (LogProcessor对象), $config (配置数组)<br>
                    方法: __destruct() - 调用 $processor->process()<br>
                    方法: __wakeup() - 执行 eval($config['init'])
                </div>
                <div class="alert alert-light border">
                    <strong>LogProcessor</strong><br>
                    属性: $logger (CommandExecutor对象), $logData (字符串)<br>
                    方法: process() - 调用 $logger->log($logData)
                </div>
                <div class="alert alert-light border">
                    <strong>CommandExecutor</strong><br>
                    属性: $cmd (字符串)<br>
                    方法: execute() - 执行 system($cmd)
                </div>
            </div>
        </div>
EOT;

$content .= <<<'EOT'
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">🎯 尝试挑战</h5></div>
            <div class="card-body">
                <form method="GET">
                    <div class="mb-3">
                        <label class="form-label">序列化数据：</label>
                        <textarea name="data" class="form-control" rows="5" placeholder="输入序列化数据"><?= isset($_GET['data']) ? htmlspecialchars($_GET['data']) : ''; ?></textarea>
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
                <h6>方法一：通过__destruct触发</h6>
                <ul>
                    <li>构造Application对象，设置 <code>$config['autoLog'] = true</code></li>
                    <li>设置 <code>$processor</code> 为LogProcessor对象</li>
                    <li>设置LogProcessor的 <code>$logger</code> 为CommandExecutor对象</li>
                    <li>设置CommandExecutor的 <code>$cmd</code> 为要执行的命令</li>
                </ul>
                <pre class="bg-dark text-light p-3 rounded">O:11:"Application":2:{s:9:"processor";O:12:"LogProcessor":2:{s:6:"logger";O:16:"CommandExecutor":1:{s:3:"cmd";s:6:"whoami";};s:7:"logData";s:4:"test";};s:6:"config";a:1:{s:8:"autoLog";b:1;}}</pre>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">🛡️ 防御措施</h5></div>
            <div class="card-body">
                <ul>
                    <li>避免在魔术方法中执行危险操作</li>
                    <li>不要对用户可控的数据进行 <code>unserialize()</code></li>
                    <li>使用 <code>allowed_classes</code> 参数限制允许的类</li>
                </ul>
            </div>
        </div>
EOT;

include '../template/module_template.php';
