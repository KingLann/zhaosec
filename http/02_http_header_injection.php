<?php
// HTTP头注入漏洞场景
$module_name = 'HTTP头注入';
$module_icon = '📝';
$module_desc = '通过注入恶意HTTP头信息，执行未授权操作或绕过安全限制';

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">📝 HTTP头注入</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                HTTP头注入（HTTP Header Injection）是一种通过在HTTP头部注入恶意数据的攻击技术。攻击者可以利用此漏洞进行缓存投毒、XSS攻击、会话劫持等恶意操作。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞原理</h6>
                </div>
                <div class="card-body">
                    <p>HTTP头注入的核心在于应用程序未对用户输入进行适当的验证和过滤：</p>
                    <ul>
                        <li><strong>未过滤换行符</strong> - 允许用户输入包含\\r\\n的字符</li>
                        <li><strong>直接拼接头部</strong> - 将用户输入直接拼接到HTTP头中</li>
                        <li><strong>缺乏头部验证</strong> - 不检查头部名称和值的合法性</li>
                    </ul>

                    <h5 class="mb-3 mt-4">攻击流程</h5>
                    <div class="bg-light p-3 rounded border">
                        <script src="../assets/js/mermaid.min.js"></script>
                        <div class="mermaid">
                            flowchart TD
                                A[攻击者] --> B[构造恶意请求]
                                B --> C[注入\\r\\n换行符]
                                C --> D[添加恶意HTTP头]
                                D --> E[服务器处理请求]
                                E --> F[执行恶意操作]
                            
                            style A fill:#f9f,stroke:#333,stroke-width:2px
                            style F fill:#f99,stroke:#333,stroke-width:2px
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击技术</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">HTTP响应分割</h5>
                    <p>通过注入换行符分割HTTP响应，添加恶意内容</p>
                    <pre class="bg-dark text-light p-3 rounded"><code>GET /page.php?name=test%0d%0aContent-Length:%200%0d%0a%0d%0aHTTP/1.1%20200%20OK%0d%0aContent-Type:%20text/html%0d%0a%0d%0a%3Cscript%3Ealert(1)%3C/script%3E</code></pre>

                    <h5 class="mb-3 mt-4">缓存投毒</h5>
                    <p>通过注入恶意头部污染缓存服务器</p>
                    <pre class="bg-dark text-light p-3 rounded"><code>GET /page.php?path=/index.php%0d%0aX-Forwarded-Host:%20evil.com</code></pre>

                    <h5 class="mb-3 mt-4">XSS攻击</h5>
                    <p>通过注入Content-Type头部执行恶意脚本</p>
                    <pre class="bg-dark text-light p-3 rounded"><code>GET /page.php?file=test.txt%0d%0aContent-Type:%20text/html%0d%0a%0d%0a%3Cscript%3Ealert(1)%3C/script%3E</code></pre>

                    <h5 class="mb-3 mt-4">Cookie注入</h5>
                    <p>通过注入Set-Cookie头部劫持会话</p>
                    <pre class="bg-dark text-light p-3 rounded"><code>GET /page.php?user=admin%0d%0aSet-Cookie:%20session=malicious</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 漏洞利用</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">常见利用场景</h5>
                    <ul>
                        <li><strong>缓存投毒</strong> - 污染CDN或代理服务器的缓存</li>
                        <li><strong>XSS攻击</strong> - 注入恶意脚本到响应中</li>
                        <li><strong>会话劫持</strong> - 修改用户的Cookie值</li>
                        <li><strong>重定向攻击</strong> - 注入Location头部进行钓鱼</li>
                        <li><strong>绕过安全控制</strong> - 修改Referer、X-Forwarded-For等头部</li>
                    </ul>

                    <h5 class="mb-3 mt-4">检测方法</h5>
                    <ol>
                        <li>在URL参数中注入%0d%0a（CRLF）</li>
                        <li>观察响应头是否包含注入的内容</li>
                        <li>检查是否出现额外的HTTP头</li>
                        <li>使用Burp Suite的HTTP Header插件</li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 防御建议</h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li><strong>过滤换行符</strong> - 移除或转义\\r、\\n等字符</li>
                        <li><strong>验证头部</strong> - 对所有HTTP头进行严格验证</li>
                        <li><strong>使用白名单</strong> - 只允许预定义的头部字段</li>
                        <li><strong>编码输出</strong> - 对用户输入进行适当的编码</li>
                        <li><strong>禁用CRLF</strong> - 在HTTP响应中禁用CRLF注入</li>
                        <li><strong>使用安全框架</strong> - 使用自动处理头部安全的框架</li>
                    </ol>

                    <h5 class="mb-3 mt-4">修复后的代码</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 安全的HTTP头设置
function setHeader($name, $value) {
    // 移除换行符
    $value = str_replace(["\\r", "\\n"], "", $value);
    
    // 验证头部名称
    if (!preg_match("/^[a-zA-Z0-9-]+$/", $name)) {
        return false;
    }
    
    // 设置头部
    header("$name: $value");
    return true;
}

// 使用示例
setHeader("X-Custom-Header", $_GET["value"] ?? "");</code></pre>
                </div>
            </div>
        </div>
    </div>';

include '../template/module_template.php';
?>