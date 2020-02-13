<?php


namespace app\index\controller;


use app\index\model\Base;
use think\Request;

class Kmh extends Initial
{
    public function index(){
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

    public function task_send()
    {
        if (Request::instance()->isPost()) {
            if (Request::instance()->post('cellname')) {
                $map['cellname']= ['like','%'.input('cellname').'%'] ;
            }
            if (Request::instance()->post('solve')) {
                $map['solve']= ['like','%'.input('solve').'%'] ;
            }
            if (Request::instance()->post('evaluate')) {
                $map['evaluate']= ['like','%'.input('evaluate').'%'] ;
            }
            if (Request::instance()->post('area')) {
                $map['area']= ['like','%'.input('area').'%'] ;
            }else $map['task_id'] = ['like','%%'];
        }else $map['task_id'] = ['like','%%'];
        $task_search = new Base();
        $task_search->name('kmh');
        $res = $task_search->where($map)->select();
        return json($res);
    }

}