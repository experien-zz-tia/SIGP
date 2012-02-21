<?php
require_once('correo.php');
require_once('utilities/constantes.php');
class UsuarioController extends ApplicationController {
	protected   $auth;

	protected function initialize(){
		$this->setTemplateAfter("menu");
		$this->auth=Auth::getActiveIdentity();
	}

	public function gestionarAction(){
	}

	public function gestionarUsuarioAction(){
	}

	public function consultarEmpleadosAction(){
		$resp = array();
		$errorMsj= '';
		$this->setResponse('ajax');
		$categoria=$this->auth['categoriaUsuario_id'];
		if ($categoria==CAT_USUARIO_ADMINISTRADOR){
			$id=$this->auth['idUsuario'];
			$emp = new Empleado();
			$cedula=$this->getParametro('query', 'string', '');
			$start=$this->obtenerParametroRequest('start');
			$limit=$this->obtenerParametroRequest('limit');
			$resp['resultado']= $emp->consultaEmpleados($cedula,$start,$limit);
		}else{
			$errorMsj="Ud. no posee la permisología para realizar esta operación.";
		}
		$resp['resultado']['errorMsj']= utf8_encode($errorMsj);
		$resp['resultado']['success']=($resp)?true:false;
		$this->renderText(json_encode($resp['resultado']));
	}
	public function cambiarClaveAction() {

	}

	public function modificarClaveAction(){
		$resp = array();
		$errorMsj= '';
		$this->setResponse('ajax');
		$id=$this->auth['id'];
		$claveNueva=$this->getParametro('pClaveNueva','string','');
		$claveActual=$this->getParametro('pClaveActual','string','');
		if ($claveActual!="" and $claveNueva){
			$id=$this->auth['id'];
			$usuario = new Usuario();
			$coincideClave= $usuario->coincideClave($id,$claveActual);
			if ($coincideClave){
				$usuario->actualizarClave($id,$claveNueva);
			}else{
				$errorMsj="La clave ingresada no coincide.";
			}
		}else{
			$errorMsj="Parámetros incompletos.";
		}
		$resp['errorMsj']= utf8_encode($errorMsj);
		$resp['success']=($errorMsj=='')?true:false;
		$this->renderText(json_encode($resp));
	}


	public function consultarUsuariosAction(){
		$resp = array();
		$errorMsj= '';
		$this->setResponse('ajax');
		$categoria=$this->auth['categoriaUsuario_id'];
		if ($categoria==CAT_USUARIO_ADMINISTRADOR){
			$id=$this->auth['idUsuario'];
			$user = new Usuario();
			$start=$this->obtenerParametroRequest('start');
			$limit=$this->obtenerParametroRequest('limit');
			$resp['resultado']= $user->consultarUsuarios($start,$limit);
		}else{
			$errorMsj="Ud. no posee la permisología para realizar esta operación.";
		}
		$resp['resultado']['errorMsj']= utf8_encode($errorMsj);
		$resp['resultado']['success']=($resp)?true:false;
		$this->renderText(json_encode($resp['resultado']));
	}
	
	public function generarNuevaClaveAction(){
		$resp = array();
		$errorMsj= '';
		$this->setResponse('ajax');
		$usuarioId=$this->getParametro('pUsuarioId','string','');
	
		if ($usuarioId!="" ){
			$usuario = new Usuario();
			$clave= $usuario->cambiarClave($usuarioId);
			$resp['resultado']= $clave;
		}else{
			$errorMsj="Parámetros incompletos.";
		}
		$resp['errorMsj']= utf8_encode($errorMsj);
		$resp['success']=($errorMsj=='')?true:false;
		$this->renderText(json_encode($resp));
	}
	
	
	public function eliminarAction(){
		$resp = array();
		$errorMsj= '';
		$this->setResponse('ajax');
		$usuarioId=$this->getParametro('pUsuarioId','string','');
	
		if ($usuarioId!="" ){
			$usuario = new Usuario();
			$success= $usuario->eliminarUser($usuarioId);
		}else{
			$errorMsj="Parámetros incompletos.";
		}
		$resp['errorMsj']= utf8_encode($errorMsj);
		$resp['success']=($errorMsj=='')?true:false;
		$this->renderText(json_encode($resp));
	}
	
	
	public function reactivarAction(){
		$resp = array();
		$errorMsj= '';
		$this->setResponse('ajax');
		$usuarioId=$this->getParametro('pUsuarioId','string','');
	
		if ($usuarioId!="" ){
			$usuario = new Usuario();
			$success= $usuario->reactivarUser($usuarioId);
		}else{
			$errorMsj="Parámetros incompletos.";
		}
		$resp['errorMsj']= utf8_encode($errorMsj);
		$resp['success']=($errorMsj=='')?$success:false;
		$this->renderText(json_encode($resp));
	}
	

}
?>