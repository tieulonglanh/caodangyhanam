<?php
/**
 * XPHP Framework
 *
 * LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @category	XPHP
 * @package		XPHP_Mail
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Mail.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 * @uses		PHPMailer {@link http://phpmailer.worxware.com/}
 */

/**
 * Class tiện ích dùng cho gửi mail.
 *
 * @category	XPHP
 * @package		XPHP_Mail
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_mail.html
 * @uses		PHPMailer {@link http://phpmailer.worxware.com/}
 */
class XPHP_Mail {
	
	/**
	 * Subject
	 * Tiêu đề Email
	 * 
	 * @var string
	 */
	public $subject = '';
	
	/**
	 * Content
	 * Nội dung email HTML
	 * 
	 * @var string
	 */
	public $content;
	
	/**
	 * To Email
	 * Email người nhận
	 *
	 * @var string
	 */
	public $toEmail;
	
	/**
	 * To Name
	 * Tên người nhận
	 *
	 * @var string
	 */
	public $toName;
	
	/**
	 * Priority
	 * Mục ưu tiên gửi lại của email
	 *  
	 * @var int
	 */
	public $priority;
	
	/**
	 * From Name
	 * Tên người gửi
	 *
	 * @var string
	 */
	public $fromName = '';
	
	/**
	 * From Email
	 * Email người gửi
	 *
	 * @var string
	 */
	public $fromEmail = '';
	
	/**
	 * Error Message
	 * Thông báo lỗi
	 *
	 * @var string
	 */
	public $errorMess;
	
	/**
	 * Khởi tạo
	 *
	 * @param Array $info. Thông tin của các email cần gửi
	 */
	public function __construct($info = array()) {
		$vars = array_keys(get_object_vars($this));		
		for ($i=0, $size = sizeof($vars); $i < $size; ++$i) {
			if (isset($info[$vars[$i]])) {				
				$this->$vars[$i] = $info[$vars[$i]];
			}
		}		
	}
	
	/**
	 * Send
	 * Gửi mail
	 *
	 * @param string $services localhost/gmail
	 * @param int $key -1 => random
	 * @return boolean
	 * @throws XPHP_Mail_PHPMailerException Nếu việc gửi email xảy ra lỗi
	 */
	public function send($services = 'localhost', $key = -1) {	
		$mail = new XPHP_Mail_PHPMailer(true);
		$mail->CharSet ='utf-8';
		$mail->IsSMTP();
		try {
		    $conf = XPHP_Config::get('mail');
		    $mail->SMTPDebug  = $conf->debug;
			if ($services == 'gmail') { // Gui email qua server cua gmail
				/**
				 * Load user
				 */
				$user = XPHP_Config::get('mail')->gmail;			
				$mail->SMTPAuth   = true;				// enable SMTP authentication
				$mail->SMTPSecure = "ssl";				// sets the prefix to the servier
				$mail->Host       = "smtp.gmail.com";	// sets GMAIL as the SMTP server
				$mail->Port       = 465;				// set the SMTP port
				$mail->Username   = $user->username;			// GMAIL username
				$mail->Password   = $user->password;			// GMAIL password
			}
			else { 				
				/**
				 * Gui email su dung server noi bo
				 * Load server
				 */
				$server = XPHP_Config::get('mail')->smtp;
				$mail->Host	  	  = $server->host;	// sets GMAIL as the SMTP server				
				$mail->SMTPAuth	  = $server->auth;	// enable SMTP authentication		
				$mail->Port       = isset($server->port) && !empty($server->port) ? $server->port : 25; // set the SMTP port
				if(isset($server->secure) && !empty($server->secure))
				    $mail->SMTPSecure = $server->secure;
				$mail->Username   = $server->username;			// GMAIL username
				$mail->Password   = $server->password;		
			}
			
			if (empty($this->fromEmail)) {
				$this->fromEmail = $conf->replyMail;
			}
			if (empty($this->fromName)) {
				$this->fromName = $conf->replyName;
			}
			
			$mail->From       = $this->fromEmail;
			$mail->FromName   = $this->fromName;
			$mail->Subject    = $this->subject;				
			$mail->AltBody    = strip_tags($this->content); //Text Body
			$mail->WordWrap   = 50; // set word wrap			
			$mail->MsgHTML($this->content);
			$mail->AddReplyTo($mail->From, $mail->FromName);
			$mail->AddAddress($this->toEmail, $this->toName);	
			$mail->IsHTML(true); // send as HTML

			return !$mail->Send();
		}
		catch (XPHP_Mail_PHPMailer_Exception $pme) {
			throw new XPHP_Exception('Gủi mail bị lỗi tới ' 
						. $this->toEmail .' sử dụng ' . $services .'. Lỗi: ', $pme->errorMessage());
		}
	}
	
	/**
	 * Gửi mail
	 * 	
	 * @param string $toEmail: Email nhận
	 * @param string $toName: Người nhận
	 * @param string $subject: Tiêu đề email
	 * @param string $content: Nội dung email
	 * @param string $useSMTP: SMTP gửi 
	 * @param string $From: Email gửi
	 * @param string $FromName: Người gửi
	 */
	 public function sendEmail($toEmail, $toName, $subject, $content, $useSMTP='localhost', $From='', $FromName='')
	 {
		if (!$toEmail)
			return 'EMAIL_EMPTY';
		if (!$subject)
			return 'SUBJECT EMPTY';
		if (!$content)
			return 'CONTENT EMPTY';
			
			
		$this->toEmail =$toEmail;
		$this->toName = $toName;
		$this->subject = $subject;
		$this->content = $content;
			 
		if ($From != '')
			$this->fromEmail = $From;
		if ($FromName!='')
			$this->fromName = $FromName;
			
		$this->priority = 1;
			 
		return $this->send($useSMTP);
	 }
}