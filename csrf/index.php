<?php
$module_name = 'CSRF漏洞';
$module_icon = '🔄';
$module_desc = '跨站请求伪造(CSRF)允许攻击者诱导已认证的用户在不知情的情况下执行非预期操作。';
$vulns = [
    ['name' => 'CSRF基础与原理', 'desc' => '学习CSRF漏洞的基础知识和原理', 'file' => '00_csrf_basics.php', 'level' => 'low'],
    ['name' => 'GET型CSRF', 'desc' => 'GET请求修改数据', 'file' => '01_get_csrf.php', 'level' => 'low'],
    ['name' => 'POST型CSRF', 'desc' => 'POST请求修改数据', 'file' => '02_post_csrf.php', 'level' => 'medium'],
    ['name' => 'Token绕过', 'desc' => 'CSRF Token验证缺陷', 'file' => '03_token_bypass.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>