<?php
$module_name = 'PHP反序列化';
$module_icon = '📦';
$module_desc = 'PHP反序列化漏洞可导致代码执行，是PHP应用中常见的高危漏洞。';
$vulns = [
    ['name' => '反序列化基础与原理', 'desc' => '学习PHP反序列化的基础知识和原理', 'file' => '00_serialize_basics.php', 'level' => 'low'],
    ['name' => 'PHP实战靶场', 'desc' => '跳转到宿主机另一个容器的10005端口进行实战练习', 'file' => '01_php_target.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>