<?php
// PHP伪协议利用
$module_name = 'PHP伪协议利用';
$module_icon = '🔧';
$module_desc = '利用PHP伪协议进行文件包含攻击。';

// 漏洞代码
if (isset($_GET['file'])) {
    $file = $_GET['file'];
    // 漏洞：直接包含用户输入的文件路径，没有过滤
    include $file;
}

// 页面内容
$content = '    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">🔧 PHP伪协议利用</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <strong>💡 漏洞说明：</strong><br>
                PHP伪协议允许攻击者利用PHP的内置协议进行文件包含攻击。<br>
                本场景中，系统直接包含用户输入的文件路径，没有进行任何过滤。
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
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">尝试使用以下PHP伪协议：</p>
                    <ul class="list-group mb-3">
                        <li class="list-group-item">
                            <strong>php://filter</strong> - 读取文件内容
                            <pre class="bg-light p-2 mt-2"><code>?file=php://filter/convert.base64-encode/resource=test.txt</code></pre>
                        </li>
                        <li class="list-group-item">
                            <strong>php://input</strong> - 执行POST数据中的PHP代码
                            <pre class="bg-light p-2 mt-2"><code>POST请求体：&lt;?php phpinfo(); ?&gt;</code></pre>
                        </li>
                        <li class="list-group-item">
                            <strong>data://</strong> - 执行内联数据中的PHP代码
                            <pre class="bg-light p-2 mt-2"><code>?file=data://text/plain,<?php phpinfo(); ?></code></pre>
                        </li>
                    </ul>
                    
                    <div class="alert alert-info">
                        <strong>提示：</strong>使用php://filter可以读取文件内容，使用base64编码可以避免乱码。
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
                        <li>禁用危险的PHP伪协议</li>
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