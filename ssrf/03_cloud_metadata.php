<?php
// 云元数据SSRF漏洞
$module_name = '云元数据';
$module_icon = '☁️';
$module_desc = '通过SSRF漏洞获取云服务器元数据，可能导致凭证泄露。';

// 漏洞代码
$result = '';
$error = '';

if (isset($_GET['metadata'])) {
    $metadata = $_GET['metadata'];
    
    // 漏洞：直接使用用户输入的元数据路径，没有过滤
    try {
        // 常见云服务的元数据地址
        $metadata_endpoints = [
            'aws' => 'http://169.254.169.254/latest/meta-data/' . $metadata,
            'azure' => 'http://169.254.169.254/metadata/instance?api-version=2021-02-01',
            'gcp' => 'http://metadata.google.internal/computeMetadata/v1/' . $metadata
        ];
        
        $endpoint = $metadata_endpoints['aws']; // 默认使用AWS
        if (strpos($metadata, 'azure') === 0) {
            $endpoint = $metadata_endpoints['azure'];
        } elseif (strpos($metadata, 'gcp') === 0) {
            $endpoint = $metadata_endpoints['gcp'];
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        
        // 对于GCP，需要添加元数据头
        if (strpos($metadata, 'gcp') === 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Metadata-Flavor: Google']);
        }
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error = '请求失败：' . curl_error($ch);
        } else {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code == 200) {
                $result = '元数据获取成功：\n\n' . htmlspecialchars($response);
            } else {
                $result = '请求返回状态码：' . $http_code . '\n\n' . htmlspecialchars($response);
            }
        }
        
        curl_close($ch);
    } catch (Exception $e) {
        $error = '请求异常：' . $e->getMessage();
    }
}

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">☁️ 云元数据</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>💡 漏洞说明：</strong><br>
                本场景演示云元数据SSRF漏洞。<br>
                云服务提供商通常提供元数据服务，攻击者可以通过SSRF漏洞访问这些服务，获取敏感信息如访问凭证、实例信息等。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 漏洞代码</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>if (isset($_GET["metadata"])) {
    $metadata = $_GET["metadata"];
    
    // 漏洞：直接使用用户输入的元数据路径，没有过滤
    $endpoint = "http://169.254.169.254/latest/meta-data/" . $metadata;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    
    curl_close($ch);
}
</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 攻击演示</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">本场景演示云元数据SSRF漏洞，尝试以下攻击：</p>

                    <ol>
                        <li>AWS 元数据：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/cloud_metadata.php?metadata=instance-id</code> - 实例ID</li>
                            <li><code>http://localhost/zhaosec/ssrf/cloud_metadata.php?metadata=iam/security-credentials/</code> - IAM角色</li>
                            <li><code>http://localhost/zhaosec/ssrf/cloud_metadata.php?metadata=public-keys/0/openssh-key</code> - SSH密钥</li>
                        </ul>
                        <li>Azure 元数据：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/cloud_metadata.php?metadata=azure</code> - Azure实例信息</li>
                        </ul>
                        <li>GCP 元数据：</li>
                        <ul>
                            <li><code>http://localhost/zhaosec/ssrf/cloud_metadata.php?metadata=gcp/instance/name</code> - 实例名称</li>
                        </ul>
                    </ol>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>💻 实际测试</h6>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">元数据路径</span>
                            <input type="text" name="metadata" class="form-control" placeholder="例如：instance-id">
                            <button type="submit" class="btn btn-danger">获取</button>
                        </div>
                    </form>

                    ';

if ($error) {
    $content .= '<div class="alert alert-danger">
                        <strong>错误：</strong>
                        <p>' . htmlspecialchars($error) . '</p>
                    </div>';
}

if ($result) {
    $content .= '<div class="alert alert-secondary">
                        <strong>元数据结果：</strong>
                        <pre class="mb-0 mt-2"><code>' . htmlspecialchars($result) . '</code></pre>
                    </div>';
}

$content .= '                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 防御建议</h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li><strong>禁止访问元数据服务：</strong>过滤169.254.169.254等元数据服务地址</li>
                        <li><strong>网络隔离：</strong>实施网络分段，限制服务器对元数据服务的访问</li>
                        <li><strong>使用IAM角色：</strong>避免在实例中存储永久凭证</li>
                        <li><strong>元数据服务配置：</strong>启用元数据服务的访问控制</li>
                        <li><strong>定期轮换凭证：</strong>即使凭证泄露，也能减少影响范围</li>
                        <li><strong>监控异常访问：</strong>监控对元数据服务的异常访问</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>';

// 包含模板
include '../template/module_template.php';
?>