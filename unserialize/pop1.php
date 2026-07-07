<?php 
highlight_file(__FILE__);
error_reporting(0);
class A{
    public $name;
    public function __destruct()
{
      echo $this->name;
    }
}
class B{
    public $age;
    public function __tostring(){
        eval($this->age);
    }
}

unserialize($_GET['pop']);
?>