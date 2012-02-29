<?php
require_once('correo.php');
require_once('utilities/constantes.php');

class RegistroController extends ApplicationController{
	public $hash;
	public  $contenido;
	protected   $auth;

	protected function initialize(){

		$this->auth=Auth::getActiveIdentity();
	}

	public function empresaAction(){

	}
	public function indexAction(){}

	public function pasanteAction(){}

	public function tutorAction(){}


	public function redirectAction(){
		$this->routeTo('controller: login','action: index');
	}


	public function activarRegistroEmpresaAction(){
		$success= false;
		$idTutor=0;
		$aux=array();
		$this->setResponse('ajax');
		$hash= $this->getParametro('txtHash','string','');
		$userName = $this->getParametro('txtUsuario','string','');
		$clave = $this->getParametro('pClave','string','');
		if ($hash!='' and $userName!='' and $clave!=''){
			$registroAux = new Registro();
			$usuarioTemporal=$registroAux->getUsuariobyHash($hash);
			$claveTemporal=md5($usuarioTemporal);
			$flag=$registroAux->activarRegistro($hash,$userName);
			if ($flag){
				$usuario = new Usuario();
				$aux = $usuario->activarUsuario($usuarioTemporal, $claveTemporal, $userName, $clave);
				if ($aux['success']){
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

	public function confirmarEmpresaAction(){
		$id= $this->getParametro('id','string','');
		$registroAux = new Registro();
		$registro = $registroAux->findFirst("hash='$id'");
		if ($registro){
			if ($registro->getEstatus()=='A') {
				Router::routeToURI('/registro/error/usuarioYaActivado');
			}else{
				$this->setParamToView('hash',$id);
			}
		}else{
			Router::routeToURI('/registro/error/noEncontrado');
		}
	}
	/**
	 * Confirma el resgistro de la empresa y la activa.
	 */
	public function confirmarRegistroAction(){
		$id= $this->getRequestParam('id');
		$registroAux = new Registro();
		$registro = $registroAux->findFirst("hash='$id'");
		if ($registro){
			if ($registro->getEstatus()=='P'){
				//Se activa el usuario
				$registro->setEstatus('A');
				$successRegistro = $registro->update();
				$usuarioAux= new Usuario();
				$usuario = $usuarioAux->findFirst("nombreUsuario='{$registro->getUsuario()}'");
				$empresaAux = new Empresa();
				$empresa = $empresaAux->findFirst("id='{$usuario->getIdUsuario()}'");
				$empresa->setEstatus('I');
				$successEmpresa = $empresa->update();
			}else{
				Router::routeToURI('/registro/error/usuarioYaActivado');
			}

		}else {
			$this->routeTo('action: index');
		}
	}

	/**
	 * Redirecciona segun el estatus del registro del tutor, para que complete la fase dos se su registro.
	 * O la pagina apropiada segun sea el caso.
	 */
	public function registrarTutorAction(){
		$id= $this->getRequestParam('id');
		$registroAux = new Registro();
		$registro = $registroAux->findFirst("hash='$id'");
		if ($registro){
			if ($registro->getEstatus()=='A') {
				Router::routeToURI('/registro/error/usuarioYaActivado');
			}else{
				$this->hash=$id;
			}
		}else{
			Router::routeToURI('/registro/error/noEncontrado');
		}
			
	}

	public function registrarTutorAcademicoAction(){
		$id= $this->getRequestParam('id');
		$registroAux = new Registro();
		$registro = $registroAux->findFirst("hash='$id'");
		if ($registro){
			if ($registro->getEstatus()=='A') {
				Router::routeToURI('/registro/error/usuarioYaActivado');
			}else{
				$this->hash=$id;
			}
		}else{
			Router::routeToURI('/registro/error/noEncontrado');
		}
			
	}
	/**
	 *  Registra la empresa, ademas de crear un usuario asociado a esta.
	 */
	public function registrarEmpresaAction(){
		$success= true;
		$this->setResponse('ajax');
		$successEmpresa=$successUsuario=$successRegistro=false;

		$empresa = new Empresa();
		$rif= $this->getParametro('txtRif','string','');
		$razonSocial = utf8_decode($this->getParametro('txtRazonSocial','string',''));
		$direccion =utf8_decode($this->getParametro('txtDireccion','string',''));
		$estado=$this->getParametro('estado','numerico',-1);
		$ciudad=$this->getParametro('ciudad','numerico',-1);
		$telefono= $this->getParametro('txtTelefono','string','');
		$telefono2=$this->getParametro('txtTelefono2','string','');
		$descripcion=utf8_decode($this->getParametro('txtDescripcion','string',''));
		$web=utf8_decode($this->getParametro('txtWeb','string',''));
		$representante=utf8_decode($this->getParametro('txtRepresentante','string',''));
		$correo=$this->getParametro('txtCorreo','string','');
		$cargo=utf8_decode($this->getParametro('txtCargo','string',''));
		$estatusEmpresa='R';
		if ($rif!='' and $razonSocial!='' and $direccion!='' and $estado!=-1 and $ciudad!=-1 and
		$telefono!='' and $descripcion!='' and $representante!='' and $correo!=''and  $cargo!='' ){
			if ($this->auth and  $this->auth['categoriaUsuario_id']==CAT_USUARIO_COORDINADOR){
				$estatusEmpresa='A';
			}
			$successEmpresa = $empresa->registrarEmpresa($rif,$razonSocial,$direccion,$estado,$ciudad,$telefono,$telefono2,$descripcion,$web,$representante,$correo,$cargo,$estatusEmpresa);
			if ($successEmpresa){
				$usuario = new Usuario();
				if ($estatusEmpresa=='R'){
					$nombreUsuario=$this->getParametro('txtUsuario','string','');
					$clave=$this->getParametro('clave','string','');
				}else{
					$nombreUsuario='tempEmpresa'.$successEmpresa;
					$clave=md5($nombreUsuario);
				}
				$categoria=CAT_USUARIO_EMPRESA;
				$idUsuario=$successEmpresa;
				$estatusUsuario='P';
				$successUsuario = $usuario->registrarUsuario($nombreUsuario,$clave,$categoria,$idUsuario,$estatusUsuario);
				if ($successUsuario){
					$registro = new Registro();
					$hora=date("G:H:s");
					$hash = md5($nombreUsuario.$hora);
					$registroUsuario=$nombreUsuario;
					$registroEmail=$correo;
					$registroEstatus='P';
					$successRegistro = $registro->guardarRegistro($hash,$registroUsuario,$registroEmail,$registroEstatus);
				}
			}
			if (!($successEmpresa && $successUsuario && $successRegistro)){
				$success =  false;
			}
			else {
				if ($estatusEmpresa=='R'){
					$this->notificarRegistro($hash,$correo);
				}else{
					$this->notificarRegistroDirecto($hash, $correo, $razonSocial);
				}
			}
		}else {
			$success =  false;
		}

		$this->renderText(json_encode(array("success"=>$success)));
	}


	/**
	 * Busca coincidencias con el nombre de usuario pasado en el request
	 */
	public function findUsernameAction(){
		$username = $this->getRequestParam('username');
		$this->setResponse('ajax');
		$usuario = new Usuario();
		$this->renderText($usuario->existebyUsername($username));
	}

	/**
	 * Envia correo electronico a la cuenta del usuario para solicitar confimacion
	 * @param string $hash
	 * @param string $correo
	 */
	protected function notificarRegistro($hash,$correo){
		$mailer = new Correo();
		$body ='Gracias por su registro. <BR/>
		  		  Para confirmar su registro por favor haga clic en el siguiente enlace o copielo en la barra de direcciones de su navegador.<BR/>';
		$body .='http://'. $this->getServer('SERVER_NAME').'/SIGP/registro/confirmarRegistro?id='.$hash;
		$mailer->enviarCorreo($correo, 'Registro en el sistema', $body);
	}

	protected function notificarRegistroDirecto($hash,$correo,$empresa){
		$mailer = new Correo();
		$body ='Experientia (Sistema para la gestión de pasantias  UCLA-DCyT) le informa que ud. ha sido registrado como: <BR>';
		$body .= $empresa.'.  <BR/>
		  		 Para completar la siguiente etapa de su registro por favor haga clic en el siguiente enlace o copielo en la barra de direcciones de su navegador.<BR/>';
		$body .='http://'. $this->getServer('SERVER_NAME').'/SIGP/registro/confirmarEmpresa?id='.$hash;
		$mailer->enviarCorreo($correo, 'Registro en el sistema', $body);
	}

	/**
	 * Action apra manejar los cursos atipicos en el registro
	 * @param string $msj
	 */
	public function errorAction($msj){
		$msj = (string)$msj;
		switch ($msj) {
			case 'usuarioYaActivado':
				$this->contenido='El usuario ya ha sido activado.<BR>
				Para ingresar al sistema inicie sesi&oacute;n.';
				break;
			case 'noEncontrado':
				$this->contenido='Informaci&oacute;n de registro no v&alido.';
				break;
			default:
				$this->contenido='Registro no encontrado.';
				break;
		}
	}

	public function activarAction(){


	}
	/**
	 * Envia un correo electronico a la direccion dada, con informacion de culminacion del registro de la empresa .
	 * @param string $usuario
	 * @param string $correo
	 */
	protected function notificarCulminacionFaseDos($usuario,$pCorreo) {

		$correo = new Correo();
		$body ='Experientia (Sistema para la gestión de pasantias  UCLA- DCyT) le informa que ud. ha finalizado exitosamente su registro.<BR>';
		$body .='Ud. ya puede iniciar sesi&oacute;n usando el usuario: '.$usuario.'  <BR/>';
		$body .='http://'. $this->getServer('SERVER_NAME').'/SIGP/';
		$correo->enviarCorreo($pCorreo, 'Registro en el sistema', $body);
	}


	public function confirmarRegistroEmpAction(){
		$id= $this->getRequestParam('id');
		$registroAux = new Registro();
		$registro = $registroAux->findFirst("hash='$id'");
		if ($registro){
			if ($registro->getEstatus()=='A') {
				Router::routeToURI('/registro/error/usuarioYaActivado');
			}else{
				$this->setParamToView('hash', $id);
			}
		}else{
			Router::routeToURI('/registro/error/noEncontrado');
		}
			
	}

	public function empresaRegistradaAction(){
		$resultado = array();
		$this->setResponse('ajax');
		$categoria=$this->auth['categoriaUsuario_id'];
		if ($categoria == CAT_USUARIO_EMPRESA){
			$id = $this->auth['idUsuario'];
		}

		if($id!=-1){
			$empresa = new Empresa();
			$resultado=$empresa->getEmpresa($id);
		}
		$this->renderText(json_encode(array("success"=>($resultado)?true:false,
											"resultado"=>$resultado)));
	}

}

?>