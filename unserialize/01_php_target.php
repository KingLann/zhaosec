<?php
$module_name = 'PHP实战靶场';
$module_icon = '🎯';
$module_desc = '跳转到宿主机另一个容器的10002端口进行实战练习。';

$content = <<<'EOT'
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">🎯 PHP实战靶场</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>💡 说明：</strong><br>
            本靶场包含PHP反序列化漏洞的实战环境，支持完整的漏洞测试和利用。
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6>🚀 靶场环境</h6>
            </div>
            <div class="card-body text-center">
                <h5 class="mb-3">🔥 PHP Deserialize Lab</h5>
                <p class="mb-3">包含多种反序列化漏洞的实战环境</p>
                <a href="#" id="phpLabLink" class="btn btn-primary btn-lg" target="_blank">
                    🎯 进入靶场
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>⚠️ 注意事项</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>靶场环境运行在独立的容器中</li>
                    <li>如果无法访问，请检查容器是否正常运行</li>
                    <li>靶场仅供学习和授权测试使用</li>
                    <li>请勿在生产环境中使用相关技术</li>
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
    document.getElementById('phpLabLink').href = labUrl;
}

// 页面加载时执行
document.addEventListener('DOMContentLoaded', getLabUrl);
</script>
EOT;

include '../template/module_template.php';
?>
