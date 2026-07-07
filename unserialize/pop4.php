<?php
header("Content-Type: text/html; charset=utf-8");
highlight_file(__FILE__);
error_reporting(0);

class A {
    public $obj;

    public function __wakeup() {
        // 反序列化时自动调用
        // 触发 B::__toString()
        echo $this->obj;
    }
}

class B {
    public $callable;

    public function __toString() {
        // 此处试图打印对象，但若是可调用对象则会触发 C::__invoke()
        return ($this->callable)();
    }
}

class C {
    public $cmd;

    public function __invoke() {
        // 最终命令执行
        return system($this->cmd);
    }
}


// ----------- SINK TRIGGER -----------------
if (isset($_GET['pop'])) {
    $data = $_GET['pop'];
    unserialize($data);
}

?>
