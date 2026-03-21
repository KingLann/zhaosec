<?php
// HTTP头伪造挑战漏洞场景
$module_name = 'HTTP头伪造挑战';
$module_icon = '🎯';
$module_desc = '通过逐步伪造HTTP请求头字段，完成挑战并获取flag';

// 初始化会话
session_start();

// 重置挑战状态
if (isset($_GET['reset'])) {
    $_SESSION['challenge_level'] = 0;
    $_SESSION['flag'] = 'flag{http_header_forgery_challenge_completed}';
    header('Location: 04_header_forgery_challenge.php');
    exit;
}

// 初始化挑战
if (!isset($_SESSION['challenge_level'])) {
    $_SESSION['challenge_level'] = 0;
    $_SESSION['flag'] = 'flag{http_header_forgery_challenge_completed}';
}

$level = $_SESSION['challenge_level'];
$message = '';
$success = false;

// 处理挑战
switch ($level) {
    case 0:
        // 第1关：User-Agent头
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'zhaowendao') !== false) {
            $_SESSION['challenge_level'] = 1;
            $message = '🎉 第1关通过！浏览器标识验证成功\n\nUser-Agent头用于标识客户端类型和版本信息，服务器可以根据这些信息提供不同的内容。';
            $success = true;
        } else {
            $message = '👋 欢迎来到HTTP头伪造挑战！\n\n第1关：请修改浏览器标识信息，在标识中包含 "zhaowendao"\n\n提示：这个头字段用于告诉服务器你使用的是什么浏览器和操作系统。';
        }
        break;
        
    case 1:
        // 第2关：Referer头
        if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] === 'https://zhaosec.com') {
            $_SESSION['challenge_level'] = 2;
            $message = '🎉 第2关通过！来源页面验证成功\n\nReferer头用于告诉服务器请求来自哪个页面，常用于跟踪用户来源和防止CSRF攻击。';
            $success = true;
        } else {
            $message = '🔍 第2关：请设置请求的来源页面为 "https://zhaosec.com"\n\n提示：这个头字段记录了用户是从哪个页面链接过来的，服务器可以根据这个信息进行访问控制。';
        }
        break;
        
    case 2:
        // 第3关：X-Forwarded-For头
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] === '127.0.0.1') {
            $_SESSION['challenge_level'] = 3;
            $message = '🎉 第3关通过！客户端IP验证成功\n\nX-Forwarded-For头用于在代理服务器环境中传递真实的客户端IP地址。';
            $success = true;
        } else {
            $message = '🌐 第3关：请设置真实的客户端IP地址为 "127.0.0.1"\n\n提示：当请求通过代理服务器时，这个头字段可以帮助服务器识别原始的客户端IP地址。';
        }
        break;
        
    case 3:
        // 第4关：Custom-Authorization头
        if (isset($_SERVER['HTTP_CUSTOM_AUTHORIZATION']) && $_SERVER['HTTP_CUSTOM_AUTHORIZATION'] === 'Bearer zhao_wen_dao') {
            $_SESSION['challenge_level'] = 4;
            $message = '🎉 第4关通过！认证信息验证成功\n\nAuthorization头用于在HTTP请求中传递认证凭证，如Bearer令牌、Basic认证等。';
            $success = true;
        } else {
            $message = '🔐 第4关：请提供认证凭证，格式为 "Bearer zhao_wen_dao"\n\n提示：这个头字段用于向服务器证明你的身份，常见于API调用和需要认证的服务。';
        }
        break;
        
    case 4:
        // 第5关：X-Admin-Key头
        if (isset($_SERVER['HTTP_X_ADMIN_KEY']) && $_SERVER['HTTP_X_ADMIN_KEY'] === 'zhao_secret_key') {
            $_SESSION['challenge_level'] = 5;
            $message = '🎉 第5关通过！管理密钥验证成功\n\nX-前缀的头字段通常是自定义头，用于传递应用程序特定的信息。';
            $success = true;
        } else {
            $message = '👑 第5关：请提供管理访问密钥 "zhao_secret_key"\n\n提示：以X-开头的头字段通常是自定义的，用于传递特定应用的配置或认证信息。';
        }
        break;
        
    case 5:
        // 完成挑战
        $message = '🏆 恭喜完成所有挑战！\n\nFlag: ' . $_SESSION['flag'] . '\n\n你已经成功掌握了HTTP头伪造技术！\n\n通过这个挑战，你学习了：\n- User-Agent：浏览器标识\n- Referer：来源页面\n- X-Forwarded-For：客户端IP\n- Authorization：认证凭证\n- 自定义头字段\n\n记住：HTTP头可以被轻易伪造，不要仅依赖HTTP头进行安全验证！';
        $success = true;
        break;
}

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🎯 HTTP头伪造挑战</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>💡 挑战说明：</strong><br>
                本挑战要求你通过理解和伪造HTTP请求头字段来完成各个关卡，最终获取flag。<br>
                每个关卡都会介绍一个HTTP头字段的作用，帮助你理解它们的含义和用途。<br>
                你可以使用浏览器开发者工具、Burp Suite或其他工具来修改请求头。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🚩 挑战状态</h6>
                </div>
                <div class="card-body">
                    <div class="alert ' . ($success ? 'alert-success' : 'alert-warning') . '">
                        <strong>当前关卡：' . $level . '/5</strong><br>
                        <pre class="mb-0">' . nl2br($message) . '</pre>
                    </div>

                    <div class="mt-4">
                        <h6>当前请求头：</h6>
                        <pre class="bg-dark text-light p-3 rounded"><code>';

// 显示当前请求头
$headers = getallheaders();
foreach ($headers as $name => $value) {
    $content .= $name . ': ' . $value . "\n";
}

$content .= '</code></pre>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>📖 挑战指南</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">如何伪造HTTP头</h5>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="chrome">
                            <h6>Chrome开发者工具</h6>
                            <ol>
                                <li>打开开发者工具（F12）</li>
                                <li>切换到 "网络" 标签</li>
                                <li>刷新页面，找到请求</li>
                                <li>右键点击请求 → "复制" → "复制为cURL"</li>
                                <li>在终端中修改cURL命令，添加或修改请求头</li>
                                <li>执行修改后的命令</li>
                            </ol>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">cURL示例</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>curl -X GET "http://localhost/zhaosec/http/04_header_forgery_challenge.php" \
  -H "User-Agent: Mozilla/5.0 zhaowendao" \
  -H "Referer: https://zhaosec.com" \
  -H "X-Forwarded-For: 127.0.0.1" \
  -H "Custom-Authorization: Bearer zhao_wen_dao" \
  -H "X-Admin-Key: zhao_secret_key"</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💡 技术原理</h6>
                </div>
                <div class="card-body">
                    <p>HTTP头伪造是一种常见的攻击技术，攻击者通过修改HTTP请求头来：</p>
                    <ul>
                        <li><strong>绕过访问控制</strong> - 伪装成合法用户或IP</li>
                        <li><strong>欺骗服务器</strong> - 让服务器相信请求来自不同的来源</li>
                        <li><strong>执行未授权操作</strong> - 访问受限制的功能</li>
                        <li><strong>绕过安全设备</strong> - 欺骗WAF或其他安全工具</li>
                    </ul>

                    <h5 class="mb-3 mt-4">常见的可伪造头</h5>
                    <ul>
                        <li><code>User-Agent</code> - 浏览器标识和操作系统信息</li>
                        <li><code>Referer</code> - 请求来源页面URL</li>
                        <li><code>X-Forwarded-For</code> - 客户端真实IP地址</li>
                        <li><code>X-Real-IP</code> - 真实客户端IP</li>
                        <li><code>Authorization</code> - 认证凭证</li>
                        <li><code>Origin</code> - 请求来源域名</li>
                        <li><code>X-CSRF-Token</code> - 跨站请求伪造令牌</li>
                        <li><code>Accept</code> - 客户端可接受的内容类型</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 防御建议</h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li><strong>不要完全信任HTTP头</strong> - 所有用户输入（包括HTTP头）都可能被伪造</li>
                        <li><strong>使用HTTPS</strong> - 防止中间人攻击修改HTTP头</li>
                        <li><strong>实施严格的访问控制</strong> - 不要仅依赖HTTP头进行认证</li>
                        <li><strong>验证请求来源</strong> - 使用CSRF令牌和SameSite Cookie</li>
                        <li><strong>限制允许的头字段</strong> - 只接受必要的HTTP头</li>
                        <li><strong>使用安全框架</strong> - 利用框架的安全特性</li>
                        <li><strong>实施多因素认证</strong> - 结合多种验证方式</li>
                        <li><strong>监控异常请求</strong> - 检测可疑的HTTP头模式</li>
                    </ol>

                    <div class="mt-4 text-center">
                        <a href="04_header_forgery_challenge.php?reset" class="btn btn-danger">重置挑战</a>
                    </div>
                </div>
            </div>
        </div>
    </div>';

include '../template/module_template.php';
?>