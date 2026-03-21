<?php
$module_name = 'SQL注入漏洞';
$module_icon = '💉';
$module_desc = 'SQL注入（SQL Injection）是Web应用中最常见的高危漏洞之一。攻击者通过在用户输入中插入恶意SQL代码，从而操纵数据库查询，获取敏感信息或执行未授权操作。';
$vulns = [
    ['name' => 'SQL注入基础', 'desc' => '最基本的SQL注入场景，通过UNION、OR等操作获取数据', 'file' => '01_basic_injection.php', 'level' => 'low'],
    ['name' => '报错注入', 'desc' => '利用数据库错误信息获取敏感数据', 'file' => '02_error_injection.php', 'level' => 'medium'],
    ['name' => '布尔盲注', 'desc' => '通过页面返回的布尔值判断数据库信息', 'file' => '03_boolean_blind.php', 'level' => 'medium'],
    ['name' => '时间盲注', 'desc' => '通过响应时间差异判断数据库信息', 'file' => '04_time_blind.php', 'level' => 'high'],
    ['name' => 'SQL注入绕过', 'desc' => '绕过WAF、过滤等防护措施的技巧', 'file' => '05_bypass.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>
