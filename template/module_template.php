<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $module_name ?> - Zhaosec靶场</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hero-section {
            background: var(--primary-gradient);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
        }
        .hero-section h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .hero-section p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        .vuln-item {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border-left: 4px solid #667eea;
        }
        .vuln-item:hover {
            transform: translateX(5px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.15);
        }
        .vuln-item h5 {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        .vuln-item p {
            color: #666;
            margin-bottom: 15px;
        }
        .vuln-item .btn {
            border-radius: 20px;
            padding: 6px 20px;
            font-size: 0.9rem;
        }
        .badge-level {
            font-size: 0.75rem;
            padding: 4px 12px;
            border-radius: 15px;
            margin-left: 10px;
        }
        .level-low { background: #d4edda; color: #155724; }
        .level-medium { background: #fff3cd; color: #856404; }
        .level-high { background: #f8d7da; color: #721c24; }
        .level-info { background: #d1ecf1; color: #0c5460; }
        .back-link {
            color: white;
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        .back-link:hover {
            color: white;
            opacity: 1;
        }
        .module-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            color: #666;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="container">
            <?php if (isset($content)): ?>
            <a href="index.php" class="back-link">← 返回模块首页</a>
            <?php else: ?>
            <a href="../index.php" class="back-link">← 返回靶场首页</a>
            <?php endif; ?>
            <div class="module-icon"><?= $module_icon ?></div>
            <h1><?= $module_name ?></h1>
            <p><?= $module_desc ?></p>
        </div>
    </div>

    <div class="container">
        <?php if (isset($content)): ?>
            <?= $content ?>
        <?php else: ?>
        <div class="row">
            <div class="col-12">
                <h4 class="mb-4">漏洞场景列表</h4>
            </div>
        </div>
        <div class="row">
            <?php if (isset($vulns) && is_array($vulns)): ?>
            <?php $index = 1; foreach ($vulns as $vuln): ?>
            <div class="col-lg-6">
                <div class="vuln-item">
                    <h5>
                        <?= $index ?>. <?= $vuln['name'] ?>
                        <?php 
                        $level_class = 'level-' . $vuln['level'];
                        $level_text = $vuln['level'] == 'low' ? '初级' : ($vuln['level'] == 'medium' ? '中级' : ($vuln['level'] == 'info' ? '信息' : '高级'));
                        ?>
                        <span class="badge badge-level <?= $level_class ?>"><?= $level_text ?></span>
                    </h5>
                    <p><?= $vuln['desc'] ?></p>
                    <a href="<?= $vuln['file'] ?>" class="btn btn-outline-primary btn-sm">开始演练</a>
                </div>
            </div>
            <?php $index++; endforeach; ?>
            <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <strong>提示：</strong>当前模块没有配置漏洞场景
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="footer">
            <p>⚠️ 本靶场仅供安全学习和研究使用，请勿用于非法用途</p>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
