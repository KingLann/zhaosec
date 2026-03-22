<?php
$module_name = '服务端模板注入 (SSTI)';
$module_icon = '🔧';
$module_desc = '服务端模板注入(SSTI)允许攻击者通过注入模板语法执行任意代码，可导致服务器完全被控制。';
$vulns = [
    ['name' => 'SSTI基础与原理', 'desc' => '学习SSTI漏洞的基础知识和原理', 'file' => '00_ssti_basics.php', 'level' => 'low'],
    ['name' => 'SSTI实战靶场', 'desc' => '跳转到真实的SSTI模板注入靶场环境', 'file' => '01_ssti_lab.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>
