<?php
class EstadoController extends ApplicationController {

	public function indexAction(){
	}
	
	/**
	 * Obtiene los estados  codificados en Json
	 */
	public function getEstadosAction(){
		$this->setResponse('ajax');
		$estado = new Estado();
		$this->renderText(json_encode($estado->getEstados()));
	}
	
	
	
}
?>