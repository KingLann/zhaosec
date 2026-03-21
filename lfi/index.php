<?php
$module_name = '文件包含漏洞';
$module_icon = '📂';
$module_desc = '文件包含漏洞允许攻击者包含并执行任意文件，可能导致代码执行。';
$vulns = [
    ['name' => '本地文件包含', 'desc' => '包含本地文件', 'file' => 'lfi.php', 'level' => 'medium'],
    ['name' => '远程文件包含', 'desc' => '包含远程文件', 'file' => 'rfi.php', 'level' => 'high'],
    ['name' => '伪协议利用', 'desc' => '使用PHP伪协议', 'file' => 'php_wrapper.php', 'level' => 'high'],
    ['name' => '日志包含', 'desc' => '包含日志文件GetShell', 'file' => 'log_include.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>
