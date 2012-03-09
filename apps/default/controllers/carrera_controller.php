<?php
class CarreraController extends ApplicationController {

	
protected   $auth;
	protected function initialize(){
		$this->setTemplateAfter("menu");
		$this->auth=Auth::getActiveIdentity();
	}
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

	public function getSemestresAction(){
		$id = $this->getRequestParam('idCarrera');
		$this->setResponse('ajax');
		$carrera = new Carrera();
		$this->renderText(json_encode($carrera->getSemestres($id)));
		
	}

	public function getCarrerasbyDecanatoLightAction(){
		$id=$this->auth['decanato_id'];
		$this->setResponse('ajax');
		$carrera = new Carrera();
		$this->renderText(json_encode($carrera->getCarrerasbyDecanato($id)));
	}

}
?>