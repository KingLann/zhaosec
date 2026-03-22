<?php
$module_name = 'Web CTF实战';
$module_icon = '🏆';
$module_desc = 'CTF竞赛实战靶场环境，包含多种CTF题型和解题思路。';

$content = <<<'EOT'
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">🏆 Web CTF实战</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>💡 说明：</strong><br>
            本靶场提供CTF竞赛环境，包含Web安全相关的各种题型，适合锻炼CTF解题能力和安全思维。
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🚀 靶场环境</h6>
            </div>
            <div class="card-body text-center">
                <h5 class="mb-3">🎯 Web CTF Lab</h5>
                <p class="mb-3">CTF竞赛实战训练环境</p>
                <div class="row justify-content-center mb-3">
                    <div class="col-md-8">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">靶场信息</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-end"><strong>端口：</strong></td>
                                        <td class="text-start">10004</td>
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
                <a href="#" id="ctfLabLink" class="btn btn-success btn-lg" target="_blank">
                    🏆 进入CTF靶场
                </a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🎯 题目类型</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <div class="card h-100 border-primary">
                            <div class="card-body text-center">
                                <h5 class="card-title">🔐 Crypto</h5>
                                <p class="card-text small">密码学题目</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="card h-100 border-danger">
                            <div class="card-body text-center">
                                <h5 class="card-title">🌐 Web</h5>
                                <p class="card-text small">Web安全题目</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="card h-100 border-warning">
                            <div class="card-body text-center">
                                <h5 class="card-title">🔍 Misc</h5>
                                <p class="card-text small">杂项题目</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="card h-100 border-success">
                            <div class="card-body text-center">
                                <h5 class="card-title">🔧 Reverse</h5>
                                <p class="card-text small">逆向工程题目</p>
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
                    <li>建议使用团队协作模式进行CTF训练</li>
                    <li>靶场仅供学习和竞赛训练使用</li>
                    <li>请勿使用自动化工具对靶场进行暴力破解</li>
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
    var labUrl = protocol + '//' + hostname + ':10005/';
    document.getElementById('ctfLabLink').href = labUrl;
}

// 页面加载时执行
document.addEventListener('DOMContentLoaded', getLabUrl);
</script>
EOT;

include '../template/module_template.php';
?>
