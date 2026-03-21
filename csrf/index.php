<?php
$module_name = 'CSRF漏洞';
$module_icon = '🔄';
$module_desc = '跨站请求伪造(CSRF)允许攻击者诱导用户执行非预期操作。';
$vulns = [
    ['name' => 'GET型CSRF', 'desc' => 'GET请求修改数据', 'file' => 'get_csrf.php', 'level' => 'low'],
    ['name' => 'POST型CSRF', 'desc' => 'POST请求修改数据', 'file' => 'post_csrf.php', 'level' => 'medium'],
    ['name' => 'JSON CSRF', 'desc' => 'JSON格式的CSRF', 'file' => 'json_csrf.php', 'level' => 'medium'],
    ['name' => 'Token绕过', 'desc' => 'CSRF Token验证缺陷', 'file' => 'token_bypass.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>
