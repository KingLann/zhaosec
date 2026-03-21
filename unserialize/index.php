<?php
$module_name = 'PHP反序列化';
$module_icon = '📦';
$module_desc = 'PHP反序列化漏洞可导致代码执行，是PHP应用中常见的高危漏洞。';
$vulns = [
    ['name' => '基础反序列化', 'desc' => 'unserialize使用不当', 'file' => 'basic_unserialize.php', 'level' => 'medium'],
    ['name' => 'POP链', 'desc' => '构造利用链', 'file' => 'pop_chain.php', 'level' => 'high'],
    ['name' => 'Phar反序列化', 'desc' => '利用phar协议', 'file' => 'phar_unserialize.php', 'level' => 'high'],
    ['name' => 'Session反序列化', 'desc' => 'Session处理漏洞', 'file' => 'session_unserialize.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>
