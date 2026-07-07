<?php 
highlight_file(__FILE__);
error_reporting(0);
class A{
    public $name;
    public function __destruct(){
      $hello = $this->name;
      $hello();
      
    }
}
class C{
    public $sex;
    public function __invoke(){
        eval($this->sex);
    }
}
unserialize($_GET['pop']);
?>