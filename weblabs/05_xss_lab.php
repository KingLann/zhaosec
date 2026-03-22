<?php
$module_name = 'XSS漏洞专项练习';
$module_icon = '☠️';
$module_desc = 'XSS漏洞专项训练靶场，包含反射型、存储型和DOM型XSS。';

$content = <<<'EOT'
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">☠️ XSS漏洞专项练习</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>💡 说明：</strong><br>
            本靶场提供XSS漏洞专项训练环境，包含反射型、存储型和DOM型XSS等多种攻击场景，适合深入学习和练习XSS漏洞。
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🚀 靶场环境</h6>
            </div>
            <div class="card-body text-center">
                <h5 class="mb-3">🎯 XSS专项靶场</h5>
                <p class="mb-3">XSS漏洞专项训练环境</p>
                <div class="row justify-content-center mb-3">
                    <div class="col-md-8">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">靶场信息</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-end"><strong>端口：</strong></td>
                                        <td class="text-start">10007</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end"><strong>协议：</strong></td>
                                        <td class="text-start">HTTP</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end"><strong>难度：</strong></td>
                                        <td class="text-start"><span class="badge bg-info">初级-高级</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="#" id="xssLabLink" class="btn btn-success btn-lg" target="_blank">
                    ☠️ 进入XSS靶场
                </a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>📋 XSS类型</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-primary">
                            <div class="card-body text-center">
                                <h5 class="card-title">🔄 反射型XSS</h5>
                                <p class="card-text small">Reflected XSS</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-danger">
                            <div class="card-body text-center">
                                <h5 class="card-title">💾 存储型XSS</h5>
                                <p class="card-text small">Stored XSS</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-warning">
                            <div class="card-body text-center">
                                <h5 class="card-title">🌐 DOM型XSS</h5>
                                <p class="card-text small">DOM XSS</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-success">
                            <div class="card-body text-center">
                                <h5 class="card-title">🔗 URL跳转</h5>
                                <p class="card-text small">Open Redirect</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-info">
                            <div class="card-body text-center">
                                <h5 class="card-title">📝 Self-XSS</h5>
                                <p class="card-text small">Self XSS</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-secondary">
                            <div class="card-body text-center">
                                <h5 class="card-title">🎭 Blind XSS</h5>
                                <p class="card-text small">Blind XSS</p>
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
                    <li>建议使用XSS Hunter等工具进行XSS测试</li>
                    <li>靶场仅供学习和研究使用</li>
                    <li>请勿对其他用户进行XSS攻击</li>
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
    var labUrl = protocol + '//' + hostname + ':10007/';
    document.getElementById('xssLabLink').href = labUrl;
}

// 页面加载时执行
document.addEventListener('DOMContentLoaded', getLabUrl);
</script>
EOT;

include '../template/module_template.php';
?>
