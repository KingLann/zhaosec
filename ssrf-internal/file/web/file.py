from flask import Flask, request, jsonify, render_template_string, Response
import json

app = Flask(__name__)

FILES_DATA = [
    {'path': '/etc/passwd', 'type': '系统文件', 'size': '2.1 KB'},
    {'path': '/etc/shadow', 'type': '敏感文件', 'size': '1.5 KB'},
    {'path': '/var/www/html/config.php', 'type': '配置文件', 'size': '3.2 KB'},
    {'path': '/var/www/html/.env', 'type': '环境配置', 'size': '1.8 KB'},
    {'path': '/root/.ssh/id_rsa', 'type': 'SSH私钥', 'size': '3.2 KB'},
    {'path': '/root/.bash_history', 'type': '历史记录', 'size': '5.6 KB'},
    {'path': '/flag.txt', 'type': 'Flag文件', 'size': '128 B'},
]

PASSWD_CONTENT = """root:x:0:0:root:/root:/bin/bash
daemon:x:1:1:daemon:/usr/sbin:/usr/sbin/nologin
bin:x:2:2:bin:/bin:/usr/sbin/nologin
sys:x:3:3:sys:/dev:/usr/sbin/nologin
www-data:x:33:33:www-data:/var/www:/usr/sbin/nologin
admin:x:1000:1000:Admin User:/home/admin:/bin/bash"""

ENV_CONTENT = """APP_NAME=Internal System
APP_ENV=production
APP_KEY=base64:random_app_key_2024_xyz
APP_DEBUG=true
APP_URL=http://internal.local

DB_CONNECTION=mysql
DB_HOST=192.168.100.20
DB_PORT=3306
DB_DATABASE=internal_db
DB_USERNAME=internal_admin
DB_PASSWORD=File_Server_Pass@2024!

REDIS_HOST=192.168.100.30
REDIS_PASSWORD=Redis_File_Pass#2024
REDIS_PORT=6379

MAIL_HOST=smtp.internal.local
MAIL_USERNAME=internal@internal.local
MAIL_PASSWORD=Mail_Pass_2024"""

FLAG_CONTENT = "FLAG{ssrf_file_read_success_2024}"

FILE_CONTENT_MAP = {
    '/etc/passwd': PASSWD_CONTENT,
    '/var/www/html/.env': ENV_CONTENT,
    '/flag.txt': FLAG_CONTENT,
}

METADATA_AWS = {
    '': {'available_paths': ['ami-id', 'hostname', 'local-ipv4', 'public-ipv4', 'instance-id', 'security-groups', 'iam/security-credentials', 'flag']},
    'latest': {'available_paths': ['ami-id', 'hostname', 'local-ipv4', 'public-ipv4', 'instance-id', 'security-groups', 'iam/security-credentials', 'flag']},
    'latest/meta-data/': {'available_paths': ['ami-id', 'hostname', 'local-ipv4', 'public-ipv4', 'instance-id', 'security-groups', 'iam/security-credentials', 'flag']},
    'meta-data/': {'available_paths': ['ami-id', 'hostname', 'local-ipv4', 'public-ipv4', 'instance-id', 'security-groups', 'iam/security-credentials', 'flag']},
    'ami-id': {'ami-id': 'ami-0c55b159cbfafe1f0'},
    'hostname': {'hostname': 'ip-192-168-100-10.ec2.internal'},
    'local-ipv4': {'local-ipv4': '192.168.100.10'},
    'public-ipv4': {'public-ipv4': '54.169.123.45'},
    'instance-id': {'instance-id': 'i-0abcd1234efgh5678'},
    'security-groups': {'security-groups': ['default', 'web-server', 'database']},
    'iam/security-credentials': {'iam/security-credentials': 'admin-role'},
    'iam/security-credentials/admin-role': {
        'Code': 'Success',
        'LastUpdated': '2024-01-15T10:30:00Z',
        'Type': 'AWS-HMAC',
        'AccessKeyId': 'AKIAIOSFODNN7EXAMPLE',
        'SecretAccessKey': 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY',
        'Token': 'AQoDYXdzEJr...<rest of token>',
        'Expiration': '2024-01-15T16:30:00Z'
    },
    'flag': {'flag': 'FLAG{ssrf_cloud_metadata_success_2024}'},
}

METADATA_ALIYUN = {
    '': {'available_paths': ['hostname', 'instance-id', 'region-id', 'zone-id', 'ram/security-credentials']},
    'ecs': {'available_paths': ['hostname', 'instance-id', 'region-id', 'zone-id', 'ram/security-credentials']},
    'metadata': {'available_paths': ['hostname', 'instance-id', 'region-id', 'zone-id', 'ram/security-credentials']},
    'ecs/metadata': {'available_paths': ['hostname', 'instance-id', 'region-id', 'zone-id', 'ram/security-credentials']},
    'metadata/v1': {'available_paths': ['hostname', 'instance-id', 'region-id', 'zone-id', 'ram/security-credentials']},
    'hostname': {'hostname': 'i-uf6abcd1234xyz.cn-hangzhou.internal'},
    'instance-id': {'instance-id': 'i-uf6abcd1234xyz567890'},
    'region-id': {'region-id': 'cn-hangzhou'},
    'zone-id': {'zone-id': 'cn-hangzhou-a'},
    'ram/security-credentials': {'ram/security-credentials': 'admin-role'},
    'ram/security-credentials/admin-role': {
        'AccessKeyId': 'STS.NUxxxxxxxxxx',
        'AccessKeySecret': 'xxxxxxxxxxxxxxxxxxxxxx',
        'Expiration': '2024-01-15T16:30:00Z',
        'SecurityToken': 'CAISxxxxxxxxxx',
        'LastUpdated': '2024-01-15T10:30:00Z',
        'Code': 'Success'
    },
}

METADATA_TENCENT = {
    '': {'available_paths': ['instance-id', 'instance-type', 'region', 'zone']},
    'cvm': {'available_paths': ['instance-id', 'instance-type', 'region', 'zone']},
    'metadata': {'available_paths': ['instance-id', 'instance-type', 'region', 'zone']},
    'cvm/metadata': {'available_paths': ['instance-id', 'instance-type', 'region', 'zone']},
    'instance-id': {'instance-id': 'ins-12345678'},
    'instance-type': {'instance-type': 'S1.SMALL1'},
    'region': {'region': 'ap-guangzhou'},
    'zone': {'zone': 'ap-guangzhou-1'},
}

HTML_TEMPLATE = """<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>内部文件服务器</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .header { background: #252526; padding: 20px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #569cd6; }
        .header h1 { color: #569cd6; }
        .card { background: #252526; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .card h2 { color: #569cd6; border-bottom: 2px solid #569cd6; padding-bottom: 10px; margin-bottom: 15px; }
        .file-list { list-style: none; }
        .file-list li { padding: 10px; border-bottom: 1px solid #3c3c3c; display: flex; justify-content: space-between; align-items: center; }
        .file-list li:hover { background: #3c3c3c; }
        .file-name { color: #4ec9b0; }
        .file-size { color: #ce9178; }
        .file-type { color: #dcdcaa; font-size: 0.9em; }
        .warning { background: #ff6b6b; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        pre { background: #1e1e1e; padding: 15px; border-radius: 5px; overflow-x: auto; color: #4ec9b0; }
        .secret { background: #3c3c3c; padding: 10px; border-radius: 5px; color: #f08; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📁 内部文件服务器</h1>
            <p>Internal File Server - 端口: 8080</p>
        </div>
        <div class="warning">⚠️ 警告：此服务仅限内网访问！如果您能看到此页面，说明存在SSRF漏洞！</div>
        <div class="card">
            <h2>文件列表</h2>
            <ul class="file-list">
                {% for file in files %}
                <li>
                    <div>
                        <span class="file-name">📄 {{ file.path }}</span>
                        <span class="file-type">[{{ file.type }}]</span>
                    </div>
                    <span class="file-size">{{ file.size }}</span>
                </li>
                {% endfor %}
            </ul>
        </div>
        <div class="card">
            <h2>文件内容预览</h2>
            <h3>/etc/passwd</h3>
            <pre>{{ passwd_content }}</pre>
            <h3>/var/www/html/.env</h3>
            <pre>{{ env_content }}</pre>
            <h3>/flag.txt</h3>
            <pre class="secret">{{ flag_content }}</pre>
        </div>
        <div class="card">
            <h2>利用方式</h2>
            <pre># 通过SSRF读取文件
# 使用file协议
file:///etc/passwd
file:///var/www/html/.env
file:///flag.txt

# 使用dict协议
dict://127.0.0.1:8080/file?path=/etc/passwd

# 使用gopher协议
gopher://127.0.0.1:8080/_GET%20/file?path=/flag.txt

# 读取PHP文件源码
file:///var/www/html/config.php</pre>
        </div>
        <div class="card">
            <h2>文件下载接口</h2>
            <pre># API接口
GET /download?file=/etc/passwd
GET /download?file=/flag.txt
GET /download?file=/var/www/html/.env

# 利用示例
http://127.0.0.1:8080/download?file=/flag.txt</pre>
        </div>
    </div>
</body>
</html>"""


@app.route('/')
def index():
    return render_template_string(HTML_TEMPLATE,
                                   files=FILES_DATA,
                                   passwd_content=PASSWD_CONTENT,
                                   env_content=ENV_CONTENT,
                                   flag_content=FLAG_CONTENT)


@app.route('/download')
def download():
    file_path = request.args.get('file', '')
    if file_path in FILE_CONTENT_MAP:
        return Response(FILE_CONTENT_MAP[file_path], mimetype='text/plain')
    return Response(f'文件未找到: {file_path}', status=404, mimetype='text/plain')


@app.route('/api')
@app.route('/api.php')
def api():
    path = request.path.replace('/api.php', '').replace('/api', '')
    path = path.strip('/')
    if not path:
        return jsonify({'error': 'Path is required'}), 400

    if path.startswith('latest/meta-data/') or path.startswith('meta-data/'):
        metadata_path = path.replace('latest/', '').replace('meta-data/', '').strip('/')
        if metadata_path in METADATA_AWS:
            return jsonify(METADATA_AWS[metadata_path])
        return jsonify({'error': 'Metadata path not found', 'path': metadata_path}), 404

    if path.startswith('ecs/metadata') or path.startswith('metadata/v1'):
        ali_path = path.replace('ecs/metadata/', '').replace('metadata/v1/', '').strip('/')
        if ali_path in METADATA_ALIYUN:
            return jsonify(METADATA_ALIYUN[ali_path])
        return jsonify({'error': 'Aliyun metadata path not found', 'path': ali_path}), 404

    if path.startswith('cvm/metadata'):
        tencent_path = path.replace('cvm/metadata/', '').strip('/')
        if tencent_path in METADATA_TENCENT:
            return jsonify(METADATA_TENCENT[tencent_path])
        return jsonify({'error': 'Tencent Cloud metadata path not found', 'path': tencent_path}), 404

    return jsonify({'error': 'Invalid metadata request', 'path': path}), 400


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=80, debug=True)
