<?php
$module_name = '综合Web安全实战演练';
$module_icon = '🎯';
$module_desc = '综合Web安全实战演练，包含Web渗透测试、CTF竞赛等多种实战场景。';
$vulns = [
    ['name' => 'Web渗透测试实战', 'desc' => '真实的Web渗透测试靶场环境', 'file' => '01_web_pentest.php', 'level' => 'high'],
    ['name' => 'Web CTF实战', 'desc' => 'CTF竞赛实战靶场环境', 'file' => '02_web_ctf.php', 'level' => 'high'],
    ['name' => '高级渗透实战', 'desc' => '高级渗透测试实战靶场环境', 'file' => '03_advanced_pentest.php', 'level' => 'dev'],
];
include '../template/module_template.php';
?>
