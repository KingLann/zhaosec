<?php
$module_name = 'PHP反序列化';
$module_icon = '📦';
$module_desc = 'PHP反序列化漏洞可导致代码执行，是PHP应用中常见的高危漏洞。';
$vulns = [
    ['name' => '反序列化基础与原理', 'desc' => '学习PHP反序列化的基础知识和原理', 'file' => '00_serialize_basics.php', 'level' => 'low'],
    ['name' => 'PHP实战靶场', 'desc' => '跳转到宿主机另一个容器的10002端口进行实战练习', 'file' => '01_php_target.php', 'level' => 'high'],
    ['name' => '基本反序列化', 'desc' => '通过直接利用unserialize()修改对象属性，了解反序列化漏洞的基本原理', 'file' => '01_basic_deserialization.php', 'level' => 'low'],
    ['name' => '__toString利用', 'desc' => '通过对象转字符串触发文件读取，展示__toString()魔术方法的利用方式', 'file' => '03_tostring_exploit.php', 'level' => 'medium'],
    ['name' => 'POP链构造 - 简单(1)', 'desc' => '__destruct → __toString 两层POP链，通过echo触发eval代码执行', 'file' => 'pop1.php', 'level' => 'low'],
    ['name' => 'POP链构造 - 简单(2)', 'desc' => '__destruct → __invoke 两层POP链，通过函数调用触发eval代码执行', 'file' => 'pop2.php', 'level' => 'low'],
    ['name' => 'POP链构造 - 中等', 'desc' => '__destruct → __toString → __invoke 三层POP链', 'file' => 'pop3.php', 'level' => 'medium'],
    ['name' => 'POP链构造 - 困难', 'desc' => '__wakeup → __toString → __invoke 三层POP链，通过system命令执行', 'file' => 'pop4.php', 'level' => 'high'],
];
include '../template/module_template.php';
