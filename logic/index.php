<?php
$module_name = '逻辑漏洞';
$module_icon = '🧩';
$module_desc = '逻辑漏洞是指业务逻辑设计或实现上的缺陷，往往难以通过常规安全测试发现。';
$vulns = [
    ['name' => '水平越权', 'desc' => '访问其他用户的数据', 'file' => 'horizontal_privilege.php', 'level' => 'medium'],
    ['name' => '垂直越权', 'desc' => '普通用户访问管理员功能', 'file' => 'vertical_privilege.php', 'level' => 'high'],
    ['name' => '条件竞争', 'desc' => '并发操作导致数据不一致', 'file' => 'race_condition.php', 'level' => 'high'],
    ['name' => '支付逻辑漏洞', 'desc' => '金额篡改、数量篡改等', 'file' => 'payment_logic.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>
