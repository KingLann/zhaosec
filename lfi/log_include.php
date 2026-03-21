<?php
// 日志包含漏洞
$module_name = '日志包含';
$module_icon = '📋';
$module_desc = '通过包含服务器日志文件来获取Shell。';

// 漏洞代码
if (isset($_GET['file'])) {
    $file = $_GET['file'];
    // 漏洞：直接包含用户输入的文件路径，没有过滤
    include $file;
}

// 页面内容
$content = '    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">📋 日志包含漏洞</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <strong>💡 漏洞说明：</strong><br>
                日志包含漏洞允许攻击者通过包含服务器日志文件来执行PHP代码。<br>
                当攻击者向服务器发送包含PHP代码的请求时，这些代码会被记录到日志文件中，然后通过文件包含漏洞执行。
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_GET[\'file\'])) {
    $file = $_GET[\'file\'];
    // 漏洞：直接包含用户输入的文件路径，没有过滤
    include $file;
}</code></pre>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击步骤</h6>
                </div>
                <div class="card-body">
                    <ol class="mb-3">
                        <li><strong>步骤1：</strong>向服务器发送包含PHP代码的请求</li>
                        <li><strong>步骤2：</strong>找到服务器日志文件的位置</li>
                        <li><strong>步骤3：</strong>通过文件包含漏洞包含日志文件</li>
                        <li><strong>步骤4：</strong>执行日志中的PHP代码</li>
                    </ol>
                    
                    <div class="alert alert-info">
                        <strong>示例请求：</strong><br>
                        <code>GET /zhaosec/lfi/log_include.php?<?php phpinfo(); ?> HTTP/1.1</code><br>
                        <code>Host: localhost</code>
                    </div>
                    
                    <div class="alert alert-info">
                        <strong>常见日志文件路径：</strong><br>
                        <ul>
                            <li>Apache: <code>/var/log/apache2/access.log</code></li>
                            <li>Nginx: <code>/var/log/nginx/access.log</code></li>
                            <li>Windows IIS: <code>C:\\inetpub\\logs\\LogFiles\\W3SVC1\\u_exYYYYMMDD.log</code></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 防御建议</h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li>对用户输入进行严格过滤和验证</li>
                        <li>使用白名单机制，只允许包含指定的文件</li>
                        <li>设置日志文件的权限，防止被web服务器读取</li>
                        <li>定期清理日志文件</li>
                        <li>使用绝对路径包含文件</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>';

// 包含模板
include '../template/module_template.php';
?>