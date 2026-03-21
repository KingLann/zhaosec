<?php
// POP链反序列化漏洞场景
$module_name = 'POP链';
$module_icon = '🔗';
$module_desc = '演示PHP反序列化中的POP链攻击技术。';

// 定义多个类，用于构造POP链
class A {
    public $b;
    
    public function __destruct() {
        if (isset($this->b)) {
            $this->b->execute();
        }
    }
}

class B {
    public $c;
    
    public function execute() {
        if (isset($this->c)) {
            $this->c->run();
        }
    }
}

class C {
    public $cmd;
    
    public function run() {
        if (isset($this->cmd)) {
            // 危险操作：执行命令
            system($this->cmd);
        }
    }
}

class D {
    public $data;
    
    public function __wakeup() {
        // 安全的操作
        echo "D::__wakeup() called\n";
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

// 生成POP链
function generate_pop_chain($cmd) {
    $c = new C();
    $c->cmd = $cmd;
    
    $b = new B();
    $b->c = $c;
    
    $a = new A();
    $a->b = $b;
    
    return serialize($a);
}

// 生成恶意POP链
$malicious_pop_chain = generate_pop_chain('echo "<h3 style=\"color:red\">POP链攻击成功！当前用户：$(whoami)</h3>"');

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🔗 POP链</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示PHP反序列化中的POP链攻击。<br>
                POP（Property Oriented Programming）链是指通过构造对象之间的属性引用链，将多个类的方法调用串联起来，最终触发危险操作。即使单个类看起来安全，通过链式调用也可能执行危险代码。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>class A {
    public $b;
    
    public function __destruct() {
        if (isset($this->b)) {
            $this->b->execute();
        }
    }
}

class B {
    public $c;
    
    public function execute() {
        if (isset($this->c)) {
            $this->c->run();
        }
    }
}

class C {
    public $cmd;
    
    public function run() {
        if (isset($this->cmd)) {
            // 危险操作：执行命令
            system($this->cmd);
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
                    <h6>🔄 POP链执行流程</h6>
                </div>
                <div class="card-body">
                    <div class="bg-light p-3 rounded border">
                        <script src="../assets/js/mermaid.min.js"></script>
                        <div class="mermaid">
                            flowchart TD
                                A[反序列化开始] --> B[创建对象A]
                                B --> C[对象A的__destruct方法被调用]
                                C --> D[调用对象B的execute方法]
                                D --> E[调用对象C的run方法]
                                E --> F[执行system命令]
                            
                            style A fill:#f9f,stroke:#333,stroke-width:2px
                            style F fill:#f99,stroke:#333,stroke-width:2px
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">POP链构造过程</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 构造POP链
$c = new C();
$c->cmd = "echo '攻击成功'";

$b = new B();
$b->c = $c;

$a = new A();
$a->b = $b;

// 序列化整个链
$pop_chain = serialize($a);</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">本场景演示POP链攻击，尝试以下操作：</p>

                    <h5 class="mb-2">1. 执行POP链攻击</h5>
                    <p>构造的POP链序列化字符串：</p>
                    <div class="input-group mb-3">
                        <span class="input-group-text">POP链</span>
                        <input type="text" class="form-control" value="' . htmlspecialchars($malicious_pop_chain) . '" readonly>
                        <a href="?data=' . urlencode($malicious_pop_chain) . '" class="btn btn-danger">执行POP链</a>
                    </div>

                    <h5 class="mb-2 mt-4">2. 分析POP链</h5>
                    <p>POP链的执行过程：</p>
                    <ol>
                        <li>反序列化生成对象A</li>
                        <li>对象A的__destruct方法被调用</li>
                        <li>调用对象B的execute方法</li>
                        <li>调用对象C的run方法</li>
                        <li>执行system命令</li>
                    </ol>

                    <h5 class="mb-2 mt-4">3. 自定义POP链</h5>
                    <p>输入自定义的POP链序列化字符串：</p>
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

$content .= '                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 防御建议</h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li><strong>避免反序列化用户输入</strong> - 不要对用户可控的数据使用 unserialize()</li>
                        <li><strong>使用类型提示和验证</strong> - 验证对象的类型和属性</li>
                        <li><strong>限制魔术方法的权限</strong> - 不要在魔术方法中执行危险操作</li>
                        <li><strong>使用安全的序列化格式</strong> - 如 JSON 或 msgpack</li>
                        <li><strong>实施沙箱</strong> - 限制反序列化过程中的操作权限</li>
                    </ol>

                    <h5 class="mb-3 mt-4">修复后的代码</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 修复1：使用类型验证
class A {
    public $b;
    
    public function __destruct() {
        if (isset($this->b) && $this->b instanceof SafeClass) {
            $this->b->execute();
        }
    }
}

// 修复2：移除危险操作
class C {
    public $cmd;
    
    public function run() {
        if (isset($this->cmd)) {
            // 安全操作：记录日志
            error_log("Command attempted: " . $this->cmd);
        }
    }
}

// 修复3：使用JSON
$json = json_encode($data);
$decoded = json_decode($json);</code></pre>
                </div>
            </div>
        </div>
    </div>';

include '../template/module_template.php';
?>