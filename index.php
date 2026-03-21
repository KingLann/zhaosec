<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Web安全漏洞导航 | 漏洞学习与防御指南</title>
    <!-- Font Awesome 6 (免费图标库) -->
    <link rel="stylesheet" href="assets/css/all.min.css">
    <!-- 本地样式文件 -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>
            <i class="fas fa-shield-alt"></i> 
            Web安全漏洞导航
        </h1>
        <div class="subhead">OWASP Top 10 & 关键风险 · 学习 · 防御 · 渗透测试指南</div>
        <div class="description">
            <i class="fas fa-book-open"></i> 精选常见Web安全漏洞，提供权威学习资源
        </div>
    </div>

    <div class="controls">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="按漏洞名称或描述搜索... 例如：注入、XSS、SSRF">
        </div>
        <div class="stats" id="statsInfo">
            <i class="fas fa-database"></i> 共 <span id="totalCount">0</span> 个漏洞
        </div>
    </div>

    <div class="grid" id="vulnGrid">
        <!-- 卡片由js动态渲染，保证过滤性能与一致性 -->
        <div class="no-results" style="display: none;">加载中...</div>
    </div>

    <div class="footer">
        <i class="fas fa-graduation-cap"></i> 安全学习导航 · 数据基于 OWASP 及 PortSwigger Web Security Academy  · 
        <a href="#" target="_blank" rel="noopener noreferrer">了解最新漏洞动态</a>  |  教育用途，链接均为官方权威资源
    </div>
</div>

<script>
    // ----- 常见Web安全漏洞数据集 (名称, 描述, 图标, 风险等级, 学习链接, 漏洞场景数量)
    const vulnerabilities = [
        {
            name: "身份认证漏洞 (Auth)",
            description: "包括弱密码、暴力破解、会话管理缺陷、凭证存储不安全等认证相关漏洞，可能导致未授权访问。",
            icon: "fas fa-user-lock",
            severity: "high",
            severityLabel: "高危",
            link: "auth/index.php",
            linkText: "进入演练",
            count: 6
        },
        {
            name: "SQL 注入 (SQLi)",
            description: "攻击者通过插入恶意SQL语句，绕过认证、窃取数据库敏感信息或执行破坏性操作，是Web最危险的漏洞之一。",
            icon: "fas fa-database",
            severity: "critical",
            severityLabel: "严重",
            link: "sqli/index.php",
            linkText: "进入演练",
            count: 8
        },
        {
            name: "跨站脚本 (XSS)",
            description: "恶意脚本注入到可信网站，盗取Cookie、会话令牌或执行未授权操作，分为反射型、存储型与DOM型。",
            icon: "fas fa-code",
            severity: "high",
            severityLabel: "高危",
            link: "xss/index.php",
            linkText: "进入演练",
            count: 5
        },
        {
            name: "逻辑漏洞 (Logic)",
            description: "包括业务逻辑缺陷、越权访问、条件竞争、支付逻辑漏洞等，通常源于代码逻辑设计不当。",
            icon: "fas fa-brain",
            severity: "medium",
            severityLabel: "中危",
            link: "logic/index.php",
            linkText: "进入演练",
            count: 8
        },
        {
            name: "跨站请求伪造 (CSRF)",
            description: "诱使已认证用户执行非本意的状态更改请求，如修改密码、转账等，利用受害者身份发起恶意请求。",
            icon: "fas fa-user-secret",
            severity: "medium",
            severityLabel: "中危",
            link: "csrf/index.php",
            linkText: "进入演练",
            count: 4
        },
        {
            name: "服务端请求伪造 (SSRF)",
            description: "攻击者通过服务器端发起任意请求，可绕过防火墙访问内网服务、云元数据或执行端口扫描。",
            icon: "fas fa-server",
            severity: "high",
            severityLabel: "高危",
            link: "ssrf/index.php",
            linkText: "进入演练",
            count: 5
        },
        {
            name: "XML外部实体注入 (XXE)",
            description: "利用XML解析器对外部实体的处理，读取本地文件、进行内网探测或导致拒绝服务攻击。",
            icon: "fas fa-file-code",
            severity: "high",
            severityLabel: "高危",
            link: "xxe/index.php",
            linkText: "进入演练",
            count: 4
        },
        {
            name: "文件包含漏洞 (LFI/RFI)",
            description: "包括本地文件包含和远程文件包含，攻击者可通过构造特殊路径读取服务器文件或执行远程代码。",
            icon: "fas fa-file-invoice",
            severity: "high",
            severityLabel: "高危",
            link: "lfi/index.php",
            linkText: "进入演练",
            count: 4
        },
        {
            name: "不安全直接对象引用 (IDOR)",
            description: "通过修改对象ID（如URL参数）访问未授权的数据，属于访问控制失效的典型场景。",
            icon: "fas fa-lock-open",
            severity: "medium",
            severityLabel: "中危",
            link: "idor/index.php",
            linkText: "进入演练",
            count: 4
        },
        {
            name: "文件上传漏洞",
            description: "允许上传危险文件类型（webshell、木马），导致服务器被控制、网站被篡改或数据泄露。",
            icon: "fas fa-upload",
            severity: "critical",
            severityLabel: "严重",
            link: "upload/index.php",
            linkText: "进入演练",
            count: 6
        },
        {
            name: "命令注入 (Command Injection)",
            description: "在系统命令中注入恶意参数，可远程执行操作系统命令，完全控制服务器。",
            icon: "fas fa-terminal",
            severity: "critical",
            severityLabel: "严重",
            link: "rce/index.php",
            linkText: "进入演练",
            count: 4
        },
        {
            name: "不安全的反序列化",
            description: "反序列化不可信数据可导致远程代码执行、权限提升或重放攻击，危害极大。",
            icon: "fas fa-box-open",
            severity: "high",
            severityLabel: "高危",
            link: "unserialize/index.php",
            linkText: "进入演练",
            count: 4
        }
    ];

    // 辅助函数: 根据severity返回对应badge样式类
    function getSeverityClass(severity) {
        if (severity === 'critical') return 'critical';
        if (severity === 'high') return 'high';
        return 'medium';
    }

    // 渲染卡片 (根据过滤后的漏洞数组)
    function renderCards(filteredVulns) {
        const grid = document.getElementById('vulnGrid');
        const totalSpan = document.getElementById('totalCount');
        const statsInfo = document.getElementById('statsInfo');
        
        if (!filteredVulns.length) {
            grid.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-shield-alt"></i>
                    <h3>😕 未找到相关漏洞</h3>
                    <p>试试其他关键词，例如 “注入”、“XSS”、“反序列化” 或 “SSRF”</p>
                    <button id="resetSearchBtn" style="margin-top:12px; background:#2c7da0; border:none; color:white; padding:8px 22px; border-radius:40px; cursor:pointer; font-weight:500;">清除搜索</button>
                </div>
            `;
            totalSpan.innerText = '0';
            statsInfo.style.opacity = '0.8';
            const resetBtn = document.getElementById('resetSearchBtn');
            if (resetBtn) {
                resetBtn.addEventListener('click', () => {
                    const searchInput = document.getElementById('searchInput');
                    if (searchInput) searchInput.value = '';
                    filterAndRender();
                });
            }
            return;
        }
        
        // 正常渲染卡片
        const cardsHTML = filteredVulns.map(vul => {
            const severityClass = getSeverityClass(vul.severity);
            // 确保链接安全：target="_blank" + rel
            const countBadge = vul.count > 0 ? `<div class="count-badge">${vul.count}个场景</div>` : '';
            return `
                <div class="card">
                    <div class="card-content">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class="${vul.icon}"></i>
                            </div>
                            <div class="badge ${severityClass}">${vul.severityLabel}</div>
                            ${countBadge}
                        </div>
                        <div class="card-title">${escapeHtml(vul.name)}</div>
                        <div class="card-desc">${escapeHtml(vul.description)}</div>
                        <a href="${vul.link}" class="card-link" ${vul.link.startsWith('http') ? 'target="_blank" rel="noopener noreferrer"' : ''}>
                            ${escapeHtml(vul.linkText)} <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            `;
        }).join('');
        
        grid.innerHTML = cardsHTML;
        totalSpan.innerText = filteredVulns.length;
        statsInfo.style.opacity = '1';
    }
    
    // 简单的防XSS辅助函数 (避免动态内容注入)
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        }).replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g, function(c) {
            return c;
        });
    }
    
    // 过滤逻辑：根据搜索框文本（不区分大小写，匹配名称和描述）
    function filterAndRender() {
        const searchTerm = document.getElementById('searchInput').value.trim().toLowerCase();
        let filtered = vulnerabilities;
        if (searchTerm !== '') {
            filtered = vulnerabilities.filter(vul => 
                vul.name.toLowerCase().includes(searchTerm) || 
                vul.description.toLowerCase().includes(searchTerm)
            );
        }
        renderCards(filtered);
    }
    
    // 添加搜索监听 & 初始化页面
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', filterAndRender);
        }
        // 初始化渲染全部漏洞
        renderCards(vulnerabilities);
        
        // 附加功能：给清除搜索一个优雅的外联（已有无结果中的重置按钮，但额外加一个键盘支持）
        // 增加ESC清空搜索框的小交互
        if (searchInput) {
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    searchInput.value = '';
                    filterAndRender();
                    e.preventDefault();
                }
            });
        }
        
        // 动态显示漏洞总数悬停说明（可选）
        const statsSpan = document.getElementById('totalCount');
        if (statsSpan) {
            const tooltip = document.createElement('span');
            tooltip.style.cursor = 'help';
            tooltip.title = `涵盖 OWASP Top 10 及其他关键漏洞，点击卡片链接获得专业教程`;
        }
    });
</script>
</body>
</html>