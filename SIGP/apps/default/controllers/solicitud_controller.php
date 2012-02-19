<?php
require_once('correo.php');
require_once('utilities/constantes.php');
class SolicitudController extends ApplicationController{
	protected   $auth;

	protected function initialize(){
		$this->setTemplateAfter("menu");
		$this->auth=Auth::getActiveIdentity();
	}
	public function tutorAcademicoAction() {

	}
	public function historialSolicitudesAction() {

	}
	public function solicitudesPendientesAction() {

	}
	public  function getSolicitudesTutorAcademicoAction() {
		$resultado= array();

		if ($this->auth['categoriaUsuario_id']==CAT_USUARIO_PASANTE){
			$pasanteId = $this->auth['idUsuario'];
			$start = $this->obtenerParametroRequest('start');
			$limit = $this->obtenerParametroRequest('limit');
			$solicitudes = new Solicitudtutoracademico();
			$resultado = $solicitudes->getSolicitudesTutor($pasanteId,$start,$limit);
		}
		$this->setResponse('ajax');
		$this->renderText(json_encode($resultado));
	}
	public  function getSolicitudesPasanteTutorAcademicoAction() {
		$resultado= array();

		if ($this->auth['categoriaUsuario_id']==CAT_USUARIO_TUTOR_ACAD){
			$empresaId = $this->auth['idUsuario'];
			$start = $this->obtenerParametroRequest('start');
			$limit = $this->obtenerParametroRequest('limit');
			$solicitudes = new Solicitudtutoracademico();
			$resultado = $solicitudes->getSolicitudesbyTutor($empresaId,$start,$limit);
		}
		$this->setResponse('ajax');
		$this->renderText(json_encode($resultado));
	}
	public function solicitarTutorAcademicoAction() {
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		if ($this->auth['categoriaUsuario_id']==CAT_USUARIO_PASANTE){
			$idPasante=$this->auth['idUsuario'];
			$idTutor= $this->getParametro('pTutorId', 'numerico', -1);
			if ($idTutor!=-1){
				$conf = new Configuracion();
				$solicitud = new Solicitudtutoracademico();
				$nroMaxSolic= $conf->getNroMaxSolicTutorbyDecanato(DECANATO_CIENCIAS);
				if ($nroMaxSolic>$solicitud->contarSolicitudesPasante($idPasante)){
					if (!$solicitud->existeSolicitudPrevia($idPasante, $idTutor)){
						if ($conf->getNroMaxSolitudesRecibidasbyDecanato(DECANATO_CIENCIAS)>$solicitud->contarSolicitudesTutor($idTutor)){
							if (!$solicitud->obtenerTutorAsignado($idPasante)) {
								$resp['success']= $solicitud->solicitarTutor($idTutor,$idPasante);
								if (!$resp['success']){
									$resp['errorMsj']= "No se ha registrado la solicitud.";
								}else{
									$tutor = new TutorAcademico();
									$datosT= $tutor->getTutorAcademicoById($idTutor);
									$pasante = new Pasante();
									$datosP = $pasante->getNombreApellido($idPasante);
									$this->notificarSolicitud($datosP, $datosT['correo']);
								}
							}else{
								$resp['errorMsj']= "Ud. ya tiene asignado un tutor.";
							}
						}else{
							$resp['errorMsj']= "El tutor seleccionado no puede recibir mas solicitudes.";					
						}
					}else{
						$resp['errorMsj']= "Existe una solicitud pendiente con el tutor seleccionado.";
					}
				}else{
					$resp['errorMsj']= "El número máximo($nroMaxSolic) de solicitudes simultaneas ha sido superado.";
				}
			}else{
				$resp['errorMsj']= 'Parámetros incorrectos.';
			}
		}else{
			$resp['errorMsj']= 'Usuario sin permisos.';
		}
		$resp['errorMsj']= utf8_encode($resp['errorMsj']);
		$this->setResponse('ajax');
		$this->renderText(json_encode($resp));
	}
	
	protected function notificarSolicitud($nombreCompleto,$pCorreo) {
		$correo = new Correo();
		$body ='Experientia (Sistema para la gestión de pasantias  UCLA- DCyT) le informa que: <BR>';
		$body .= "<B>$nombreCompleto </B> ha realizado una petición para solicitarlo a Ud. como <B>Tutor Académico</B> en su periodo de pasantías.<BR/>";
		$body .= "Ud. puede aceptar o rechazar la solicitud accediendo a su cuenta de usuario.<BR/>";
		$body .='http://'. $this->getServer('SERVER_NAME').'/SIGP/';
		$correo->enviarCorreo($pCorreo, 'Solicitud tutor académico', $body);
	}
	
	public function rechazarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$solicitudId= $this->getParametro('pSolicitudId','numerico',-1);
		if ($solicitudId!=-1){
			$solicitud = new Solicitudtutoracademico();
			$resp['success']= $solicitud->rechazar($solicitudId);	
			if (!$resp['success']){
				$resp['errorMsj']= "No se ha rechazado la solicitud.";
			}else{
				$datos = $solicitud->getSolicitudbyId($solicitudId);
				if($datos){
					$tutor = new TutorAcademico();
					$datosT= $tutor->getTutorAcademicoById($datos['tutorAcademicoId']);
					$pasante = new Pasante();
					$datosP = $pasante->getPasantebyId($datos['pasanteId']);
					if ($datosP and $datosT){
						$this->notificarRechazo($datos['fchSolicitud'],$datosT['nombre'],$datosT['apellido'], $datosP['email']);	
					}
				}
			}
		}else{
			$resp['errorMsj']= 'Parámetros incorrectos.';
		}
		$resp['errorMsj']= utf8_encode($resp['errorMsj']);
		$this->setResponse('ajax');
		$this->renderText(json_encode($resp));
	}
	
	protected function notificarRechazo($fechaS,$nombreT,$apellidoT,$correoP) {
		$correo = new Correo();
		$body ='Experientia (Sistema para la gestión de pasantias  UCLA- DCyT) le informa que: <BR>';
		$body .= "<B>$nombreT, $apellidoT </B> ha rechazado la solicitud de Tutor Académico realizada el: $fechaS .<BR/>";
		$body .= "Ud. puede realizar otra solicitud ingresando al sistema.<BR/>";
		$body .='http://'. $this->getServer('SERVER_NAME').'/SIGP/';
		$correo->enviarCorreo($correoP, 'Rechazo solicitud tutor académico ', $body);
	}
	
	public function aceptarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$solicitudId= $this->getParametro('pSolicitudId','numerico',-1);
		if ($solicitudId!=-1){
			$solicitud = new Solicitudtutoracademico();
			$datos = $solicitud->getSolicitudbyId($solicitudId);
			if($datos){
				$resp['success']= $solicitud->aceptar($solicitudId,$datos['pasanteId']);
				if (!$resp['success']){
					$resp['errorMsj']= "No se ha aceptado la solicitud.";
				}else{
					$pasantia = new Pasantia();
					if ($pasantia->estaEnPasantia($datos['pasanteId'])){
						$pasantia->registrarTutor($datos['pasanteId'], $datos['tutorAcademicoId']);
					}
					$tutor = new TutorAcademico();
					$datosT= $tutor->getTutorAcademicoById($datos['tutorAcademicoId']);
					$pasante = new Pasante();
					$datosP = $pasante->getPasantebyId($datos['pasanteId']);
					if ($datosP and $datosT){
						$this->notificarAceptacion($datos['fchSolicitud'],$datosT['nombre'],$datosT['apellido'], $datosP['email']);	
					}
				}
			}else{
				$resp['errorMsj']= "La solicitud seleccionada no se encuentra.";
			}	
		}else{
			$resp['errorMsj']= 'Parámetros incorrectos.';
		}
		$resp['errorMsj']= utf8_encode($resp['errorMsj']);
		$this->setResponse('ajax');
		$this->renderText(json_encode($resp));
	}
	
	protected function notificarAceptacion($fechaS,$nombreT,$apellidoT,$correoP) {
		$correo = new Correo();
		$body ='Experientia (Sistema para la gestión de pasantias  UCLA- DCyT) le informa que: <BR>';
		$body .= "<B>$nombreT, $apellidoT </B> ha aceptado la solicitud de Tutor Académico realizada el: $fechaS .<BR/>";
		$body .= "Se le invita a contactar a su tutor e informarle de sus actividades.<BR/>";
		$body .='http://'. $this->getServer('SERVER_NAME').'/SIGP/';
		$correo->enviarCorreo($correoP, 'Aceptación solicitud tutor académico ', $body);
	}
	
	public function cancelarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		if ($this->auth['categoriaUsuario_id']==CAT_USUARIO_PASANTE){
			$idPasante=$this->auth['idUsuario'];
			$solicitudId= $this->getParametro('pSolicitudId','numerico',-1);
			if ($solicitudId!=-1){
				$solicitud = new Solicitudtutoracademico();
				if ($solicitud->perteneceSolicitud($idPasante, $solicitudId)){
					$resp['success']= $solicitud->cancelar($solicitudId);
					if (!$resp['success']){
						$resp['errorMsj']= "No se ha cancelado la solicitud.";
					}
				}else{
					$resp['errorMsj']= 'La solicitud seleccionada no está asociada a su usuario.';
				}		
			}else{
				$resp['errorMsj']= 'Parámetros incorrectos.';
			}
		}else{
			$resp['errorMsj']= 'Usuario sin permisos.';
		}
		$resp['errorMsj']= utf8_encode($resp['errorMsj']);
		$this->setResponse('ajax');
		$this->renderText(json_encode($resp));
	}
}
	

?>