# ZhaoSec Web安全漏洞靶场

<div align="center">

![ZhaoSec](https://img.shields.io/badge/ZhaoSec-Web安全靶场-blue)
![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1)
![License](https://img.shields.io/badge/License-教育用途-green)

**朝闻道 - Web安全漏洞综合靶场**

[功能特性](#功能特性) · [快速开始](#快速开始) · [漏洞列表](#漏洞列表) · [Docker部署](#docker部署)

</div>

---

## 项目简介

ZhaoSec Web安全漏洞靶场是一个综合性的Web安全学习平台，涵盖OWASP Top 10及其他常见Web安全漏洞。靶场提供真实的漏洞场景、基础知识讲解和实战演练，帮助安全爱好者、渗透测试人员和开发者深入理解Web安全漏洞的原理、利用方法和防御措施。

### 项目特色

- 🎯 **全面覆盖**：涵盖16大类Web安全漏洞，共80+个实战场景
- 📚 **系统学习**：每个漏洞模块包含基础知识、原理讲解和防御方法
- 🚀 **实战演练**：提供从入门到高级的多种难度级别
- 🐳 **容器化部署**：支持Docker一键部署，环境隔离
- 📖 **详细文档**：每个场景都有清晰的说明和提示
- 🔧 **真实环境**：模拟真实业务场景，贴近实际渗透测试

---

## 功能特性

### 漏洞类型

| 漏洞类型 | 场景数量 | 风险等级 |
|---------|---------|---------|
| 身份认证漏洞 (Auth) | 7 | 高危 |
| SQL 注入 (SQLi) | 8 | 严重 |
| 跨站脚本 (XSS) | 6 | 高危 |
| 逻辑漏洞 (Logic) | 11 | 中危 |
| 跨站请求伪造 (CSRF) | 3 | 中危 |
| 服务端请求伪造 (SSRF) | 6 | 高危 |
| XML外部实体注入 (XXE) | 5 | 高危 |
| 文件包含漏洞 (LFI/RFI) | 5 | 高危 |
| 不安全直接对象引用 (IDOR) | 3 | 中危 |
| 文件上传漏洞 | 7 | 严重 |
| 命令/代码注入漏洞 | 4 | 严重 |
| 不安全的反序列化 | 2 | 高危 |
| HTTP协议基础漏洞 | 5 | 中危 |
| 服务端模板注入 (SSTI) | 2 | 严重 |
| JWT安全漏洞 | 5 | 高危 |
| Web实战 & 专项漏洞 | 8 | 实战 |

### 学习模块

- **基础知识**：每个漏洞类型都有详细的基础知识讲解
- **漏洞场景**：从简单到复杂的多级别漏洞场景
- **实战靶场**：专项漏洞练习和综合实战环境
- **防御指南**：提供漏洞修复和防御的最佳实践

---

## 快速开始

### 环境要求

- PHP 7.4 或更高版本
- MySQL 5.7 或更高版本
- Apache 或 Nginx Web服务器
- 推荐使用 PHPStudy、XAMPP 或 Docker

### 本地部署

1. **下载项目**
```bash
git clone https://github.com/yourusername/zhaosec.git
cd zhaosec
```

2. **配置Web服务器**
   - 将项目目录设置为Web根目录
   - 确保PHP和MySQL服务正常运行

3. **初始化数据库**
   - 访问 `http://localhost/sqli/init_db.php` 初始化SQL注入数据库
   - 访问 `http://localhost/logic/init_db.php` 初始化逻辑漏洞数据库

4. **访问靶场**
   - 打开浏览器访问 `http://localhost/`
   - 开始你的Web安全学习之旅

---

## Docker部署

### 使用Docker Compose（推荐）

1. **构建并启动容器**
```bash
cd zhaosec
docker-compose up -d
```

2. **访问靶场**
   - 主靶场：`http://localhost`
   - PHP反序列化专项：`http://localhost:10002`
   - SSTI专项：`http://localhost:10003`
   - Web渗透实战：`http://localhost:10004`
   - Web CTF综合实战：`http://localhost:10005`
   - SQL注入专项：`http://localhost:10006`
   - XSS专项：`http://localhost:10007`
   - CSRF专项：`http://localhost:10008`，`http://localhost:10009`
3. **停止容器**
```bash
docker-compose down
```

---

## 漏洞列表

### 1. 身份认证漏洞 (Auth)

- 身份认证基础
- 明文传输简单密码爆破
- Base64编码爆破
- 前端AES加密爆破
- 账户枚举
- 未授权访问
- JWT弱密钥漏洞

### 2. SQL 注入 (SQLi)

- 数据库初始化
- SQL注入基础
- 联合查询注入
- 万能密码注入
- 报错注入
- 布尔盲注
- 时间盲注
- SQL注入绕过

### 3. 跨站脚本 (XSS)

- XSS漏洞基础
- 反射型XSS
- 存储型XSS
- DOM型XSS
- XSS绕过
- Cookie窃取伪造登录

### 4. 逻辑漏洞 (Logic)

- 逻辑漏洞基础
- 数据库初始化
- 价格篡改
- 支付状态绕过
- 水平越权
- 垂直越权
- 业务流程绕过
- 密码重置缺陷
- 条件竞争
- 积分滥刷
- 短信轰炸

### 5. 跨站请求伪造 (CSRF)

- CSRF基础与原理
- GET型CSRF
- POST型CSRF

### 6. 服务端请求伪造 (SSRF)

- SSRF漏洞基础
- 基础SSRF
- 内网探测
- 云元数据
- 协议利用
- SSRF绕过

### 7. XML外部实体注入 (XXE)

- XML基础与XXE原理
- 文件读取
- SSRF利用
- 盲XXE
- XXE RCE

### 8. 文件包含漏洞 (LFI/RFI)

- 文件包含漏洞基础
- 本地文件包含
- 远程文件包含
- 伪协议利用
- 日志包含

### 9. 不安全直接对象引用 (IDOR)

- IDOR基础与原理
- 基础IDOR
- 不安全的文件下载

### 10. 文件上传漏洞

- 文件上传漏洞基础
- 无过滤上传
- 前端验证绕过
- MIME类型绕过
- 扩展名绕过
- 图片马+文件包含
- 解析漏洞

### 11. 命令/代码注入漏洞

- 命令执行漏洞
- 代码执行漏洞
- 命令执行绕过演示
- 绕过技巧

### 12. 不安全的反序列化

- 反序列化基础与原理
- PHP实战靶场

### 13. HTTP协议基础漏洞

- HTTP协议基础与原理
- HTTP请求走私
- HTTP头注入
- Cookie安全问题
- HTTP头伪造挑战

### 14. 服务端模板注入 (SSTI)

- SSTI基础与原理
- SSTI实战靶场

### 15. JWT安全漏洞

- JWT基础与原理
- 算法混淆攻击
- 密钥泄露攻击
- 令牌伪造攻击
- JWT实战挑战

### 16. Web实战 & 专项漏洞

- Web渗透测试实战
- Web CTF实战
- 高级渗透实战
- SQL注入专项练习
- XSS漏洞专项练习
- PHP反序列化专项练习
- SSTI注入专项练习
- CSRF漏洞专项练习

---

## 项目结构

```
zhaosec/
├── auth/                    # 身份认证漏洞
├── sqli/                    # SQL注入
├── xss/                     # 跨站脚本
├── logic/                   # 逻辑漏洞
├── csrf/                    # 跨站请求伪造
├── ssrf/                    # 服务端请求伪造
├── xxe/                     # XML外部实体注入
├── lfi/                     # 文件包含漏洞
├── idor/                    # 不安全直接对象引用
├── upload/                  # 文件上传漏洞
├── rce/                     # 命令/代码注入
├── unserialize/             # 反序列化
├── http/                    # HTTP协议基础漏洞
├── ssti/                    # 服务端模板注入
├── jwt/                     # JWT安全漏洞
├── weblabs/                 # Web实战 & 专项漏洞
├── assets/                  # 静态资源
│   ├── css/
│   └── js/
├── template/                # 模板文件
├── Dockerfile               # Docker镜像构建文件
├── docker-compose.yml       # Docker Compose配置
├── docker-entrypoint.sh     # Docker容器启动脚本
└── index.php                # 主页
```

---

## 使用说明

### 学习路径建议

1. **初学者路径**
   - 从基础知识模块开始，了解每种漏洞的基本概念
   - 练习低难度场景，掌握基本利用方法
   - 学习防御措施，理解如何修复漏洞

2. **进阶路径**
   - 挑战中高难度场景，掌握高级利用技巧
   - 学习绕过方法，了解WAF绕过技术
   - 参与实战靶场，提升综合渗透能力

3. **专项训练**
   - 选择感兴趣的漏洞类型进行深入练习
   - 完成专项练习场景，提升特定技能
   - 参与CTF实战，锻炼实战能力

### 注意事项

⚠️ **重要提示**

- 本靶场仅用于**教育目的**和**授权测试**
- 请勿在未经授权的系统上使用所学技术
- 建议在隔离环境中进行练习
- 部分场景可能需要特殊工具（如Burp Suite、SQLMap等）
- 命令执行场景请谨慎操作，避免造成系统损坏

### 常见问题

**Q: 数据库连接失败怎么办？**
A: 请确保MySQL服务已启动，并检查数据库配置是否正确。

**Q: 文件上传失败怎么办？**
A: 请检查uploads目录权限，确保Web服务器有写入权限。

**Q: Docker容器无法访问怎么办？**
A: 请检查端口映射是否正确，确保防火墙未阻止相应端口。

---

## 贡献指南

欢迎提交Issue和Pull Request来改进这个项目。

### 贡献方式

1. Fork本仓库
2. 创建特性分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 开启Pull Request

---

## 许可证

本项目仅用于教育目的。请勿将本靶场用于任何非法活动。

---

## 免责声明

本靶场提供的所有漏洞场景和代码仅供学习和研究使用。使用者应当：
- 仅在授权环境中使用本靶场
- 遵守当地法律法规
- 对自己的行为负责
- 不得将所学技术用于非法用途

作者不对因使用本靶场造成的任何损失或法律责任负责。

---

<div align="center">

**朝闻道，夕死可矣。**

Made with ❤️ for Web Security Education

</div>
