<?php
// 基础SSRF漏洞
$module_name = '基础SSRF';
$module_icon = '🌐';
$module_desc = 'URL参数可控，攻击者可以构造恶意URL访问内部资源。';

// 漏洞代码
$result = '';
$error = '';

if (isset($_GET['url'])) {
    $url = $_GET['url'];
    
    // 漏洞：直接使用用户输入的URL，没有过滤
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟随重定向
        
        $result = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error = '请求失败：' . curl_error($ch);
        }
        
        curl_close($ch);
    } catch (Exception $e) {
        $error = '请求异常：' . $e->getMessage();
    }
}

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🌐 基础SSRF漏洞</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示基础SSRF漏洞。<br>
                服务器直接使用用户输入的URL进行请求，没有任何过滤，攻击者可以构造恶意URL访问内部资源。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_GET["url"])) {
    $url = $_GET["url"];
    
    // 漏洞：直接使用用户输入的URL，没有过滤
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    $result = curl_exec($ch);
    
    curl_close($ch);
}
</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">本场景演示基础SSRF漏洞，尝试以下攻击：</p>

                    <ol>
                        <li>访问外部网站：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/01_basic_ssrf.php?url=https://www.example.com</code></li>
                        </ul>
                        <li>访问内部资源：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/01_basic_ssrf.php?url=http://127.0.0.1</code></li>
                            <li><code>http://localhost/zhaosec/ssrf/01_basic_ssrf.php?url=http://localhost</code></li>
                        </ul>
                        <li>访问内部服务：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/01_basic_ssrf.php?url=http://127.0.0.1:3306</code> - MySQL服务</li>
                            <li><code>http://localhost/zhaosec/ssrf/01_basic_ssrf.php?url=http://127.0.0.1:6379</code> - Redis服务</li>
                        </ul>
                    </ol>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 实际测试</h6>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">URL</span>
                            <input type="text" name="url" class="form-control" placeholder="例如：https://www.example.com">
                            <button type="submit" class="btn btn-danger">发起请求</button>
                        </div>
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
                        <strong>请求结果：</strong>
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
                        <li><strong>URL白名单：</strong>只允许访问特定的域名或IP</li>
                        <li><strong>禁止访问内网：</strong>过滤127.0.0.1、localhost等内网地址</li>
                        <li><strong>协议限制：</strong>只允许http、https协议，禁止file://、gopher://等危险协议</li>
                        <li><strong>超时设置：</strong>设置合理的请求超时时间，防止DoS攻击</li>
                        <li><strong>错误处理：</strong>不向用户泄露详细的错误信息</li>
                        <li><strong>使用代理：</strong>通过安全的代理服务器发起请求</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>';

// 包含模板
include '../template/module_template.php';
?>