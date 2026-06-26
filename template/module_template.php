<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= $module_name ?> - ZhaoSec 靶场 · 朝闻道</title>
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root {
            --primary: #1e3c72;
            --accent: #2c7da0;
            --accent-soft: #eaf4fa;
            --text-primary: #0f2c3f;
            --text-secondary: #4a627a;
            --text-muted: #6f8aac;
        }

        .module-wrap {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem 3rem;
        }

        .module-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0 1.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(44, 125, 160, 0.15);
        }

        .module-nav .back-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--accent);
            text-decoration: none;
            background: var(--accent-soft);
            padding: 8px 18px;
            border-radius: 40px;
            transition: all 0.2s ease;
        }

        .module-nav .back-home:hover {
            background: var(--accent);
            color: #fff;
            transform: translateX(-2px);
        }

        .module-nav .module-crumb {
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .module-nav .module-crumb i {
            color: var(--accent);
            margin-right: 6px;
        }

        .module-hero {
            text-align: center;
            padding: 2.5rem 1.5rem 2rem;
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 28px;
            box-shadow: 0 12px 24px -12px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(44, 125, 160, 0.12);
        }

        .module-hero .hero-icon {
            font-size: 3.5rem;
            width: 80px;
            height: 80px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--accent-soft), #d6eaf3);
            border-radius: 24px;
            margin-bottom: 1rem;
            color: var(--accent);
        }

        .module-hero h1 {
            font-size: 2.3rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), #2b4c7c);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.5px;
            margin-bottom: 0.5rem;
        }

        .module-hero .hero-desc {
            font-size: 1.05rem;
            color: var(--text-secondary);
            max-width: 720px;
            margin: 0 auto;
            font-weight: 400;
        }

        /* 漏洞列表卡片 */
        .vuln-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 1.5rem;
        }

        .vuln-card {
            background: white;
            border-radius: 28px;
            padding: 1.5rem;
            box-shadow: 0 12px 24px -12px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.04);
            transition: all 0.25s ease-in-out;
            display: flex;
            flex-direction: column;
            height: 100%;
            position: relative;
        }

        .vuln-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 30px -12px rgba(0, 32, 64, 0.15);
            border-color: #cde3ef;
        }

        .vuln-card .vuln-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
            gap: 8px;
        }

        .vuln-card .vuln-num {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--accent);
            background: var(--accent-soft);
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .vuln-card .vuln-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0.25rem 0 0.5rem;
            line-height: 1.35;
        }

        .vuln-card .vuln-desc {
            font-size: 0.9rem;
            color: var(--text-secondary);
            line-height: 1.5;
            margin-bottom: 1rem;
            flex: 1;
        }

        .vuln-card .vuln-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            color: var(--accent);
            background: var(--accent-soft);
            padding: 8px 18px;
            border-radius: 40px;
            width: fit-content;
            transition: all 0.2s ease;
        }

        .vuln-card .vuln-link:hover {
            background: var(--accent);
            color: #fff;
        }

        .vuln-card .vuln-link:hover i {
            transform: translateX(4px);
        }

        .vuln-card .vuln-link i {
            transition: transform 0.2s;
        }

        /* 难度 badge */
        .badge-level {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 40px;
            letter-spacing: 0.3px;
        }

        .level-low { background: #d4edda; color: #155724; }
        .level-medium { background: #fff3cd; color: #856404; }
        .level-high { background: #ffe8e6; color: #b13e3e; }
        .level-info { background: #d1ecf1; color: #0c5460; }
        .level-dev { background: #e2e3e5; color: #383d41; }

        /* 关卡详情页的内容容器 */
        .level-content-wrap {
            margin-top: 1.5rem;
        }

        /* 覆盖 Bootstrap card 使其与整体风格统一 */
        .level-content-wrap .card {
            border-radius: 20px !important;
            border: 1px solid rgba(44, 125, 160, 0.12) !important;
            box-shadow: 0 8px 20px -10px rgba(0, 0, 0, 0.08) !important;
            overflow: hidden;
        }

        .level-content-wrap .card-header {
            background: linear-gradient(135deg, var(--accent-soft), #fff) !important;
            border-bottom: 1px solid rgba(44, 125, 160, 0.15) !important;
            padding: 0.9rem 1.2rem !important;
        }

        .level-content-wrap .card-header h5,
        .level-content-wrap .card-header h6 {
            font-weight: 700 !important;
            color: var(--primary) !important;
            margin: 0 !important;
        }

        .level-content-wrap .card-body {
            padding: 1.3rem 1.4rem !important;
            color: var(--text-primary);
            line-height: 1.55;
        }

        .level-content-wrap .card-body h5,
        .level-content-wrap .card-body h6 {
            font-weight: 700 !important;
            color: var(--primary) !important;
            margin: 1.2rem 0 0.75rem;
        }

        .level-content-wrap .card-body h5:first-child,
        .level-content-wrap .card-body h6:first-child {
            margin-top: 0;
        }

        /* alert 统一风格 */
        .level-content-wrap .alert {
            border-radius: 16px !important;
            border: 1px solid transparent !important;
            padding: 1rem 1.2rem !important;
            box-shadow: 0 4px 12px -6px rgba(0, 0, 0, 0.06);
        }

        .level-content-wrap .alert-success {
            background: linear-gradient(135deg, #e8f6ec, #d4edda) !important;
            border-color: #c3e6cb !important;
            color: #155724 !important;
        }

        .level-content-wrap .alert-danger {
            background: linear-gradient(135deg, #fdecea, #ffe8e6) !important;
            border-color: #f5c6cb !important;
            color: #721c24 !important;
        }

        .level-content-wrap .alert-info {
            background: linear-gradient(135deg, #e8f3f8, #d1ecf1) !important;
            border-color: #bee5eb !important;
            color: #0c5460 !important;
        }

        .level-content-wrap .alert-warning {
            background: linear-gradient(135deg, #fdf4dd, #fff3cd) !important;
            border-color: #ffeeba !important;
            color: #856404 !important;
        }

        /* pre 代码块 */
        .level-content-wrap pre {
            border-radius: 16px !important;
            border: 1px solid rgba(44, 125, 160, 0.18) !important;
            font-family: 'Fira Code', Consolas, Menlo, Monaco, monospace;
            font-size: 0.88rem !important;
            line-height: 1.6 !important;
        }

        .level-content-wrap pre.bg-dark {
            background: linear-gradient(135deg, #1e2a3a, #2c3e50) !important;
            color: #e4ecf7 !important;
            box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        /* 表单 */
        .level-content-wrap .form-control {
            border-radius: 14px !important;
            border: 1px solid #dce5ec !important;
            padding: 0.6rem 1rem !important;
            font-size: 0.9rem !important;
            transition: all 0.2s ease;
        }

        .level-content-wrap .form-control:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 3px rgba(44, 125, 160, 0.18) !important;
        }

        .level-content-wrap .btn-primary {
            background: var(--accent) !important;
            border-color: var(--accent) !important;
            border-radius: 40px !important;
            padding: 0.55rem 1.6rem !important;
            font-weight: 600 !important;
            box-shadow: 0 6px 16px -6px rgba(44, 125, 160, 0.5);
            transition: all 0.2s ease;
        }

        .level-content-wrap .btn-primary:hover {
            background: #1d6586 !important;
            border-color: #1d6586 !important;
            transform: translateY(-1px);
            box-shadow: 0 10px 20px -8px rgba(44, 125, 160, 0.55);
        }

        .level-content-wrap .btn-outline-primary {
            border-radius: 40px !important;
            padding: 0.45rem 1.3rem !important;
            border-color: var(--accent) !important;
            color: var(--accent) !important;
            background: transparent !important;
            transition: all 0.2s ease;
        }

        .level-content-wrap .btn-outline-primary:hover {
            background: var(--accent) !important;
            color: #fff !important;
            border-color: var(--accent) !important;
        }

        .level-content-wrap .btn-sm {
            font-size: 0.82rem !important;
        }

        /* 章节标题 */
        .level-content-wrap h5 {
            font-size: 1.15rem !important;
            font-weight: 700 !important;
            color: var(--primary) !important;
            margin: 1.5rem 0 0.8rem !important;
            padding-left: 14px;
            border-left: 4px solid var(--accent);
            line-height: 1.3;
        }

        .level-content-wrap h6 {
            font-size: 1rem !important;
            font-weight: 700 !important;
            color: var(--text-primary) !important;
            margin: 1rem 0 0.6rem !important;
        }

        .level-content-wrap p {
            color: var(--text-primary);
            line-height: 1.7;
            margin-bottom: 0.75rem;
        }

        .level-content-wrap ul,
        .level-content-wrap ol {
            padding-left: 1.4rem;
            margin-bottom: 1rem;
            line-height: 1.8;
            color: var(--text-primary);
        }

        /* 底部 */
        .module-footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(44, 125, 160, 0.15);
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        @media (max-width: 680px) {
            .module-hero {
                padding: 1.8rem 1rem 1.5rem;
            }
            .module-hero h1 {
                font-size: 1.75rem;
            }
            .vuln-grid {
                grid-template-columns: 1fr;
            }
            .module-nav {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="module-wrap">
        <div class="module-nav">
            <?php if (isset($content)): ?>
            <a href="index.php" class="back-home"><i class="fas fa-list"></i> 返回关卡列表</a>
            <div class="module-crumb"><i class="fas fa-shield-alt"></i> <?= $module_name ?></div>
            <?php else: ?>
            <a href="../index.php" class="back-home"><i class="fas fa-home"></i> 返回靶场首页</a>
            <div class="module-crumb"><i class="fas fa-shield-alt"></i> <?= $module_name ?> · ZhaoSec 靶场</div>
            <?php endif; ?>
        </div>

        <div class="module-hero">
            <div class="hero-icon"><?= $module_icon ?></div>
            <h1><?= $module_name ?></h1>
            <div class="hero-desc"><?= $module_desc ?></div>
        </div>

        <?php if (isset($content)): ?>
        <div class="level-content-wrap">
            <?= $content ?>
        </div>
        <?php else: ?>
        <div class="vuln-grid">
            <?php if (isset($vulns) && is_array($vulns)): ?>
            <?php $index = 1; foreach ($vulns as $vuln): ?>
            <?php
                $level_class = 'level-' . $vuln['level'];
                $level_map = ['low' => '初级', 'medium' => '中级', 'high' => '高级', 'info' => '信息', 'dev' => '开发中'];
                $level_text = $level_map[$vuln['level']] ?? $vuln['level'];
            ?>
            <div class="vuln-card">
                <div class="vuln-header">
                    <span class="vuln-num"><?= $index ?></span>
                    <span class="badge badge-level <?= $level_class ?>"><?= $level_text ?></span>
                </div>
                <div class="vuln-title"><?= htmlspecialchars($vuln['name']) ?></div>
                <div class="vuln-desc"><?= htmlspecialchars($vuln['desc']) ?></div>
                <a href="<?= htmlspecialchars($vuln['file']) ?>" class="vuln-link">
                    开始演练 <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <?php $index++; endforeach; ?>
            <?php else: ?>
            <div class="vuln-card" style="grid-column: 1 / -1; text-align: center;">
                <div class="vuln-title">当前模块暂无场景</div>
                <div class="vuln-desc">敬请期待后续更新</div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="module-footer">
            <i class="fas fa-graduation-cap"></i> ZhaoSec Web安全靶场 · 朝闻道  · 仅供安全学习与授权测试
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
