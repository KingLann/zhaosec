<?php
$module_name = 'SSTI注入漏洞专项练习';
$module_icon = '🎨';
$module_desc = 'SSTI注入漏洞专项训练靶场，包含多种模板引擎的注入攻击。';

$content = <<<'EOT'
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">🎨 SSTI注入漏洞专项练习</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>💡 说明：</strong><br>
            本靶场提供SSTI注入漏洞专项训练环境，包含Jinja2、Twig、Freemarker等多种模板引擎的注入攻击场景，适合深入学习和练习SSTI漏洞。
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🚀 靶场环境</h6>
            </div>
            <div class="card-body text-center">
                <h5 class="mb-3">🎯 SSTI专项靶场</h5>
                <p class="mb-3">SSTI注入漏洞专项训练环境</p>
                <div class="row justify-content-center mb-3">
                    <div class="col-md-8">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">靶场信息</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-end"><strong>端口：</strong></td>
                                        <td class="text-start">10003</td>
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
                <a href="#" id="sstiLabLink" class="btn btn-success btn-lg" target="_blank">
                    🎨 进入SSTI靶场
                </a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>📋 模板引擎</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-primary">
                            <div class="card-body text-center">
                                <h5 class="card-title">🐍 Jinja2</h5>
                                <p class="card-text small">Python模板引擎</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-danger">
                            <div class="card-body text-center">
                                <h5 class="card-title">🌿 Twig</h5>
                                <p class="card-text small">PHP模板引擎</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-warning">
                            <div class="card-body text-center">
                                <h5 class="card-title">☕ Freemarker</h5>
                                <p class="card-text small">Java模板引擎</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-success">
                            <div class="card-body text-center">
                                <h5 class="card-title">🎯 Velocity</h5>
                                <p class="card-text small">Java模板引擎</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-info">
                            <div class="card-body text-center">
                                <h5 class="card-title">🔥 Smarty</h5>
                                <p class="card-text small">PHP模板引擎</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card h-100 border-secondary">
                            <div class="card-body text-center">
                                <h5 class="card-title">💎 Jade/Pug</h5>
                                <p class="card-text small">Node.js模板引擎</p>
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
                    <li>建议先学习模板引擎的语法和特性</li>
                    <li>靶场仅供学习和研究使用</li>
                    <li>需要一定的模板引擎编程基础</li>
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
    var labUrl = protocol + '//' + hostname + ':10003/';
    document.getElementById('sstiLabLink').href = labUrl;
}

// 页面加载时执行
document.addEventListener('DOMContentLoaded', getLabUrl);
</script>
EOT;

include '../template/module_template.php';
?>
