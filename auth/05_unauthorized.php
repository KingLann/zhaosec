<?php
session_start();

// 模拟敏感配置文件内容（未授权访问可获取）
$config_content = <<<'CONFIG'
<?php
// 系统配置文件 - 重要：请勿将此文件暴露给公网
return [
    'database' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => 'SuperSecretDBPass123!',
        'database' => 'zhaosec_db'
    ],
    'jwt' => [
        'secret_key' => 'weak_secret_key_123',
        'algorithm' => 'HS256',
        'expire_time' => 3600
    ],
    'admin' => [
        'default_user' => 'admin',
        'default_pass' => 'Admin@P@ssw0rd!2024',
        'backup_codes' => ['BK001', 'BK002', 'BK003']
    ],
    'api_keys' => [
        'payment_gateway' => 'sk_live_51H7xYZ1234567890abcdef',
        'sms_service' => 'AKIAIOSFODNN7EXAMPLE'
    ]
];
CONFIG;

// 模拟API文档（包含未公开的接口）
$api_docs = <<<'API'
内部API文档 - 仅限管理员访问

GET /api/admin/users - 获取所有用户信息
POST /api/admin/users/delete - 删除用户
GET /api/system/config - 获取系统配置
POST /api/system/backup - 执行数据库备份
GET /api/logs/access - 获取访问日志

认证方式：Header中携带JWT Token
Authorization: Bearer <token>
API;

// 模拟后台快捷入口
$admin_panel_url = 'admin_panel.php';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>未授权访问漏洞 - 敏感信息泄露</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
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
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .alert-danger h4 {
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        .info-section {
            background: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 20px;
            border-radius: 0 10px 10px 0;
            margin-bottom: 25px;
        }
        .code-block {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin: 15px 0;
            position: relative;
        }
        .code-block::before {
            content: 'CONFIDENTIAL';
            position: absolute;
            top: 5px;
            right: 10px;
            color: #ff6b6b;
            font-size: 10px;
            font-weight: bold;
        }
        .secret-box {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin: 20px 0;
        }
        .secret-box h4 {
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        .key-display {
            background: rgba(255,255,255,0.2);
            padding: 15px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            text-align: center;
            margin: 10px 0;
            word-break: break-all;
        }
        .admin-entry {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            margin: 20px 0;
        }
        .admin-entry h4 {
            margin-bottom: 15px;
        }
        .btn-admin {
            display: inline-block;
            background: white;
            color: #11998e;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 10px;
            transition: transform 0.3s;
        }
        .btn-admin:hover {
            transform: translateY(-2px);
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #ff6b6b;
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
            font-size: 1.2rem;
        }
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .tab-btn {
            padding: 10px 20px;
            border: none;
            background: #e9ecef;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        .tab-btn.active {
            background: #ff6b6b;
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- 漏洞说明 -->
        <div class="card">
            <div class="alert-danger">
                <h4>🚨 严重安全漏洞检测</h4>
                <p>本页面存在<strong>未授权访问漏洞</strong>，攻击者无需任何认证即可访问敏感信息，包括系统配置、JWT密钥、数据库凭证等。</p>
            </div>
            
            <h2>5. 🔓 未授权访问漏洞</h2>
            
            <div class="info-section">
                <strong>💡 漏洞利用指南：</strong><br>
                1. 本页面直接暴露了系统配置文件<br>
                2. 可获取JWT密钥用于伪造token<br>
                3. 可直接进入后台管理界面<br>
                4. 获取数据库凭据进行进一步攻击
            </div>
        </div>

        <!-- 泄露的JWT密钥 -->
        <div class="card">
            <div class="secret-box">
                <h4>🔑 泄露的JWT密钥</h4>
                <p>通过未授权访问获取的JWT签名密钥：</p>
                <div class="key-display">weak_secret_key_123</div>
                <p style="font-size: 0.9rem; margin-top: 10px;">
                    💡 <strong>利用方式：</strong>使用此密钥可伪造任意JWT Token，包括admin权限！<br>
                    前往 <a href="06_jwt_weak_key.php" style="color: #fff; text-decoration: underline;">JWT漏洞场景</a> 进行测试
                </p>
            </div>
        </div>

        <!-- 系统配置文件 -->
        <div class="card">
            <h2>📁 泄露的系统配置文件</h2>
            <div class="tabs">
                <button class="tab-btn active" onclick="showTab('config')">config.php</button>
                <button class="tab-btn" onclick="showTab('api')">API文档</button>
            </div>
            
            <div id="config" class="tab-content active">
                <p style="margin-bottom: 10px; color: #666;">路径: /var/www/html/config/config.php</p>
                <div class="code-block"><?php echo htmlspecialchars($config_content); ?></div>
            </div>
            
            <div id="api" class="tab-content">
                <p style="margin-bottom: 10px; color: #666;">路径: /var/www/html/docs/internal_api.md</p>
                <div class="code-block"><?php echo htmlspecialchars($api_docs); ?></div>
            </div>
        </div>

        <!-- 后台快捷入口 -->
        <div class="card">
            <div class="admin-entry">
                <h4>🎯 未授权后台入口</h4>
                <p>通过未授权访问发现的管理后台地址</p>
                <a href="jwt_dashboard.php" class="btn-admin">直接进入管理后台</a>
                <p style="margin-top: 15px; font-size: 0.9rem;">
                    💡 提示：尝试使用上面获取的JWT密钥伪造admin token
                </p>
            </div>
        </div>

        <!-- 攻击成功Flag -->
        <div class="card">
            <div class="flag-box">
                🚩 FLAG{Unauthorized_Access_Sensitive_Data_Exposed}<br>
                <span style="font-size: 0.9rem;">你发现并利用了未授权访问漏洞！</span>
            </div>
            
            <a href="index.php" class="back-link">← 返回身份认证首页</a>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // 隐藏所有tab内容
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            // 移除所有按钮active状态
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            // 显示选中的tab
            document.getElementById(tabName).classList.add('active');
            // 添加按钮active状态
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
