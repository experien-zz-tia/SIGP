<?php
require_once 'correo.php';
require_once('utilities/constantes.php');
class NotificacionController extends ApplicationController {
	protected   $auth;

	protected function initialize(){
		$this->setTemplateAfter("menu");
		$this->auth=Auth::getActiveIdentity();
	}
	public function verAction(){}

	public function enviarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$this->setResponse('ajax');
		$catUsuario=$this->auth['categoriaUsuario_id'];
		if ($catUsuario==CAT_USUARIO_COORDINADOR or $catUsuario==CAT_USUARIO_ANALISTA){
			$notificacion = new Notificacion();
			$pasanteId = $this->getParametro('txtIdPasante', 'numerico', -1);
			$mensaje= $this->getParametro('txtMensaje', 'string', '');
			$enviarCorreo= $this->getParametro('pEnviarCorreo', 'string', '');
			$usuarioId=$this->auth['idUsuario'];
			if ($pasanteId!=-1 and $mensaje!='' ){
				//$decanato = DECANATO_CIENCIAS;
				$decanato=$this->auth['decanato_id'];
				$resp['success'] = $notificacion->guardar($usuarioId, $catUsuario, $pasanteId, CAT_USUARIO_PASANTE, $mensaje);
				if (!$resp['success']){
					$resp['errorMsj']='No se ha enviado la notificación.';
				}else{
					if ($enviarCorreo=='true'){
						$pasante = new Pasante();
						$datos= $pasante->getPasantebyId($pasanteId);
						if ($datos){
							$this->enviarNotificacion($datos['nombre'], $datos['email'], $mensaje);
						}
					}
				}
			}else{
				$resp['errorMsj']= 'Parámetros incompletos.';
			}
		}else{
			$resp['errorMsj']= 'Ud. no posee la permisologia para realizar esta operaci&oacute;n.';
		}
		$resp['errorMsj']=utf8_encode($resp['errorMsj']);
		$this->renderText(json_encode($resp));
	}

	private function enviarNotificacion($nombre, $correo, $mensaje){
		$mailer = new Correo();
		$body = " $nombre has recibido la siguiente notificación:<BR> ";
		$body .= html_entity_decode($mensaje);
		$body .="<BR/> Ud. puede revisar el resto de notificaciones ingresando a Experientia.";
		$mailer->enviarCorreo($correo, 'Nueva Notificación', $body);
	}

	public function getNotificacionesAction(){
		$resp=array();
		$this->setResponse('ajax');
		$catUsuario=$this->auth['categoriaUsuario_id'];
		$notificacion = new Notificacion();
		$usuarioId=$this->auth['idUsuario'];
		$start = $this->obtenerParametroRequest('start');
		$limit = $this->obtenerParametroRequest('limit');
		$resp = $notificacion->getNotificaciones($usuarioId,$catUsuario,$start,$limit);
		$this->renderText(json_encode($resp));
	}


	public function eliminarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$this->setResponse('ajax');
		$notificacion = new Notificacion();
		$notifId = $this->getParametro('pId', 'numerico', -1);
		$usuarioId=$this->auth['idUsuario'];
		if ($notifId!=-1){
			$resp['success'] = $notificacion->eliminar($usuarioId,$notifId);
			if (!$resp['success']){
				$resp['errorMsj']='No se ha eliminado la notificación.';
			}
		}else{
			$resp['errorMsj']= 'Parámetros incompletos.';
		}
		$resp['errorMsj']=utf8_encode($resp['errorMsj']);
		$this->renderText(json_encode($resp));
	}


}
?>