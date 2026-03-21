<?php
// Phar反序列化漏洞场景
$module_name = 'Phar反序列化';
$module_icon = '📦';
$module_desc = '演示PHP Phar文件导致的反序列化漏洞。';

// 有漏洞的类
class TestPhar {
    public $cmd;
    
    public function __destruct() {
        if (isset($this->cmd)) {
            // 危险操作：执行命令
            system($this->cmd);
        }
    }
}

// 生成Phar文件（首次访问时生成）
if (!file_exists('test.phar')) {
    // 创建Phar文件
    $phar = new Phar('test.phar');
    $phar->startBuffering();
    $phar->addFromString('test.txt', 'test');
    $phar->setStub('<?php __HALT_COMPILER(); ?>');
    
    // 创建恶意对象
    $object = new TestPhar();
    $object->cmd = 'echo "<h3 style=\"color:red\">Phar反序列化攻击成功！当前用户：$(whoami)</h3>"';
    
    // 序列化对象并写入metadata
    $phar->setMetadata($object);
    $phar->stopBuffering();
}

$output = '';
$error = '';

// 漏洞代码：使用file_exists等函数处理用户输入
if (isset($_GET['file'])) {
    $file = $_GET['file'];
    
    // 漏洞：file_exists等函数会解析phar://协议，触发反序列化
    if (file_exists($file)) {
        $output = '文件存在！';
    } else {
        $output = '文件不存在！';
    }
}

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">📦 Phar反序列化</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示Phar文件导致的反序列化漏洞。<br>
                当使用file_exists、file_get_contents等文件操作函数处理用户输入时，如果用户输入phar://协议，PHP会解析Phar文件并触发其中metadata的反序列化，从而执行恶意代码。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>class TestPhar {
    public $cmd;
    
    public function __destruct() {
        if (isset($this->cmd)) {
            // 危险操作：执行命令
            system($this->cmd);
        }
    }
}

// 漏洞：file_exists等函数会解析phar://协议
if (isset($_GET["file"])) {
    $file = $_GET["file"];
    if (file_exists($file)) { // 这里会触发Phar反序列化
        $output = "文件存在！";
    }
}</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔄 Phar反序列化原理</h6>
                </div>
                <div class="card-body">
                    <div class="bg-light p-3 rounded border">
                        <script src="../assets/js/mermaid.min.js"></script>
                        <div class="mermaid">
                            flowchart TD
                                A[用户输入phar://协议] --> B[file_exists函数]
                                B --> C[解析Phar文件]
                                C --> D[反序列化metadata]
                                D --> E[执行__destruct方法]
                                E --> F[执行恶意命令]
                            
                            style A fill:#f9f,stroke:#333,stroke-width:2px
                            style F fill:#f99,stroke:#333,stroke-width:2px
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Phar文件结构</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// Phar文件结构
// 1. Stub（存根）：<?php __HALT_COMPILER(); ?>
// 2. Manifest（清单）：包含文件元数据
// 3. File contents（文件内容）：存储的文件
// 4. Signature（签名）：可选的签名</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">本场景演示Phar反序列化漏洞，尝试以下攻击：</p>

                    <h5 class="mb-2">1. 正常文件检查</h5>
                    <p>检查普通文件：</p>
                    <div class="input-group mb-3">
                        <span class="input-group-text">文件路径</span>
                        <input type="text" class="form-control" value="test.txt" readonly>
                        <a href="?file=test.txt" class="btn btn-primary">检查</a>
                    </div>

                    <h5 class="mb-2 mt-4">2. Phar反序列化攻击</h5>
                    <p>使用phar://协议触发反序列化：</p>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Phar路径</span>
                        <input type="text" class="form-control" value="phar://test.phar" readonly>
                        <a href="?file=phar://test.phar" class="btn btn-danger">执行攻击</a>
                    </div>

                    <h5 class="mb-2 mt-4">3. 自定义攻击</h5>
                    <p>输入自定义的文件路径：</p>
                    <form method="GET" class="mb-3">
                        <div class="mb-3">
                            <label for="file" class="form-label">文件路径</label>
                            <input type="text" name="file" id="file" class="form-control" placeholder="输入文件路径，如 phar://test.phar">
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
                        <strong>结果：</strong>
                        <p>' . htmlspecialchars($output) . '</p>
                    </div>';
}

if ($error) {
    $content .= '<div class="alert alert-danger">
                        <strong>错误：</strong>
                        <p>' . htmlspecialchars($error) . '</p>
                    </div>';
}

// 检查test.phar文件是否存在
if (file_exists('test.phar')) {
    $content .= '<div class="alert alert-info">
                        <strong>Phar文件：</strong>
                        <p>test.phar 文件已生成，包含恶意对象</p>
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
                        <li><strong>过滤phar://协议</strong> - 拒绝处理phar://协议的输入</li>
                        <li><strong>使用白名单验证</strong> - 只允许特定的文件路径</li>
                        <li><strong>避免使用危险的魔术方法</strong> - 不在__destruct等方法中执行危险操作</li>
                        <li><strong>禁用Phar扩展</strong> - 如果不需要，禁用Phar扩展</li>
                        <li><strong>使用安全的文件操作函数</strong> - 避免使用可能触发Phar解析的函数</li>
                    </ol>

                    <h5 class="mb-3 mt-4">修复后的代码</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 修复1：过滤phar://协议
if (isset($_GET["file"])) {
    $file = $_GET["file"];
    if (strpos($file, "phar://") !== false) {
        die("非法文件路径");
    }
    if (file_exists($file)) {
        $output = "文件存在！";
    }
}

// 修复2：使用白名单
$allowed_files = ["test.txt", "config.php"];
if (isset($_GET["file"])) {
    $file = $_GET["file"];
    if (in_array($file, $allowed_files)) {
        if (file_exists($file)) {
            $output = "文件存在！";
        }
    }
}</code></pre>
                </div>
            </div>
        </div>
    </div>';

include '../template/module_template.php';
?>