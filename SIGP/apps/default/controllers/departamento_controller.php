<?php
class DepartamentoController extends ApplicationController {

	public function indexAction(){}
//-----------------------------------------------------------------------------------------
	public function getDepartamentosAction(){
		$this->setResponse('ajax');
		$departamento = new Departamento();
		$this->renderText(json_encode($departamento->getDepartamentos()));
	}
//-----------------------------------------------------------------------------------------	
	public function getDepartamentosbyDecanatoAction(){
		$id = $this->getRequestParam('decanato_id');
		$this->setResponse('ajax');
		$departamento = new Departamento();
		$this->renderText(json_encode($departamento->getDepartamentosbyDecanato($id)));
	}
//-----------------------------------------------------------------------------------------	
}
?>