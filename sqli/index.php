<?php
$module_name = 'SQL注入漏洞';
$module_icon = '💉';
$module_desc = 'SQL注入是最经典的Web漏洞，允许攻击者操作数据库获取敏感数据。';
$vulns = [
    ['name' => '联合注入', 'desc' => '使用UNION查询数据', 'file' => 'union_injection.php', 'level' => 'low'],
    ['name' => '报错注入', 'desc' => '利用错误信息获取数据', 'file' => 'error_injection.php', 'level' => 'medium'],
    ['name' => '布尔盲注', 'desc' => '通过真假判断提取数据', 'file' => 'bool_blind.php', 'level' => 'medium'],
    ['name' => '时间盲注', 'desc' => '通过响应时间判断', 'file' => 'time_blind.php', 'level' => 'medium'],
];
include '../template/module_template.php';
?>
