<?php
// 命令执行绕过演示
$module_name = '命令执行绕过演示';
$module_icon = '🔐';
$module_desc = '演示不同难度的命令执行绕过技术，包括空格过滤、关键字过滤、命令连接符过滤等。';

$level = isset($_GET['level']) ? intval($_GET['level']) : 0;
$is_win = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

// 关卡定义
$levels = [
    1 => [
        'name' => 'Level 1: 无过滤命令执行',
        'difficulty' => '初级',
        'goal' => '模拟网络设备的Ping测试功能，后台直接拼接用户输入，无任何过滤。利用命令注入漏洞执行任意系统命令。',
        'theory' => '当用户输入直接拼接到系统命令中时，攻击者可以通过插入命令分隔符来执行额外的命令。',
        'items' => ['<code>&</code> - 后台执行', '<code>&&</code> - 前一个命令成功后执行', '<code>|</code> - 管道', '<code>||</code> - 前一个命令失败后执行'],
        'hint' => '尝试输入 <code>127.0.0.1 && whoami</code> 或 <code>127.0.0.1 & dir</code>',
        'defense' => '使用escapeshellarg()对命令参数进行转义',
        'code' => '$ip = $_POST["ip"];
if (strtoupper(substr(PHP_OS, 0, 3)) === "WIN") {
    $cmd = "ping -n 2 " . $ip;
} else {
    $cmd = "ping -c 2 " . $ip;
}
system($cmd);',
    ],
    2 => [
        'name' => 'Level 2: 关键字黑名单绕过',
        'difficulty' => '初级',
        'goal' => '后台过滤了 <code>cat</code> 命令，使用其他命令替代来读取文件内容。',
        'theory' => '当系统过滤了特定命令时，攻击者可以使用功能相似的其他命令来达到相同的目的。',
        'items' => ['<code>more</code> - 分页显示文件', '<code>less</code> - 交互式查看', '<code>head</code> / <code>tail</code> - 显示头/尾部', '<code>nl</code> - 带行号显示', '<code>type</code> - Windows下查看文件'],
        'hint' => '尝试 <code>127.0.0.1 && more flag.txt</code> 或 Windows下 <code>127.0.0.1 && type flag.txt</code>',
        'defense' => '使用白名单而非黑名单，只允许特定的安全命令',
        'code' => '$ip = $_POST["ip"];
if (stripos($ip, "cat") !== false) {
    echo "检测到危险命令：cat，已被过滤！";
} else {
    if (strtoupper(substr(PHP_OS, 0, 3)) === "WIN") {
        $cmd = "ping -n 2 " . $ip;
    } else {
        $cmd = "ping -c 2 " . $ip;
    }
    system($cmd);
}',
    ],
    3 => [
        'name' => 'Level 3: 空格过滤绕过',
        'difficulty' => '中级',
        'goal' => '后台过滤了空格字符，使用空格绕过技巧来执行命令。',
        'theory' => '当系统过滤了空格字符时，攻击者可以使用各种空格替代字符来绕过过滤。',
        'items' => ['<code>%20</code> - URL编码的空格', '<code>%09</code> - URL编码的制表符', '<code>${IFS}</code> - 内部字段分隔符（Linux）', '<code>$IFS</code> - 内部字段分隔符'],
        'hint' => 'Linux: <code>127.0.0.1&&cat${IFS}flag.txt</code><br>Windows: <code>127.0.0.1&&type%20flag.txt</code>',
        'defense' => '参数转义 + 白名单验证，确保所有输入都经过正确转义',
        'code' => '$ip = $_POST["ip"];
if (strpos($ip, " ") !== false) {
    echo "检测到空格字符，已被过滤！";
} else {
    if (strtoupper(substr(PHP_OS, 0, 3)) === "WIN") {
        $cmd = "ping -n 2 " . $ip;
    } else {
        $cmd = "ping -c 2 " . $ip;
    }
    system($cmd);
}',
    ],
    4 => [
        'name' => 'Level 4: 命令执行无回显',
        'difficulty' => '中级',
        'goal' => '后台执行命令但没有显示输出（无回显），使用带外攻击或其他方法来获取结果。',
        'theory' => '当命令执行没有回显时，攻击者需要使用带外（OOB）攻击或将结果写入文件再读取。',
        'items' => ['写入文件：<code>whoami > result.txt</code>', '延时判断：<code>ping -n 5 127.0.0.1</code>', 'DNS请求外带数据', 'HTTP请求外带数据'],
        'hint' => '将结果写入文件 <code>127.0.0.1 && whoami > result.txt</code>，然后通过Web访问读取',
        'defense' => '网络隔离 + 参数验证 + 命令白名单',
        'code' => '$ip = $_POST["ip"];
if (strtoupper(substr(PHP_OS, 0, 3)) === "WIN") {
    $cmd = "ping -n 2 " . $ip;
} else {
    $cmd = "ping -c 2 " . $ip;
}
exec($cmd);  // 不显示输出
echo "命令已执行，但无回显输出...";',
    ],
    5 => [
        'name' => 'Level 5: 关键词过滤绕过',
        'difficulty' => '高级',
        'goal' => '后台过滤了 <code>flag</code> 关键词，使用绕过技巧来读取包含flag的文件。',
        'theory' => '当系统过滤了特定关键词时，可以使用大小写混淆、字符串拼接、命令替换等技巧绕过。',
        'items' => ['<code>FlAg</code> - 大小写混淆', '<code>fla\'g</code> / <code>fla"g</code> - 引号插入', '<code>fl\\ag</code> - 反斜杠转义', '<code>fl$(echo ag)</code> - 命令替换', '<code>fl*txt</code> - 通配符匹配'],
        'hint' => '提示1：<code>127.0.0.1 && type FlAg.txt</code><br>提示2：<code>127.0.0.1 && type fla"g".txt</code><br>提示3：<code>127.0.0.1 && type fl$(echo ag).txt</code>',
        'defense' => '白名单验证 + 输入规范化 + 参数转义',
        'code' => '$ip = $_POST["ip"];
if (stripos($ip, "flag") !== false) {
    echo "检测到危险关键词：flag，已被过滤！";
} else {
    if (strtoupper(substr(PHP_OS, 0, 3)) === "WIN") {
        $cmd = "ping -n 2 " . $ip;
    } else {
        $cmd = "ping -c 2 " . $ip;
    }
    system($cmd);
}',
    ],
];

// 执行逻辑
$output = '';
$error = '';

if ($level > 0 && $level <= 5 && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $ip = isset($_POST['ip']) ? $_POST['ip'] : '';
        if ($ip !== '') {
            $blocked = false;
            switch ($level) {
                case 2:
                    if (stripos($ip, 'cat') !== false) { $error = '检测到危险命令：cat，已被过滤！'; $blocked = true; }
                    break;
                case 3:
                    if (strpos($ip, ' ') !== false) { $error = '检测到空格字符，已被过滤！'; $blocked = true; }
                    break;
                case 5:
                    if (stripos($ip, 'flag') !== false) { $error = '检测到危险关键词：flag，已被过滤！'; $blocked = true; }
                    break;
            }
            if (!$blocked) {
                $cmd = $is_win ? 'ping -n 2 ' . $ip : 'ping -c 2 ' . $ip;
                if ($level == 4) {
                    @exec($cmd);
                    $output = '命令已执行，但无回显输出...';
                } else {
                    ob_start();
                    system($cmd);
                    $output = ob_get_clean();
                }
            }
        }
    }

// ========== 构建页面 ==========
$content = '';
$content .= '
';

// 关卡选择按钮
$content .= '<div class="mb-3">';
for ($i = 1; $i <= 5; $i++) {
    $active = ($i == $level) ? 'btn-danger' : 'btn-outline-secondary';
    $content .= '<a href="bypass_demo.php?level=' . $i . '" class="btn ' . $active . ' me-1 mb-1">Level ' . $i . '</a>';
}
$content .= '</div>';

if ($level == 0) {
    // 关卡选择页
    $content .= '<div class="row">';
    foreach ($levels as $id => $lv) {
        $badge = $lv['difficulty'] === '初级' ? 'bg-success' : ($lv['difficulty'] === '中级' ? 'bg-warning text-dark' : 'bg-danger');
        $content .= '<div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Level ' . $id . '</h5>
                        <span class="badge ' . $badge . '">' . $lv['difficulty'] . '</span>
                    </div>
                    <p class="text-muted">' . $lv['name'] . '</p>
                    <a href="bypass_demo.php?level=' . $id . '" class="btn btn-outline-primary btn-sm">开始挑战</a>
                </div>
            </div>
        </div>';
    }
    $content .= '</div>';

} else {
    $lv = $levels[$level];

    // 关卡目标
    $content .= '<div class="card mb-3">
        <div class="card-header"><h6 class="mb-0">' . $lv['name'] . ' <span class="badge bg-' . ($lv['difficulty'] === '初级' ? 'success' : ($lv['difficulty'] === '中级' ? 'warning text-dark' : 'danger')) . '">' . $lv['difficulty'] . '</span></h6></div>
        <div class="card-body">
            <h6>🎯 关卡目标</h6>
            <p>' . $lv['goal'] . '</p>
            <h6 class="mt-3">📖 漏洞原理</h6>
            <p>' . $lv['theory'] . '</p>
            <ul>';
    foreach ($lv['items'] as $item) {
        $content .= '<li>' . $item . '</li>';
    }
    $content .= '</ul>
        </div>
    </div>';

    // 测试表单
    $content .= '<div class="card mb-3">
        <div class="card-header"><h6 class="mb-0">💻 实际测试</h6></div>
        <div class="card-body">
            <form method="POST" class="mb-3">';
    $content .= '<div class="input-group">
                    <span class="input-group-text">目标IP</span>
                    <input type="text" name="ip" class="form-control" placeholder="例如: 127.0.0.1" value="' . (isset($_POST['ip']) ? htmlspecialchars($_POST['ip']) : '') . '">
                    <button type="submit" class="btn btn-danger">执行Ping测试</button>
                </div>';
    $content .= '</form>';

    // 输出结果
    if ($error) {
        $content .= '<div class="alert alert-danger"><strong>错误：</strong><pre class="mb-0 mt-1">' . htmlspecialchars($error) . '</pre></div>';
    } elseif ($output) {
        $exec_cmd = '';
        if ($level <= 5 && isset($_POST['ip'])) {
            $exec_cmd = '执行命令: ' . htmlspecialchars($is_win ? 'ping -n 2 ' . $_POST['ip'] : 'ping -c 2 ' . $_POST['ip']) . "\n\n";
        }
        $content .= '<div class="alert alert-secondary"><strong>执行结果：</strong><pre class="mb-0 mt-1">' . $exec_cmd . htmlspecialchars($output) . '</pre></div>';
    }
    $content .= '</div></div>';

    // 源代码
    $content .= '<div class="card mb-3">
        <div class="card-header"><h6 class="mb-0">📄 漏洞源代码</h6></div>
        <div class="card-body"><pre class="bg-dark text-light p-3 rounded"><code>' . $lv['code'] . '</code></pre></div>
    </div>';

    // 防护方法
    $content .= '<div class="card mb-3">
        <div class="card-header"><h6 class="mb-0">🛡️ 防护方法</h6></div>
        <div class="card-body"><p>' . $lv['defense'] . '</p></div>
    </div>';

    // 提示
    $content .= '<div class="card mb-3">
        <div class="card-header" style="cursor:pointer" onclick="document.getElementById(\'hint\').style.display=document.getElementById(\'hint\').style.display===\'none\'?\'block\':\'none\'"><h6 class="mb-0">💡 点击显示提示</h6></div>
        <div class="card-body" id="hint" style="display:none">' . $lv['hint'] . '</div>
    </div>';
}

// 包含模板
include '../template/module_template.php';
?>
