<?php
require_once('utilities/PHPMailer_v5.1/class.phpmailer.php');

class Correo {
	protected $passwordGmail;
	protected $passwordAlternativa;
	protected $cuentaGmail;
	protected $cuentaAlternativa;

	function __construct(){
		//leemos el archivo de configuracion  y cargamos las claves de los correo de cada cuenta de gmail y la de hotmail
		$datos = parse_ini_file('config/mail.ini',true);
		$this->cuentaGmail = $datos['gmail']['cuenta'];
		$this->passwordGmail = $datos['gmail']['password'];

	}

	/**
	 * Envia un correo con el mensaje (body) a la cuenta de un usuario (to). 
	 * @param string $to
	 * @param string $subject
	 * @param string $body
	 * @param string $from_name
	 */
	public function enviarCorreo($to, $subject, $body, $attachedPDF='',$nameAttached='',$from_name ='SIGP') {
		global $error;
		$mail = new PHPMailer();  // create a new object
		$mail->IsSMTP(); // enable SMTP
		$mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true;  // authentication enabled
		$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 465;
		$mail->Username = $this->cuentaGmail;
		$mail->Password = $this->passwordGmail;
		$mail->SetFrom($this->cuentaGmail, $from_name);
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->AltBody  = strip_tags($body);
		$mail->IsHTML(true);
		$mail->AddAddress($to);
		if ($attachedPDF!=''){
			$mail->AddStringAttachment($attachedPDF, $nameAttached, 'base64', 'application/pdf');
		}
		if(!$mail->Send()) {
			$error = 'Error al enviar correo: '.$mail->ErrorInfo;
			$this->guardarLog($error, $to);
		
		} 
	}
	
	/**
	 * Almacena en un log por fecha, la ocurrencia de un error al enviar un correo
	 * @param string $mensaje
	 * @param string $destinatario
	 */
	private function guardarLog($mensaje,$destinatario){
		$log = new Logger();
		$log->log("Error: '$mensaje'.
				  Destinatario: $destinatario", Logger::ERROR);
	} 
}
?>