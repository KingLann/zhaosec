<?php
$module_name = 'SSRF漏洞';
$module_icon = '🌐';
$module_desc = '服务端请求伪造(SSRF)允许攻击者以服务器为跳板访问内部资源。';
$vulns = [
    ['name' => '基础SSRF', 'desc' => 'URL参数可控', 'file' => '01_basic_ssrf.php', 'level' => 'medium'],
    ['name' => '内网探测', 'desc' => '探测内网服务和端口', 'file' => '02_internal_scan.php', 'level' => 'high'],
    ['name' => '云元数据', 'desc' => '获取云服务器凭证', 'file' => '03_cloud_metadata.php', 'level' => 'high'],
    ['name' => '协议利用', 'desc' => '利用各种协议', 'file' => '04_protocol_ssrf.php', 'level' => 'high'],
    ['name' => 'SSRF绕过', 'desc' => '绕过SSRF限制的方法', 'file' => '05_ssrf_bypass.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>
