<?php
$module_name = 'PHP反序列化';
$module_icon = '📦';
$module_desc = 'PHP反序列化漏洞可导致代码执行，是PHP应用中常见的高危漏洞。';
$vulns = [
    ['name' => '反序列化基础与原理', 'desc' => '学习PHP反序列化的基础知识和原理', 'file' => '00_serialize_basics.php', 'level' => 'low'],
    ['name' => 'PHP实战靶场', 'desc' => '跳转到宿主机另一个容器的10002端口进行实战练习', 'file' => '01_php_target.php', 'level' => 'high'],
    ['name' => '基本反序列化', 'desc' => '通过直接利用unserialize()修改对象属性，了解反序列化漏洞的基本原理', 'file' => '01_basic_deserialization.php', 'level' => 'low'],
    ['name' => '__wakeup绕过', 'desc' => '利用CVE-2016-7124漏洞，通过修改属性数量绕过__wakeup()安全检查', 'file' => '02_wakeup_bypass.php', 'level' => 'medium'],
    ['name' => '__toString利用', 'desc' => '通过对象转字符串触发文件读取，展示__toString()魔术方法的利用方式', 'file' => '03_tostring_exploit.php', 'level' => 'medium'],
    ['name' => 'POP链构造 - 简单', 'desc' => '通过单个魔术方法调用形成的入门级POP链', 'file' => '04_pop_chain_simple.php', 'level' => 'low'],
    ['name' => 'POP链构造 - 中等', 'desc' => '涉及两个类的POP链，通过对象属性传递形成方法调用链', 'file' => '05_pop_chain_medium.php', 'level' => 'medium'],
    ['name' => 'POP链构造 - 困难', 'desc' => '涉及三个类的复杂POP链，通过多层对象嵌套实现命令执行', 'file' => '06_pop_chain_hard.php', 'level' => 'high'],
    ['name' => 'Phar反序列化', 'desc' => '利用phar://协议触发Phar文件元数据的自动反序列化', 'file' => '07_phar_deserialization.php', 'level' => 'high'],
    ['name' => 'Session序列化机制缺陷', 'desc' => '利用Session处理器差异，通过php_serialize与php处理器混用注入恶意对象', 'file' => '08_session_serialization.php', 'level' => 'high'],
];
include '../template/module_template.php';
