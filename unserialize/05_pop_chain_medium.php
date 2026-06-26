<?php
$module_name = 'POP链构造 - 中等';
$module_icon = '🔗';
$module_desc = '涉及两个类的POP链，通过对象属性传递形成方法调用链。';

$flag = getenv('FLAG') ?: 'flag{deserialization_success}';

class Database {
    public $sql;

    public function query() {
        // 模拟SQL执行
        echo "<div class='info'><p>执行SQL查询: " . htmlspecialchars($this->sql) . "</p></div>";
        if (preg_match('/(union|select|insert|update|delete|drop|alter)/i', $this->sql)) {
            echo "<div class='result error'><p>检测到危险SQL语句！</p></div>";
        }
        return $this->sql;
    }
}

class UserManager {
    public $db;      // Database对象
    public $action;  // 操作类型

    public function __destruct() {
        if ($this->action == 'query' && $this->db instanceof Database) {
            $this->db->query();
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

        if (is_object($result) && $result instanceof UserManager) {
            if ($result->action == 'query' &&
                $result->db instanceof Database &&
                !empty($result->db->sql)) {
                if (preg_match('/(union|select)/i', $result->db->sql)) {
                    $challenge_result .= '<div class="alert alert-success"><h5>🎉 恭喜你，挑战成功！</h5><p>你成功构造了中等难度的POP链！</p><p>通过 UserManager 的 __destruct() 调用 Database 的 query() 方法，实现了SQL注入。</p><strong>🚩 Flag：</strong>' . htmlspecialchars($flag) . '</div>';
                } else {
                    $challenge_result .= '<div class="alert alert-warning">POP链结构正确，但SQL语句未包含union或select关键字。</div>';
                }
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
                    <strong>中等难度POP链：</strong>涉及两个类，通过对象属性的传递，形成方法调用链。
                </div>
                <div class="alert alert-warning">
                    <strong>攻击思路：</strong>利用第一个类的 <code>__destruct()</code> 调用第二个类的方法，最终实现攻击目标。
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">💻 漏洞代码</h5></div>
            <div class="card-body">
                <pre class="bg-dark text-light p-3 rounded"><code>class Database {
    public $sql;

    public function query() {
        echo "执行SQL查询: " . $this->sql;
        // 检测SQL注入关键字
        if (preg_match('/(union|select|...)/i', $this->sql)) {
            echo "检测到危险SQL语句！";
        }
    }
}

class UserManager {
    public $db;      // Database对象
    public $action;  // 操作类型

    public function __destruct() {
        if ($this->action == 'query' && $this->db instanceof Database) {
            $this->db->query();
        }
    }
}</code></pre>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">🔗 POP链分析</h5></div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>攻击流程：</strong><br>
                    <code>UserManager → __destruct() → Database->query() → SQL注入</code>
                </div>
                <h6>类结构图</h6>
                <div class="alert alert-light border">
                    <strong>UserManager</strong><br>
                    属性: $db (Database对象)<br>
                    属性: $action (字符串)<br>
                    方法: __destruct() - 调用 $db->query()
                </div>
                <div class="alert alert-light border">
                    <strong>Database</strong><br>
                    属性: $sql (字符串)<br>
                    方法: query() - 执行SQL语句
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">🎯 尝试挑战</h5></div>
            <div class="card-body">
                <form method="GET">
                    <div class="mb-3">
                        <label class="form-label">序列化数据：</label>
                        <textarea name="data" class="form-control" rows="4" placeholder="输入序列化数据"><?= isset($_GET['data']) ? htmlspecialchars($_GET['data']) : '' ?></textarea>
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
                    <li>构造UserManager对象，设置 <code>$action = 'query'</code></li>
                    <li>设置Database对象，设置 <code>$sql</code> 为恶意SQL语句</li>
                    <li>嵌套对象：将Database对象赋值给UserManager的 <code>$db</code> 属性</li>
                </ul>
                <h6>示例Payload</h6>
                <pre class="bg-dark text-light p-3 rounded">O:11:"UserManager":2:{s:2:"db";O:8:"Database":1:{s:3:"sql";s:27:"select * from users";};s:6:"action";s:5:"query";}</pre>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">🛡️ 防御措施</h5></div>
            <div class="card-body">
                <ul>
                    <li>避免在魔术方法中调用其他对象的方法</li>
                    <li>对反序列化的对象类型进行严格检查</li>
                    <li>使用 <code>allowed_classes</code> 参数限制允许的类</li>
                    <li>对SQL查询使用参数化查询</li>
                </ul>
            </div>
        </div>
EOT;

include '../template/module_template.php';
