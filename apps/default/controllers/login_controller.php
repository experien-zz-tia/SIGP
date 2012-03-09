<?php
require_once('utilities/constantes.php');
class LoginController extends ApplicationController{

	protected function initialize(){
		$this->setTemplateAfter("registro");
	}

	public function indexAction(){
	}

	/**
	 * Valida al usuario, en caso afirmativo lo redirige a su pagina de inicio
	 */
	public function loginAction(){
		$usuario = trim($this->getRequestParam('usuario', 'addslaches'));
		$clave = trim($this->getRequestParam('clave'));
		$auth = new Auth(array(
	                "model",
	                "class" => "Usuario",
	                "nombreUsuario" => $usuario,
	                "clave" => $clave
		));

		if($auth->authenticate()==true){
			$auth=Auth::getActiveIdentity();
			if ($auth['estatus']=='A'){
				$this->agregarIdentificacion($auth['idUsuario'],$auth['categoriaUsuario_id']);
				switch ($auth['categoriaUsuario_id']) {
					case CAT_USUARIO_ADMINISTRADOR: 
						$this->routeToURI("/usuario/gestionar");
						break;
					case CAT_USUARIO_COORDINADOR: 
						$this->routeToURI("/pasante/gestionar");
						break;
					case CAT_USUARIO_ANALISTA: 
						$this->routeToURI("/noticia/gestionar");
						break;
					case CAT_USUARIO_EMPRESA: 
						$this->routeToURI("/oferta/gestionar");
						break;
					case CAT_USUARIO_TUTOR_EMP: 
						$this->routeToURI("/pasante/administrar");
						break;
					case CAT_USUARIO_PASANTE: 
						$this->routeToURI("/oferta/index");
						break;
					case CAT_USUARIO_TUTOR_ACAD: 
						$this->routeToURI("/pasante/administrar");
						break;
					default:
						$this->routeToURI("/noticia/index");
					break;
				}
			}else{
				Auth::destroyIdentity();
				switch ($auth['estatus']) {
					case 'P':
						$this->routeToURI("/registro/activar");
						break;
					default:
						$this->routeToURI("/login/error");
						break;
				}
			}
		}else{
			$this->routeToURI("/login/error");
		}
}


/**
 * Destruye la identidad activa y envia a sesion/index
 */
public function logoutAction(){
	Auth::destroyIdentity();
	Session::unsetData('nombre');
	Session::unsetData('decanato_id');
	Router::routeTo(array("controller" => "noticia", "action" => "index"));
}

public function errorAction(){

}

private function agregarIdentificacion($idUsuario,$categoria) {
	$aux='';
	$decanato=0;
	switch ($categoria) {
		case CAT_USUARIO_ADMINISTRADOR: 
		case CAT_USUARIO_COORDINADOR: 
		case CAT_USUARIO_ANALISTA: 
			$empleado = new Empleado();
			$aux=$empleado->getNombreApellido($idUsuario);
			$decanato= $empleado->getDecanato_id();
			break;
		case CAT_USUARIO_EMPRESA: 
			$empresa = new Empresa();
			$aux=$empresa->getRazonSocialbyId($idUsuario);
			break;
		case CAT_USUARIO_TUTOR_EMP: 
			$tutorE = new TutorEmpresarial();
			$aux=$tutorE->getNombreApellido($idUsuario);
			break;
		case CAT_USUARIO_PASANTE: 
			$pasante = new Pasante();
			$aux=$pasante->getNombreApellido($idUsuario);
			$carrera= $pasante->getCarrera();
			$decanato= $carrera->getDecanato_id();
			break;
		case CAT_USUARIO_TUTOR_ACAD: 
			$tutorA = new TutorAcademico();
			$aux=$tutorA->getNombreApellido($idUsuario);
			$departamento= $tutorA->getDepartamento();
			$decanato= $departamento->getDecanato_id();
			break;		
	}
	Session::setData('decanato_id', $decanato);
	Session::setData('nombre', (($aux=='')?'Visitante.':$aux.'.'));
	
}

}


?>
