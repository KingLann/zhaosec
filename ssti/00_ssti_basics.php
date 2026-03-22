<?php
$module_name = 'SSTI基础与原理';
$module_icon = '📚';
$module_desc = '讲解服务端模板注入(SSTI)漏洞的基础知识和原理。';

$content = <<<'EOT'
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">📚 SSTI基础与原理</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>💡 学习目标：</strong><br>
            了解服务端模板注入(SSTI)漏洞的基础知识和原理，掌握其攻击方式和防御方法。
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>📄 什么是SSTI？</h6>
            </div>
            <div class="card-body">
                <p>SSTI（Server-Side Template Injection，服务端模板注入）是一种攻击技术，攻击者利用模板引擎的语法注入恶意payload，从而在服务器端执行任意代码。</p>

                <h5 class="mb-3 mt-4">SSTI的危害</h5>
                <ul>
                    <li>远程代码执行（RCE）</li>
                    <li>读取敏感文件</li>
                    <li>获取服务器权限</li>
                    <li>数据泄露</li>
                </ul>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🔍 SSTI攻击原理</h6>
            </div>
            <div class="card-body">
                <div class="bg-light p-3 rounded border mb-3">
                    <script src="../assets/js/mermaid.min.js"></script>
                    <div class="mermaid">
                        flowchart TD
                            A[用户输入] --> B{是否过滤模板语法?}
                            B -->|否| C[输入进入模板渲染]
                            B -->|是| D[安全处理]
                            C --> E[模板引擎解析]
                            E --> F{包含恶意payload?}
                            F -->|是| G[执行任意代码]
                            F -->|否| H[正常渲染输出]
                            G --> I[服务器被控制]
                    </div>
                </div>

                <h5 class="mb-3">常见模板引擎</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>语言</th>
                            <th>模板引擎</th>
                            <th>典型语法</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Python</td>
                            <td>Jinja2, Mako, Tornado</td>
                            <td><code>{{ }}</code>, <code>{% %}</code></td>
                        </tr>
                        <tr>
                            <td>PHP</td>
                            <td>Twig, Smarty, Blade</td>
                            <td><code>{{ }}</code>, <code>{ }</code></td>
                        </tr>
                        <tr>
                            <td>Java</td>
                            <td>Freemarker, Velocity, Thymeleaf</td>
                            <td><code>${ }</code>, <code>#</code></td>
                        </tr>
                        <tr>
                            <td>Node.js</td>
                            <td>EJS, Pug, Handlebars</td>
                            <td><code>&lt;%= %&gt;</code>, <code>#{ }</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🎯 SSTI检测方法</h6>
            </div>
            <div class="card-body">
                <h5 class="mb-3">常见检测Payload</h5>
                <pre class="bg-dark text-light p-3 rounded"><code># 数学运算检测
{{7*7}}       # 期望输出: 49
${7*7}        # 期望输出: 49
&lt;%= 7*7 %&gt;   # 期望输出: 49

# 字符串拼接检测
{{'test'}}    # 期望输出: test
${"test"}     # 期望输出: test

# 对象探测
{{self}}      # Python Jinja2
{{request}}   # Flask
${T(java.lang.Runtime)}  # Java Freemarker</code></pre>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>🛡️ 防御建议</h6>
            </div>
            <div class="card-body">
                <ol>
                    <li><strong>输入验证：</strong>对用户输入进行严格过滤，禁止模板语法</li>
                    <li><strong>沙箱环境：</strong>在受限的沙箱环境中执行模板渲染</li>
                    <li><strong>白名单机制：</strong>只允许预定义的变量和函数</li>
                    <li><strong>避免动态渲染：</strong>不要将用户输入直接作为模板内容</li>
                    <li><strong>使用安全配置：</strong>禁用危险函数和特性</li>
                </ol>
            </div>
        </div>
    </div>
</div>
EOT;

include '../template/module_template.php';
?>
