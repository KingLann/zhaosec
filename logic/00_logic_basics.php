<?php
$module_name = '逻辑漏洞基础';
$module_icon = '📚';
$module_desc = '学习逻辑漏洞的基本概念、类型、原理和防御方法。';

$content = <<<'EOT'
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">📚 逻辑漏洞基础</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>💡 学习目标：</strong><br>
            理解逻辑漏洞的基本概念、常见类型、攻击原理和防御方法。
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🧩 什么是逻辑漏洞</h6>
            </div>
            <div class="card-body">
                <p><strong>逻辑漏洞</strong>是指应用程序业务逻辑设计或实现上的缺陷，导致攻击者可以绕过预期的安全控制，执行非预期的操作。</p>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card bg-light h-100">
                            <div class="card-body">
                                <h6 class="card-title">🔍 核心特征</h6>
                                <ul class="mb-0">
                                    <li>基于业务逻辑缺陷</li>
                                    <li>难以通过常规安全扫描发现</li>
                                    <li>通常不涉及技术层面的漏洞</li>
                                    <li>利用应用程序的设计逻辑</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light h-100">
                            <div class="card-body">
                                <h6 class="card-title">🎯 攻击特点</h6>
                                <ul class="mb-0">
                                    <li>不依赖特定技术栈</li>
                                    <li>利用业务规则的漏洞</li>
                                    <li>通常不需要特殊工具</li>
                                    <li>难以被自动化检测</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>📊 逻辑漏洞攻击流程图</h6>
            </div>
            <div class="card-body">
                <div class="mermaid-container" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                    <pre class="mermaid">
flowchart TD
    A[攻击者] -->|分析业务逻辑| B[发现逻辑缺陷]
    B -->|构造攻击请求| C[绕过安全控制]
    C -->|执行非预期操作| D[获取不当利益]
    D -->|业务损失| E[受害者]
    
    style A fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
    style D fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
    style E fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
                    </pre>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🏷️ 逻辑漏洞类型</h6>
            </div>
            <div class="card-body">
                <div class="accordion" id="logicTypes">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                💰 支付交易类
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#logicTypes">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>常见类型</h6>
                                                <ul class="mb-0">
                                                    <li>价格篡改</li>
                                                    <li>支付状态绕过</li>
                                                    <li>订单重放</li>
                                                    <li>数量篡改</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击示例</h6>
                                                <p>修改商品价格为负数，实现反向充值</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mermaid-container" style="background: #fff; padding: 15px; border-radius: 10px; margin: 15px 0; border: 1px solid #ddd;">
                                    <pre class="mermaid">
sequenceDiagram
        participant A as 攻击者
        participant S as 服务器
        
        A->>S: 提交订单(正常价格)
        A->>S: 拦截请求修改价格
        S->>S: 验证通过(未验证价格)
        S->>A: 订单成功(低价购买)
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                🔐 权限控制类
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#logicTypes">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>常见类型</h6>
                                                <ul class="mb-0">
                                                    <li>水平越权</li>
                                                    <li>垂直越权</li>
                                                    <li>权限提升</li>
                                                    <li>权限绕过</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击示例</h6>
                                                <p>修改用户ID参数访问其他用户数据</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mermaid-container" style="background: #fff; padding: 15px; border-radius: 10px; margin: 15px 0; border: 1px solid #ddd;">
                                    <pre class="mermaid">
sequenceDiagram
        participant A as 攻击者
        participant S as 服务器
        
        A->>S: 登录用户A
        A->>S: 请求用户B的数据(user_id=2)
        S->>S: 验证用户A是否登录
        S->>A: 返回用户B的数据(未验证权限)
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                📋 业务流程类
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#logicTypes">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>常见类型</h6>
                                                <ul class="mb-0">
                                                    <li>步骤跳跃</li>
                                                    <li>状态篡改</li>
                                                    <li>流程绕过</li>
                                                    <li>参数篡改</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击示例</h6>
                                                <p>跳过支付步骤直接完成订单</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mermaid-container" style="background: #fff; padding: 15px; border-radius: 10px; margin: 15px 0; border: 1px solid #ddd;">
                                    <pre class="mermaid">
sequenceDiagram
        participant A as 攻击者
        participant S as 服务器
        
        A->>S: 提交订单
        A->>S: 跳过支付直接请求完成订单
        S->>S: 验证订单存在
        S->>A: 订单完成(未验证支付状态)
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
                                🔄 并发竞态类
                            </button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#logicTypes">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>常见类型</h6>
                                                <ul class="mb-0">
                                                    <li>超卖问题</li>
                                                    <li>重复领取</li>
                                                    <li>数据竞争</li>
                                                    <li>并发更新</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击示例</h6>
                                                <p>并发请求抢购导致超卖</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mermaid-container" style="background: #fff; padding: 15px; border-radius: 10px; margin: 15px 0; border: 1px solid #ddd;">
                                    <pre class="mermaid">
sequenceDiagram
        participant A as 攻击者
        participant S as 服务器
        
        A->>S: 请求1: 检查库存(100)
        A->>S: 请求2: 检查库存(100)
        S->>S: 处理请求1: 库存100
        S->>S: 处理请求2: 库存100
        S->>A: 请求1: 购买成功
        S->>A: 请求2: 购买成功
        S->>S: 最终库存: 98
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading5">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5">
                                🎯 业务滥用类
                            </button>
                        </h2>
                        <div id="collapse5" class="accordion-collapse collapse" data-bs-parent="#logicTypes">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>常见类型</h6>
                                                <ul class="mb-0">
                                                    <li>积分滥刷</li>
                                                    <li>短信轰炸</li>
                                                    <li>优惠券滥用</li>
                                                    <li>邀请奖励作弊</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>攻击示例</h6>
                                                <p>利用短信验证码接口发送大量短信</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mermaid-container" style="background: #fff; padding: 15px; border-radius: 10px; margin: 15px 0; border: 1px solid #ddd;">
                                    <pre class="mermaid">
sequenceDiagram
        participant A as 攻击者
        participant S as 服务器
        
        A->>S: 请求1: 发送验证码
        A->>S: 请求2: 发送验证码
        A->>S: 请求3: 发送验证码
        S->>S: 处理请求1: 发送成功
        S->>S: 处理请求2: 发送成功
        S->>S: 处理请求3: 发送成功
        S->>A: 三个请求都成功(未限制频率)
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
                <h6>⚠️ 逻辑漏洞的危害</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0">🔴 经济损失</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li>资金损失（价格篡改）</li>
                                    <li>服务滥用（短信轰炸）</li>
                                    <li>资源浪费（超卖）</li>
                                    <li>业务损失（订单欺诈）</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">🟠 数据安全</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li>未授权数据访问</li>
                                    <li>用户隐私泄露</li>
                                    <li>敏感信息获取</li>
                                    <li>业务数据篡改</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">🔵 业务影响</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li>业务流程混乱</li>
                                    <li>用户信任度下降</li>
                                    <li>品牌声誉受损</li>
                                    <li>合规性问题</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🔧 逻辑漏洞防御方法</h6>
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
                                    <li><strong>服务端验证：</strong>所有关键参数在服务端重新验证</li>
                                    <li><strong>业务逻辑审计：</strong>定期进行业务逻辑安全审计</li>
                                    <li><strong>权限控制：</strong>实施严格的权限检查</li>
                                    <li><strong>状态管理：</strong>确保业务状态的一致性</li>
                                    <li><strong>输入验证：</strong>对所有用户输入进行验证</li>
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
                                    <li><strong>速率限制：</strong>防止API滥用</li>
                                    <li><strong>事务处理：</strong>使用数据库事务保证一致性</li>
                                    <li><strong>日志审计：</strong>记录关键操作日志</li>
                                    <li><strong>代码审查：</strong>定期代码安全审查</li>
                                    <li><strong>安全测试：</strong>针对性的逻辑漏洞测试</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mermaid-container" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                    <pre class="mermaid">
flowchart TD
    A[用户输入] --> B{服务端验证}
    B -->|验证失败| C[拒绝请求]
    B -->|验证通过| D[业务逻辑处理]
    D --> E{权限检查}
    E -->|权限不足| C
    E -->|权限通过| F[状态验证]
    F -->|状态异常| C
    F -->|状态正常| G[安全执行]
    G --> H[记录日志]
    H --> I[返回结果]
    
    style B fill:#ffd43b,stroke:#333,stroke-width:2px
    style E fill:#ffd43b,stroke:#333,stroke-width:2px
    style F fill:#ffd43b,stroke:#333,stroke-width:2px
    style G fill:#51cf66,stroke:#333,stroke-width:2px,color:#fff
    style C fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
                    </pre>
                </div>

                <div class="card bg-light mt-3">
                    <div class="card-body">
                        <h6>💡 防御示例</h6>
                        <pre class="mb-0"><code>// 价格验证示例
if ($price <= 0) {
    die('价格无效');
}

// 权限验证示例
if ($user_id != $order_user_id) {
    die('无权限访问');
}

// 并发控制示例
$pdo->beginTransaction();
try {
    // 检查库存
    $stock = $pdo->query("SELECT stock FROM products WHERE id = ? FOR UPDATE", [$product_id])->fetchColumn();
    if ($stock < $quantity) {
        throw new Exception('库存不足');
    }
    // 更新库存
    $pdo->query("UPDATE products SET stock = stock - ? WHERE id = ?", [$quantity, $product_id]);
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    die($e->getMessage());
}

// 速率限制示例
if (isset($_SESSION['last_sms_time']) && time() - $_SESSION['last_sms_time'] < 60) {
    die('请稍后再试');
}
$_SESSION['last_sms_time'] = time();</code></pre>
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
        },
        sequence: {
            useMaxWidth: true,
            wrap: true
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
