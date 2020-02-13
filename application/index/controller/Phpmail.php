<?php
/**
 * Created by PhpStorm.
 * User: fshuangjinghe
 * Date: 2020/1/22 0022
 * Time: 22:26
 */
namespace app\index\controller;

use think\Controller;

import('PHPMailer', EXTEND_PATH);
import('SMTP', EXTEND_PATH);
import('Exception', EXTEND_PATH);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Phpmail extends Controller {

    public function mailsend($subject,$body){
        $mail=new PHPMailer(true);
        try {
            //服务器配置
            $mail->CharSet ="UTF-8";                     //设定邮件编码
            $mail->SMTPDebug = 0;                        // 调试模式输出
            $mail->isSMTP();                             // 使用SMTP
            $mail->Host = 'smtp.126.com';                // SMTP服务器
            $mail->SMTPAuth = true;                      // 允许 SMTP 认证
            $mail->Username = 'huangjinghe';                // SMTP 用户名  即邮箱的用户名
            $mail->Password = 'jeshua537794';             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
            $mail->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
            $mail->Port = 465;                            // 服务器端口 25 或者465 具体要看邮箱服务器支持

            $mail->setFrom('huangjinghe@126.com', '佛山基站隐患信息');  //发件人
            $mail->addAddress('18823298298@139.com', '黄景河');  // 收件人1
            $mail->addAddress('13923113386@139.com', '伍家韬');  // 收件人2
            $mail->addAddress('18823137533@139.com', '方耿标');  // 收件人3
            $mail->addAddress('18307575033@139.com', '赖锐文');  // 收件人4
            $mail->addAddress('18316933320@139.com', '黄永杰');  // 收件人5
            $mail->addAddress('13923115118@139.com', '杨仕楷');  // 收件人5

            //$mail->addAddress('ellen@example.com');  // 可添加多个收件人
            $mail->addReplyTo('huangjinghe@126.com', 'info'); //回复的时候回复给哪个邮箱 建议和发件人一致
            //$mail->addCC('cc@example.com');                    //抄送
            //$mail->addBCC('bcc@example.com');                    //密送

            //发送附件
            // $mail->addAttachment('../xy.zip');         // 添加附件
            // $mail->addAttachment('../thumb-1.jpg', 'new.jpg');    // 发送附件并且重命名

            //Content
            $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
            $mail->Subject = $subject ;
            $mail->Body    = $body . date('Y-m-d H:i:s');
            $mail->AltBody = '如果邮件客户端不支持HTML则显示此内容';

            $mail->send();
            echo '邮件发送成功';
        } catch (Exception $e) {
            echo '邮件发送失败: ', $mail->ErrorInfo;
        }
    }
}
