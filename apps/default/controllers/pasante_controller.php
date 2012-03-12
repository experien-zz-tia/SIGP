<?php
require_once 'correo.php';
require_once('utilities/constantes.php');
/**
 * Clase controladora de  als acciones relacionadas al pasante
 * @author Yajaira S.
 * @author Robert A.
 *
 */
class PasanteController extends ApplicationController{

	protected   $auth;

	protected function initialize(){
		$this->setTemplateAfter("menu");
		$this->auth=Auth::getActiveIdentity();
	}

	public function gestionarAction(){}
	//-----------------------------------------------------------------------------------------
	public function actualizarAction(){}
	//-----------------------------------------------------------------------------------------
	public function indexAction(){}
	//-----------------------------------------------------------------------------------------
	public function pasanteAction(){}
	//-----------------------------------------------------------------------------------------
	public function redirectAction(){
		$this->routeTo('controller: pasante','action: index');
	}
	//-----------------------------------------------------------------------------------------
	public function eliminarPasanteAction(){
		$this->setResponse('ajax');
		$id = $this->getRequestParam('idPasante');
		$idPasantia = $this->getRequestParam('idPasantia');
		$resp= array();
		$resp['success']= false;
		$resp['errorMsj']= '';

		$pasantia = new Pasantia();
		if ($pasantia->eliminarPasantia($idPasantia, $id)){
			$pasante = new Pasante();
			if ($pasante->eliminarPasante($id)){
				$solicitud = new Solicitudtutoracademico();
				if ($solicitud->retirarSolicitudes($id)){
					$resp['success']=true;
				} else $resp['errorMsj']= 'solicitudes';

				$usuario = new Usuario();
				$usuario->buscarEliminarUserPasante($id);

			} else $resp['errorMsj']= 'pasante';
		} else $resp['errorMsj']= 'pasantia';

		$this->renderText(json_encode($resp));
	}

	//-----------------------------------------------------------------------------------------
	public function actualizarPasanteAction(){
		$success = true;
		$this->setResponse('ajax');
		$resp=array();
		$pasante = new Pasante();
		$idPasante = 0;
		$cedula = 0;
		if ($this->auth['categoriaUsuario_id']==CAT_USUARIO_PASANTE){
			$idPasante = $this->auth['idUsuario'];

		}

		if (($this->getRequestParam('id')) != "-"){
			$idPasante = $this->getRequestParam('id');
		}
		$this->setResponse('ajax');
		$cedula = $pasante->buscarCedulaById($idPasante);
		$fchNacimiento = $this->getRequestParam('dataFecha');
		//$nombre = $this->getRequestParam('txtNombre');

		$nombre = utf8_decode($this->getRequestParam('txtNombre'));
		$apellido = utf8_decode($this->getRequestParam('txtApellido'));
		$opcF = $this->getRequestParam('opcF');

		$sexo = '';
		if ($opcF==true){
			$sexo = 'F';
		} else {$sexo == 'M';}

		$telefono = $this->getRequestParam('txtTelefono');
		$direccion = $this->getRequestParam('txtDireccion');
		$decanato = $this->getRequestParam('decanato');
		$carrera = $this->getRequestParam('carrera');
		$semestre = $this->getRequestParam('cmbSemestre');
		$indice = $this->getRequestParam('txtIndice');
		$tipoPasantia = $this->getRequestParam('tipoPasantia');
		$modalidad = $this->getRequestParam('modalidad');
		$ciudad = $this->getRequestParam('ciudad');
		$estado = $this->getRequestParam('estado');
		$email = $this->getRequestParam('txtCorreo');

		$successPasante = true;
		$successRegistro = false;
		$successUsuario = false;
		$idUsuario = 0;
		$successPasante = $pasante->ActualizarPasante($cedula,$fchNacimiento,$nombre,$apellido,$sexo,
		$carrera,$semestre,$indice,$tipoPasantia,$modalidad,$direccion,$estado,$ciudad,$telefono,
		$email);

		if (!($successPasante)){
			$success =  false;
		}
		else {
			$correo = new Correo();
			$body ='Gracias por actualizar sus datos. <BR/>
			  	Recientemente se han actualizado sus datos en la base de datos de Experientia. Si usted no ha 
			  	realizado este procedimiento contacte a su Coordinador de Pasantías.<BR/>';
			$correo->enviarCorreo($this->getRequestParam('txtCorreo'), 'Actualización de Datos', $body);
		}
			

		$this->renderText(json_encode(array("success"=>$success, "pasante"=>$successPasante, "id"=>$idPasante)));
	}
	//-----------------------------------------------------------------------------------------
	public function registrarPasanteAction(){
		$success = true;
		$this->setResponse('ajax');
		$pasante = new Pasante();
			
		$cedula = $this->getRequestParam('txtCedula');
		$fchNacimiento = $this->getRequestParam('dataFecha');
		$nombre = $this->getRequestParam('txtNombre');
		$apellido = $this->getRequestParam('txtApellido');
		$opcF = $this->getRequestParam('opcF');

		$sexo = '';
		if ($opcF==true){
			$sexo = 'F';
		} else {$sexo = 'M';}

		$telefono = $this->getRequestParam('txtTelefono');
		$direccion = $this->getRequestParam('txtDireccion');
		$decanato = $this->getRequestParam('decanato');
		$carrera = $this->getRequestParam('carrera');
		$semestre = $this->getRequestParam('cmbSemestre');
		$indice = $this->getRequestParam('txtIndice');
		$tipoPasantia = $this->getRequestParam('tipoPasantia');
		$modalidad = $this->getRequestParam('modalidad');
		$ciudad = $this->getRequestParam('ciudad');
		$estado = $this->getRequestParam('estado');
		$email = $this->getRequestParam('txtCorreo');
		$usuario = $this->getRequestParam('txtUsuario');
		$clave=$this->getRequestParam('clave');

		$successPasante = true;
		$successRegistro = false;
		$successUsuario = false;
		$idUsuario = 0;
		$successPasante = $pasante->registrarPasante($cedula,$fchNacimiento,$nombre,$apellido,$sexo,
		$carrera,$semestre,$indice,$tipoPasantia,$modalidad,$direccion,$estado,$ciudad,$telefono,
		$email);

		if ($successPasante){
			$usuario = new Usuario();
			$nombreUsuario=$this->getRequestParam('txtUsuario');
			$clave=$this->getRequestParam('clave');
			$categoria=CAT_USUARIO_PASANTE;	//3:Pasante
			$idUsuario = $pasante->buscarId($cedula, $fchNacimiento);
			$estatusUsuario='P';
			$successUsuario = $usuario->registrarUsuario($nombreUsuario,$clave,$categoria,$idUsuario,$estatusUsuario);
			if ($successUsuario){
				$registro = new Registro();
				$hora=date("G:H:s");
				$hash = md5($nombreUsuario.$hora);
				$registroUsuario=$nombreUsuario;
				$registroEmail=$email;
				$registroEstatus='P';
				$successRegistro = $registro->guardarRegistro($hash,$registroUsuario,$registroEmail,$registroEstatus);
			}
		}

		if (!($successPasante && $successUsuario && $successRegistro)){
			$success =  false;
		}
		else {
			$correo = new Correo();
			$body ='Gracias por su registro. <BR/>
			  	Para confirmar su registro por favor haga clic en el siguiente enlace o copielo en la barra de direcciones de su navegador.<BR/>';
			$body .='http://'. $this->getServer('SERVER_NAME').'/SIGP/pasante/confirmarRegistroPasante?id='.$hash;
			$correo->enviarCorreo($this->getRequestParam('txtCorreo'), 'Registro en el sistema', $body);
		}
			

		$this->renderText(json_encode(array("success"=>$success, "pasante"=>$successPasante, "usuario"=>$successUsuario, "registro"=>$successRegistro,
		"id"=>$idUsuario)));		
	}
	//-----------------------------------------------------------------------------------------
	public function confirmarRegistroPasanteAction(){
		$id = $this->getRequestParam('id');
		$aux = new Registro();
		$registro = $aux->findFirst("hash ='$id'");
			
		if ($registro){
			if ($registro->getEstatus()== 'P'){
				$registro->setEstatus('A');
				$successRegistro = $registro->update();
					
				$auxiliarUsuario = new Usuario();
				$usuario = $auxiliarUsuario->findFirst("nombreUsuario='{$registro->getUsuario()}'");
				$usuario->setEstatus('A');
				$successUsuario = $usuario->update();
					
				$auxiliarPasante = new Pasante();
				$pasante = $auxiliarPasante->findFirst("id='{$usuario->getIdUsuario()}'");
				$pasante->setEstatus('I');
				$successPasante = $pasante->update();
				echo "Usuario activado, ya puede loguearse.";
			} else {
				echo "Activacion ya realizada.";
			}

		} else {
			echo "Registro no valido.";
		}
	}
	//-----------------------------------------------------------------------------------------
	public function findUsernameAction(){
		$username = $this->getRequestParam('username');
		$this->setResponse('ajax');
		$usuario = new Usuario();
		$this->renderText($usuario->existebyUsername($username));
	}
	//-----------------------------------------------------------------------------------------
	public function buscarPasanteAction(){
		$resp=array();
		$pCedula = $this->getRequestParam('cedula');
		//$pFecha = $this->getRequestParam('fecha');
		$this->setResponse('ajax');
		$pasante = new Pasante();
		//$resp = $pasante->buscarPasante($pCedula);
		$resp = $pasante->buscarPasanteId($pCedula);
		$this->renderText(json_encode($resp));
	}
	//-----------------------------------------------------------------------------------------
	public function buscarPasanteExistenteAction(){
		$success = true;
		$this->setResponse('ajax');
		$resp=array();
		$pasante = new Pasante();
		$idPasante = 0;
		$cedula = 0;
		$resp['success']= false;
		$resp['errorMsj']= '';
		if ($this->auth['categoriaUsuario_id']==CAT_USUARIO_PASANTE){
			$idPasante = $this->auth['idUsuario'];
		}
		if (($this->getRequestParam('id')) != "-"){
			$idPasante = $this->getRequestParam('id');
		}

		$this->setResponse('ajax');
		$cedula = $pasante->buscarCedulaById($idPasante);
		if ($cedula != 0){
			$resp = $pasante->buscarPasanteId($cedula);
		}

		$this->renderText(json_encode($resp));
	}
	//-----------------------------------------------------------------------------------------

	/**
	 * Action de la vits Notas
	 */
	public function notasAction() {
		$this->setTemplateAfter("menu");
		$this->setParamToView('categoria', $this->auth['categoriaUsuario_id']);
	}

	/**
	 * Obtiene resumen de notas de los pasantes, el nivel de informacion varia segun la categoria del usuario que la solicita
	 */
	public function getNotasParcialesAction() {
		$resultado=array();
		$tipoTutor=$this->verPermiso();
		$this->setResponse('ajax');
		if ($tipoTutor!='-1'){
			$tutorId =$this->auth['idUsuario'];
			$start = $this->obtenerParametroRequest('start');
			$limit = $this->obtenerParametroRequest('limit');
			$cedula = (isset($_REQUEST['query']) and is_numeric($_REQUEST['query'])) ? mysql_real_escape_string(stripslashes($_REQUEST['query'])) : '';
			$pasante = new Pasante();
			$resultado = $pasante->getNotasPorTutor($tutorId,$cedula,$start,$limit,$tipoTutor);
		}
		$this->renderText(json_encode($resultado));
	}

	/**
	 * Verifica la categoria del usuario para asociarlo a un tipo y determinar su permiso.
	 * @return string
	 */
	private function verPermiso() {
		$categoria=$this->auth['categoriaUsuario_id'];
		$tipoTutor='-1';
		switch ($categoria) {
			case CAT_USUARIO_COORDINADOR:
				$tipoTutor='*';
				break;
			case CAT_USUARIO_TUTOR_ACAD:
				$tipoTutor='A';
				break;
			case CAT_USUARIO_TUTOR_EMP:
				$tipoTutor='E';
				break;
			case CAT_USUARIO_PASANTE:
				$tipoTutor='P';
				break;
		}
		return $tipoTutor;
	}

	/**
	 * Obtiene los detalles de la nota del pasante ( notas por aspectos evaluados)
	 */
	public function getDetalleNotasAction() {
		$resultado=array();
		$tipoTutor=$this->verPermiso();
		if ($tipoTutor!='-1'){
			$evaluaciones =  new Pasanteevaluacion();
			if ($tipoTutor!='P'){
				$id= $this->getRequestParam('pPasanteId');
				if ($id and is_numeric($id)){
					$resultado=$evaluaciones->getDetalleNotas($id,$tipoTutor);
				}
			}else {
				$id=$this->auth['idUsuario'];
				$resultado=$evaluaciones->getDetalleNotas($id,'*');
			}
		}
		$this->setResponse('ajax');
		$this->renderText(json_encode(array("resultado"=>$resultado,
											"success"=>($resultado)?true:false)));
	}


	/**
	 * Registra/ actualiza las notas de un pasante
	 */
	public function registrarNotaAction(){
		$resp = array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$errores='';
		$this->setResponse('ajax');
		$datos = $this->getRequestParam('datos');
		if ($datos){
			$registros = json_decode(stripslashes($datos),true);
			foreach ($registros as $registro) {
				$pasanteId= $registro['id'];
				$aspectoId= $registro['aspectoId'];
				$nota= $registro['nota'];
				$item= isset($registro['item'])?utf8_decode($registro['item']):'';
				if (is_numeric($pasanteId) and is_numeric($aspectoId)){
					if ($this->validarNota($nota)){
						$evaluacion = new Pasanteevaluacion();
						$success = $evaluacion->registrarNota($pasanteId, $aspectoId, $nota);
						if (!$success){
							$errores="No se ha actualizado el &iacute;tem: $item <BR>";
						}
					}else{
						$errores = "$item: Nota en rango no v&aacute;lido (0-10)";
					}
				}else{
					$errores = 'Par&acute;metros incorrectos';
				}
				if($errores!=''){
					$resp['errorMsj'].=$errores.'<BR>' ;
					$errores='';
				}
			}
			$resp['success']=($resp['errorMsj']== '')?true:false;
			$resp['errorMsj']= utf8_encode($resp['errorMsj']);
		}
		$this->renderText(json_encode($resp));
	}

	/**
	 * Verifica que la nota sea un valor valido y se encuentre en un rango valido.
	 * @param int $nota
	 * @return boolean
	 */
	private function validarNota($nota) {
		$flag = false;
		if (isset($nota) AND is_numeric($nota)){
			if (($nota >= NOTA_INDIVIDUAL_MINIMA) AND ($nota <= NOTA_INDIVIDUAL_MAXIMA) ){
				$flag=true;
			}
		}
		return  $flag;
	}

	public function getDetallePasanteAction(){
		$resultado=array();
		$success= false;
		$pasanteId= $this->getParametro('pPasanteId', 'numerico', -1);
		if ($pasanteId!=-1){
			$pasante = new Pasante();
			$resultado= $pasante->getPasantebyId($pasanteId);
			$resultado['descripcion']='';
			$resultado['experiencia']='';
			$resultado['cursos']='';
			$perfil= new Perfil();
			$datosPerfil= $perfil->buscarPerfil($pasanteId);
			$datosPerfil=$datosPerfil['datos'];
			if ($datosPerfil){
				$resultado['descripcion']=$datosPerfil['descripcion'];
				$resultado['experiencia']=$datosPerfil['experiencia'];
				$resultado['cursos']=$datosPerfil['cursos'];
			}
			$success= ($resultado)?true:false;

		}
		$this->setResponse('ajax');
		$this->renderText(json_encode(array("success"=>$success,
											"datos"=>$resultado)));

	}

	public function verNotasAction(){
		$conf= new Configuracion();
		//$decanatoId= DECANATO_CIENCIAS;
		$decanatoId=Session::getData('decanato_id');
		if ($conf->getConsultaCalificacionesbyDecanato($decanatoId)!='S'){
			$this->routeTo('controller: pasante','action: consultaSinHabilitar');
		}
	}

	public function getDatosPasanteAction() {
		$resultado=array();
		$conf= new Configuracion();
		$categoria=$this->auth['categoriaUsuario_id'];
		$success= false;
		//$decanatoId= DECANATO_CIENCIAS;
		$decanatoId=Session::getData('decanato_id');
		if ($conf->getConsultaCalificacionesbyDecanato($decanatoId)=='S'){
			if($categoria==CAT_USUARIO_PASANTE){
				$id=$this->auth['idUsuario'];
				$pasante = new Pasante();
				$resultado= $pasante->getPasantebyId($id);
				$aux =$pasante->getNotasPorTutor('',$resultado['cedula']);
				if ($aux){
					if ($aux['resultado']){
						$aux=$aux['resultado'][0];
						$resultado['notaInforme']=$aux['notaInforme'];
						$resultado['notaEmpresaTE']=$aux['notaEmpresaTE'];
						$resultado['notaEmpresaTA']=$aux['notaEmpresaTA'];
						$resultado['acumulado']=$aux['acumulado'];
					}
				}
			}
		}
		$success= ($resultado)?true:false;
		$this->setResponse('ajax');
		$this->renderText(json_encode(array("success"=>$success,
											"datos"=>$resultado)));

	}

	public function consultaSinHabilitarAction(){

	}
	public function consultarPasantiasAction(){
		$resp = array();
		$errorMsj= '';
		$this->setResponse('ajax');
		$categoria=$this->auth['categoriaUsuario_id'];
		if ($categoria==CAT_USUARIO_COORDINADOR or $categoria==CAT_USUARIO_ANALISTA){
			$pasante = new Pasante();
			$carrera=$this->getParametro('pCarreraId', 'numerico', '');
			$cedula=$this->getParametro('query', 'string', '');
			$start=$this->obtenerParametroRequest('start');
			$limit=$this->obtenerParametroRequest('limit');
			$decanato=Session::getData('decanato_id');
			$resp['resultado']= $pasante->consultaPasantias($decanato,$carrera,$cedula,$start,$limit);
		}else{
			$errorMsj="Ud. no posee la permisología para realizar esta operación.";
		}
		$resp['resultado']['errorMsj']= utf8_encode($errorMsj);
		$resp['resultado']['success']=($resp)?true:false;
		$this->renderText(json_encode($resp['resultado']));
	}
	public function administrarAction(){
		$tipo='';
		$categoria=$this->auth['categoriaUsuario_id'];
		if ($categoria==CAT_USUARIO_TUTOR_ACAD){
			$tipo='A';
		}else{
			$tipo='E';
		}
		$this->setParamToView('tipo', $tipo);
	}
	public function consultarPasantiasTAAction(){
		$resp = array();
		$errorMsj= '';
		$this->setResponse('ajax');
		$categoria=$this->auth['categoriaUsuario_id'];
		if ($categoria==CAT_USUARIO_TUTOR_ACAD){
			$id=$this->auth['idUsuario'];
			$pasante = new Pasante();
			$carrera=$this->getParametro('pCarreraId', 'numerico', '');
			$cedula=$this->getParametro('query', 'string', '');
			$start=$this->obtenerParametroRequest('start');
			$limit=$this->obtenerParametroRequest('limit');
			$decanato=Session::getData('decanato_id');
			$resp['resultado']= $pasante->consultaPasantiasTA($decanato,$id,$carrera,$cedula,$start,$limit);
		}else{
			$errorMsj="Ud. no posee la permisología para realizar esta operación.";
		}
		$resp['resultado']['errorMsj']= utf8_encode($errorMsj);
		$resp['resultado']['success']=($resp)?true:false;
		$this->renderText(json_encode($resp['resultado']));
	}



	public function consultarPasantiasTEAction(){
		$resp = array();
		$errorMsj= '';
		$this->setResponse('ajax');
		$categoria=$this->auth['categoriaUsuario_id'];
		if ($categoria==CAT_USUARIO_TUTOR_EMP){
			$id=$this->auth['idUsuario'];
			$pasante = new Pasante();
			$carrera=$this->getParametro('pCarreraId', 'numerico', '');
			$cedula=$this->getParametro('query', 'string', '');
			$start=$this->obtenerParametroRequest('start');
			$limit=$this->obtenerParametroRequest('limit');
			$decanato=Session::getData('decanato_id');
			$resp['resultado']= $pasante->consultaPasantiasTE($decanato,$id,$carrera,$cedula,$start,$limit);
		}else{
			$errorMsj="Ud. no posee la permisología para realizar esta operación.";
		}
		$resp['resultado']['errorMsj']= utf8_encode($errorMsj);
		$resp['resultado']['success']=($resp)?true:false;
		$this->renderText(json_encode($resp['resultado']));
	}

}

?>
