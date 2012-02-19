<?php
class UsuarioController extends ApplicationController {
	protected   $auth;

	protected function initialize(){
		$this->setTemplateAfter("menu");
		$this->auth=Auth::getActiveIdentity();
	}
	
	public function gestionarAction(){
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
}
?>