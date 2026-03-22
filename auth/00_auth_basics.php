<?php
$module_name = '身份认证基础';
$module_icon = '📚';
$module_desc = '学习身份认证的基本概念、常见机制和安全原理。';

$content = <<<'EOT'
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">📚 身份认证基础</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>💡 学习目标：</strong><br>
            理解身份认证的基本概念、常见机制、安全原理以及常见漏洞类型。
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🔑 什么是身份认证</h6>
            </div>
            <div class="card-body">
                <p><strong>身份认证（Authentication）</strong>是验证用户身份的过程，确认"你是谁"。它是Web应用安全的第一道防线。</p>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">认证三要素</h6>
                                <ul class="mb-0">
                                    <li><strong>所知（Something you know）</strong><br>密码、PIN码、安全问题</li>
                                    <li><strong>所有（Something you have）</strong><br>手机、令牌、智能卡</li>
                                    <li><strong>所是（Something you are）</strong><br>指纹、面部、虹膜</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">认证 vs 授权</h6>
                                <table class="table table-sm table-bordered mb-0">
                                    <tr>
                                        <th>概念</th>
                                        <th>问题</th>
                                        <th>英文</th>
                                    </tr>
                                    <tr>
                                        <td>认证</td>
                                        <td>你是谁？</td>
                                        <td>Authentication</td>
                                    </tr>
                                    <tr>
                                        <td>授权</td>
                                        <td>你能做什么？</td>
                                        <td>Authorization</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🛡️ 常见认证机制</h6>
            </div>
            <div class="card-body">
                <div class="accordion" id="authMechanisms">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                Session-Cookie 认证
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#authMechanisms">
                            <div class="accordion-body">
                                <p>最传统的认证方式，服务器创建Session，通过Cookie传递Session ID。</p>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>流程：</h6>
                                        <ol>
                                            <li>用户提交用户名密码</li>
                                            <li>服务器验证成功后创建Session</li>
                                            <li>服务器通过Set-Cookie返回Session ID</li>
                                            <li>浏览器后续请求自动携带Cookie</li>
                                            <li>服务器通过Session ID查找用户身份信息</li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="alert alert-warning mt-2 mb-0">
                                    <strong>安全风险：</strong>Session固定攻击、Cookie劫持、Session劫持
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                Token 认证（JWT）
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#authMechanisms">
                            <div class="accordion-body">
                                <p>JSON Web Token，将用户信息编码在Token中，服务器无需保存Session状态。</p>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>JWT结构：</h6>
                                        <code>Header.Payload.Signature</code>
                                        <ul class="mb-0 mt-2">
                                            <li><strong>Header：</strong>算法和Token类型</li>
                                            <li><strong>Payload：</strong>用户数据（claims）</li>
                                            <li><strong>Signature：</strong>签名，防止篡改</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="alert alert-warning mt-2 mb-0">
                                    <strong>安全风险：</strong>算法混淆、密钥泄露、Token伪造、弱密钥
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                OAuth 2.0 / OpenID Connect
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#authMechanisms">
                            <div class="accordion-body">
                                <p>第三方认证授权框架，允许用户使用第三方账号（如微信、Google）登录。</p>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>授权流程（Authorization Code）：</h6>
                                        <ol>
                                            <li>用户点击"使用XX登录"</li>
                                            <li>重定向到授权服务器</li>
                                            <li>用户授权后返回授权码</li>
                                            <li>应用用授权码换取Access Token</li>
                                            <li>使用Access Token获取用户信息</li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="alert alert-warning mt-2 mb-0">
                                    <strong>安全风险：</strong>CSRF攻击、redirect_uri劫持、Token泄露
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
                                多因素认证（MFA）
                            </button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#authMechanisms">
                            <div class="accordion-body">
                                <p>结合两种或以上认证要素，提高安全性。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h6>常见形式</h6>
                                                <ul class="mb-0">
                                                    <li>密码 + 短信验证码</li>
                                                    <li>密码 + 邮箱验证码</li>
                                                    <li>密码 + 动态令牌（TOTP）</li>
                                                    <li>密码 + 硬件密钥</li>
                                                    <li>密码 + 生物识别</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h6>绕过风险</h6>
                                                <ul class="mb-0">
                                                    <li>暴力破解验证码</li>
                                                    <li>验证码泄露/重用</li>
                                                    <li>逻辑缺陷绕过</li>
                                                    <li>会话固定攻击</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>⚠️ 常见身份认证漏洞</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>漏洞类型</th>
                                <th>描述</th>
                                <th>风险等级</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>暴力破解</td>
                                <td>无验证码、无频率限制，可自动化猜测密码</td>
                                <td><span class="badge bg-danger">高</span></td>
                            </tr>
                            <tr>
                                <td>账户枚举</td>
                                <td>根据错误提示判断用户名是否存在</td>
                                <td><span class="badge bg-warning">中</span></td>
                            </tr>
                            <tr>
                                <td>弱密码策略</td>
                                <td>允许使用简单密码，如123456、admin等</td>
                                <td><span class="badge bg-danger">高</span></td>
                            </tr>
                            <tr>
                                <td>明文传输</td>
                                <td>密码以明文形式在网络中传输</td>
                                <td><span class="badge bg-danger">高</span></td>
                            </tr>
                            <tr>
                                <td>未授权访问</td>
                                <td>无需登录即可访问敏感功能或数据</td>
                                <td><span class="badge bg-danger">高</span></td>
                            </tr>
                            <tr>
                                <td>会话固定</td>
                                <td>登录前后Session ID不变，可被利用</td>
                                <td><span class="badge bg-warning">中</span></td>
                            </tr>
                            <tr>
                                <td>JWT漏洞</td>
                                <td>弱密钥、算法混淆、Token伪造等</td>
                                <td><span class="badge bg-danger">高</span></td>
                            </tr>
                            <tr>
                                <td>密码重置缺陷</td>
                                <td>重置链接可预测、无过期时间、可重用</td>
                                <td><span class="badge bg-danger">高</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🔒 安全最佳实践</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>密码策略</h6>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item">✅ 最小长度8位以上，建议12位</li>
                            <li class="list-group-item">✅ 包含大小写字母、数字、特殊字符</li>
                            <li class="list-group-item">✅ 定期更换密码（90天）</li>
                            <li class="list-group-item">✅ 禁止重复使用历史密码</li>
                            <li class="list-group-item">✅ 使用密码管理器生成强密码</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>防护措施</h6>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item">✅ 实施登录失败锁定（5次失败锁定15分钟）</li>
                            <li class="list-group-item">✅ 添加图形验证码或行为验证码</li>
                            <li class="list-group-item">✅ 使用HTTPS加密传输</li>
                            <li class="list-group-item">✅ 实施多因素认证（MFA）</li>
                            <li class="list-group-item">✅ 记录安全日志并监控异常</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>📖 学习资源</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>推荐资料</h6>
                        <ul>
                            <li>OWASP Authentication Cheat Sheet</li>
                            <li>OWASP Session Management Cheat Sheet</li>
                            <li>RFC 6749 - OAuth 2.0 授权框架</li>
                            <li>RFC 7519 - JSON Web Token (JWT)</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>相关工具</h6>
                        <ul>
                            <li>Burp Suite - Web渗透测试</li>
                            <li>JWT.io - JWT编解码调试</li>
                            <li>Hashcat - 密码破解</li>
                            <li>Hydra - 在线密码爆破</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
EOT;

include '../template/module_template.php';
?>
