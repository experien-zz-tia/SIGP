<?php

require_once('utilities/constantes.php');
/**
 * Clase controladora para las acciones relacionadas con la entidad pasantia 
 * @author Robert A
 *
 */
class PasantiaController extends ApplicationController{

	protected function initialize(){
	
	}

	public function indexAction(){
	
	}

	/**
	 * Obtiene las pasantia por partes ( basado en parametros start y limit ).
	 */
	public function getPasantiasAction(){
		$id = $this->getRequestParam('pEmpresa_id');
		$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
		$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;
		$this->setResponse('ajax');
		$pasantias = new Pasantia();
		$resultado = $pasantias->getPasantias($id,$start,$limit);
		$this->renderText(json_encode($resultado));
	}
	
	public function getDetallePasantiaAction(){
		$resp = array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$errores='';
		$this->setResponse('ajax');
		$id = $this->getParametro('pPasantiaId', 'numerico', -1);
		if ($id!=-1){
			$pasantia= new Pasantia();
			$resp['resultado']=	$pasantia->getDetallePasantias($id);
			$resp['success']= ($resp['resultado'])?true:false;
		}else{
			$errores = 'Par&acute;metros incorrectos';
		}
		$resp['errorMsj']= utf8_encode($resp['errorMsj']);
		$this->renderText(json_encode($resp));
	}
	
	
}
?>