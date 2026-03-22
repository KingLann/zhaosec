<?php
$module_name = 'CSRF漏洞专项练习';
$module_icon = '🔐';
$module_desc = 'CSRF漏洞专项训练靶场，包含基础CSRF、高级CSRF等多种攻击场景。';

$content = <<<'EOT'
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">🔐 CSRF漏洞专项练习</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>💡 说明：</strong><br>
            本靶场提供CSRF漏洞专项训练环境，包含基础CSRF、高级CSRF、CSRF防护绕过等多种攻击场景，适合深入学习和练习CSRF漏洞。
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🚀 靶场环境</h6>
            </div>
            <div class="card-body text-center">
                <h5 class="mb-3">🎯 CSRF专项靶场</h5>
                <p class="mb-3">CSRF漏洞专项训练环境</p>
                <div class="row justify-content-center mb-3">
                    <div class="col-md-8">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">靶场信息</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-end"><strong>端口：</strong></td>
                                        <td class="text-start">10008, 10009</td>
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
                <div class="row justify-content-center mb-3">
                    <div class="col-md-5">
                        <a href="#" id="csrfLabLink1" class="btn btn-success btn-lg w-100" target="_blank">
                            🔐 进入CSRF靶场 (端口10008)
                        </a>
                    </div>
                    <div class="col-md-5">
                        <a href="#" id="csrfLabLink2" class="btn btn-success btn-lg w-100" target="_blank">
                            🔐 进入CSRF靶场 (端口10009)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>📋 CSRF类型</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-primary">
                            <div class="card-body text-center">
                                <h5 class="card-title">🎯 基础CSRF</h5>
                                <p class="card-text small">Basic CSRF</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-danger">
                            <div class="card-body text-center">
                                <h5 class="card-title">🔓 GET型CSRF</h5>
                                <p class="card-text small">GET CSRF</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-warning">
                            <div class="card-body text-center">
                                <h5 class="card-title">📝 POST型CSRF</h5>
                                <p class="card-text small">POST CSRF</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-success">
                            <div class="card-body text-center">
                                <h5 class="card-title">🔗 JSON型CSRF</h5>
                                <p class="card-text small">JSON CSRF</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-info">
                            <div class="card-body text-center">
                                <h5 class="card-title">🎭 CSRF绕过</h5>
                                <p class="card-text small">CSRF Bypass</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-secondary">
                            <div class="card-body text-center">
                                <h5 class="card-title">🛡️ CSRF防护</h5>
                                <p class="card-text small">CSRF Protection</p>
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
                    <li>建议先学习CSRF的基本原理和攻击方式</li>
                    <li>靶场仅供学习和研究使用</li>
                    <li>请勿对真实系统进行CSRF攻击</li>
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
    var labUrl1 = protocol + '//' + hostname + ':10008/';
    var labUrl2 = protocol + '//' + hostname + ':10009/';
    document.getElementById('csrfLabLink1').href = labUrl1;
    document.getElementById('csrfLabLink2').href = labUrl2;
}

// 页面加载时执行
document.addEventListener('DOMContentLoaded', getLabUrl);
</script>
EOT;

include '../template/module_template.php';
?>
