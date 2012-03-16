<?php
class TipoPasantiaController extends ApplicationController{

	public function indexAction(){}
	//-----------------------------------------------------------------------------------------
	public function getTiposPasantiaAction(){
		$this->setResponse('ajax');
		$tipos = new TipoPasantia();
		$this->renderText(json_encode($tipos->getTiposPasantia()));
	}
	//-----------------------------------------------------------------------------------------
	public function buscarAction(){
		$this->setResponse('ajax');
		$tipos = new TipoPasantia();
		$id = $this->getRequestParam('id');
		$this->renderText(json_encode($tipos->buscar($id)));
	}
	//-----------------------------------------------------------------------------------------
}

?>