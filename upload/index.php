<?php
$module_name = '文件上传漏洞';
$module_icon = '📁';
$module_desc = '文件上传漏洞允许攻击者上传恶意文件到服务器，可能导致获取服务器权限。';
$vulns = [
    ['name' => '01. 无过滤上传', 'desc' => '完全没有任何过滤的文件上传', 'file' => 'no_filter.php', 'level' => 'low'],
    ['name' => '02. 前端验证绕过', 'desc' => '仅前端JS验证文件类型', 'file' => 'frontend_bypass.php', 'level' => 'low'],
    ['name' => '03. MIME类型绕过', 'desc' => '仅验证Content-Type', 'file' => 'mime_bypass.php', 'level' => 'low'],
    ['name' => '04. 扩展名绕过', 'desc' => '黑名单不完整', 'file' => 'extension_bypass.php', 'level' => 'medium'],
    ['name' => '05. 图片马+文件包含', 'desc' => '上传图片马并通过文件包含执行', 'file' => 'image_include.php', 'level' => 'high'],
    ['name' => '06. 解析漏洞', 'desc' => '文件解析漏洞学习资料', 'file' => 'parse_vuln.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>
