<?php
$module_name = '逻辑漏洞';
$module_icon = '🧩';
$module_desc = '逻辑漏洞是指业务逻辑设计或实现上的缺陷，往往难以通过常规安全测试发现。本模块涵盖支付交易、权限控制、业务流程、认证会话等多种类型的逻辑漏洞。';
$vulns = [
    ['name' => '数据库初始化', 'desc' => '初始化逻辑漏洞测试所需的数据库表和模拟数据', 'file' => 'init_db.php', 'level' => 'info'],
    ['name' => '价格篡改', 'desc' => '通过修改前端价格参数以低价购买商品', 'file' => '01_price_tampering.php', 'level' => 'high'],
    ['name' => '支付状态绕过', 'desc' => '直接修改订单状态绕过支付流程', 'file' => '02_payment_bypass.php', 'level' => 'high'],
    ['name' => '水平越权', 'desc' => '访问其他同权限用户的数据', 'file' => '04_horizontal_privilege.php', 'level' => 'medium'],
    ['name' => '垂直越权', 'desc' => '普通用户访问管理员功能', 'file' => '05_vertical_privilege.php', 'level' => 'high'],
    ['name' => '业务流程绕过', 'desc' => '跳过必要的业务步骤完成操作', 'file' => '06_step_skip.php', 'level' => 'medium'],
    ['name' => '密码重置缺陷', 'desc' => '利用密码重置功能缺陷重置任意用户密码', 'file' => '07_password_reset.php', 'level' => 'high'],
    ['name' => '条件竞争', 'desc' => '利用并发操作导致超卖或重复领取', 'file' => '08_race_condition.php', 'level' => 'high'],
    ['name' => '积分滥刷', 'desc' => '利用业务逻辑缺陷刷取积分', 'file' => '09_points_abuse.php', 'level' => 'medium'],
    ['name' => '短信轰炸', 'desc' => '利用短信接口发送大量短信', 'file' => '10_sms_bombing.php', 'level' => 'low'],
];
include '../template/module_template.php';
?>
