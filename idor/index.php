<?php
$module_name = 'IDOR漏洞';
$module_icon = '🔓';
$module_desc = '不安全直接对象引用(IDOR)是一种常见的安全漏洞，当应用程序直接使用用户提供的输入来访问对象时，没有进行适当的访问控制检查。';
$vulns = [
    ['name' => 'IDOR基础与原理', 'desc' => '学习IDOR漏洞的基础知识和原理', 'file' => '00_idor_basics.php', 'level' => 'low'],
    ['name' => '基础IDOR', 'desc' => '通过修改用户ID访问其他用户信息', 'file' => '01_basic_idor.php', 'level' => 'low'],
];
include '../template/module_template.php';
?>