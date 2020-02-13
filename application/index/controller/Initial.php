<?php
namespace app\Index\controller;
use think\Controller;
use think\Request;
use think\Session;
class Initial extends Controller
{
    //如果你的控制器类继承了\think\Controller类的话，可以定义控制器初始化方法_initialize，在该控制器的方法调用之前首先执行。
    public function _initialize()
    {
        session_start();

        if(!isset($_SESSION['username'])){
            $this->error('请登陆','index/login/index');
        }else {
            $username=$_SESSION['username'];
            $username_zh=$_SESSION['username_zh'];
            $this->assign('username_zh',$username_zh);
            $this->assign('username',$username);
        }


    }
}