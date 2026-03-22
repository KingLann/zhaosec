<?php
// CSRF基础知识和原理
$module_name = 'CSRF基础与原理';
$module_icon = '📚';
$module_desc = '讲解跨站请求伪造(CSRF)漏洞的基础知识和原理。';

// 页面内容
$content = <<<'EOT'
<div class="card">
        <div class="card-header">
            <h5 class="mb-0">📚 CSRF基础与原理</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>💡 学习目标：</strong><br>
                了解跨站请求伪造(CSRF)漏洞的基础知识和原理，掌握其攻击方式和防御方法。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>📄 什么是CSRF？</h6>
                </div>
                <div class="card-body">
                    <p>CSRF（Cross-Site Request Forgery，跨站请求伪造）是一种攻击，攻击者诱导已认证的用户在不知情的情况下执行非预期的操作。由于浏览器会自动携带用户的认证信息（如Cookie），攻击者可以利用这一点来伪造用户的请求。</p>

                    <h5 class="mb-3 mt-4">CSRF的危害</h5>
                    <ul>
                        <li>修改用户账户信息（密码、邮箱等）</li>
                        <li>进行未经授权的交易或转账</li>
                        <li>发布或删除内容</li>
                        <li>提升用户权限</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 CSRF攻击原理</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="mb-3">🔄 CSRF攻击流程</h5>
                        <div class="bg-light p-3 rounded border">
                            <script src="../assets/js/mermaid.min.js"></script>
                            <div class="mermaid">
                                sequenceDiagram
                                    participant User as 用户
                                    participant Bank as 银行网站
                                    participant Evil as 恶意网站
                                    
                                    User->>Bank: 登录银行网站
                                    Bank-->>User: 设置会话Cookie
                                    User->>Evil: 访问恶意网站
                                    Evil->>Bank: 伪造请求（携带用户Cookie）
                                    Bank-->>Evil: 执行操作
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3">CSRF攻击条件</h5>
                    <ol>
                        <li>用户已登录目标网站并持有有效的会话凭证</li>
                        <li>攻击者能够构造包含目标网站操作的恶意请求</li>
                        <li>目标网站没有验证请求的来源</li>
                    </ol>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 CSRF攻击类型</h6>
                </div>
                <div class="card-body">
                    <div class="accordion" id="csrfTypes">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    1. GET型CSRF
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#csrfTypes">
                                <div class="accordion-body">
                                    <p>利用GET请求执行操作，通常通过图片标签、链接等方式触发。</p>
                                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;!-- 恶意网页中的代码 --&gt;
&lt;img src="http://bank.com/transfer?to=attacker&amount=10000" width="0" height="0"&gt;</code></pre>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    2. POST型CSRF
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#csrfTypes">
                                <div class="accordion-body">
                                    <p>利用POST请求执行操作，通过自动提交的表单实现。</p>
                                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;!-- 恶意网页中的代码 --&gt;
&lt;form action="http://bank.com/transfer" method="POST" id="csrf-form"&gt;
    &lt;input type="hidden" name="to" value="attacker"&gt;
    &lt;input type="hidden" name="amount" value="10000"&gt;
&lt;/form&gt;
&lt;script&gt;document.getElementById(&#39;csrf-form&#39;).submit();&lt;/script&gt;</code></pre>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    3. JSON CSRF
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#csrfTypes">
                                <div class="accordion-body">
                                    <p>针对JSON API的CSRF攻击，利用Flash或CORS配置不当。</p>
                                    <pre class="bg-dark text-light p-3 rounded"><code>// 利用CORS配置不当
fetch(&#39;http://api.bank.com/transfer&#39;, {
    method: &#39;POST&#39;,
    headers: {&#39;Content-Type&#39;: &#39;application/json&#39;},
    body: JSON.stringify({to: &#39;attacker&#39;, amount: 10000})
});</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 防御建议</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">如何防御CSRF漏洞？</h5>
                    <ol>
                        <li><strong>CSRF Token：</strong>为每个表单生成唯一的Token，验证请求时检查Token</li>
                        <li><strong>SameSite Cookie：</strong>设置Cookie的SameSite属性为Strict或Lax</li>
                        <li><strong>Referer/Origin检查：</strong>验证请求的来源</li>
                        <li><strong>自定义请求头：</strong>使用自定义Header，跨域请求需要预检</li>
                        <li><strong>双重Cookie验证：</strong>将Token同时放在Cookie和请求参数中</li>
                        <li><strong>用户交互确认：</strong>敏感操作需要用户再次确认（如输入密码）</li>
                    </ol>

                    <h5 class="mb-3 mt-4">PHP中的防御代码示例</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 生成CSRF Token
session_start();
if (empty($_SESSION[&#39;csrf_token&#39;])) {
    $_SESSION[&#39;csrf_token&#39;] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION[&#39;csrf_token&#39;];

// 表单中嵌入Token
&lt;input type="hidden" name="csrf_token" value="&lt;?php echo $csrf_token; ?&gt;"&gt;

// 验证Token
if (!hash_equals($_SESSION[&#39;csrf_token&#39;], $_POST[&#39;csrf_token&#39;])) {
    die(&#39;CSRF验证失败&#39;);
}</code></pre>
                </div>
            </div>
        </div>
    </div>
EOT;

include '../template/module_template.php';
?>