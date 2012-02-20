<?php
	class TipoPasantiaController extends ApplicationController{
		
		public function indexAction(){}
		
		public function getTiposPasantiaAction(){
			$this->setResponse('ajax');
			$tipos = new TipoPasantia();
			$this->renderText(json_encode($tipos->getTiposPasantia()));	
		}
	}

?>