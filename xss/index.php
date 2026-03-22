<?php
$module_name = 'XSS跨站脚本漏洞';
$module_icon = '💉';
$module_desc = 'XSS（Cross-Site Scripting）跨站脚本攻击，是Web应用中最常见的漏洞之一。攻击者通过注入恶意脚本，可在用户浏览器中执行任意代码。';
$vulns = [
    ['name' => 'XSS漏洞基础', 'desc' => '学习XSS跨站脚本漏洞的基本概念、类型、原理和防御方法', 'file' => '00_xss_basics.php', 'level' => 'info'],
    ['name' => '反射型XSS', 'desc' => '恶意脚本通过URL参数传入，服务器立即返回并执行', 'file' => '01_reflected.php', 'level' => 'low'],
    ['name' => '存储型XSS', 'desc' => '恶意脚本存储在服务器，所有访问者都会触发', 'file' => '02_stored.php', 'level' => 'medium'],
    ['name' => 'DOM型XSS', 'desc' => '通过修改页面DOM结构触发，不经过服务器', 'file' => '03_dom.php', 'level' => 'medium'],
    ['name' => 'XSS绕过', 'desc' => '使用各种技巧绕过前端和后端过滤', 'file' => '04_bypass.php', 'level' => 'high'],
    ['name' => 'Cookie窃取伪造登录', 'desc' => '通过XSS窃取用户Cookie，实现会话劫持', 'file' => '05_cookie_steal.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>
