<?php
class EstadoController extends ApplicationController {

	public function indexAction(){
	}
	//-----------------------------------------------------------------------------------------
	/**
	 * Obtiene los estados  codificados en Json
	 */
	public function getEstadosAction(){
		$this->setResponse('ajax');
		$estado = new Estado();
		$this->renderText(json_encode($estado->getEstados()));
	}
	//-----------------------------------------------------------------------------------------
	public function buscarAction(){
		$this->setResponse('ajax');
		$estado = new Estado();
		$id = $this->getRequestParam('id');
		$this->renderText(json_encode($estado->buscar($id)));
	}
	//-----------------------------------------------------------------------------------------
	public function getEstadosLimitAction(){
		$this->setResponse('ajax');
		$estado = new Estado();
		$this->renderText(json_encode($estado->getEstadosLimit()));
	}
	//-----------------------------------------------------------------------------------------
}
?>