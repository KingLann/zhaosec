<?php
$module_name = '身份认证漏洞';
$module_icon = '🔐';
$module_desc = '身份认证是Web应用安全的第一道防线，本模块包含多种身份认证相关的漏洞场景。';
$vulns = [
    ['name' => '弱密码漏洞', 'desc' => '系统存在弱密码用户，可尝试暴力破解', 'file' => 'weak_password.php', 'level' => 'low'],
    ['name' => '暴力破解', 'desc' => '登录接口无验证码/频率限制', 'file' => 'brute_force.php', 'level' => 'medium'],
    ['name' => '认证绕过', 'desc' => '通过修改参数绕过身份认证', 'file' => 'auth_bypass.php', 'level' => 'medium'],
    ['name' => '会话固定', 'desc' => '登录后Session ID不变', 'file' => 'session_fixation.php', 'level' => 'medium'],
];
include '../template/module_template.php';
?>
