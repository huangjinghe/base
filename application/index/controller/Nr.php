<?php


namespace app\index\controller;


use app\index\model\Base;
use think\Request;

class Nr extends Initial
{
    public function ptgz(){
        $nr=new Base();
        $nr->name('nr');

        if(Request::instance()->isGet()){
            $room_name=Request::instance()->param('room_name');
            $res=$nr->where('room_name','like','%'.$room_name.'%')
                ->paginate(6,false,['query'=>request()->param()]);;
        }else $res=$nr->paginate(6);
        //var_dump($res);
        $page=$res->render();
        $this->assign('page',$page);
        $this->assign('res',$res);
        return $this->fetch();
    }
}