<?php
$module_name = '文件上传漏洞';
$module_icon = '📁';
$module_desc = '文件上传漏洞允许攻击者上传恶意文件到服务器，可能导致获取服务器权限。';
$vulns = [
    ['name' => '前端验证绕过', 'desc' => '仅前端JS验证文件类型', 'file' => 'frontend_bypass.php', 'level' => 'low'],
    ['name' => 'MIME类型绕过', 'desc' => '仅验证Content-Type', 'file' => 'mime_bypass.php', 'level' => 'low'],
    ['name' => '扩展名绕过', 'desc' => '黑名单不完整', 'file' => 'extension_bypass.php', 'level' => 'medium'],
    ['name' => '解析漏洞', 'desc' => '利用服务器解析规则', 'file' => 'parse_vuln.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>
