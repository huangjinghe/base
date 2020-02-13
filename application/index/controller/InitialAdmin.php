<?php
/**
 * Created by PhpStorm.
 * User: fshuangjinghe
 * Date: 2020/1/29 0029
 * Time: 21:57
 */

namespace app\index\controller;
use think\Controller;


class InitialAdmin extends Controller
{
    public function _initialize()
    {
        session_start();

        if(!isset($_SESSION['username']) && !isset($_SESSION['username'])){
            $this->error('请登陆','index/login/index');
        }elseif ($_SESSION['quanxian'] != 1){
            $this->error('您不是管理员','index/login/index');
        };

        $username=$_SESSION['username'];
        $quanxian=$_SESSION['quanxian'];

        $this->assign('quanxian',$quanxian);
        $this->assign('username',$username);

    }
}