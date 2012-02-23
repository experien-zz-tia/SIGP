<?php
require_once('correo.php');
require_once('utilities/constantes.php');

class TutorAcademicoController extends ApplicationController{

	public  $auth;
//-----------------------------------------------------------------------------------------	
	protected function initialize(){
		$this->setTemplateAfter("menu");
		$this->auth=Auth::getActiveIdentity();
	}
//-----------------------------------------------------------------------------------------
	public function indexAction(){
		
	}
//-----------------------------------------------------------------------------------------
	public function gestionarAction(){
		
	}
//-----------------------------------------------------------------------------------------
	public function confirmarAction(){
		$success= false;
		$idTutor=0;
		$aux=array();
		$this->setResponse('ajax');
		$hash= $this->getRequestParam('txtHash');
		$userName = $this->getRequestParam('txtUsuario');
		$clave = $this->getRequestParam('pClave');
		$registroAux = new Registro();
		$usuarioTemporal=$registroAux->getUsuariobyHash($hash);
		$claveTemporal=md5($usuarioTemporal);
		$flag=$registroAux->activarRegistro($hash,$userName);
		if ($flag){
			$usuario = new Usuario();
			$aux = $usuario->activarUsuario($usuarioTemporal, $claveTemporal, $userName, $clave);
			if ($aux['success']){
				$idTutor= $aux['idUsuario'];
				$tutorAux = new TutorAcademico();
				$tutorActivado = $tutorAux->activarTutor($idTutor); 
				if ($tutorActivado){
					$email=$registroAux->getEmailbyHash($hash);
					if ($email!=''){
						$this->notificarCulminacionFaseDos($userName, $email);
					}
					$success=true;
				}
			}

		}

		$this->renderText(json_encode(array("success"=>$success)));

	}
//-----------------------------------------------------------------------------------------	
	protected function contarPasantiasActivas($idTutor, $tipoTutor){
		$nroPasantias=0;
		$pasantia = new Pasantia();
		$nroPasantias = $pasantia->contarPasantiasActivasTutor($idTutor, $tipoTutor);
		return $nroPasantias;
	}
//-----------------------------------------------------------------------------------------
	public function eliminarTutorAction(){
		$this->setResponse('ajax');
		$id = $this->getRequestParam('pTutor');
		/*
		 * WARNING:: El parametro no puede ser 'E', E es de Empresarial. 
		 */
		$nro= $this->contarPasantiasActivas($id, 'E');
		$resp= array();
		$resp['success']= false;
		$resp['errorMsj']= $nro;
		if ($nro==0){ // Si no tiene pasantias activas, es candidado a eliminar
			$tutor = new TutorAcademico();
			if (!$tutor->eliminarTutor($id)){
				$resp['errorMsj']= utf8_encode('Error al eliminar.');
			}else{
				$resp['success']=true;
			}
		}else{
			$resp['errorMsj']= utf8_encode('El tutor tiene '.$nro.' pasantía(s) activa(s) asociada(s).');
		}
		$this->renderText(json_encode($resp));
	}
//-----------------------------------------------------------------------------------------
	public function registrarTutorAAction(){
		$resp=array();
		$resp['success']= false;
		$successUser= false;
		$successRegistro= false;
		$this->setResponse('ajax');
		$tutorA = new TutorAcademico();
		$departamento=$this->getRequestParam('departamento');
		
		$cedula=$this->getRequestParam('txtCedula');
		$nombre=utf8_decode($this->getRequestParam('txtNombre'));
		$apellido=utf8_decode($this->getRequestParam('txtApellido'));
		$telefono= ($this->getRequestParam('txtTelefono')==null?'':$this->getRequestParam('txtTelefono'));
		$correo = $this->getRequestParam('txtCorreo');
		$cargo= utf8_decode($this->getRequestParam('txtCargo'));
		$tipoEvento = $this->getRequestParam('tipoevento');
		$idDep = $this->getRequestParam('departamento');
		$idDecanato = $this->getRequestParam('decanato');
		
		$decanato = new Decanato();
		$dec = 0;
		$dec = $decanato->getUniversidadByDecanato($idDecanato);
		if ($dec == 1){
			$dependencia = 1;
		} else $dependencia = 2;
		
		if ($tipoEvento=='registrar'){
			$aux = $tutorA->guardarTutorA($idDep,$cedula,$nombre,$apellido,$telefono,$correo,$cargo,$dependencia);
		} 
		else {
			$aux = $tutorA->actualizarTutorA($idDep,$cedula,$nombre,$apellido,$telefono,$correo,$cargo,$dependencia);
			$successUser = true;
			$successRegistro = true;
		}
		
		if ($aux['correo']){
			$id = $aux['id'];
			$hora=date("G:H:s");
			$hash = md5($nombre.$id.$hora);
			$usuario = new Usuario();
			$successUser = $usuario->registrarUsuario('tmpAcademico'.$id, md5('tmpAcademico'.$id), CAT_USUARIO_TUTOR_ACAD, $id, 'P');
			
			$registro = new Registro();
			$successRegistro = 	$registro->guardarRegistro($hash, 'tmpAcademico'.$id, $correo, 'P');
			$this->notificarRegistro($hash,$nombre,$apellido,$correo);
		}
		if (($aux['success'] and  $successRegistro and $successUser)== true ){
			$resp['success']=true;
		}
		
		$this->renderText(json_encode(array($resp,'success'=>$aux['success'], 'pasaPor'=>$aux['pasaPor'], 'id'=>$aux['id'])));
	}
//-----------------------------------------------------------------------------------------
	protected function notificarRegistro($hash,$nombre,$apellido,$pCorreo) {

		$correo = new Correo();
		$body ='Experientia (Sistema para la gestión de pasantias  UCLA- DCyT) le informa que ud. ha sido registrado bajo el nombre: <BR>';
		$body .= $nombre.' '.$apellido.'.  <BR/>
		  		 Para completar la siguiente etapa de su registro por favor haga clic en el siguiente enlace o copielo en la barra de direcciones de su navegador.<BR/>';
		$body .='http://'. $this->getServer('SERVER_NAME').'/SIGP/registro/registrarTutorAcademico?id='.$hash;
		$correo->enviarCorreo($pCorreo, 'Registro en el sistema', $body);
	}
//-----------------------------------------------------------------------------------------
	protected function notificarCulminacionFaseDos($usuario,$pCorreo) {

		$correo = new Correo();
		$body ='Experientia (Sistema para la gestión de pasantias  UCLA- DCyT) le informa que ud. ha finalizado exitosamente la segunda etapa del registro.<BR>';
		$body .='Ud. ya puede iniciar sesi&oacute;n usando el usuario: '.$usuario.'  <BR/>';
		$body .='http://'. $this->getServer('SERVER_NAME').'/SIGP/';
		$correo->enviarCorreo($pCorreo, 'Registro Fase 2 en el sistema', $body);
	}
//-----------------------------------------------------------------------------------------
	public function getTutoresAcademicosAction(){
		$id = $this->getRequestParam('pDepartamento_id')!=NULL ? $this->getRequestParam('pDepartamento_id') : '%' ;
		$start = $this->obtenerParametroRequest('start');
		$limit = $this->obtenerParametroRequest('limit');

		$this->setResponse('ajax');
		$tutores = new TutorAcademico();
		$resultado = $tutores->getTutoresAcademicos($id,$start,$limit);
		$this->renderText(json_encode($resultado));
	}
//-----------------------------------------------------------------------------------------
	public function getTutorAcademicoByIdAction(){
		$success= false;
		$this->setResponse('ajax');
		$id=$this->getRequestParam('pTutorAcademicoId');
		$tutor = new TutorAcademico();
		$resultado = $tutor->getTutorAcademicoById($id);
		if ($resultado){
			$success =  true;
		}
		$this->renderText(json_encode(array("success"=>$success,
											"resultado"=> $resultado)));
	}
//-----------------------------------------------------------------------------------------
	public function buscarTutorAcademicoAction(){
		$resp=array();
		$pCedula = $this->getRequestParam('cedula');
		$pDepartamento_id =$this->auth['idUsuario'];
		$this->setResponse('ajax');
		$tutorA = new TutorAcademico();
		$resp=$tutorA->buscarTutorAcademico($pCedula,$pDepartamento_id);
		$this->renderText(json_encode($resp));
	}
//-----------------------------------------------------------------------------------------
	public function buscarTutorAcadAction(){
		$resp=array();
		$pCedula = $this->getRequestParam('cedula');
		$this->setResponse('ajax');
		$tutorA = new TutorAcademico();
		$resp=$tutorA->buscarTutorAcad($pCedula);
		$this->renderText(json_encode($resp));
	}
//-----------------------------------------------------------------------------------------

	public function getTutoresAcademicosLightAction(){
		$start = $this->obtenerParametroRequest('start');
		$limit = $this->obtenerParametroRequest('limit');
		$nombre = $this->getParametro('query','string','');
		
		$this->setResponse('ajax');
		$tutores = new TutorAcademico();
		$resultado = $tutores->getTutoresAcademicosLight($nombre,$start,$limit);
		$this->renderText(json_encode($resultado));
	}
	
}
?>