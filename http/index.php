<?php
$module_name = 'HTTP协议基础漏洞';
$module_icon = '🌐';
$module_desc = 'HTTP协议是Web通信的基础，理解其安全问题对于Web安全防御至关重要。本模块涵盖HTTP请求走私、HTTP头注入、Cookie安全等基于HTTP协议的安全问题。';
$vulns = [
    ['name' => 'HTTP协议基础与原理', 'desc' => '了解HTTP协议的基本原理、请求/响应结构和常见安全问题', 'file' => '00_http_basics.php', 'level' => 'low'],
    ['name' => 'HTTP请求走私', 'desc' => '通过构造特殊的HTTP请求，绕过安全设备或服务器的防护', 'file' => '01_http_request_smuggling.php', 'level' => 'medium'],
    ['name' => 'HTTP头注入', 'desc' => '通过注入恶意HTTP头信息，执行未授权操作或绕过安全限制', 'file' => '02_http_header_injection.php', 'level' => 'medium'],
    ['name' => 'Cookie安全问题', 'desc' => '学习Cookie的安全配置和常见的Cookie相关漏洞', 'file' => '03_cookie_security.php', 'level' => 'low'],
];
include '../template/module_template.php';
?>