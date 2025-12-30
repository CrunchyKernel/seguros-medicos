<?php
require "/home/cotizars/public_html/segurodegastosmedicosmayores.mx/vendor/phpmailer/phpmailer/src/Exception.php";
require "/home/cotizars/public_html/segurodegastosmedicosmayores.mx/vendor/phpmailer/phpmailer/src/PHPMailer.php";
require "/home/cotizars/public_html/segurodegastosmedicosmayores.mx/vendor/phpmailer/phpmailer/src/SMTP.php";

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\SMTP;
use \PHPMailer\PHPMailer\Exception;

class ALTMailer {
	public static function mail($view, $datosPlantilla, $cotizacionDatos, $from, $fromName, $to = null, $subject = null){
		$html = \View::make($view, $datosPlantilla)->render();
		
		$mail = new PHPMailer(true);
		
		//$mail->SMTPDebug = SMTP::DEBUG_SERVER;
	    $mail->isSMTP();
	    $mail->Host = 'email-smtp.us-west-2.amazonaws.com';
	    $mail->SMTPAuth = true;
	    $mail->Username = 'AKIAJYE33JVO5RI7HFQQ';
	    $mail->Password = 'AoszB2efVsK3zM0IoC8f1Gikx6hFSuhDwwoFcM0grGFs';
	    $mail->SMTPSecure = 'tls';
	    $mail->Port = 587;

	    //Recipients
	    $mail->setFrom($from, utf8_decode($fromName));
	    if(is_null($to)){
		    $mail->addAddress($cotizacionDatos->e_mail, utf8_decode($cotizacionDatos->nombre));
			//$mail->addCC("info@segurodegastosmedicosmayores.mx", "Cotizaciones");
		}
		else{
			foreach($to as $address){
				$mail->addAddress($address);
			}
		}
	    
	    //Attachments
	    //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
	    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
	    if(!is_null($cotizacionDatos)){
		    if(file_exists($cotizacionDatos->pdf)){
		    	$mail->addAttachment($cotizacionDatos->pdf, 'cotizacion-' . $cotizacionDatos->id_cotizacion . '.pdf');
		    }
		}

	    //Content
	    $mail->isHTML(true);
	    if(is_null($subject)){
	    	$mail->Subject = utf8_decode('Cotización de Gastos Médicos Mayores - ' . $cotizacionDatos->id_cotizacion);
	    	$mail->AltBody = utf8_decode('Cotización de Gastos Médicos Mayores - ' . $cotizacionDatos->id_cotizacion);
		}
		else{
			$mail->Subject = utf8_decode($subject);
			$mail->AltBody = utf8_decode($subject);
		}
	    $mail->Body    = $html;

	    $mail->send();
	}
	
	public static function usuarioBloqueado($email, $secret){
		$mail = new PHPMailer(true);
		$mail->isSMTP();
	    $mail->Host = 'email-smtp.us-west-2.amazonaws.com';
	    $mail->SMTPAuth = true;
	    $mail->Username = 'AKIAJYE33JVO5RI7HFQQ';
	    $mail->Password = 'AoszB2efVsK3zM0IoC8f1Gikx6hFSuhDwwoFcM0grGFs';
	    $mail->SMTPSecure = 'tls';
	    $mail->Port = 587;
	    
	    $mail->setFrom('admin@segurodegastosmedicosmayores.mx', 'Administrador del sistema');
	    $mail->addAddress($email);
	    $mail->isHTML(true);
	    $mail->Subject = "Usuario bloqueado";
	    $mail->Body = 'Tu usuario ha sido bloqueado por realizar demasiados intentos fallidos. Para desbloquearlo haz click <a href="https://segurodegastosmedicosmayores.mx/desbloquear-usuario/' . $secret . '">aqui</a>';
	    $mail->AltBody = 'Tu usuario ha sido bloqueado por realizar demasiados intentos fallidos. Para desbloquearlo haz click <a href="https://segurodegastosmedicosmayores.mx/desbloquear-usuario/' . $secret . '">aqui</a>';
	    $mail->send();
	}
}
?>