<?php
// PHP反序列化基础知识
$module_name = '反序列化基础与原理';
$module_icon = '📚';
$module_desc = '讲解PHP反序列化的基础知识和原理，包括类与对象、序列化与反序列化过程。';

// 页面内容
$content = <<<'EOT'
<div class="card">
        <div class="card-header">
            <h5 class="mb-0">📚 反序列化基础与原理</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>💡 学习目标：</strong><br>
                了解PHP反序列化的基础知识和原理，掌握类与对象的概念，理解序列化与反序列化的过程及其安全隐患。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>📄 什么是序列化和反序列化？</h6>
                </div>
                <div class="card-body">
                    <p><strong>序列化（Serialization）</strong>：将PHP对象转换为可存储或传输的字符串格式。</p>
                    <p><strong>反序列化（Deserialization）</strong>：将序列化后的字符串转换回PHP对象。</p>

                    <h5 class="mb-3 mt-4">基本示例</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 序列化数组
$array = [1, 2, 3, 'hello'];
$serialized = serialize($array);
echo $serialized; // 输出: a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;s:5:"hello";}

// 反序列化
$unserialized = unserialize($serialized);
print_r($unserialized); // 恢复为原始数组</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 类与对象</h6>
                </div>
                <div class="card-body">
                    <p>在PHP中，类是对象的蓝图，对象是类的实例。</p>

                    <h5 class="mb-3">类的定义</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>class User {
    public $name;
    private $age;
    protected $email;
    
    public function __construct($name, $age, $email) {
        $this->name = $name;
        $this->age = $age;
        $this->email = $email;
    }
    
    public function greet() {
        return "Hello, my name is {$this->name}";
    }
}</code></pre>

                    <h5 class="mb-3 mt-4">对象的创建和使用</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 创建对象
$user = new User("John", 30, "john@example.com");
echo $user->greet(); // 输出: Hello, my name is John

// 序列化对象
$serialized = serialize($user);

// 反序列化
$unserialized = unserialize($serialized);
echo $unserialized->greet(); // 同样输出: Hello, my name is John</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔄 序列化与反序列化过程</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="mb-3">📊 序列化流程</h5>
                        <div class="bg-light p-3 rounded border">
                            <script src="../assets/js/mermaid.min.js"></script>
                            <div class="mermaid">
                                flowchart TD
                                    A[PHP对象] --> B[serialize函数]
                                    B --> C[序列化字符串]
                                    C --> D[存储/传输]
                                    D --> E[unserialize函数]
                                    E --> F[PHP对象]
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3">PHP魔术方法</h5>
                    <p>PHP提供了一系列特殊的魔术方法，这些方法在特定情况下会被自动调用：</p>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>魔术方法</th>
                                    <th>触发时机</th>
                                    <th>说明</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>__construct()</code></td>
                                    <td>创建对象时</td>
                                    <td>构造函数，初始化对象属性</td>
                                </tr>
                                <tr>
                                    <td><code>__destruct()</code></td>
                                    <td>对象销毁时</td>
                                    <td>析构函数，清理资源</td>
                                </tr>
                                <tr>
                                    <td><code>__sleep()</code></td>
                                    <td>序列化前</td>
                                    <td>返回需要序列化的属性数组</td>
                                </tr>
                                <tr>
                                    <td><code>__wakeup()</code></td>
                                    <td>反序列化后</td>
                                    <td>恢复对象状态</td>
                                </tr>
                                <tr>
                                    <td><code>__toString()</code></td>
                                    <td>对象被当作字符串时</td>
                                    <td>返回对象的字符串表示</td>
                                </tr>
                                <tr>
                                    <td><code>__call()</code></td>
                                    <td>调用不存在的方法时</td>
                                    <td>处理方法调用</td>
                                </tr>
                                <tr>
                                    <td><code>__callStatic()</code></td>
                                    <td>调用不存在的静态方法时</td>
                                    <td>处理静态方法调用</td>
                                </tr>
                                <tr>
                                    <td><code>__get()</code></td>
                                    <td>获取不存在的属性时</td>
                                    <td>处理属性读取</td>
                                </tr>
                                <tr>
                                    <td><code>__set()</code></td>
                                    <td>设置不存在的属性时</td>
                                    <td>处理属性写入</td>
                                </tr>
                                <tr>
                                    <td><code>__isset()</code></td>
                                    <td>使用isset()检查不存在的属性时</td>
                                    <td>处理属性存在性检查</td>
                                </tr>
                                <tr>
                                    <td><code>__unset()</code></td>
                                    <td>使用unset()删除不存在的属性时</td>
                                    <td>处理属性删除</td>
                                </tr>
                                <tr>
                                    <td><code>__invoke()</code></td>
                                    <td>将对象当作函数调用时</td>
                                    <td>处理对象函数调用</td>
                                </tr>
                                <tr>
                                    <td><code>__set_state()</code></td>
                                    <td>使用var_export()导出类时</td>
                                    <td>处理类导出</td>
                                </tr>
                                <tr>
                                    <td><code>__clone()</code></td>
                                    <td>克隆对象时</td>
                                    <td>处理对象克隆</td>
                                </tr>
                                <tr>
                                    <td><code>__debugInfo()</code></td>
                                    <td>使用var_dump()打印对象时</td>
                                    <td>控制调试信息输出</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mb-3 mt-4">魔术方法示例</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>class MagicDemo {
    private $data = [];
    
    public function __construct() {
        echo "__construct() called\n";
    }
    
    public function __destruct() {
        echo "__destruct() called\n";
    }
    
    public function __sleep() {
        echo "__sleep() called\n";
        return ['data']; // 只序列化data属性
    }
    
    public function __wakeup() {
        echo "__wakeup() called\n";
        // 恢复对象状态
    }
    
    public function __toString() {
        return "MagicDemo object";
    }
    
    public function __call($name, $arguments) {
        echo "__call() called for method: $name\n";
    }
    
    public function __get($name) {
        echo "__get() called for property: $name\n";
        return $this->data[$name] ?? null;
    }
    
    public function __set($name, $value) {
        echo "__set() called for property: $name\n";
        $this->data[$name] = $value;
    }
    
    public function __invoke() {
        echo "__invoke() called\n";
        return "Object called as function";
    }
}

// 测试魔术方法
$obj = new MagicDemo(); // __construct()
$obj->nonExistentMethod(); // __call()
$obj->nonExistentProperty = "value"; // __set()
echo $obj->nonExistentProperty; // __get()
echo $obj; // __toString()
$result = $obj(); // __invoke()

// 序列化和反序列化
$serialized = serialize($obj); // __sleep()
echo "Serialized: $serialized\n";
$unserialized = unserialize($serialized); // __wakeup()
// 脚本结束时会调用__destruct()</code></pre>

                    <h5 class="mb-3 mt-4">序列化格式</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 对象序列化格式
O:4:"User":3:{s:4:"name";s:4:"John";s:7:"Userage";i:30;s:7:"*email";s:16:"john@example.com";}

// 格式说明：
// O:4:"User" - 对象类型，类名长度为4，类名为User
// :3: - 有3个属性
// {s:4:"name";s:4:"John"} - 公共属性name，值为John
// {s:7:"Userage";i:30} - 私有属性age，格式为\0类名\0属性名
// {s:7:"*email";s:16:"john@example.com"} - 保护属性email，格式为\0*\0属性名</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>⚠️ 反序列化的安全风险</h6>
                </div>
                <div class="card-body">
                    <p>反序列化漏洞的根本原因是：当 unserialize() 函数处理用户可控的数据时，攻击者可以构造恶意的序列化字符串，导致：</p>

                    <ul>
                        <li><strong>代码执行</strong> - 通过调用魔术方法执行恶意代码</li>
                        <li><strong>对象注入</strong> - 注入恶意对象</li>
                        <li><strong>POP链攻击</strong> - 构造属性链触发漏洞</li>
                        <li><strong>信息泄露</strong> - 读取敏感信息</li>
                    </ul>

                    <h5 class="mb-3 mt-4">常见的攻击向量</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 危险的反序列化示例
$user_input = $_GET['data'];
// 漏洞：直接反序列化用户输入
$object = unserialize($user_input); // 这里可能执行恶意代码</code></pre>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 防御建议</h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li><strong>避免反序列化用户输入</strong> - 不要对用户可控的数据使用 unserialize()</li>
                        <li><strong>使用安全的序列化格式</strong> - 如 JSON 或 msgpack</li>
                        <li><strong>使用白名单验证</strong> - 验证反序列化的对象类型</li>
                        <li><strong>加密序列化数据</strong> - 防止攻击者篡改</li>
                        <li><strong>限制魔术方法</strong> - 避免在魔术方法中执行危险操作</li>
                        <li><strong>使用最新版本的PHP</strong> - 修复已知的反序列化漏洞</li>
                    </ol>

                    <h5 class="mb-3 mt-4">安全的替代方案</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 使用JSON代替序列化
$json = json_encode($data);
$decoded = json_decode($json);

// 或使用msgpack
$packed = msgpack_pack($data);
$unpacked = msgpack_unpack($packed);</code></pre>
                </div>
            </div>
        </div>
    </div>
EOT;

include '../template/module_template.php';
?>