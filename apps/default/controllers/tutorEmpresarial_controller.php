<?php
require_once('correo.php');
require_once('utilities/constantes.php');

class TutorEmpresarialController extends ApplicationController{

	protected   $auth;

	protected function initialize(){
		$this->setTemplateAfter("menu");
		$this->auth=Auth::getActiveIdentity();
	}

	public function indexAction(){
		$this->routeTo('action: gestionar');
	}

	public function actualizarAction(){}

	public function gestionarAction(){
		$this->setParamToView('idUsuario', $this->auth['idUsuario']);
	}

	/**
	 * Confirmacion de fase dos del registro de los tutores empresariales
	 */
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
				$tutorAux = new TutorEmpresarial();
				if ($tutorAux->activarTutor($idTutor)){
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
	/**
	 * Cuenta las pasantias activas del tutor indicado.
	 * @param int $idTutor
	 * @param string $tipoTutor
	 * @return int
	 */
	protected function contarPasantiasActivas($idTutor, $tipoTutor){
		$nroPasantias=0;
		$pasantia = new Pasantia();
		$nroPasantias = $pasantia->contarPasantiasActivasTutor($idTutor, $tipoTutor);
		return $nroPasantias;
	}

	/**
	 * Elimina el tutor indicado. Recibe un parametro a través de la var REQUEST
	 */
	public function eliminarTutorAction(){
		$this->setResponse('ajax');
		$id = $this->getRequestParam('pTutor');
		$nro= $this->contarPasantiasActivas($id, 'E');
		$resp= array();
		$resp['success']= false;
		$resp['errorMsj']= $nro;
		if ($nro==0){ // Si no tiene pasantias activas, es candidado a eliminar
			$tutor = new TutorEmpresarial();
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


	/**
	 * Crea y/o actualiza la informacion de un tutor empresarial
	 */
	public function registrarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$successUser= false;
		$successRegistro= false;
		$this->setResponse('ajax');
		$idEmpresa=$this->auth['idUsuario'];
		if ($this->auth['categoriaUsuario_id']==CAT_USUARIO_EMPRESA){
			$tutorE = new TutorEmpresarial();
			$cedula=$this->getParametro('txtCedula','string','');
			$nombre=utf8_decode($this->getParametro('txtNombre','string',''));
			$apellido=utf8_decode($this->getParametro('txtApellido','string',''));
			$telefono= $this->getParametro('txtTelefono','string','');
			$correo = $this->getParametro('txtCorreo','string','');
			$cargo= utf8_decode($this->getParametro('txtCargo','string',''));
			if ($cedula!='' and $nombre!='' and $apellido!='' and $correo!='' and $cargo!=''){
				$aux = $tutorE->guardarTutorE($idEmpresa,$cedula,$nombre,$apellido,$telefono,$correo,$cargo);
				if ($aux['correo']){
					$id = $aux['id'];
					$hora=date("G:H:s");
					$hash = md5($nombre.$id.$hora);
					$usuario = new Usuario();
					$successUser = $usuario->registrarUsuario('temporal'.$id, md5('temporal'.$id), CAT_USUARIO_TUTOR_EMP, $id, 'P');
					$registro = new Registro();
					$successRegistro = 	$registro->guardarRegistro($hash, 'temporal'.$id, $correo, 'P');
					$this->notificarRegistro($hash,$nombre,$apellido,$correo);
				}else{//Si no se envia correo es actualizacion, asi que successRegistro y user no se toman
					$successRegistro=true;
					$successUser=true;
				}
				if (($aux['success'] and  $successRegistro and $successUser)== true ){
					$resp['success']=true;
				}
			}else{
				$resp['errorMsj']= 'Parámetros incompletos.';
			}
		} else if ($this->auth['categoriaUsuario_id'] == CAT_USUARIO_TUTOR_EMP){
			$idTutor = $this->auth['idUsuario'];
			$respE = array();
			$tutorE = new TutorEmpresarial();
			$respE = $tutorE->getTutorEmpresarialById($idTutor);

			$cedula= $respE['cedula'];
			$idEmpresa = $respE['empresa'];
			$nombre = utf8_decode($this->getParametro('txtNombre','string',''));
			$apellido = utf8_decode($this->getParametro('txtApellido','string',''));
			$telefono = $this->getParametro('txtTelefono','string','');
			$correo = $this->getParametro('txtCorreo','string','');
			$cargo= utf8_decode($this->getParametro('txtCargo','string',''));

			if ($nombre!='' and $apellido!='' and $correo!='' and $cargo!=''){
				$aux = $tutorE->guardarTutorE($idEmpresa,$cedula,$nombre,$apellido,$telefono,$correo,$cargo);
			}

		} else{
			$resp['errorMsj']= 'Ud. no posee la permisologia para realizar esta operaci&oacute;n.';
		}
		$resp['errorMsj']=utf8_encode($resp['errorMsj']);
		$this->renderText(json_encode($resp));
	}


	/**
	 * Envia un correo electronico a la direccion dada, con un enlace para realizar la etapa 2
	 * (creacion de usuario y contraseña)  del tutor empresarial.
	 * @param string $nombre
	 * @param string $apellido
	 * @param string $correo
	 */
	protected function notificarRegistro($hash,$nombre,$apellido,$pCorreo) {

		$correo = new Correo();
		$body ='Experientia (Sistema para la gestión de pasantias  UCLA- DCyT) le informa que ud. ha sido registrado bajo el nombre: <BR>';
		$body .= $nombre.' '.$apellido.'.  <BR/>
		  		 Para completar la siguiente etapa de su registro por favor haga clic en el siguiente enlace o copielo en la barra de direcciones de su navegador.<BR/>';
		$body .='http://'. $this->getServer('SERVER_NAME').'/SIGP/registro/registrarTutor?id='.$hash;
		$correo->enviarCorreo($pCorreo, 'Registro en el sistema', $body);
	}
	/**
	 * Envia un correo electronico a la direccion dada, con informacion de culminacion del registro del tutor fase dos.
	 * @param string $usuario
	 * @param string $correo
	 */
	protected function notificarCulminacionFaseDos($usuario,$pCorreo) {

		$correo = new Correo();
		$body ='Experientia (Sistema para la gestión de pasantias  UCLA- DCyT) le informa que ud. ha finalizado exitosamente la segunda etapa del registro.<BR>';
		$body .='Ud. ya puede iniciar sesi&oacute;n usando el usuario: '.$usuario.'  <BR/>';
		$body .='http://'. $this->getServer('SERVER_NAME').'/SIGP/';
		$correo->enviarCorreo($pCorreo, 'Registro Fase 2 en el sistema', $body);
	}

	/**
	 * Obtiene la lista de los tutores empresarial es asociados a la empresa, usado para las grid paginables.
	 */
	public function getTutoresEmpresarialesAction(){
		$id = $this->getRequestParam('pEmpresa_id')!=NULL ? $this->getRequestParam('pEmpresa_id') : '%' ;
		$start = $this->obtenerParametroRequest('start');
		$limit = $this->obtenerParametroRequest('limit');

		$this->setResponse('ajax');
		$tutores = new TutorEmpresarial();
		$resultado = $tutores->getTutoresEmpresariales($id,$start,$limit);
		$this->renderText(json_encode($resultado));
	}

	/**
	 * Busca la informacion del tutor empresarial por su id
	 */
	public function getTutorEmpresarialByIdAction(){
		$success= false;
		$this->setResponse('ajax');
		$id=$this->getRequestParam('pTutorEmpresarialId');
		$tutor = new TutorEmpresarial();
		$resultado = $tutor->getTutorEmpresarialById($id);
		if ($resultado){
			$success =  true;
		}
		$this->renderText(json_encode(array("success"=>$success,
											"resultado"=> $resultado)));

	}

	/**
	 * Busca la informacion del tutor empresarial, segun los parametros enviados en el request
	 */
	public function buscarTutorEmpresarialAction(){
		$resp=array();
		$pCedula = 0;
		$pEmpresa_id = 0;
		if ($this->auth['categoriaUsuario_id'] == CAT_USUARIO_TUTOR_EMP){
			$idTutor = $this->auth['idUsuario'];
			$respE = array();
			$tutorEmp = new TutorEmpresarial();
			$respE = $tutorEmp->getTutorEmpresarialById($idTutor);

			$pCedula = $respE['cedula'];
			$pEmpresa_id = $respE['empresa'];
		} else {
			$pCedula = $this->getRequestParam('cedula');
			$pEmpresa_id =$this->auth['idUsuario'];
			$this->setResponse('ajax');
		}

		$tutorE = new TutorEmpresarial();
		$resp=$tutorE->buscarTutorEmpresarial($pCedula,$pEmpresa_id);
		$this->renderText(json_encode($resp));
	}

	/**
	 * Obtiene los tutores emrpesariales de una empresa
	 */
	public function getTutoresAction(){
		$this->setResponse('ajax');
		$tutor = new TutorEmpresarial();
		$pEmpresa_id =$this->auth['idUsuario'];
		$this->renderText(json_encode($tutor->getTutores($pEmpresa_id)));
	}


}
?>