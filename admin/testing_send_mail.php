<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// database connection will be here
include_once '../config/Database.php';
include_once '../models/Staff.php';
include_once '../models/Api_key.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';


/*$staff = new Staff();
$api_key = new Api_key();
$user_id = $api_key->validate_api_key();
//Only Admin can
//get id by access token_get_all
//check role by access token
$staff->id = $user_id;
$to = 'mohammadjubayer999@gmail.com';
$subject = 'Mahir Floral Management App: Registration successful';
$message = 'You have been registered with the Mahir Floral Management App. Please use following credentials for login.
                 email =' + $to + ' password= 123456789' ;
$headers = 'From: jubayer.sust.23@gmail.com' . "\r\n" .
	'Reply-To: jubayer.sust.23@gmail.com' . "\r\n" .
	'X-Mailer: PHP/' . phpversion();

$result = mail($to, $subject, $message, $headers);

if($result){
	echo 'success';
}else{
	echo 'failed';
}*/



// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
	//Server settings
	$mail->SMTPDebug = 2;                                       // Enable verbose debug output
	$mail->isSMTP();                                            // Set mailer to use SMTP
	$mail->Host       = 'mahirfloralevents.com';  // Specify main and backup SMTP servers
	$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
	$mail->Username   = 'admin@mahirfloralevents.com';                     // SMTP username
	$mail->Password   = 'Admin@@1234';                               // SMTP password
	$mail->SMTPSecure = 'ssl';                                  // Enable TLS encryption, `ssl` also accepted
	$mail->Port       = 465;                                    // TCP port to connect to

	//Recipients
	$mail->setFrom('admin@mahirfloralevents.com', 'Mailer');
	$mail->addAddress('mohammadjubayer999@gmail.com', 'Joe User');     // Add a recipient
	//$mail->addAddress('ellen@example.com');               // Name is optional
	$mail->addReplyTo('info@example.com', 'Information');
	//$mail->addCC('cc@example.com');
	//$mail->addBCC('bcc@example.com');

	// Attachments
	//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

	// Content
	$email = "abc@gmail.com";
	$password = "1234567";
	$mail->isHTML(true);                                  // Set email format to HTML
	$mail->Subject = 'Here is the subject';
	$mail->Body    = "An account has been created with this email address. Please use the following credentials for login into the app. <br><br> <b>Email: </b>".$email."<br> <b>Password : </b>".$password."<br><br> Please use the following link to download the app: <a href='https://drive.google.com/file/d/1_3fXDz0n5bREViOutp1d-w6Mn0-LTEne/view?usp=sharing'>Download Mahir Floral App</a>";

	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

	$mail->send();
	echo 'Message has been sent';
} catch (Exception $e) {
	echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}


