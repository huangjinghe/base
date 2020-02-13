<?php
/**
 * Created by PhpStorm.
 * User: fshuangjinghe
 * Date: 2020/1/29 0029
 * Time: 21:56
 */

namespace app\index\controller;

use app\index\model\Base;

class Admin extends InitialAdmin
{
    public function index()
        {

            $base_count=new Base();
            $base_count->name('base');
            $base_num=$base_count->count();
            $fix=($base_count->where('end','已解决')->count())/$base_num;
            $fix_rate=round($fix*10000,2)."%";

            $count_user=new Base();
            $count_user->name('user');
            $admin_num=$count_user->where('quanxian',1)->count();
            //var_dump($admin_num);
            $normal_num=$count_user->where('quanxian',0)->count();



            $this->assign('tp_version',THINK_VERSION);
            $this->assign('php_version',phpversion());
            $this->assign('Apache_version',apache_get_version());

            $this->assign('base_num',$base_num);
            $this->assign('fix_rate',$fix_rate);

            $this->assign('admin_num',$admin_num);
            $this->assign('normal_num',$normal_num);

            return $this->fetch();
        }
    public function baselist(){
    return $this->fetch();
}

    public function userlist(){
        return $this->fetch();
    }
    public function picture(){
        return $this->fetch();
    }

    public function baselist_send(){
        $base=new Base();
        $base->name('base');
        $get_list=$base->select();
        $get_list_count=$base->count();
        $result=[
            'rows'=>$get_list,
            'total'=>$get_list_count,

        ];
        return json($result);

    }
    public function userlist_send(){

        $base=new Base();
        $base->name('user');
        $get_list=$base->select();
        $get_list_count=$base->count();
        $result=[
            'rows'=>$get_list,
            'total'=>$get_list_count,
        ];
        return json($result);

    }

    public function picture_send(){
        $base=new Base();
        $base->name('baseimg');
        $get_list=$base->select();
        $get_list_count=$base->count();
        $result=[
            'rows'=>$get_list,
            'total'=>$get_list_count,

        ];
        return json($result);
    }
    public function user_del(){
        $userid=input('userid');
        $user_del= Base::name('user')-> where('userid',$userid)->delete();
        if ($user_del) {
            return $this->success('用户已删除');
        }else return $this->error('删除失败');

    }
    public function user_confirm(){
        $userid=input('userid');
        $user=new Base();
        $user->name('user');
        $user_confirm=$user->save([
            'status'  => '1',
        ],['userid' => $userid]);
        if ($user_confirm){
            return $this->success('用户注册已审批通过');
        }else return $this->error('审批失败');
    }
    public function user_lock(){
        $userid=input('userid');

        $user=new Base();
        $user->name('user');
        $user_admin=$user->where('userid',$userid)->value('username');
        if ($user_admin=='admin'){
            return $this->error('不允许锁定管理员');
        }
        $user_status=$user->where('userid',$userid)->value('status');
        //判断用户是否锁定或解锁，执行相反操作
        if ($user_status==0){
            $user->save(['status'=>1],['userid'=>$userid]);
            return $this->success('解锁成功');
        }else if($user_status==1){
            $user->save(['status'=>0],['userid'=>$userid]);
            return $this->success('锁定成功');
        }else return $this->error('操作失败');
    }

    public function kmh(){
        return $this->fetch();
    }


    public function tasklist_send(){
        $task=new Base();
        $task->name('kmh');
        $rows=$task->select();
        $total=$task->count();
        $result=[
            'rows'=>$rows,
            'total'=>$total
        ];
        return json($result);
    }



}
