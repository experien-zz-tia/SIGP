<?php
class CarreraController extends ApplicationController {

	public function indexAction(){}

	public function getCarrerasAction(){
		$this->setResponse('ajax');
		$carrera = new Carrera();
		$this->renderText(json_encode($carrera->getCarreras()));
	}

	public function getCarrerasFullAction(){
		$this->setResponse('ajax');
		$carrera = new Carrera();
		$vId = $this->getRequestParam('idDecanato');
		$this->renderText(json_encode($carrera->getCarrerasFull($vId)));
	}

	public function getCarrerasbyDecanatoAction(){
		$this->setResponse('ajax');
		$id = $this->getRequestParam('idDecanato');
		$carrera = new Carrera();
		$this->renderText(json_encode($carrera->getCarrerasbyDecanato($id)));
	}

	public function getSemestresAction(){
		$id = $this->getRequestParam('idCarrera');
		$this->setResponse('ajax');
		$carrera = new Carrera();
		$this->renderText(json_encode($carrera->getSemestres($id)));

	}

	public function getCarrerasbyDecanatoLightAction(){
		$id = DECANATO_CIENCIAS;
		$this->setResponse('ajax');
		$carrera = new Carrera();
		$this->renderText(json_encode($carrera->getCarrerasbyDecanato($id)));
	}

}
?>