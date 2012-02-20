<?php
require_once('correo.php');
require_once('utilities/constantes.php');

class EmpleadoController extends ApplicationController{

	protected   $auth;

	protected function initialize(){
		$this->setTemplateAfter("menu");
		$this->auth=Auth::getActiveIdentity();
	}



	/**
	 * Busca la informacion, segun los parametros enviados en el request
	 */
	public function buscarAction(){
		$resp=array();
		$pCedula = $this->getRequestParam('pCedula');
		$this->setResponse('ajax');
		$empleado = new Empleado();
		$resp=$empleado->buscar($pCedula);
		$this->renderText(json_encode($resp));

	}

	public function registrarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$successUser= false;
		$successRegistro= false;
		$this->setResponse('ajax');
		$idEmpresa=$this->auth['idUsuario'];
		if ($this->auth['categoriaUsuario_id']==CAT_USUARIO_ADMINISTRADOR){
			$empleado = new Empleado();
			$cedula=$this->getParametro('txtCedula','string','');
			$nombre=utf8_decode($this->getParametro('txtNombre','string',''));
			$apellido=utf8_decode($this->getParametro('txtApellido','string',''));
			$correo = $this->getParametro('txtCorreo','string','');
			$pRadioTipo = $this->getParametro('pRadioTipo', 'string', '');
			$decanato=DECANATO_CIENCIAS;
			if ($cedula!='' and $nombre!='' and $apellido!='' and $correo!='' and $pRadioTipo!=''){
				switch (strtoupper($pRadioTipo)) {
					case 'A':
						$categoria= CAT_USUARIO_ADMINISTRADOR;
						break;
					case 'C':
						$categoria= CAT_USUARIO_COORDINADOR;
						break;
					default:
						$categoria= CAT_USUARIO_ANALISTA;
						break;
				}
				$coordinacion = new Coordinacion();
				$existeCordinador = ($coordinacion->getDatosCoordinador($decanato))?true:false;
				if (($categoria== CAT_USUARIO_COORDINADOR and !$existeCordinador)or $categoria!= CAT_USUARIO_COORDINADOR  ){
					$aux = $empleado->guardar($cedula,$nombre,$apellido,$correo,strtoupper($pRadioTipo),$decanato);
					if ($aux['correo']){
						$coordinacion->asignarCoordinador($decanato,$aux['id']);
						$id = $aux['id'];
						$hora=date("G:H:s");
						$hash = md5($nombre.$id.$hora);
						$usuario = new Usuario();
						$successUser = $usuario->registrarUsuario('empleadoTemp'.$id, md5('empleadoTemp'.$id), $categoria, $id, 'P');
						$registro = new Registro();
						$successRegistro = 	$registro->guardarRegistro($hash, 'empleadoTemp'.$id, $correo, 'P');
						$this->notificarRegistro($hash,$nombre,$apellido,$correo);
					}else{//Si no se envia correo es actualizacion, asi que successRegistro y user no se toman
						$successRegistro=true;
						$successUser=true;
					}
					if (($aux['success'] and  $successRegistro and $successUser)== true ){
						$resp['success']=true;
					}
				}else{
					$resp['errorMsj']= 'Ya existe un coordinador registrado.';
				}

			}else{
				$resp['errorMsj']= 'Par�metros incompletos.';
			}
		}else{
			$resp['errorMsj']= 'Ud. no posee la permisologia para realizar esta operaci&oacute;n.';
		}
		$resp['errorMsj']=utf8_encode($resp['errorMsj']);
		$this->renderText(json_encode($resp));

	}

	protected function notificarRegistro($hash,$nombre,$apellido,$pCorreo) {

		$correo = new Correo();
		$body ='Experientia (Sistema para la gesti�n de pasantias  UCLA- DCyT) le informa que ud. ha sido registrado bajo el nombre: <BR>';
		$body .= $nombre.' '.$apellido.'.  <BR/>
		  		 Para completar la siguiente etapa de su registro por favor haga clic en el siguiente enlace o copielo en la barra de direcciones de su navegador.<BR/>';
		$body .='http://'. $this->getServer('SERVER_NAME').'/SIGP/registro/confirmarRegistroEmp?id='.$hash;
		$correo->enviarCorreo($pCorreo, 'Registro en el sistema', $body);
	}


	public function confirmarAction(){
		$success= false;
		$id=0;
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
				$id= $aux['idUsuario'];
				$empleado = new Empleado();
				if ($empleado->activarEmpleado($id)){
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

	protected function notificarCulminacionFaseDos($usuario,$pCorreo) {

		$correo = new Correo();
		$body ='Experientia (Sistema para la gesti�n de pasantias  UCLA- DCyT) le informa que ud. ha finalizado exitosamente la segunda etapa del registro.<BR>';
		$body .='Ud. ya puede iniciar sesi&oacute;n usando el usuario: '.$usuario.'  <BR/>';
		$body .='http://'. $this->getServer('SERVER_NAME').'/SIGP/';
		$correo->enviarCorreo($pCorreo, 'Registro Fase 2 en el sistema', $body);
	}

	public function actualizarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$this->setResponse('ajax');
		if ($this->auth['categoriaUsuario_id']==CAT_USUARIO_ADMINISTRADOR){
			$empleado = new Empleado();
			$nombre=utf8_decode($this->getParametro('txtNombre','string',''));
			$apellido=utf8_decode($this->getParametro('txtApellido','string',''));
			$correo = $this->getParametro('txtCorreo','string','');
			$id = $this->getParametro('txtIdEmpleado', 'numerico', -1);
			$decanato=DECANATO_CIENCIAS;
			if ( $nombre!='' and $apellido!='' and $correo!='' and $id!=-1){
				$resp['success']= $empleado->actualizar($id, $nombre, $apellido, $correo);
			}else{
				$resp['errorMsj']= 'Par�metros incompletos.';
			}
		}else{
			$resp['errorMsj']= 'Ud. no posee la permisologia para realizar esta operaci&oacute;n.';
		}
		$resp['errorMsj']=utf8_encode($resp['errorMsj']);
		$this->renderText(json_encode($resp));

	}

	public function eliminarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$this->setResponse('ajax');
		$idUsuario=$this->auth['idUsuario'];
		if ($this->auth['categoriaUsuario_id']==CAT_USUARIO_ADMINISTRADOR){
			$id = $this->getParametro('pEmpleadoId', 'numerico', -1);
			if ( $id!=-1){
				if ($id!=$idUsuario){
					$empleado = new Empleado();
					$decanato=DECANATO_CIENCIAS;
					$datos=$empleado->buscarbyId($id);
					if ($datos){
						switch (strtoupper($datos['tipo'])) {
							case 'A':
								$categoria= CAT_USUARIO_ADMINISTRADOR;
								break;
							case 'C':
								$categoria= CAT_USUARIO_COORDINADOR;
								break;
							default:
								$categoria= CAT_USUARIO_ANALISTA;
								break;
						}
						$usuario= new Usuario();
						$usuario->eliminar($id, $categoria);
					}
					$resp['success']= $empleado->eliminar($id);
				}else{
					$resp['errorMsj']= 'No se permite la auto-eliminaci�n.';
				}
			}else{
				$resp['errorMsj']= 'Par�metros incompletos.';
			}
		}else{
			$resp['errorMsj']= 'Ud. no posee la permisologia para realizar esta operaci&oacute;n.';
		}
		$resp['errorMsj']=utf8_encode($resp['errorMsj']);
		$this->renderText(json_encode($resp));

	}

}
?>
