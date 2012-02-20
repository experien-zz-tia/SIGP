<?php
require_once('utilities/constantes.php');
class CoordinacionController extends ApplicationController {

	protected   $auth;
	protected function initialize(){
		$this->setTemplateAfter("menu");
		$this->auth=Auth::getActiveIdentity();
	}
	public function indexAction(){

	}

	public function getDatosCoordinadorAction() {
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$resp['resultado']= array();
		$this->setResponse('ajax');
		$decanato= DECANATO_CIENCIAS;
		$coord = new Coordinacion();
		$resp['resultado']=$coord->getDatosCoordinador($decanato);
		if ($resp['resultado']){
			$resp['success']= true;
		}else{
			$resp['errorMsj']= 'No se pueden obtener los datos';
		}
		$resp['errorMsj']=utf8_encode($resp['errorMsj']);
		$this->renderText(json_encode($resp));
	}


}
?>