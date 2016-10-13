<?php

define('MAIL_SERVER',	'smtp.biz.rr.com');
define('MAIL_USER',		'dpotter@cnymail.com');
define('MAIL_NAME',		'Billing Info Sender');
define('MAIL_PASSWORD',	'Nathan03');
define('MAIL_PORT',		25);
define('MAIL_AUTH',		true);
define('MAIL_SUBJECT_BILLING',	'Billing Invoice');
define('MAIL_BODY_BILLING', 'Your billing invoice attached. Please review it.');

function sendSMTPMail($email, $subject, $body, $attach_file = null, $cc = array(), $name_file = 'Billing_Invoice')
{
	require_once('phpmailer/class.phpmailer.php');

	$mail = new PHPMailer();
	$mail->IsSMTP();
	//$mail->SMTPDebug  = true;
	$mail->Host       = MAIL_SERVER;
	$mail->SMTPAuth   = MAIL_AUTH;
	$mail->Port       = MAIL_PORT;
	$mail->Username   = MAIL_USER;
	$mail->Password   = MAIL_PASSWORD;

	$mail->From		= MAIL_USER;
	$mail->FromName = MAIL_NAME;


	$mail->Sender = MAIL_USER;
	$mail->AddReplyTo(MAIL_USER, "Replies for my site");

	$mail->AddAddress($email);
	foreach($cc as $address)
		$mail->AddCC($address);
	
	$mail->Subject = $subject;

	$mail->IsHTML(true);
	$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
	$mail->CharSet ="utf-8";
	$mail->Body = $body;
	
	if($attach_file != null)
		$mail->AddAttachment ($attach_file, $name_file);

	if($mail->Send())
		return true;
	else
		return $mail->ErrorInfo;
}