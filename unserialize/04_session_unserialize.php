<?php
// Session反序列化漏洞场景
$module_name = 'Session反序列化';
$module_icon = '🔐';
$module_desc = '演示PHP Session处理过程中的反序列化漏洞。';

// 有漏洞的类
class TestSession {
    public $cmd;
    
    public function __destruct() {
        if (isset($this->cmd)) {
            // 危险操作：执行命令
            system($this->cmd);
        }
    }
}

// 设置Session存储路径
session_save_path('./sessions');
if (!is_dir('./sessions')) {
    mkdir('./sessions', 0755, true);
}

// 启动Session
session_start();

$output = '';
$error = '';

// 处理Session操作
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    switch ($action) {
        case 'set':
            // 设置正常的Session数据
            $_SESSION['user'] = 'test';
            $_SESSION['data'] = 'normal data';
            $output = 'Session设置成功！';
            break;
        case 'get':
            // 获取Session数据
            $output = 'Session数据：<br>';
            $output .= 'user: ' . ($_SESSION['user'] ?? '未设置') . '<br>';
            $output .= 'data: ' . ($_SESSION['data'] ?? '未设置');
            break;
        case 'destroy':
            // 销毁Session
            session_destroy();
            $output = 'Session销毁成功！';
            break;
    }
}

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🔐 Session反序列化</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示Session处理过程中的反序列化漏洞。<br>
                当Session存储格式与读取格式不一致时，攻击者可以构造恶意的Session数据，导致反序列化漏洞。常见于不同脚本使用不同的session.serialize_handler设置。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞原理</h6>
                </div>
                <div class="card-body">
                    <p>Session反序列化漏洞的核心在于：</p>
                    <ol>
                        <li>不同的PHP脚本使用不同的 <code>session.serialize_handler</code> 设置</li>
                        <li>默认的 <code>php</code> 格式使用 <code>serialize()</code> 函数</li>
                        <li>如果一个脚本使用 <code>php</code> 格式写入Session，另一个脚本使用 <code>php_serialize</code> 格式读取，就会导致反序列化漏洞</li>
                    </ol>

                    <h5 class="mb-3 mt-4">Session存储格式</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// php格式（默认）: name|serialized_data
user|s:4:"test";
data|s:11:"normal data";

// php_serialize格式: serialized(array(name => value, ...))
a:2:{s:4:"user";s:4:"test";s:4:"data";s:11:"normal data";}

// php_binary格式: binary_length_namebinary_data
// 注：binary_length是name的长度的二进制表示</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔄 攻击流程</h6>
                </div>
                <div class="card-body">
                    <div class="bg-light p-3 rounded border">
                        <script src="../assets/js/mermaid.min.js"></script>
                        <div class="mermaid">
                            flowchart TD
                                A[攻击者构造恶意Session数据] --> B[使用php格式写入Session]
                                B --> C[目标脚本使用php_serialize格式读取]
                                C --> D[触发反序列化漏洞]
                                D --> E[执行恶意代码]
                            
                            style A fill:#f9f,stroke:#333,stroke-width:2px
                            style E fill:#f99,stroke:#333,stroke-width:2px
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">攻击示例</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 构造恶意Session数据
// 格式：name|serialized_object
// 其中name可以是任意值，serialized_object是恶意对象的序列化字符串

// 构造恶意对象
$object = new TestSession();
$object->cmd = "echo 'Session反序列化攻击成功！'";
$serialized = serialize($object);

// 构造Session数据
// 注意：这里使用|分隔符
$session_data = 'user|' . $serialized;

// 将数据写入Session文件</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">本场景演示Session反序列化漏洞，尝试以下操作：</p>

                    <h5 class="mb-2">1. 管理Session</h5>
                    <div class="btn-group mb-3" role="group">
                        <a href="?action=set" class="btn btn-primary">设置Session</a>
                        <a href="?action=get" class="btn btn-info">获取Session</a>
                        <a href="?action=destroy" class="btn btn-danger">销毁Session</a>
                    </div>

                    <h5 class="mb-2 mt-4">2. 模拟攻击</h5>
                    <p>攻击步骤：</p>
                    <ol>
                        <li>创建一个使用 <code>session.serialize_handler = php</code> 的脚本</li>
                        <li>构造恶意Session数据：<code>user|O:11:"TestSession":1:{s:3:"cmd";s:36:"echo \"攻击成功！\";"}</code></li>
                        <li>将数据写入Session文件</li>
                        <li>访问使用 <code>session.serialize_handler = php_serialize</code> 的脚本</li>
                    </ol>

                    <h5 class="mb-2 mt-4">3. Session文件</h5>
                    <p>当前Session ID：<code>" . session_id() . "</code></p>
                    <p>Session文件路径：<code>./sessions/sess_" . session_id() . "</code></p>
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
                        <p>' . $output . '</p>
                    </div>';
}

if ($error) {
    $content .= '<div class="alert alert-danger">
                        <strong>错误：</strong>
                        <p>' . htmlspecialchars($error) . '</p>
                    </div>';
}

// 检查Session文件
$session_file = './sessions/sess_' . session_id();
if (file_exists($session_file)) {
    $session_content = file_get_contents($session_file);
    $content .= '<div class="alert alert-info">
                        <strong>Session文件内容：</strong>
                        <p><code>' . htmlspecialchars($session_content) . '</code></p>
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
                        <li><strong>统一Session存储格式</strong> - 确保所有脚本使用相同的 <code>session.serialize_handler</code> 设置</li>
                        <li><strong>使用默认的php格式</strong> - 避免使用 <code>php_serialize</code> 格式</li>
                        <li><strong>验证Session数据</strong> - 对Session数据进行验证和过滤</li>
                        <li><strong>使用安全的Session处理</strong> - 避免在Session中存储敏感信息</li>
                        <li><strong>定期清理Session文件</strong> - 防止Session文件积累</li>
                    </ol>

                    <h5 class="mb-3 mt-4">修复后的配置</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 在php.ini中设置统一的Session存储格式
session.serialize_handler = php

// 或在脚本中设置
ini_set('session.serialize_handler', 'php');

// 验证Session数据
function validate_session_data($data) {
    // 验证数据格式
    if (!is_array($data)) {
        return false;
    }
    // 验证数据内容
    foreach ($data as $key => $value) {
        if (!is_scalar($value)) {
            return false;
        }
    }
    return true;
}</code></pre>
                </div>
            </div>
        </div>
    </div>';

include '../template/module_template.php';
?>