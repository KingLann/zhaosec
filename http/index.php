<?php
// HTTP协议基础漏洞模块首页
$module_name = 'HTTP协议基础漏洞';
$module_desc = '学习HTTP协议相关的安全漏洞和防御方法';

// 漏洞场景配置
$vuln_scenarios = [
    [
        'id' => '00_http_basics',
        'name' => 'HTTP协议基础与原理',
        'desc' => '了解HTTP协议的基本原理、请求/响应结构和常见安全问题',
        'level' => 'beginner',
        'level_text' => '初级',
        'file' => '00_http_basics.php'
    ],
    [
        'id' => '01_http_request_smuggling',
        'name' => 'HTTP请求走私',
        'desc' => '通过构造特殊的HTTP请求，绕过安全设备或服务器的防护',
        'level' => 'intermediate',
        'level_text' => '中级',
        'file' => '01_http_request_smuggling.php'
    ],
    [
        'id' => '02_http_header_injection',
        'name' => 'HTTP头注入',
        'desc' => '通过注入恶意HTTP头信息，执行未授权操作或绕过安全限制',
        'level' => 'intermediate',
        'level_text' => '中级',
        'file' => '02_http_header_injection.php'
    ],
    [
        'id' => '03_cookie_security',
        'name' => 'Cookie安全问题',
        'desc' => '学习Cookie的安全配置和常见的Cookie相关漏洞',
        'level' => 'beginner',
        'level_text' => '初级',
        'file' => '03_cookie_security.php'
    ],
    [
        'id' => '04_http2_vulnerabilities',
        'name' => 'HTTP/2漏洞',
        'desc' => '了解HTTP/2协议的安全问题和潜在漏洞',
        'level' => 'advanced',
        'level_text' => '高级',
        'file' => '04_http2_vulnerabilities.php'
    ]
];

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🌐 HTTP协议基础漏洞</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>💡 模块说明：</strong><br>
                本模块专注于HTTP协议相关的安全漏洞，包括HTTP请求走私、HTTP头注入、Cookie安全等问题。<br>
                HTTP协议是Web通信的基础，理解其安全问题对于Web安全防御至关重要。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>📚 学习目标</h6>
                </div>
                <div class="card-body">
                    <ul>
                        <li>了解HTTP协议的基本原理和结构</li>
                        <li>掌握HTTP请求走私的原理和利用方法</li>
                        <li>学习HTTP头注入的安全风险</li>
                        <li>理解Cookie安全配置的重要性</li>
                        <li>了解HTTP/2协议的安全问题</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞场景列表</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">';

// 生成漏洞场景列表
foreach ($vuln_scenarios as $scenario) {
    $level_class = $scenario['level'] === 'beginner' ? 'badge-success' : ($scenario['level'] === 'intermediate' ? 'badge-warning' : 'badge-danger');
    $content .= '<a href="' . $scenario['file'] . '" class="list-group-item list-group-item-action flex-column align-items-start">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">' . $scenario['name'] . '</h6>
                            <span class="badge ' . $level_class . '">' . $scenario['level_text'] . '</span>
                        </div>
                        <p class="mb-1 text-muted small">' . $scenario['desc'] . '</p>
                    </a>';
}

$content .= '</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6>📖 相关资源</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-external-link-alt text-primary"></i> 
                            <a href="https://developer.mozilla.org/zh-CN/docs/Web/HTTP" target="_blank" rel="noopener noreferrer">MDN Web Docs - HTTP</a>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-external-link-alt text-primary"></i> 
                            <a href="https://portswigger.net/web-security/request-smuggling" target="_blank" rel="noopener noreferrer">PortSwigger - HTTP Request Smuggling</a>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-external-link-alt text-primary"></i> 
                            <a href="https://owasp.org/www-community/attacks/HTTP_Response_Splitting" target="_blank" rel="noopener noreferrer">OWASP - HTTP Response Splitting</a>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-external-link-alt text-primary"></i> 
                            <a href="https://http2.github.io/http2-spec/" target="_blank" rel="noopener noreferrer">HTTP/2 Specification</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>';

include '../template/module_template.php';
?>