<?php
$module_name = 'XXE漏洞';
$module_icon = '📄';
$module_desc = 'XML外部实体注入(XXE)是一种针对XML解析器的攻击，可导致文件读取、SSRF等。';
$vulns = [
    ['name' => '文件读取', 'desc' => '读取服务器本地文件', 'file' => 'file_read.php', 'level' => 'high'],
    ['name' => 'SSRF利用', 'desc' => '通过XXE进行SSRF', 'file' => 'xxe_ssrf.php', 'level' => 'high'],
    ['name' => '盲XXE', 'desc' => '无回显的XXE利用', 'file' => 'blind_xxe.php', 'level' => 'high'],
    ['name' => 'XXE RCE', 'desc' => '利用XXE执行命令', 'file' => 'xxe_rce.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>
