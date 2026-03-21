<?php
// Cookie安全问题场景
$module_name = 'Cookie安全问题';
$module_icon = '🍪';
$module_desc = '学习Cookie的安全配置和常见的Cookie相关漏洞';

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🍪 Cookie安全问题</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                Cookie是Web应用中用于维护用户会话的重要机制，但如果配置不当，可能导致会话劫持、XSS攻击、CSRF攻击等严重安全问题。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 Cookie安全属性</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>属性</th>
                                    <th>作用</th>
                                    <th>推荐值</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>HttpOnly</td>
                                    <td>防止JavaScript访问Cookie，防止XSS窃取</td>
                                    <td><code class="text-success">HttpOnly</code></td>
                                </tr>
                                <tr>
                                    <td>Secure</td>
                                    <td>只在HTTPS连接下传输Cookie</td>
                                    <td><code class="text-success">Secure</code></td>
                                </tr>
                                <tr>
                                    <td>SameSite</td>
                                    <td>控制Cookie在跨站请求中的发送</td>
                                    <td><code class="text-success">SameSite=Strict</code></td>
                                </tr>
                                <tr>
                                    <td>Path</td>
                                    <td>限制Cookie的路径范围</td>
                                    <td><code class="text-success">Path=/</code></td>
                                </tr>
                                <tr>
                                    <td>Domain</td>
                                    <td>限制Cookie的域名范围</td>
                                    <td><code class="text-success">Domain=.example.com</code></td>
                                </tr>
                                <tr>
                                    <td>Expires/Max-Age</td>
                                    <td>设置Cookie的过期时间</td>
                                    <td><code class="text-success">Max-Age=3600</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 常见Cookie漏洞</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">会话固定攻击</h5>
                    <p>攻击者强制用户使用攻击者已知的会话ID</p>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 不安全的Cookie设置
setcookie("session_id", "attacker_known_id", time() + 3600);

// 安全的Cookie设置
session_regenerate_id(true);
setcookie("session_id", session_id(), time() + 3600, "/", "", true, true);</code></pre>

                    <h5 class="mb-3 mt-4">会话劫持</h5>
                    <p>通过XSS或网络嗅探窃取用户Cookie</p>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 不安全的Cookie（可被JavaScript访问）
setcookie("user_id", $user_id);

// 安全的Cookie（HttpOnly）
setcookie("user_id", $user_id, 0, "/", "", true, true);</code></pre>

                    <h5 class="mb-3 mt-4">CSRF攻击</h5>
                    <p>跨站请求伪造攻击利用Cookie自动发送的特性</p>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 不安全的Cookie（跨站请求会发送）
setcookie("session", $session_id);

// 安全的Cookie（SameSite=Strict）
setcookie("session", $session_id, 0, "/", "", true, true);
header("Set-Cookie: session=$session_id; Path=/; Secure; HttpOnly; SameSite=Strict");</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 Cookie安全最佳实践</h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li><strong>始终设置HttpOnly</strong> - 防止XSS窃取Cookie</li>
                        <li><strong>使用Secure标志</strong> - 只在HTTPS下传输</li>
                        <li><strong>配置SameSite</strong> - 防止CSRF攻击</li>
                        <li><strong>限制Path和Domain</strong> - 缩小Cookie的作用范围</li>
                        <li><strong>设置合理的过期时间</strong> - 避免永久性Cookie</li>
                        <li><strong>定期重新生成会话ID</strong> - 防止会话固定攻击</li>
                        <li><strong>加密敏感数据</strong> - 不要在Cookie中存储明文敏感信息</li>
                        <li><strong>验证Cookie完整性</strong> - 使用签名或HMAC验证</li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 安全Cookie设置示例</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">PHP安全Cookie设置</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 安全的Cookie设置函数
function setSecureCookie($name, $value, $expires = 0, $path = "/", $domain = "") {
    $options = [
        "expires" => $expires,
        "path" => $path,
        "domain" => $domain,
        "secure" => true,           // 只在HTTPS下传输
        "httponly" => true,         // 防止JavaScript访问
        "samesite" => "Strict"      // 防止CSRF攻击
    ];
    
    setcookie($name, $value, $options);
}

// 使用示例
setSecureCookie("session_id", $session_id, time() + 3600);
setSecureCookie("user_token", $token, time() + 86400);</code></pre>

                    <h5 class="mb-3 mt-4">Session配置</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// php.ini 安全配置
session.cookie_httponly = 1
session.cookie_secure = 1
session.cookie_samesite = "Strict"
session.use_strict_mode = 1
session.use_only_cookies = 1
session.cookie_lifetime = 3600</code></pre>
                </div>
            </div>
        </div>
    </div>';

include '../template/module_template.php';
?>