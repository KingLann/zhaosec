<?php
highlight_file(__FILE__);
error_reporting(0); 
class user{
    public $name;
    public function __destruct(){
     echo $this->name;
    }
}
class ctf{
    public $name;
    public function __tostring(){
         $one = $this->name;
         $one();
    }
}
class getflag{
    public $sex;
    public function __invoke(){
        eval($this->sex);
    }
}

unserialize($_GET['pop']);
?>