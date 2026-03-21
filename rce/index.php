<?php
$module_name = '命令/代码执行漏洞';
$module_icon = '💻';
$module_desc = '远程命令执行(RCE)和代码执行漏洞允许攻击者在服务器上执行任意命令或代码，是最危险的漏洞类型之一。';
$vulns = [
    ['name' => '命令执行漏洞', 'desc' => '通过系统命令执行函数导致的命令注入', 'file' => 'command_exec.php', 'level' => 'high'],
    ['name' => '代码执行漏洞', 'desc' => '通过代码执行函数导致的代码注入', 'file' => 'code_exec.php', 'level' => 'high'],
    ['name' => '命令执行绕过演示', 'desc' => '不同难度的命令执行绕过技术演示', 'file' => 'bypass_demo.php', 'level' => 'high'],
    ['name' => '绕过技巧', 'desc' => '常见WAF绕过和过滤绕过方法', 'file' => 'bypass.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>