<?php
// HTTP请求走私漏洞场景
$module_name = 'HTTP请求走私';
$module_icon = '🚶';
$module_desc = '通过构造特殊的HTTP请求，绕过安全设备或服务器的防护';

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🚶 HTTP请求走私</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                HTTP请求走私（HTTP Request Smuggling）是一种利用HTTP协议解析差异的攻击技术。攻击者通过构造特殊的HTTP请求，使得前端服务器和后端服务器对请求边界的理解不一致，从而绕过安全设备或访问未授权资源。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞原理</h6>
                </div>
                <div class="card-body">
                    <p>HTTP请求走私的核心在于利用不同服务器对HTTP协议解析的差异：</p>
                    <ul>
                        <li><strong>Content-Length vs Transfer-Encoding</strong> - 服务器优先使用不同的头部</li>
                        <li><strong>请求边界解析差异</strong> - 前后端服务器对请求边界的理解不同</li>
                        <li><strong>请求走私攻击</strong> - 将恶意请求隐藏在正常请求中</li>
                    </ul>

                    <h5 class="mb-3 mt-4">攻击流程</h5>
                    <div class="bg-light p-3 rounded border">
                        <script src="../assets/js/mermaid.min.js"></script>
                        <div class="mermaid">
                            flowchart TD
                                A[攻击者] --> B[构造走私请求]
                                B --> C[发送到前端服务器]
                                C --> D[前端服务器解析请求]
                                D --> E[转发到后端服务器]
                                E --> F[后端服务器解析请求]
                                F --> G[解析差异导致走私成功]
                                G --> H[绕过安全控制]
                            
                            style A fill:#f9f,stroke:#333,stroke-width:2px
                            style H fill:#f99,stroke:#333,stroke-width:2px
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击技术</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">CL.TE攻击</h5>
                    <p>前端服务器使用Content-Length，后端服务器使用Transfer-Encoding</p>
                    <pre class="bg-dark text-light p-3 rounded"><code>POST / HTTP/1.1
Host: example.com
Content-Length: 10
Transfer-Encoding: chunked

0

G</code></pre>

                    <h5 class="mb-3 mt-4">TE.CL攻击</h5>
                    <p>前端服务器使用Transfer-Encoding，后端服务器使用Content-Length</p>
                    <pre class="bg-dark text-light p-3 rounded"><code>POST / HTTP/1.1
Host: example.com
Transfer-Encoding: chunked
Content-Length: 6

0

G</code></pre>

                    <h5 class="mb-3 mt-4">TE.TE攻击</h5>
                    <p>前后端服务器都使用Transfer-Encoding，但处理方式不同</p>
                    <pre class="bg-dark text-light p-3 rounded"><code>POST / HTTP/1.1
Host: example.com
Transfer-Encoding: chunked

5
GPOST
0

</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 漏洞利用</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">利用场景</h5>
                    <ul>
                        <li><strong>绕过WAF</strong> - 将恶意请求隐藏在正常请求中</li>
                        <li><strong>缓存投毒</strong> - 污染前端服务器的缓存</li>
                        <li><strong>会话劫持</strong> - 窃取其他用户的会话</li>
                        <li><strong>权限提升</strong> - 访问未授权的管理功能</li>
                        <li><strong>XSS攻击</strong> - 向其他用户注入恶意脚本</li>
                    </ul>

                    <h5 class="mb-3 mt-4">检测方法</h5>
                    <ol>
                        <li>发送包含Content-Length和Transfer-Encoding的请求</li>
                        <li>观察服务器响应是否异常</li>
                        <li>使用Burp Suite的HTTP Smuggling插件</li>
                        <li>使用专门的检测工具如smuggler</li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 防御建议</h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li><strong>统一HTTP解析器</strong> - 确保前后端使用相同的HTTP解析库</li>
                        <li><strong>禁用Transfer-Encoding</strong> - 如果不需要，禁用此头部</li>
                        <li><strong>严格验证请求</strong> - 对请求边界进行严格验证</li>
                        <li><strong>使用反向代理</strong> - 配置正确的代理规则</li>
                        <li><strong>更新服务器软件</strong> - 修复已知的解析漏洞</li>
                        <li><strong>实施请求验证</strong> - 对每个请求进行完整性检查</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>';

include '../template/module_template.php';
?>