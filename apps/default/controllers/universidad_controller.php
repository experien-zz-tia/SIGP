<?php
class UniversidadController extends ApplicationController {

	public function indexAction(){
	}
	/**
	 * Obtiene un listado de las universidades registradas
	 * Retorna un objeto en Ajax
	 */
	public function getUniversidadesAction(){
		$this->setResponse('ajax');
		$universidad = new Universidad();
		$this->renderText(json_encode($universidad->getUniversidades()));
	}

	public function getUniversidadFullAction(){
		$this->setResponse('ajax');
		$universidad = new Universidad();
		$this->renderText(json_encode($universidad->getUniversidadFull()));
	}

	public function buscarAction(){
		$this->setResponse('ajax');
		$universidad = new Universidad();
		$id = $this->getRequestParam('id');
		$this->renderText(json_encode($universidad->buscar($id)));
	}

	public function contarAction(){
		$this->setResponse('ajax');
		$universidad = new Universidad();
		$this->renderText(json_encode($universidad->contar()));
	}
}
?>