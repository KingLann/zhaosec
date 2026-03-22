<?php
$module_name = '实战演练与专项漏洞';
$module_icon = '🎯';
$module_desc = '综合Web安全实战演练，包含Web渗透测试、CTF竞赛等多种实战场景。';
$vulns = [
    ['name' => 'Web渗透测试实战', 'desc' => '真实的Web渗透测试靶场环境', 'file' => '01_web_pentest.php', 'level' => 'high'],
    ['name' => 'Web CTF实战', 'desc' => 'CTF竞赛实战靶场环境', 'file' => '02_web_ctf.php', 'level' => 'high'],
    ['name' => '高级渗透实战', 'desc' => '高级渗透测试实战靶场环境', 'file' => '03_advanced_pentest.php', 'level' => 'dev'],
    ['name' => 'SQL注入专项练习', 'desc' => 'SQL注入漏洞专项训练靶场', 'file' => '04_sqli_lab.php', 'level' => 'high'],
    ['name' => 'XSS漏洞专项练习', 'desc' => 'XSS漏洞专项训练靶场', 'file' => '05_xss_lab.php', 'level' => 'high'],
    ['name' => 'PHP反序列化专项练习', 'desc' => 'PHP反序列化漏洞专项训练靶场', 'file' => '06_unserialize_lab.php', 'level' => 'high'],
    ['name' => 'SSTI注入专项练习', 'desc' => 'SSTI注入漏洞专项训练靶场', 'file' => '07_ssti_lab.php', 'level' => 'high'],
    ['name' => 'CSRF漏洞专项练习', 'desc' => 'CSRF漏洞专项训练靶场', 'file' => '08_csrf_lab.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>
