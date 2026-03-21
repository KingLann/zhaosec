<?php
$module_name = '命令/代码执行';
$module_icon = '⚡';
$module_desc = '命令执行和代码执行是最危险的漏洞类型，可直接获取服务器控制权。';
$vulns = [
    ['name' => '命令注入', 'desc' => '系统命令拼接执行', 'file' => 'command_injection.php', 'level' => 'high'],
    ['name' => '代码注入', 'desc' => '动态代码执行', 'file' => 'code_injection.php', 'level' => 'high'],
    ['name' => 'Eval注入', 'desc' => 'eval函数使用不当', 'file' => 'eval_injection.php', 'level' => 'high'],
    ['name' => '反引号执行', 'desc' => '反引号命令执行', 'file' => 'backtick_exec.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>
