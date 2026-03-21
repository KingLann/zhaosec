<?php
$module_name = 'XSS跨站脚本';
$module_icon = '📜';
$module_desc = '跨站脚本攻击(XSS)是最常见的Web漏洞之一，允许攻击者在受害者浏览器中执行恶意脚本。';
$vulns = [
    ['name' => '反射型XSS', 'desc' => 'URL参数直接输出到页面', 'file' => 'reflect_xss.php', 'level' => 'low'],
    ['name' => '存储型XSS', 'desc' => '用户输入存储后显示', 'file' => 'stored_xss.php', 'level' => 'medium'],
    ['name' => 'DOM型XSS', 'desc' => '前端JavaScript动态渲染', 'file' => 'dom_xss.php', 'level' => 'medium'],
    ['name' => 'XSS绕过', 'desc' => '存在过滤但可绕过', 'file' => 'xss_bypass.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>
