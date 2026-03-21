<?php
$module_name = 'IDOR漏洞';
$module_icon = '🔓';
$module_desc = '不安全直接对象引用(IDOR)是一种常见的安全漏洞，当应用程序直接使用用户提供的输入来访问对象时，没有进行适当的访问控制检查。';
$vulns = [
    ['name' => 'IDOR基础与原理', 'desc' => '学习IDOR漏洞的基础知识和原理', 'file' => '00_idor_basics.php', 'level' => 'low'],
    ['name' => '基础IDOR', 'desc' => '通过修改用户ID访问其他用户信息', 'file' => '01_basic_idor.php', 'level' => 'low'],
    ['name' => '订单信息IDOR', 'desc' => '通过修改订单ID访问其他用户订单信息', 'file' => '02_order_idor.php', 'level' => 'low'],
    ['name' => '文件下载IDOR', 'desc' => '通过修改文件ID下载其他用户文件', 'file' => '03_file_idor.php', 'level' => 'low'],
];
include '../template/module_template.php';
?>