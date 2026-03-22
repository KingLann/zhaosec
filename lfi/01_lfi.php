<?php
// 本地文件包含漏洞
$module_name = '本地文件包含';
$module_icon = '📄';
$module_desc = '本地文件包含(LFI)允许攻击者包含服务器上的本地文件。';

// 漏洞代码
if (isset($_GET['file'])) {
    $file = $_GET['file'];
    // 漏洞：直接包含用户输入的文件路径，没有过滤
    include $file;
}

// 页面内容
$content = '    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">📄 本地文件包含 (LFI) 漏洞</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <strong>💡 漏洞说明：</strong><br>
                本地文件包含漏洞允许攻击者包含并执行服务器上的本地文件。<br>
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
                    <p class="mb-3">尝试包含以下文件：</p>
                    <ul class="list-group mb-3">
                        <li class="list-group-item">
                            <a href="?file=test.txt" class="text-danger">包含 test.txt 文件</a>
                        </li>
                        <li class="list-group-item">
                            <a href="?file=../../../../../windows/win.ini" class="text-danger">包含 Windows 系统文件 (win.ini)</a>
                        </li>
                        <li class="list-group-item">
                            <a href="?file=../../../../../etc/passwd" class="text-danger">包含 Linux 系统文件 (passwd)</a>
                        </li>
                    </ul>
                    
                    <div class="alert alert-info">
                        <strong>提示：</strong>可以使用 <code>../</code> 进行目录穿越，尝试包含其他文件。
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
                        <li>禁用 <code>allow_url_include</code> 选项</li>
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