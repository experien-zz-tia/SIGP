<?php
require_once('utilities/constantes.php');
class ConfiguracionController extends ApplicationController {

	protected   $auth;
	protected function initialize(){
		$this->setTemplateAfter("menu");
		$this->auth=Auth::getActiveIdentity();
	}
	public function indexAction(){

	}
	public function guardarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$this->setResponse('ajax');
		$catUsuario=$this->auth['categoriaUsuario_id'];
		if ($catUsuario==CAT_USUARIO_COORDINADOR){
			$decanato=Session::getData('decanato_id');
			$pClave = $this->getParametro('pClave', 'string', '');
			$pMaxRecSolicTutor = $this->getParametro('pMaxRecSolicTutor', 'numerico', -1);
			$pMaxSolicOferta = $this->getParametro('pMaxSolicOferta', 'numerico', -1);
			$pMaxSolicTutor = $this->getParametro('pMaxSolicTutor', 'numerico', -1);
			$pRadioInscrip = $this->getParametro('pRadioInscrip', 'string', '');
			$pRadioCalif = $this->getParametro('pRadioCalif', 'string', '');
			$pRadioActCalif = $this->getParametro('pRadioActCalif', 'string', '');
			$pMaxMensajes = $this->getParametro('pMaxMensajes', 'numerico', -1);
			if ($pClave!='' and $pMaxRecSolicTutor!=-1 and $pMaxSolicOferta!=-1 and $pMaxSolicTutor!=-1 and $pRadioInscrip!='' and $pRadioCalif!='' and $pMaxMensajes!=-1 and $pRadioActCalif!=''){
				$username=$this->auth['nombreUsuario'];
				$usuario= new Usuario();
				$successCredenciales= $usuario->validarCredenciales($username,$pClave);
				if ($successCredenciales){
					$configuracion= new Configuracion();
					$aux= $configuracion->guardarConfiguracion($decanato,$pMaxRecSolicTutor,$pMaxSolicOferta,$pMaxSolicTutor,$pRadioInscrip,$pRadioCalif,$pMaxMensajes,$pRadioActCalif);
					if (!$aux['success']){
						$resp['errorMsj']=$aux['errores'];
					}else{
						$resp['success']= true;
					}
				}else{
					$resp['errorMsj']= 'Contraseña no válida.';
				}
			}else{
				$resp['errorMsj']= 'Parámetros incompletos.';
			}
		}else{
			$resp['errorMsj']= 'Ud. no posee la permisologia para realizar esta operaci&oacute;n.';
		}
		$resp['errorMsj']=utf8_encode($resp['errorMsj']);
		$this->renderText(json_encode($resp));
	}

	public function getConfiguracionAction() {
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$resp['resultado']= array();
		$this->setResponse('ajax');
		$catUsuario=$this->auth['categoriaUsuario_id'];
		if ($catUsuario==CAT_USUARIO_COORDINADOR){
			//$decanato= DECANATO_CIENCIAS;
			$decanato=Session::getData('decanato_id');
			$config = new Configuracion();
			$resp['resultado']=$config->getConfiguracionbyDecanato($decanato);
			if ($resp['resultado']){
				$resp['success']= true;
			}else{
				$resp['errorMsj']= 'No se pueden obtener los datos';
			}
		}else{
			$resp['errorMsj']= 'Ud. no posee la permisologia para realizar esta operaci&oacute;n.';
		}
		$resp['errorMsj']=utf8_encode($resp['errorMsj']);
		$this->renderText(json_encode($resp));
	}

	public function datosAction(){
		
	}
	public function respaldarAction(){
		$config = new Configuracion();
		header('Content-Description: File Transfer');
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename=backup.sql.gz');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		ob_clean();
		flush();
		$config->backupTables();		
	}

}
?>