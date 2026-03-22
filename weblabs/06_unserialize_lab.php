<?php
$module_name = 'PHP反序列化漏洞专项练习';
$module_icon = '🔓';
$module_desc = 'PHP反序列化漏洞专项训练靶场，包含基础反序列化和POP链攻击。';

$content = <<<'EOT'
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">🔓 PHP反序列化漏洞专项练习</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>💡 说明：</strong><br>
            本靶场提供PHP反序列化漏洞专项训练环境，包含基础反序列化、POP链攻击、Phar反序列化等多种攻击场景，适合深入学习和练习反序列化漏洞。
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🚀 靶场环境</h6>
            </div>
            <div class="card-body text-center">
                <h5 class="mb-3">🎯 PHP反序列化专项靶场</h5>
                <p class="mb-3">PHP反序列化漏洞专项训练环境</p>
                <div class="row justify-content-center mb-3">
                    <div class="col-md-8">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">靶场信息</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-end"><strong>端口：</strong></td>
                                        <td class="text-start">10002</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end"><strong>协议：</strong></td>
                                        <td class="text-start">HTTP</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end"><strong>难度：</strong></td>
                                        <td class="text-start"><span class="badge bg-warning">中级-高级</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="#" id="unserializeLabLink" class="btn btn-success btn-lg" target="_blank">
                    🔓 进入反序列化靶场
                </a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>📋 反序列化类型</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-primary">
                            <div class="card-body text-center">
                                <h5 class="card-title">🎯 基础反序列化</h5>
                                <p class="card-text small">Basic Unserialize</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-danger">
                            <div class="card-body text-center">
                                <h5 class="card-title">🔗 POP链攻击</h5>
                                <p class="card-text small">POP Chain</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-warning">
                            <div class="card-body text-center">
                                <h5 class="card-title">📦 Phar反序列化</h5>
                                <p class="card-text small">Phar Deserialization</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-success">
                            <div class="card-body text-center">
                                <h5 class="card-title">🎪 Session反序列化</h5>
                                <p class="card-text small">Session Deserialization</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-info">
                            <div class="card-body text-center">
                                <h5 class="card-title">🧩 原生类利用</h5>
                                <p class="card-text small">Native Classes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-secondary">
                            <div class="card-body text-center">
                                <h5 class="card-title">🎭 字符串逃逸</h5>
                                <p class="card-text small">String Escape</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>⚠️ 注意事项</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>靶场环境运行在独立的Docker容器中</li>
                    <li>如果无法访问，请检查容器是否正常运行</li>
                    <li>建议先学习PHP魔术方法和序列化原理</li>
                    <li>靶场仅供学习和研究使用</li>
                    <li>需要一定的PHP编程基础</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// 获取宿主机IP并构建靶场链接
function getLabUrl() {
    var hostname = window.location.hostname;
    var protocol = window.location.protocol;
    var labUrl = protocol + '//' + hostname + ':10002/';
    document.getElementById('unserializeLabLink').href = labUrl;
}

// 页面加载时执行
document.addEventListener('DOMContentLoaded', getLabUrl);
</script>
EOT;

include '../template/module_template.php';
?>
