<?php
// 命令执行漏洞
$module_name = '命令执行漏洞';
$module_icon = '⚡';
$module_desc = '通过系统命令执行函数导致的命令注入漏洞。';

// 漏洞代码
$output = '';
$error = '';
$os = strtolower(PHP_OS);
$is_windows = strpos($os, 'win') !== false;

if (isset($_GET['ip'])) {
    $ip = $_GET['ip'];
    // 漏洞：直接使用用户输入拼接命令，没有过滤
    if (function_exists('exec')) {
        // 直接执行命令并捕获输出
        if ($is_windows) {
            // Windows系统
            $ping_cmd = 'ping -n 4 ' . $ip;
        } else {
            // Linux系统
            $ping_cmd = 'ping -c 4 ' . $ip;
        }
        
        // 使用exec函数执行命令
        $exec_output = array();
        $return_var = 0;
        exec($ping_cmd, $exec_output, $return_var);
        
        if ($return_var === 0 && !empty($exec_output)) {
            // 命令执行成功
            $ping_result = implode("\n", $exec_output);
            // 尝试不同的编码转换
            if (function_exists('mb_detect_encoding')) {
                $encoding = mb_detect_encoding($ping_result, array('UTF-8', 'GBK', 'GB2312'));
                if ($encoding && $encoding != 'UTF-8') {
                    $output = mb_convert_encoding($ping_result, 'UTF-8', $encoding);
                } else {
                    $output = $ping_result;
                }
            } else {
                // 尝试使用iconv
                try {
                    $output = iconv('GBK', 'UTF-8//IGNORE', $ping_result);
                } catch (Exception $e) {
                    $output = $ping_result;
                }
            }
        } else {
            // 如果ping失败，测试其他命令
            $output = "=== 命令执行测试 ===\n";
            $output .= "操作系统: " . PHP_OS . "\n";
            $output .= "IP地址: " . $ip . "\n";
            $output .= "返回码: " . $return_var . "\n";
            
            // 测试echo命令
            exec('echo "Hello from Zhaosec"', $echo_output);
            $output .= "Echo命令: " . implode("\n", $echo_output) . "\n";
            
            // 测试whoami命令
            exec('whoami', $whoami_output);
            $output .= "当前用户: " . implode("\n", $whoami_output) . "\n";
            
            $output .= "Ping命令执行失败，可能是权限限制或网络问题\n";
        }
    } else {
        $error = 'exec函数不可用，可能已被禁用';
    }
}

// 页面内容
$content = '<div class="card">
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
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_GET[\'ip\'])) {
    $ip = $_GET[\'ip\'];
    // 漏洞：直接使用用户输入拼接命令，没有过滤
    $output = shell_exec("ping -n 4 " . $ip);
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
                                    <td><code>exec("whoami", $output);</code></td>
                                </tr>
                                <tr>
                                    <td><code>shell_exec()</code></td>
                                    <td>执行命令，返回完整输出</td>
                                    <td><code>$result = shell_exec("dir");</code></td>
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
                                    <td><code>$fp = popen("ls", "r");</code></td>
                                </tr>
                                <tr>
                                    <td><code>反引号 ``</code></td>
                                    <td>shell_exec的简写形式</td>
                                    <td><code>$result = \`whoami\`;</code></td>
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
                    <div class="mb-4">
                        <h6 class="mb-2">Ping测试</h6>
                        <form method="GET" class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text">IP地址</span>
                                <input type="text" name="ip" class="form-control" placeholder="输入IP地址，例如：127.0.0.1">
                                <button type="submit" class="btn btn-primary">Ping测试</button>
                            </div>
                        </form>
                    </div>

                    ';

if ($error) {
    $content .= '<div class="alert alert-danger">
                        <strong>错误信息：</strong>
                        <p>' . htmlspecialchars($error) . '</p>
                    </div>';
} elseif ($output) {
    $content .= '<div class="alert alert-secondary">
                        <strong>命令输出：</strong>
                        <pre class="mb-0 mt-2"><code>' . htmlspecialchars($output) . '</code></pre>
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
    </div>';

// 包含模板
include '../template/module_template.php';
?>