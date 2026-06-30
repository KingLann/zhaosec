<?php
// SQL注入模块 - 数据库公共配置
// 支持 Docker 容器网络、本地 LAMP 环境和各种密码配置

$db_name = 'zhao';

function getDbConnection() {
    global $db_name;
    
    // 连接配置列表（按优先级排序）
    $configs = [
        // 1. 本地连接 - 有密码（LAMP 环境）
        ['host' => '127.0.0.1', 'user' => 'root', 'pass' => 'root'],
        // 2. 本地连接 - 无密码（基础镜像默认）
        ['host' => '127.0.0.1', 'user' => 'root', 'pass' => ''],
        // 3. 本地连接 - 密码 123456（phpStudy 环境）
        ['host' => '127.0.0.1', 'user' => 'root', 'pass' => '123456'],
        // 4. Docker 网络 - 服务名
        ['host' => 'db', 'user' => 'root', 'pass' => 'root'],
        // 5. Docker 网络 - 容器 IP
        ['host' => '192.168.100.16', 'user' => 'root', 'pass' => 'root'],
        // 6. 主机名 localhost
        ['host' => 'localhost', 'user' => 'root', 'pass' => 'root'],
        ['host' => 'localhost', 'user' => 'root', 'pass' => ''],
    ];
    
    $lastError = '';
    
    foreach ($configs as $config) {
        $conn = @new mysqli($config['host'], $config['user'], $config['pass'], $db_name);
        
        if (!$conn->connect_error) {
            // 连接成功，确保数据库存在
            $conn->query("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $conn->select_db($db_name);
            return $conn;
        } else {
            $lastError = $conn->connect_error;
        }
    }
    
    // 所有配置都失败
    die("数据库连接失败！请检查 MySQL 是否启动。最后错误: " . $lastError);
}
