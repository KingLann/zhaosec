<?php
$module_name = '身份认证漏洞';
$module_icon = '🔐';
$module_desc = '身份认证是Web应用安全的第一道防线，本模块包含多种身份认证相关的漏洞场景。';
$vulns = [
    ['name' => '身份认证基础', 'desc' => '学习身份认证的基本概念、常见机制和安全原理', 'file' => '00_auth_basics.php', 'level' => 'info'],
    ['name' => '明文传输简单密码爆破', 'desc' => '密码以明文形式传输，无验证码和频率限制', 'file' => '01_plaintext_brute.php', 'level' => 'low'],
    ['name' => 'Base64编码爆破', 'desc' => '密码使用Base64编码传输，可解码后暴力破解', 'file' => '02_base64_brute.php', 'level' => 'low'],
    ['name' => '前端AES加密爆破', 'desc' => '密钥硬编码在前端，可获取密钥后加密爆破', 'file' => '03_aes_brute.php', 'level' => 'medium'],
    ['name' => '账户枚举', 'desc' => '根据不同错误信息判断用户是否存在', 'file' => '04_account_enum.php', 'level' => 'medium'],
    ['name' => '未授权访问', 'desc' => '无需登录即可访问敏感数据', 'file' => '05_unauthorized.php', 'level' => 'high'],
    ['name' => 'JWT弱密钥漏洞', 'desc' => 'JWT使用弱密钥签名，可伪造任意token', 'file' => '06_jwt_weak_key.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>
