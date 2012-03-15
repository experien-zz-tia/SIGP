<?php
require_once('utilities/constantes.php');
class RecursoController extends ApplicationController {
	protected   $auth;
	protected function initialize(){
		$this->auth=Auth::getActiveIdentity();
		$this->setTemplateAfter("registro");
	}
	
	public function indexAction(){
	}
	

}