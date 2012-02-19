<?php
class CarreraController extends ApplicationController {

	public function indexAction(){}

	public function getCarrerasAction(){
		$this->setResponse('ajax');
		$carrera = new Carrera();
		$this->renderText(json_encode($carrera->getCarreras()));
	}

	public function getCarrerasbyDecanatoAction(){
		$id = $this->getRequestParam('idDecanato');
		$this->setResponse('ajax');
		$carrera = new Carrera();
		$this->renderText(json_encode($carrera->getCarrerasbyDecanato($id)));
	}
	
	
	public function getCarrerasbyDecanatoLightAction(){
		$id = DECANATO_CIENCIAS;
		$this->setResponse('ajax');
		$carrera = new Carrera();
		$this->renderText(json_encode($carrera->getCarrerasbyDecanato($id)));
	}
	
}
?>