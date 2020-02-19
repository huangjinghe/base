<?php
namespace app\index\controller;

use app\index\model\Base;
use http\Client\Curl\User;
use PHPMailer\PHPMailer\PHPMailer;
use think\Controller;
use think\Db;
use think\Request;
use app\index\controller\Phpmail;

class Index extends Initial
{

    public function index()
    {

		return $this->fetch();

    }

    public function showlist(){
        $basename=Request::instance()->get('basename');
        $base=new Base();
        $list=$base->where('tp_basename','like','%'.$basename.'%')
            ->paginate(10,false,['query'=>request()->get()]);;
        // 获取分页显示
        $page=$list->render();
        $this->assign('page',$page);
        $this->assign('list',$list);
        return $this->fetch();
    }
    public function searchlist(){

        $map['tp_basename'] = ['like','%'.input('basename').'%'];

        if(!empty(input('end'))){
            $map['end'] = ['like','%'.input('end').'%'];
        }
        if(!empty(input('beizhu'))){
            $map['tp_beizhu'] = ['like','%'.input('beizhu').'%'];
        }
        if(!empty(input('tp_company'))){
            $map['tp_company'] = ['like','%'.input('tp_company').'%'];
        }


        $base=new Base();
        $searchlist=$base->where($map)->paginate(10,false,['query'=>request()->param()]);

        $page=$searchlist->render();
        $this->assign('page',$page);
        $this->assign('list',$searchlist);
        return $this->fetch();
    }


    public function showdetail(){

        //Get方法获取需要查看的站点

        if (isset($_GET['basename'])){
            $basename=$_GET['basename'];
        }else return $this->error("未选择站点");
        $showbase=Base::where('tp_basename',$basename)->select();
        $this->assign('showbase',$showbase);
        return $this->fetch();
    }

    public  function postinfo(){
        //Get方法获取需要查看的站点

        if (isset($_GET['basename'])){
            $basename=$_GET['basename'];
        }else return $this->error("未选择站点");
        $postbase=Base::where('tp_basename',$basename)->select();
        //dump($showbase);
        $date_time=date('yy-m-d');

        $this->assign('postbase',$postbase);
        $this->assign('datetime',$date_time);

        return $this->fetch();
    }

    public function posting(){
        $id=$_POST['tp_id'];
        $basename=$_POST['tp_basename'];
        $updateuser_new=$_SESSION['username'];
        $date_time=date('yy-m-d H:i:s');

        $baseupdate=new Base();
        $baseupdate->name='tp_base';
        $updateuser_old=$baseupdate->where('tp_basename',$basename)->field('updateuser')->find();
        $baseupdate->allowField(true)->save($_POST, ['tp_id' =>$id]);

        $updateuser=$updateuser_new;
        $baseupdate->where('tp_id',$id)->update(['updateuser' => $updateuser,'updatetime'=>$date_time]);

        $email=new  Phpmail();

        $subject=$updateuser_new.'上报一个基站信息：'.$basename;

        $body= '<p>
	<span style="font-family:Microsoft YaHei;"> 
	<div style="background-image: url("http://localhost/base/public/static/img/the-town-bg-01.jpg")">
	<table class="table table-bordered table-hover text-left" style="width:600px;background-color:#FFFFFF;border:2px solid #dee2e6;color:#3a518f;font-family: -apple-system , BlinkMacSystemFont, &quot;font-size:30px;text-align:left !important;">
		<tbody>
			<tr>
				<th style="vertical-align:top;border:2px solid #DEE2E6;">
					自有站点编号
				</th>
				<td style="vertical-align:top;border:2px solid #DEE2E6;">
					' .$_POST['tp_id'].'
				</td>
			</tr>
			<tr>
				<th style="vertical-align:top;border:2px solid #DEE2E6;">
					站点名称
				</th>
				<td style="vertical-align:top;border:2px solid #DEE2E6;">
					'.$_POST['tp_basename'].'
				</td>
			</tr>
		
			<tr>
				<th style="vertical-align:top;border:2px solid #DEE2E6;">
					外告接入设备逻辑站名
				</th>
				<td style="vertical-align:top;border:2px solid #DEE2E6;">
					'.$_POST['tp_alarmexist'].'
				</td>
			</tr>
			<tr>
				<th style="vertical-align:top;border:2px solid #DEE2E6;">
					是否有外告系统
				</th>
				<td style="vertical-align:top;border:2px solid #DEE2E6;">
					'.$_POST['tp_alarmbase'].'
				</td>
			</tr>
			<tr>
				<th style="vertical-align:top;border:2px solid #DEE2E6;">
					外告是否正常上报
				</th>
				<td style="vertical-align:top;border:2px solid #DEE2E6;">
					'.$_POST['tp_alarmworking'].'
				</td>
			</tr>
			<tr>
				<th style="vertical-align:top;border:2px solid #DEE2E6;">
					最近一次外告上报日期
				</th>
				<td style="vertical-align:top;border:2px solid #DEE2E6;">
					'.$_POST['tp_alarm_send_date'].'
				</td>
			</tr>
			<tr>
				<th style="vertical-align:top;border:2px solid #DEE2E6;">
					空调是否正常
				</th>
				<td style="vertical-align:top;border:2px solid #DEE2E6;">
					'.$_POST['tp_aircon'].'
				</td>
			</tr>
			<tr>
				<th style="vertical-align:top;border:2px solid #DEE2E6;">
					外电是否正常
				</th>
				<td style="vertical-align:top;border:2px solid #DEE2E6;">
					'.$_POST['tp_eletric'].'
				</td>
			</tr>
			<tr>
				<th style="vertical-align:top;border:2px solid #DEE2E6;">
					电池是否可续航两小时以上
				</th>
				<td style="vertical-align:top;border:2px solid #DEE2E6;">
					'.$_POST['tp_xuhang'].'
				</td>
			</tr>
			<tr>
				<th style="vertical-align:top;border:2px solid #DEE2E6;">
					传输设备(OLT/PTN/SDH)是否接24V电源柜
				</th>
				<td style="vertical-align:top;border:2px solid #DEE2E6;">
					'.$_POST['tp_chuanshu'].'
				</td>
			</tr>
			<tr>
				<th style="vertical-align:top;border:2px solid #DEE2E6;">
					问题具体描述
				</th>
				<td style="vertical-align:top;border:2px solid #DEE2E6;">
				'.$_POST['tp_miaoshu'].'
				</td>
			</tr>
			<tr>
				<th style="vertical-align:top;border:2px solid #DEE2E6;">
					专项标签
				</th>
				<td style="vertical-align:top;border:2px solid #DEE2E6;">
				'.$_POST['tp_beizhu'].'
				</td>
			</tr>
			<tr>
				<th style="vertical-align:top;border:2px solid #DEE2E6;">
					隐患是否已解决
				</th>
				<td style="vertical-align:top;border:2px solid #DEE2E6;">
				    '.$_POST['end'].'
				</td>
			</tr>
		</tbody>
	</table>
	</div>
	';

        if($email->mailsend($subject,$body)){
            echo '邮件发送成功';
        }
       if($baseupdate){
            return $this->success("上报成功",'/base/public/index/index/showdetail.html?basename='.$basename);
        }else return $this->error("上报失败");

    }

    //上传图片函数

    /**
     *
     */
    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('img');

        // 移动到框架应用根目录/public/upload/ 目录下
        if($file){
            $info =$file->validate(['ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS .'upload');

            if($info){
                $img['img_miaoshu']=input('img_miaoshu');
                $img['img_uploader']=input('img_uploader');
                $img['img_basename']=input('img_basename');
                $img['img_name']=$info->getFilename();
                $img['img_uploadtime']=date('yy-m-d H:i:s');
                $img['img_location']='/base/public/upload/'.str_replace('\\','/',$info->getSaveName());
                $imginfo=new Base();
                $imginfo->name('baseimg');

                if($imginfo->insert($img)){
                    return true;
                }else return false;

            }else{
                // 上传失败获取错误信息
                return $file->getError();
            }
        }

    }

    public function showimg(){

        $basename=input('basename');
        $this->assign('basename',$basename);

        $get_img=new Base();
        $get_img->name('baseimg');
        $img_data=$get_img->where('img_basename',$basename) ->select();
        $img_count=$get_img->where('img_basename',$basename)->count();

        $this->assign('img_count',$img_count);

        $this->assign('img_data',$img_data);

        return $this->fetch();
    }

}
