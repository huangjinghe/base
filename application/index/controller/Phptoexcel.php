<?php
/**
 * Excel导出，TODO 可继续优化
 *
 * @param array  $datas      导出数据，格式['A1' => 'XXXX公司报表', 'B1' => '序号']
 * @param string $fileName   导出文件名称
 * @param array  $options    操作选项，例如：
 *                           bool   print       设置打印格式
 *                           string freezePane  锁定行数，例如表头为第一行，则锁定表头输入A2
 *                           array  setARGB     设置背景色，例如['A1', 'C1']
 *                           array  setWidth    设置宽度，例如['A' => 30, 'C' => 20]
 *                           bool   setBorder   设置单元格边框
 *                           array  mergeCells  设置合并单元格，例如['A1:J1' => 'A1:J1']
 *                           array  formula     设置公式，例如['F2' => '=IF(D2>0,E42/D2,0)']
 *                           array  format      设置格式，整列设置，例如['A' => 'General']
 *                           array  alignCenter 设置居中样式，例如['A1', 'A2']
 *                           array  bold        设置加粗样式，例如['A1', 'A2']
 *                           string savePath    保存路径，设置后则文件保存到服务器，不通过浏览器下载
 */
namespace app\index\controller;

require '../vendor/autoload.php';
use think\Controller;
use think\Db;
use app\index\model\Base;

use PHPExcel;


class Phptoexcel extends Controller{

    public function index(){

        $listexport=new Base();
        $listexport->name('base');
        $data=Db::name('base')->select();

        $title=['站点编号','站点名称','维护单位','结算类型',
            '外告系统是否存在','外告是否正常上报','外告接入设备逻辑站名', '最近一次外告上报日期',
            '空调是否正常','空调是否正常',
            '外电是否正常','是否可以发电','电源负载是否超80%',
            '电池是否可续航两小时以上','电池是否超8年',
            '是否OLT共址','传输设备(OLT/PTN/SDH)是否接24V电源柜','是否具备二次下电',
            '问题具体描述','专项标签','隐患是否已解决','上报人员','上报时间'];
        $filename='佛山移动自有基站隐患清单'.date('yy-m-d h:i:s');

        $this->To_excel($title,$data,$filename);



    }

    public function To_excel($title,$data,$filename){
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getActiveSheet()->getStyle("A1:ZZ1")->getFont()->setSize(12)->setBold(true);//设置第一行字体大小和加粗
        foreach($title as $k=>$v){
            if($k<26){
                    $colum[$k]=chr(65+$k);
                    $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum[$k].'1', $v);
                    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($colum[$k])->setWidth(15);
                }else if($k<702){
                    $colum[$k]=chr(64+($k/26)).chr(65+$k%26);
                    $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum[$k].'1', $v);
                    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($colum[$k])->setWidth(15);
                    }
        }
        $row = 2; //第二行开始
        foreach ($data as $item) {
            $column = 0;
            foreach ($item as $value) {

                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($column, $row, $value);
                $column++;
            }
            $row++;
        }




        //生成2007excel格式的xlsx文件

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter =\PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $objWriter->save( 'php://output');




    }




}