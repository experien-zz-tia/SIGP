<?php
require_once('utilities/constantes.php');

class PerfilController extends ApplicationController{
	public  $auth;
//-----------------------------------------------------------------------------------------
	protected function initialize(){
		$this->setTemplateAfter("menu");
		$this->auth=Auth::getActiveIdentity();
	}
//-----------------------------------------------------------------------------------------
	public function indexAction(){
	}
//-----------------------------------------------------------------------------------------	
	public function registrarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$this->setResponse('ajax');
		$perfil = new Perfil();
		$idPasante = 0;
		$evento = $this->getRequestParam('tipoevento');
		if ($this->auth['categoriaUsuario_id']==CAT_USUARIO_PASANTE){
			$idPasante = $this->auth['idUsuario'];
			$pDescripcion = $this->getRequestParam('txtDescripcion');
			$pExperiencia = utf8_decode($this->getRequestParam('txtExperiencia'));
			$pCursos =utf8_decode($this->getRequestParam('txtCursos'));
			
			if ($evento=="registrar"){
				$successPerfil = $perfil->registrarPerfil($idPasante, $pDescripcion, $pExperiencia, $pCursos);	
			} else {
				$successPerfil = $perfil->actualizarPerfil($idPasante, $pDescripcion, $pExperiencia, $pCursos);
			}			
		}
				
		if ($successPerfil ){
			$resp['success']= true;
			$resp['errorMsj']= 'Perfil guardado';
		}
		//echo " ".$idPasante." ";
		$this->renderText(json_encode($resp));
	}
//-----------------------------------------------------------------------------------------
	public function buscarPerfilAction(){
		$resp=array();
		$this->setResponse('ajax');
		$idPasante = 0;
		if ($this->auth['categoriaUsuario_id']==CAT_USUARIO_PASANTE){
			$idPasante = $this->auth['idUsuario'];
			$this->setResponse('ajax');
			$perfil = new Perfil();
			$resp = $perfil->buscarPerfil($idPasante);
		}
		$this->renderText(json_encode($resp));
	}
//-----------------------------------------------------------------------------------------
}
?>