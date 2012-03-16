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
	public function buscarAction(){
		$this->setResponse('ajax');
		$departamento = new Departamento();
		$id = $this->getRequestParam('id');
		$this->renderText(json_encode($departamento->buscar($id)));
	}
	//-----------------------------------------------------------------------------------------
	public function getDepartamentosFullAction(){
		$this->setResponse('ajax');
		$departamento = new Departamento();
		$vId = $this->getRequestParam('idDecanato');
		$this->renderText(json_encode($departamento->getDepartamentosFull($vId)));
	}
	//-----------------------------------------------------------------------------------------
}
?>