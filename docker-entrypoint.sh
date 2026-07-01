#!/bin/bash

echo "=== 初始化容器环境 ==="

echo "1. 检查 MySQL 状态..."
if service mysql status &>/dev/null; then
    echo "   MySQL 已在运行，跳过启动"
else
    echo "   启动 MySQL..."
    service mysql start
    echo "   等待 MySQL 就绪..."
    sleep 3
fi

echo "2. 配置 MySQL root 密码..."
if mysql -u root -e "SELECT 1" &>/dev/null 2>&1; then
    echo "   MySQL root 无密码，设置密码为 root..."
    mysql -u root -e "ALTER USER 'root'@'localhost' IDENTIFIED BY 'root'; FLUSH PRIVILEGES;" 2>/dev/null || \
    mysql -u root -e "SET PASSWORD FOR 'root'@'localhost' = PASSWORD('root'); FLUSH PRIVILEGES;" 2>/dev/null || true
else
    echo "   MySQL root 已有密码或已配置..."
fi

echo "3. 创建必要的数据库..."
mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS zhao CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || \
mysql -u root -e "CREATE DATABASE IF NOT EXISTS zhao CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || true

echo "4. 确保 tmp 目录存在且可写..."
mkdir -p /var/www/html/tmp
chmod 777 /var/www/html/tmp

echo "5. 确保 upload/uploads 目录存在且可写（文件上传漏洞场景）..."
for upload_dir in /var/www/html/upload/uploads /app/upload/uploads; do
    mkdir -p "$upload_dir" 2>/dev/null
    chmod 777 "$upload_dir" 2>/dev/null && echo "   $upload_dir 权限已设置为 777" || true
done

echo "=== 初始化完成 ==="
echo "启动 Apache..."

exec "$@"
