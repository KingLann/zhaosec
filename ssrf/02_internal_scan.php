<?php
// 内网探测SSRF漏洞
$module_name = '内网探测';
$module_icon = '🔍';
$module_desc = '通过SSRF漏洞探测内网服务和端口，发现内部网络结构。';

// 漏洞代码
$result = '';
$error = '';

if (isset($_GET['target'])) {
    $target = $_GET['target'];
    
    // 漏洞：直接使用用户输入的目标，没有过滤
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟随重定向
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 禁用SSL证书验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 禁用SSL主机验证
        
        $start_time = microtime(true);
        $response = curl_exec($ch);
        $end_time = microtime(true);
        $response_time = round(($end_time - $start_time) * 1000, 2);
        
        if (curl_errno($ch)) {
            $error_code = curl_errno($ch);
            if ($error_code == CURLE_OPERATION_TIMEOUTED) {
                $result = '目标 ' . htmlspecialchars($target) . ' 可能开放（连接超时），响应时间：' . $response_time . 'ms';
            } else {
                $error = '请求失败：' . curl_error($ch);
            }
        } else {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $result = '目标 ' . htmlspecialchars($target) . ' 开放，HTTP状态码：' . $http_code . '，响应时间：' . $response_time . 'ms';
            if (strlen($response) > 500) {
                $response = substr($response, 0, 500) . '...（截断）';
            }
            $result .= '\n\n响应内容：\n' . htmlspecialchars($response);
        }
        
        curl_close($ch);
    } catch (Exception $e) {
        $error = '请求异常：' . $e->getMessage();
    }
}

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🔍 内网探测</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示内网探测SSRF漏洞。<br>
                攻击者可以通过SSRF漏洞探测内网服务和端口，发现内部网络结构和潜在的安全隐患。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_GET["target"])) {
    $target = $_GET["target"];
    
    // 漏洞：直接使用用户输入的目标，没有过滤
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $target);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        // 处理错误
    } else {
        // 处理响应
    }
    
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
                    <p class="mb-3">本场景演示内网探测SSRF漏洞，尝试以下攻击：</p>

                    <ol>
                        <li>探测常用端口：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/internal_scan.php?target=http://127.0.0.1:80</code> - Web服务</li>
                            <li><code>http://localhost/zhaosec/ssrf/internal_scan.php?target=http://127.0.0.1:3306</code> - MySQL</li>
                            <li><code>http://localhost/zhaosec/ssrf/internal_scan.php?target=http://127.0.0.1:6379</code> - Redis</li>
                            <li><code>http://localhost/zhaosec/ssrf/internal_scan.php?target=http://127.0.0.1:27017</code> - MongoDB</li>
                        </ul>
                        <li>探测内网网段：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/internal_scan.php?target=http://192.168.1.1</code> - 网关</li>
                            <li><code>http://localhost/zhaosec/ssrf/internal_scan.php?target=http://10.0.0.1</code> - 内网地址</li>
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
                            <span class="input-group-text">目标</span>
                            <input type="text" name="target" class="form-control" placeholder="例如：http://127.0.0.1:80">
                            <button type="submit" class="btn btn-danger">探测</button>
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
                        <strong>探测结果：</strong>
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
                        <li><strong>禁止访问内网：</strong>过滤127.0.0.1、localhost、内网网段等</li>
                        <li><strong>端口限制：</strong>只允许访问特定的端口</li>
                        <li><strong>超时设置：</strong>设置合理的超时时间，防止端口扫描</li>
                        <li><strong>日志记录：</strong>记录所有SSRF请求，便于审计和发现异常</li>
                        <li><strong>使用代理：</strong>通过安全的代理服务器发起请求</li>
                        <li><strong>网络隔离：</strong>实施网络分段，限制服务器的网络访问范围</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>';

// 包含模板
include '../template/module_template.php';
?>