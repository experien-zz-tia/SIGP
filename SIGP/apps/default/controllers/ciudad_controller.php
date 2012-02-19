<?php
class CiudadController extends ApplicationController {

	public function indexAction(){
		}

	/**
	 * Obtiene un listado de las ciudades.
	 * Retorna un objeto en Ajax
	 */
	public function getCiudadesAction(){
		$this->setResponse('ajax');
		$ciudad = new Ciudad();
		$this->renderText(json_encode($ciudad->getCiudades()));
	}
	
	/**
	 * Obtiene un listado de las ciudades de un estado especificado en el request
	 */
	public function getCiudadesbyEstadoAction(){
		$id = $this->getRequestParam('idEstado');
		$this->setResponse('ajax');
		$ciudad = new Ciudad();
		$this->renderText(json_encode($ciudad->getCiudadesbyEstado($id)));
	}
}
?>