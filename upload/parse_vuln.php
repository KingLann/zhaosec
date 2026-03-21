<?php
// 解析漏洞学习资料
$module_name = '06. 文件解析漏洞';
$module_icon = '🔍';
$module_desc = '文件解析漏洞是由于服务器配置不当导致的文件类型解析错误，可能被攻击者利用上传恶意文件。';

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🔍 文件解析漏洞</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>💡 说明：</strong><br>
                本场景为学习资料，不包含实际可利用的漏洞。<br>
                以下内容介绍常见的文件解析漏洞类型、原理和防御方法。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>📋 常见解析漏洞类型</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>漏洞类型</th>
                                    <th>服务器类型</th>
                                    <th>漏洞原理</th>
                                    <th>影响</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Apache解析漏洞</td>
                                    <td>Apache</td>
                                    <td>Apache会从右到左解析文件扩展名，直到找到可识别的MIME类型</td>
                                    <td>高</td>
                                </tr>
                                <tr>
                                    <td>IIS解析漏洞</td>
                                    <td>IIS 6.0</td>
                                    <td>分号截断、目录名解析、.asp目录等多种解析问题</td>
                                    <td>高</td>
                                </tr>
                                <tr>
                                    <td>Nginx解析漏洞</td>
                                    <td>Nginx</td>
                                    <td>文件名后加\0或\x20等特殊字符，或使用路径欺骗</td>
                                    <td>中</td>
                                </tr>
                                <tr>
                                    <td>PHP CGI解析漏洞</td>
                                    <td>PHP</td>
                                    <td>在某些配置下，PHP会解析包含.php的文件</td>
                                    <td>高</td>
                                </tr>
                                <tr>
                                    <td>文件类型混淆</td>
                                    <td>通用</td>
                                    <td>利用文件头欺骗，如在PHP文件前添加图片头</td>
                                    <td>中</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 漏洞原理详解</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">1. Apache解析漏洞</h5>
                    <p>Apache会从右到左解析文件扩展名，直到找到可识别的MIME类型。例如：</p>
                    <ul>
                        <li><code>webshell.php.jpg</code> - 如果Apache不识别.jpg后缀，会继续向左解析，最终解析为.php文件</li>
                        <li><code>webshell.php.xxx</code> - 同样原理，只要最后一个扩展名不被识别，就会继续向左解析</li>
                    </ul>

                    <h5 class="mb-3 mt-4">2. IIS 6.0解析漏洞</h5>
                    <ul>
                        <li><strong>分号截断：</strong> <code>webshell.asp;jpg</code> - IIS会截断分号后的内容，解析为.asp文件</li>
                        <li><strong>目录名解析：</strong> <code>webshell.asp/1.jpg</code> - 目录名包含.asp，整个目录下的文件都会被解析为ASP文件</li>
                        <li><strong>.asp目录：</strong> <code>webshell.asp;jpg</code> - 类似分号截断</li>
                    </ul>

                    <h5 class="mb-3 mt-4">3. Nginx解析漏洞</h5>
                    <ul>
                        <li><strong>特殊字符：</strong> <code>webshell.php\0.jpg</code> - Nginx会忽略\0后的内容，解析为.php文件</li>
                        <li><strong>路径欺骗：</strong> <code>webshell.jpg/.php</code> - 利用Nginx的路径处理逻辑</li>
                    </ul>

                    <h5 class="mb-3 mt-4">4. PHP CGI解析漏洞</h5>
                    <p>在某些配置下，PHP会解析包含.php的文件，例如：</p>
                    <ul>
                        <li><code>webshell.jpg.php</code> - 会被解析为PHP文件</li>
                        <li><code>webshell.php5</code> - 如果配置了.php5也作为PHP文件扩展名</li>
                    </ul>

                    <h5 class="mb-3 mt-4">5. 文件类型混淆</h5>
                    <p>攻击者在PHP文件前添加图片头等内容，欺骗文件类型检测：</p>
                    <pre class="bg-dark text-light p-3 rounded"><code>\xff\xd8\xff\xe0\x00\x10JFIF\x00\x01\x01\x00\x00\x01\x00\x01\x00\x00\xff\xdb\x00C\x00\x02\x01\x01\x01\x01\x02\x01\x01\x01\x02\x02\x02\x02\x02\x04\x03\x02\x02\x02\x02\x05\x04\x04\x03\x04\x06\x06\x08\x09\x08\x06\x06\x08\x08?&gt;
&lt;?php system($_GET['cmd']); ?&gt;</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔧 漏洞利用示例</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>漏洞类型</th>
                                    <th>利用方法</th>
                                    <th>示例文件</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Apache解析漏洞</td>
                                    <td>上传包含.php的多扩展名文件</td>
                                    <td><code>webshell.php.jpg</code></td>
                                </tr>
                                <tr>
                                    <td>IIS 6.0解析漏洞</td>
                                    <td>使用分号或目录名欺骗</td>
                                    <td><code>webshell.asp;jpg</code></td>
                                </tr>
                                <tr>
                                    <td>Nginx解析漏洞</td>
                                    <td>添加特殊字符或路径欺骗</td>
                                    <td><code>webshell.php\0.jpg</code></td>
                                </tr>
                                <tr>
                                    <td>文件类型混淆</td>
                                    <td>在PHP文件前添加图片头</td>
                                    <td>带图片头的PHP文件</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 防御建议</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">1. 服务器配置</h5>
                    <ul>
                        <li>升级服务器到最新版本，修复已知解析漏洞</li>
                        <li>正确配置MIME类型，避免使用默认配置</li>
                        <li>禁用不必要的模块和功能</li>
                    </ul>

                    <h5 class="mb-3 mt-4">2. 文件上传验证</h5>
                    <ul>
                        <li>使用白名单验证文件扩展名，只允许特定类型的文件</li>
                        <li>验证文件MIME类型，确保与扩展名匹配</li>
                        <li>检查文件内容，确保文件类型与声明一致</li>
                        <li>使用随机文件名，避免路径遍历和文件名欺骗</li>
                    </ul>

                    <h5 class="mb-3 mt-4">3. 安全存储</h5>
                    <ul>
                        <li>将上传文件存储在Web根目录外</li>
                        <li>设置上传目录为不可执行</li>
                        <li>使用CDN或对象存储服务存储静态文件</li>
                    </ul>

                    <h5 class="mb-3 mt-4">4. 其他措施</h5>
                    <ul>
                        <li>部署Web应用防火墙(WAF)，过滤恶意文件</li>
                        <li>实施文件内容扫描，检测恶意代码</li>
                        <li>定期检查服务器配置和上传文件</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>';

// 包含模板
include '../template/module_template.php';
?>