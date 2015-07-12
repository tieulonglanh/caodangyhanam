<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Daniel
 * Date: 6/7/13
 * Time: 9:39 AM
 * To change this template use File | Settings | File Templates.
 */

class IndexController extends XPHP_Controller
{
    public function _init()
    {
        $this->loadLayout('/default');
    }


    public function indexAction()
    {
        return $this->view(new Contact());
    }

    public function indexPost(Contact $model)
    {
        if (empty($_SESSION['captcha']) || trim(strtolower($_REQUEST['captcha'])) != $_SESSION['captcha'])
        {
            $_SESSION['alert'] = array('success' => false, 'message' => 'Mã xác nhận không chính xác');
        }
        else
        {
            if($model->validate())
            {

                    $contact = new Contact();
                    $contact->name = $model->name;
                    $contact->phone = $model->phone;
                    $contact->address = $model->address;
                    $contact->message = $model->message;
                    $contact->email = $model->email;
                    $contact->more_infor = "";
                    $contact->title = $model->title;
                    $contact->created_date = date('Y-m-d h:i:s');;

                    if($contact->insert()){
                        $_SESSION['alert'] = array('success' => true, 'message' => 'Bạn đã gửi thông tin thành công!');
                    }else{
                        $_SESSION['alert'] = array('success' => false, 'message' => 'Có lỗi khi lưu lại, xin hãy gửi lại!');
                    }
                    $this->redirect('index', "Index", "Contact");
//  
//
//                include '/Resources/PHPMailer/class.phpmailer.php';
//                $mail = new XPHP_Mail_PHPMailer();
//                $mail->IsSMTP();
//                $mail->SMTPDebug  = 0;
//                $mail->Debugoutput = 'html';
//
//
//                    $mail->SMTPAuth = true;
//                    $mail->SMTPSecure = "tls";
//                    $mail->Host = "smtp.gmail.com";
//                    $mail->Port = 587;
//                    $mail->CharSet = 'UTF-8';
//                    $mail->Username = "sirzu.heart@gmail.com";
//                    $mail->Password = "enDali@19&)^!";
//                    $mail->AddAddress('dzungnv91@gmail.com', 'ViMob.vn');
//                    $mail->SetFrom($contact->email, $contact->name);
//                    $mail->Subject = 'Mail from user';
//                    $mail->MsgHTML($contact->phone . '<br />' . $contact->address . '<br />' . $contact->message);
//                if(!$mail->Send()) {
//                    echo "Mailer Error: " . $mail->ErrorInfo;
//                } else {
//                    //echo "Message sent!";
//                    return $this->redirect('index', "Index", "Contact");
//                }die;

                

            }
            else
            {
                $err = $model->getErrorMessage();

                $err = ksort($err);
//                echo "<pre>";print_r($err);die;
                if(isset($err[0]))
                    $_SESSION['alert'] = array('success' => false, 'message' => $err[0]);
                else
                    $_SESSION['alert'] = array('success' => false, 'message' => 'Thông tin bạn nhập vào chưa chính xác');
            }
        }
        $this->view->alert = $_SESSION['alert'];
        unset($_SESSION['alert']);
    }
}