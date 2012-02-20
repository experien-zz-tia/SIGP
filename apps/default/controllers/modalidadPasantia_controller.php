<?php
	class ModalidadPasantiaController extends ApplicationController{
		
		public function indexAction(){}
		
		public function getModalidadesPasantiaAction(){
			$this->setResponse('ajax');
			$modalidades = new ModalidadPasantia();
			$this->renderText(json_encode($modalidades->getModalidadesPasantia()));
		}
	} 
?>