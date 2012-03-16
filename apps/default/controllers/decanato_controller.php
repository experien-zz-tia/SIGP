<?php
class DecanatoController extends ApplicationController {

	public function indexAction(){}

	public function getDecanatosAction(){
		$this->setResponse('ajax');
		$decanato = new Decanato();
		$this->renderText(json_encode($decanato->getDecanatos()));
	}

	public function getDecanatosFullAction(){
		$this->setResponse('ajax');
		$decanato = new Decanato();
		$this->renderText(json_encode($decanato->getDecanatosFull()));
	}

	public function buscarAction(){
		$this->setResponse('ajax');
		$decanato = new Decanato();
		$id = $this->getRequestParam('id');
		
		$this->renderText(json_encode($decanato->buscar($id)));
	}
	
}
?>