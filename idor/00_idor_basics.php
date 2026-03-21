<?php
// IDOR基础知识和原理
$module_name = 'IDOR基础与原理';
$module_icon = '📚';
$module_desc = '讲解不安全直接对象引用(IDOR)漏洞的基础知识和原理。';

// 页面内容
$content = <<<'EOT'
<div class="card">
        <div class="card-header">
            <h5 class="mb-0">📚 IDOR基础与原理</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>💡 学习目标：</strong><br>
                了解不安全直接对象引用(IDOR)漏洞的基础知识和原理，掌握其攻击方式和防御方法。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>📄 什么是IDOR？</h6>
                </div>
                <div class="card-body">
                    <p>IDOR（Insecure Direct Object Reference）是一种安全漏洞，当应用程序直接使用用户提供的输入来访问对象（如文件、数据库记录、用户账户等）时，没有进行适当的访问控制检查，导致攻击者可以访问未授权的资源。</p>

                    <h5 class="mb-3 mt-4">IDOR的危害</h5>
                    <ul>
                        <li>访问其他用户的敏感数据</li>
                        <li>修改其他用户的账户信息</li>
                        <li>访问或下载未授权的文件</li>
                        <li>执行未授权的操作</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 IDOR攻击原理</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="mb-3">🔄 IDOR攻击流程</h5>
                        <div class="bg-light p-3 rounded border">
                            <script src="../assets/js/mermaid.min.js"></script>
                            <div class="mermaid">
                                sequenceDiagram
                                    participant Attacker as 攻击者
                                    participant Server as 服务器
                                    
                                    Attacker->>Server: 访问自己的资源 (e.g., /user/123)
                                    Server-->>Attacker: 返回资源内容
                                    Attacker->>Server: 尝试访问他人资源 (e.g., /user/456)
                                    Server-->>Attacker: 未授权访问成功！
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3">常见的IDOR场景</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h6>用户ID</h6>
                                    <p>通过修改URL中的用户ID访问其他用户的信息</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6>订单ID</h6>
                                    <p>通过修改订单ID访问其他用户的订单信息</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6>文件ID</h6>
                                    <p>通过修改文件ID下载其他用户的文件</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6>参数篡改</h6>
                                    <p>通过修改请求参数访问未授权资源</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 IDOR攻击示例</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">示例1：用户信息访问</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 正常访问自己的信息
https://example.com/user/profile?id=123

// 尝试访问其他用户的信息
https://example.com/user/profile?id=456</code></pre>

                    <h5 class="mb-3 mt-4">示例2：订单信息访问</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 正常访问自己的订单
https://example.com/orders/view?id=789

// 尝试访问其他用户的订单
https://example.com/orders/view?id=987</code></pre>

                    <h5 class="mb-3 mt-4">示例3：文件下载</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 正常下载自己的文件
https://example.com/files/download?id=101

// 尝试下载其他用户的文件
https://example.com/files/download?id=102</code></pre>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 防御建议</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">如何防御IDOR漏洞？</h5>
                    <ol>
                        <li><strong>实施访问控制检查：</strong>在访问任何资源前，验证用户是否有权限访问该资源</li>
                        <li><strong>使用间接引用：</strong>不直接使用数据库ID，而是使用映射表或随机标识符</li>
                        <li><strong>会话验证：</strong>确保用户的会话与请求的资源匹配</li>
                        <li><strong>输入验证：</strong>对所有用户输入进行严格的验证</li>
                        <li><strong>最小权限原则：</strong>只授予用户必要的权限</li>
                        <li><strong>使用HTTPS：</strong>保护传输中的数据</li>
                        <li><strong>日志记录：</strong>记录所有敏感操作，以便检测异常行为</li>
                    </ol>

                    <h5 class="mb-3 mt-4">PHP中的防御代码示例</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 不好的做法：直接使用用户提供的ID
$user_id = $_GET["id"];
$query = "SELECT * FROM users WHERE id = $user_id";

// 好的做法：实施访问控制检查
$user_id = $_GET["id"];
$current_user_id = $_SESSION["user_id"];

// 检查用户是否有权限访问该资源
if ($user_id != $current_user_id && !is_admin()) {
    die("Access denied");
}

$query = "SELECT * FROM users WHERE id = $user_id";</code></pre>
                </div>
            </div>
        </div>
    </div>
EOT;

// 包含模板
include '../template/module_template.php';
?>