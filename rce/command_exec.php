<?php
// 命令执行漏洞
$module_name = '命令执行漏洞';
$module_icon = '⚡';
$module_desc = '通过系统命令执行函数导致的命令注入漏洞。';

// 漏洞代码
$output = '';
if (isset($_GET['ip'])) {
    $ip = $_GET['ip'];
    // 漏洞：直接使用用户输入拼接命令，没有过滤
    $output = shell_exec("ping -n 4 " . $ip);
}

// 页面内容
$content = <<<HTML
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">⚡ 命令执行漏洞</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                命令执行漏洞允许攻击者在服务器上执行任意系统命令。<br>
                当应用程序将用户输入直接拼接到系统命令中执行时，如果没有进行充分的过滤和验证，就可能导致命令注入。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset(\$_GET['ip'])) {
    \$ip = \$_GET['ip'];
    // 漏洞：直接使用用户输入拼接命令，没有过滤
    \$output = shell_exec("ping -n 4 " . \$ip);
}</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">本场景模拟一个ping功能，输入IP地址进行ping测试。尝试以下攻击payload：</p>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>攻击类型</th>
                                    <th>Payload示例</th>
                                    <th>说明</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>命令拼接</td>
                                    <td><code>127.0.0.1 && whoami</code></td>
                                    <td>执行ping后再执行whoami</td>
                                </tr>
                                <tr>
                                    <td>命令拼接</td>
                                    <td><code>127.0.0.1 & dir</code></td>
                                    <td>Windows下执行dir命令</td>
                                </tr>
                                <tr>
                                    <td>命令拼接</td>
                                    <td><code>127.0.0.1 | net user</code></td>
                                    <td>管道符执行命令</td>
                                </tr>
                                <tr>
                                    <td>命令拼接</td>
                                    <td><code>127.0.0.1 || ipconfig</code></td>
                                    <td>或运算执行命令</td>
                                </tr>
                                <tr>
                                    <td>分号分隔</td>
                                    <td><code>127.0.0.1; cat /etc/passwd</code></td>
                                    <td>Linux下分号分隔命令</td>
                                </tr>
                                <tr>
                                    <td>反引号执行</td>
                                    <td><code>127.0.0.1 \`whoami\`</code></td>
                                    <td>反引号内命令会被执行</td>
                                </tr>
                                <tr>
                                    <td>命令替换</td>
                                    <td><code>127.0.0.1 \$(cat /flag)</code></td>
                                    <td>\$()命令替换</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-warning mt-3">
                        <strong>⚠️ 常用连接符：</strong>
                        <ul class="mb-0">
                            <li><code>;</code> - 分号，顺序执行多个命令（Linux）</li>
                            <li><code>&</code> - 后台执行命令</li>
                            <li><code>&&</code> - 逻辑与，前命令成功才执行后命令</li>
                            <li><code>|</code> - 管道符，将前命令输出作为后命令输入</li>
                            <li><code>||</code> - 逻辑或，前命令失败才执行后命令</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🧰 常见命令执行函数</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>函数</th>
                                    <th>说明</th>
                                    <th>示例</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>system()</code></td>
                                    <td>执行命令并输出结果</td>
                                    <td><code>system("ls -la");</code></td>
                                </tr>
                                <tr>
                                    <td><code>exec()</code></td>
                                    <td>执行命令，返回最后一行</td>
                                    <td><code>exec("whoami", \$output);</code></td>
                                </tr>
                                <tr>
                                    <td><code>shell_exec()</code></td>
                                    <td>执行命令，返回完整输出</td>
                                    <td><code>\$result = shell_exec("dir");</code></td>
                                </tr>
                                <tr>
                                    <td><code>passthru()</code></td>
                                    <td>执行命令，直接输出结果</td>
                                    <td><code>passthru("ping 127.0.0.1");</code></td>
                                </tr>
                                <tr>
                                    <td><code>proc_open()</code></td>
                                    <td>执行命令，可控制输入输出</td>
                                    <td>复杂进程控制</td>
                                </tr>
                                <tr>
                                    <td><code>popen()</code></td>
                                    <td>打开进程文件指针</td>
                                    <td><code>\$fp = popen("ls", "r");</code></td>
                                </tr>
                                <tr>
                                    <td><code>反引号 ``</code></td>
                                    <td>shell_exec的简写形式</td>
                                    <td><code>\$result = \`whoami\`;</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 实际测试</h6>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">IP地址</span>
                            <input type="text" name="ip" class="form-control" placeholder="输入IP地址，例如：127.0.0.1">
                            <button type="submit" class="btn btn-primary">Ping测试</button>
                        </div>
                    </form>

                    <?php if (\$output): ?>
                    <div class="alert alert-secondary">
                        <strong>命令输出：</strong>
                        <pre class="mb-0 mt-2"><code><?php echo htmlspecialchars(\$output); ?></code></pre>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 防御建议</h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li><strong>避免使用危险函数：</strong>尽量不要使用system、exec等危险函数</li>
                        <li><strong>输入验证：</strong>对用户输入进行严格的白名单验证</li>
                        <li><strong>参数化：</strong>使用参数化方式传递命令参数</li>
                        <li><strong>转义处理：</strong>使用escapeshellarg()或escapeshellcmd()转义参数</li>
                        <li><strong>禁用危险字符：</strong>过滤|;\&\$\`\(\)\{\}\[\]等危险字符</li>
                        <li><strong>最小权限：</strong>web服务以最低权限运行</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
HTML;

// 包含模板
include '../template/module_template.php';
?>