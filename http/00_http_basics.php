<?php
// HTTP协议基础与原理
$module_name = 'HTTP协议基础与原理';
$module_icon = '🌐';
$module_desc = '了解HTTP协议的基本原理、请求/响应结构和常见安全问题';

// 页面内容
$content = '<div class="card">
        <div class="card-header">
            <h5 class="mb-0">🌐 HTTP协议基础与原理</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>💡 模块说明：</strong><br>
                本模块介绍HTTP协议的基本原理、请求/响应结构和常见安全问题，为学习HTTP相关漏洞打下基础。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>📚 HTTP协议概述</h6>
                </div>
                <div class="card-body">
                    <p>HTTP（HyperText Transfer Protocol）是Web通信的基础协议，用于在客户端和服务器之间传输数据。</p>
                    
                    <h5 class="mt-4 mb-3">HTTP协议特点</h5>
                    <ul>
                        <li><strong>无状态协议</strong> - 服务器不会保存客户端的状态信息</li>
                        <li><strong>基于请求-响应模型</strong> - 客户端发送请求，服务器返回响应</li>
                        <li><strong>明文传输</strong> - 数据以明文形式传输（HTTPS除外）</li>
                        <li><strong>灵活可扩展</strong> - 支持自定义头部和方法</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔧 HTTP请求结构</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>GET /index.html HTTP/1.1
Host: example.com
User-Agent: Mozilla/5.0
Accept: text/html
Accept-Language: en-US
Connection: keep-alive

</code></pre>
                    
                    <h5 class="mt-4 mb-3">请求组成部分</h5>
                    <ol>
                        <li><strong>请求行</strong> - 包含方法、路径和协议版本</li>
                        <li><strong>请求头</strong> - 包含客户端信息和请求参数</li>
                        <li><strong>空行</strong> - 分隔请求头和请求体</li>
                        <li><strong>请求体</strong> - 包含POST等方法的请求数据</li>
                    </ol>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔧 HTTP响应结构</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>HTTP/1.1 200 OK
Date: Wed, 21 Mar 2026 12:00:00 GMT
Server: Apache/2.4.41
Content-Type: text/html
Content-Length: 1234

&lt;html&gt;
&lt;head&gt;&lt;title&gt;Example&lt;/title&gt;&lt;/head&gt;
&lt;body&gt;&lt;h1&gt;Hello World&lt;/h1&gt;&lt;/body&gt;
&lt;/html&gt;</code></pre>
                    
                    <h5 class="mt-4 mb-3">响应组成部分</h5>
                    <ol>
                        <li><strong>状态行</strong> - 包含协议版本、状态码和状态描述</li>
                        <li><strong>响应头</strong> - 包含服务器信息和响应参数</li>
                        <li><strong>空行</strong> - 分隔响应头和响应体</li>
                        <li><strong>响应体</strong> - 包含请求的资源内容</li>
                    </ol>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🚀 HTTP方法</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>方法</th>
                                    <th>描述</th>
                                    <th>是否安全</th>
                                    <th>是否幂等</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>GET</td>
                                    <td>获取资源</td>
                                    <td>✓</td>
                                    <td>✓</td>
                                </tr>
                                <tr>
                                    <td>POST</td>
                                    <td>提交数据</td>
                                    <td>✗</td>
                                    <td>✗</td>
                                </tr>
                                <tr>
                                    <td>PUT</td>
                                    <td>更新资源</td>
                                    <td>✗</td>
                                    <td>✓</td>
                                </tr>
                                <tr>
                                    <td>DELETE</td>
                                    <td>删除资源</td>
                                    <td>✗</td>
                                    <td>✓</td>
                                </tr>
                                <tr>
                                    <td>HEAD</td>
                                    <td>获取头信息</td>
                                    <td>✓</td>
                                    <td>✓</td>
                                </tr>
                                <tr>
                                    <td>OPTIONS</td>
                                    <td>获取服务器支持的方法</td>
                                    <td>✓</td>
                                    <td>✓</td>
                                </tr>
                                <tr>
                                    <td>PATCH</td>
                                    <td>部分更新资源</td>
                                    <td>✗</td>
                                    <td>✗</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔐 HTTP安全问题</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">常见HTTP安全问题</h5>
                    <ul>
                        <li><strong>明文传输</strong> - 数据未加密，可被中间人攻击</li>
                        <li><strong>缺乏认证</strong> - 无状态特性导致认证挑战</li>
                        <li><strong>头部注入</strong> - 恶意头部可能导致安全问题</li>
                        <li><strong>Cookie安全</strong> - 不当配置可能导致会话劫持</li>
                        <li><strong>请求走私</strong> - 特殊构造的请求可能绕过安全控制</li>
                        <li><strong>响应分割</strong> - 注入恶意响应导致客户端执行不当操作</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 安全建议</h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li><strong>使用HTTPS</strong> - 加密传输数据，防止中间人攻击</li>
                        <li><strong>正确配置Cookie</strong> - 设置HttpOnly、Secure、SameSite等属性</li>
                        <li><strong>验证请求头</strong> - 对用户输入的头部进行严格验证</li>
                        <li><strong>使用安全的HTTP方法</strong> - 遵循RESTful最佳实践</li>
                        <li><strong>实施内容安全策略</strong> - 限制可执行的资源来源</li>
                        <li><strong>定期更新服务器软件</strong> - 修复已知的HTTP相关漏洞</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>';

include '../template/module_template.php';
?>