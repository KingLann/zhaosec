<?php
// 命令执行绕过演示
$module_name = '命令执行绕过演示';
$module_icon = '🔐';
$module_desc = '演示不同难度的命令执行绕过技术，包括空格过滤、关键字过滤、命令连接符过滤等。';

// 漏洞代码
$output = '';
$error = '';
$level = isset($_GET['level']) ? intval($_GET['level']) : 1;
$cmd = isset($_GET['cmd']) ? $_GET['cmd'] : '';

if (!empty($cmd)) {
    switch ($level) {
        case 1:
            // 关卡1：基础绕过 - 空格过滤
            $cmd = str_replace(' ', '', $cmd);
            if (preg_match('/whoami|dir|ls|cat/', $cmd)) {
                $error = '关卡1：关键字被过滤！';
            } else {
                // 执行命令
                if (function_exists('exec')) {
                    $exec_output = array();
                    $return_var = 0;
                    exec($cmd, $exec_output, $return_var);
                    if ($return_var === 0 && !empty($exec_output)) {
                        $output = implode("\n", $exec_output);
                    } else {
                        $error = '命令执行失败，返回码: ' . $return_var;
                    }
                } else {
                    $error = 'exec函数不可用';
                }
            }
            break;
        
        case 2:
            // 关卡2：中级绕过 - 关键字过滤 + 空格过滤
            $cmd = str_replace(' ', '', $cmd);
            if (preg_match('/whoami|dir|ls|cat|system|exec|shell_exec/', $cmd)) {
                $error = '关卡2：关键字被过滤！';
            } else {
                // 执行命令
                if (function_exists('exec')) {
                    $exec_output = array();
                    $return_var = 0;
                    exec($cmd, $exec_output, $return_var);
                    if ($return_var === 0 && !empty($exec_output)) {
                        $output = implode("\n", $exec_output);
                    } else {
                        $error = '命令执行失败，返回码: ' . $return_var;
                    }
                } else {
                    $error = 'exec函数不可用';
                }
            }
            break;
        
        case 3:
            // 关卡3：高级绕过 - 关键字过滤 + 空格过滤 + 命令连接符过滤
            $cmd = str_replace(' ', '', $cmd);
            if (preg_match('/whoami|dir|ls|cat|system|exec|shell_exec|&|\|\|', $cmd)) {
                $error = '关卡3：关键字或连接符被过滤！';
            } else {
                // 执行命令
                if (function_exists('exec')) {
                    $exec_output = array();
                    $return_var = 0;
                    exec($cmd, $exec_output, $return_var);
                    if ($return_var === 0 && !empty($exec_output)) {
                        $output = implode("\n", $exec_output);
                    } else {
                        $error = '命令执行失败，返回码: ' . $return_var;
                    }
                } else {
                    $error = 'exec函数不可用';
                }
            }
            break;
        
        case 4:
            // 关卡4：专家级绕过 - 无字母数字绕过
            if (preg_match('/[a-zA-Z0-9]/', $cmd)) {
                $error = '关卡4：不允许使用字母和数字！';
            } else {
                // 执行命令
                if (function_exists('exec')) {
                    $exec_output = array();
                    $return_var = 0;
                    exec($cmd, $exec_output, $return_var);
                    if ($return_var === 0 && !empty($exec_output)) {
                        $output = implode("\n", $exec_output);
                    } else {
                        $error = '命令执行失败，返回码: ' . $return_var;
                    }
                } else {
                    $error = 'exec函数不可用';
                }
            }
            break;
        
        default:
            $error = '无效的关卡';
            break;
    }
}

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🔐 命令执行绕过演示</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                命令执行绕过是指攻击者通过各种技术手段绕过应用程序的过滤机制，执行恶意命令。<br>
                本演示包含多个难度关卡，展示不同类型的绕过技术。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 关卡选择</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="bypass_demo.php?level=1" class="btn btn-outline-primary btn-block">关卡1：基础绕过</a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="bypass_demo.php?level=2" class="btn btn-outline-primary btn-block">关卡2：中级绕过</a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="bypass_demo.php?level=3" class="btn btn-outline-primary btn-block">关卡3：高级绕过</a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="bypass_demo.php?level=4" class="btn btn-outline-danger btn-block">关卡4：专家级绕过</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>📋 当前关卡：';

// 根据当前关卡添加说明
switch ($level) {
    case 1:
        $content .= '关卡1：基础绕过 - 空格过滤';
        break;
    case 2:
        $content .= '关卡2：中级绕过 - 关键字过滤 + 空格过滤';
        break;
    case 3:
        $content .= '关卡3：高级绕过 - 关键字过滤 + 空格过滤 + 命令连接符过滤';
        break;
    case 4:
        $content .= '关卡4：专家级绕过 - 无字母数字绕过';
        break;
    default:
        $content .= '无效关卡';
        break;
}

$content .= '</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="mb-2">绕过技巧：</h6>
                        <ul>';

// 根据当前关卡添加绕过技巧
switch ($level) {
    case 1:
        $content .= '<li>使用${IFS}或$IFS代替空格</li>
                        <li>使用%09（制表符）代替空格</li>
                        <li>使用反引号``执行命令</li>';
        break;
    case 2:
        $content .= '<li>使用命令替换：\`\`</li>
                        <li>使用环境变量：$PATH</li>
                        <li>使用通配符：*、?</li>
                        <li>使用编码：Base64、URL编码</li>';
        break;
    case 3:
        $content .= '<li>使用分号;分隔命令</li>
                        <li>使用&&、||等连接符的替代方案</li>
                        <li>使用命令替换和嵌套执行</li>
                        <li>使用特殊字符和转义</li>';
        break;
    case 4:
        $content .= '<li>使用ASCII码：$((65+66))</li>
                        <li>使用异或运算：$((1^2))</li>
                        <li>使用取反运算：~</li>
                        <li>使用环境变量和特殊字符组合</li>';
        break;
}

$content .= '</ul>
                    </div>

                    <div class="mb-3">
                        <h6 class="mb-2">Payload示例：</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Payload</th>
                                        <th>说明</th>
                                    </tr>
                                </thead>
                                <tbody>';

// 根据当前关卡添加payload示例
switch ($level) {
    case 1:
        $content .= '<tr>
                                        <td><code>echo${IFS}hello</code></td>
                                        <td>使用${IFS}代替空格</td>
                                    </tr>
                                    <tr>
                                        <td><code>echo%09world</code></td>
                                        <td>使用制表符代替空格</td>
                                    </tr>
                                    <tr>
                                        <td><code>\`echo hello\`</code></td>
                                        <td>使用反引号执行命令</td>
                                    </tr>';
        break;
    case 2:
        $content .= '<tr>
                                        <td><code>\`echo\`${IFS}hello</code></td>
                                        <td>使用反引号和IFS</td>
                                    </tr>
                                    <tr>
                                        <td><code>$\{PATH\:0:1\}cho${IFS}hello</code></td>
                                        <td>使用环境变量</td>
                                    </tr>
                                    <tr>
                                        <td><code>\`\`e${PATH:0:1}ho${IFS}hello</code></td>
                                        <td>组合使用多种技巧</td>
                                    </tr>';
        break;
    case 3:
        $content .= '<tr>
                                        <td><code>echo${IFS}hello;echo${IFS}world</code></td>
                                        <td>使用分号分隔命令</td>
                                    </tr>
                                    <tr>
                                        <td><code>echo${IFS}hello&&echo${IFS}world</code></td>
                                        <td>使用&&连接命令</td>
                                    </tr>
                                    <tr>
                                        <td><code>\`echo\`${IFS}hello||echo${IFS}world</code></td>
                                        <td>使用||连接命令</td>
                                    </tr>';
        break;
    case 4:
        $content .= '<tr>
                                        <td><code>$((65+66))</code></td>
                                        <td>使用ASCII码</td>
                                    </tr>
                                    <tr>
                                        <td><code>$((~$(~$((0)))))</code></td>
                                        <td>使用取反运算</td>
                                    </tr>
                                    <tr>
                                        <td><code>${!#}</code></td>
                                        <td>使用特殊变量</td>
                                    </tr>';
        break;
}

$content .= '</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 实际测试</h6>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <input type="hidden" name="level" value="' . $level . '">
                        <div class="input-group">
                            <span class="input-group-text">命令</span>
                            <input type="text" name="cmd" class="form-control" value="' . htmlspecialchars($cmd) . '" placeholder="输入绕过命令">
                            <button type="submit" class="btn btn-danger">执行命令</button>
                        </div>
                    </form>

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
                        <li><strong>禁用危险函数：</strong>在php.ini中禁用system、exec等危险函数</li>
                        <li><strong>输入验证：</strong>对用户输入进行严格的白名单验证</li>
                        <li><strong>最小权限：</strong>web服务以最低权限运行</li>
                        <li><strong>使用安全函数：</strong>使用escapeshellarg()和escapeshellcmd()转义参数</li>
                        <li><strong>避免拼接命令：</strong>尽量使用参数化方式执行命令</li>
                        <li><strong>WAF防护：</strong>部署Web应用防火墙，过滤恶意输入</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>';

// 包含模板
include '../template/module_template.php';
?>