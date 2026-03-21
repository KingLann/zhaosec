<?php
$module_name = 'PHP反序列化';
$module_icon = '📦';
$module_desc = 'PHP反序列化漏洞可导致代码执行，是PHP应用中常见的高危漏洞。';
$vulns = [
    ['name' => '反序列化基础与原理', 'desc' => '学习PHP反序列化的基础知识和原理', 'file' => '00_serialize_basics.php', 'level' => 'low'],
    ['name' => '基础反序列化', 'desc' => 'unserialize使用不当', 'file' => '01_basic_unserialize.php', 'level' => 'medium'],
    ['name' => 'POP链', 'desc' => '构造利用链', 'file' => '02_pop_chain.php', 'level' => 'high'],
    ['name' => 'Phar反序列化', 'desc' => '利用phar协议', 'file' => '03_phar_unserialize.php', 'level' => 'high'],
    ['name' => 'Session反序列化', 'desc' => 'Session处理漏洞', 'file' => '04_session_unserialize.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>