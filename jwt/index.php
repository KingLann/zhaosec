<?php
$module_name = 'JWT安全漏洞';
$module_icon = '🔑';
$module_desc = 'JSON Web Token的安全缺陷，包括算法混淆、密钥泄露、令牌伪造等，可导致身份认证绕过。';
$vulns = [
    ['name' => 'JWT基础与原理', 'desc' => '学习JWT的基础知识和安全机制', 'file' => '00_jwt_basics.php', 'level' => 'info'],
    ['name' => '算法混淆攻击', 'desc' => '利用算法none或HS256/RS256混淆', 'file' => '01_algorithm_confusion.php', 'level' => 'medium'],
    ['name' => '密钥泄露攻击', 'desc' => '弱密钥破解与密钥泄露利用', 'file' => '02_key_leakage.php', 'level' => 'medium'],
    ['name' => '令牌伪造攻击', 'desc' => '伪造有效JWT令牌绕过认证', 'file' => '03_token_forgery.php', 'level' => 'high'],
    ['name' => 'JWT实战挑战', 'desc' => '综合JWT漏洞利用挑战', 'file' => '04_jwt_challenge.php', 'level' => 'high'],
];
include '../template/module_template.php';
?>
