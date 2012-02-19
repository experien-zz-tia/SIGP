<?php
class DecanatoController extends ApplicationController {

	public function indexAction(){}

	
	public function getDecanatosAction(){
		$this->setResponse('ajax');
		$decanato = new Decanato();
		$this->renderText(json_encode($decanato->getDecanatos()));
	}
}
?>