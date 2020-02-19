<?php
namespace app\index\controller;

use app\index\model\Base;
use http\Client\Curl\User;
use think\Controller;
use think\Db;
use think\Request;
use think\captcha\Captcha;



class Login extends Controller
{
    public function index(){

        $captcha = new Captcha();

        $captchacode=$captcha->entry();

        return $this->fetch();
    }

    public function logincheck(){

        session_start();
        $_SESSION["username"] = null;
        $username=$_POST['username'];
        $password=md5($_POST['password']);
        $valicode=$_POST['code'];

        //校验验证码

        $captcha = new Captcha();
        $valiresult= $captcha->check($valicode) ;

        //session_start();
        if ($valiresult==false){
            return $this->error('验证码输入错误','index/login/index');
        }else if (!empty($username)){
            $usercheck= new Base();
            $usercheck->name('user');
            $userinfo= $usercheck->where('username',$username)->find();
            if(empty($userinfo)){
                return $this->error('用户名不存在');
            }
            //用户账号状态校验
            $status=$userinfo['status'];
            $quanxian=$userinfo['quanxian'];
            $username_zh=$userinfo['username_zh'];
            switch ($status){
                case '0':
                    return $this->error('用户账号已锁定，请联系管理员解锁');
                    break;

                case null:
                    return $this->error('用户注册申请未审批，请提醒管理员审批');
                    break;
                case '1':
                    break;
                default:
                    return $this->error('无法获取用户状态');
            }

            //校验密码及权限

            if ($userinfo['userpwd']!=$password){
                return $this->error('密码错误');

            } else if($userinfo['userpwd']==$password && $userinfo['quanxian']==0){


                    $_SESSION["username"]=$username;
                    $_SESSION['quanxian']=$quanxian;
                    $_SESSION['username_zh']=$username_zh;
                    $logintime=date('yy-m-h H:i:s');
                    $user_login=new Base();
                    $user_login->name('user');
                    $user_login->save([
                        'logintime'  => $logintime,
                    ],['username' => $username]);
                    return $this->success('普通用户登陆成功','index/index/index',2,1);
            } else if ($userinfo['userpwd']==$password && $userinfo['quanxian']==1){
                $_SESSION['username']=$username;
                $_SESSION['username_zh']=$username_zh;
                $_SESSION['quanxian']=$quanxian;

                $logintime=date('yy-m-h H:i:s');
                $user_login=new Base();
                $user_login->name('user');
                $user_login->save([
                    'logintime'  => $logintime,
                ],['username' => $username]);
                return $this->success('您是管理员，跳转后台管理','index/admin/index',2,1);
            }

            //var_dump($userinfo);

        }else return $this->error('用户名不能为空');



        //var_dump($username,$password,$valicode);
    }

    public function loginreset(){
        session_start();
        $_SESSION=array();
        session_destroy();
        $this->error('已退出登陆','index/login/index');

    }

    public function regist(){
        return $this->fetch();

    }

    public function registcheck(){

        //var_dump($_POST);

        $username=$_POST['username'];
        $pwd=$_POST['userpwd'];
        $_POST['userpwd']=md5($pwd);

        $captcha = new Captcha();
        $valicode=$_POST['code'];
        $valiresult= $captcha->check($valicode) ;

        //session_start();
        if ($valiresult==false){
            return $this->error('验证码输入错误','index/login/regist');
        }

        $reg=new Base();
        $reg->name('user');
        $select_user=$reg->where('username',$username)->find();

        if($select_user != null){
            return $this->error('已存在该用户名，请直接登陆或找回密码','index/login/index');
        }

        $email=new  Phpmail();

        $subject=$_POST['username_zh'].'（'.$_POST['danwei'].'）'.'申请用户注册，请审批';

        $body=$_POST['username_zh'].'申请用户注册，请点击链接审批</br><a href="https://www.gmcc-fs.xyz/base/public/index/admin/userlist">http://106.13.32.74/base/public/admin/index/index</a></br>';

        if($email->mailsend($subject,$body)){
            echo '已邮件通知管理员';
        }

        $register=$reg->allowField(true)->save($_POST);

        if($register)
            {
                $this->success('注册提交成功，等待审核','index/login/index');
            }
    }
    public function userprofile(){
            $username=Request::instance()->get('username');
            $user=new Base();
            $user->name('user');
            $userinfo=$user->where('username',$username)->select();

            $this->assign('userinfo',$userinfo);
            return $this->fetch();
    }
    public function user_save(){
        $map=Request::instance()->param();
        $username=Request::instance()->post('username');
        $user=new  Base();
        $user->name('user');
        $usersave=$user->allowField(true)->save($_POST,['username' => $username]);
        if ($usersave){
            return '修改成功';
        }else return '修改失败';
    }
    public function userpwd(){
        $username=Request::instance()->get('username');

        $this->assign('username',$username);
        return $this->fetch();
    }
    public function userpwd_save(){
        $username=Request::instance()->post('username');
        $user=new Base();
        $user->name('user');
        $userinfo=$user->where('username',$username)->find();
        $old_userpwd=Request::instance()->post('old_userpwd');
        $userpwd=Request::instance()->post('userpwd');
        if ($userinfo['userpwd'] == md5($old_userpwd)){
            $usersave=$user->allowField(true)->save(['userpwd'=>md5($userpwd)],['username' => $username]);
            if ($usersave){
                return $this->success('密码修改成功','index/index/index');
            }else return $this->error('密码修改失败','index/index/index');
        }else return $this->error('原密码不正确','index/index/index');

    }

}