<?php
require_once('utilities/constantes.php');
require_once('correo.php');
/**
 * Clase controladora para el modelo Empresa
 * @author Robert A
 *
 */
class EmpresaController extends ApplicationController{

	protected function initialize(){
		$this->setTemplateAfter("menu");

	}
	public function indexAction(){

	}
	public function actualizarAction(){ }
	/**
	 * Obtiene el id y nombre de las empresas regisradas y activas
	 */
	public function getEmpresasAction(){
		$this->setResponse('ajax');
		$empresa = new Empresa();
		$this->renderText(json_encode($empresa->getEmpresas()));
	}
	public function getEmpresaAction(){
		$this->setResponse('ajax');
		$resultado = array();
		$id = $this->getParametro('pEmpresaId', 'numerico', -1);
		if($id!=-1){
			$empresa = new Empresa();
			$resultado=$empresa->getEmpresa($id);
		}
		$this->renderText(json_encode(array("success"=>($resultado)?true:false,
											"resultado"=>$resultado)));
	}

	/**
	 * Listado de empresas segun un estatus (o conjunto de ellos)
	 */
	public function getEmpresasbyEstatusAction(){
		$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : PAGINABLE_START;
		$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : PAGINABLE_LIMIT;
		$this->setResponse('ajax');
		$pEstatus =$this->getRequestParam('pEstatus');
		$empresa = new Empresa();
		$this->renderText(json_encode($empresa->getEmpresasbyEstatus($pEstatus,$start,$limit)));
	}

	public function sinValidarAction(){

	}

	/**
	 * Cuenta las pasantias activas de la empresa indicada.
	 * @param int $idEmpresa
	 * @return int
	 */
	protected function contarPasantiasActivas($idEmpresa){
		$nroPasantias=0;
		$pasantia = new Pasantia();
		$nroPasantias = $pasantia->contarPasantiasActivasEmpresa($idEmpresa);
		return $nroPasantias;
	}

	/**
	 * Visualiza los datos de una empresa
	 */
	public function verAction() {
		$this->setResponse('ajax');
		$id = $this->getRequestParam('pIdEmpresa');
		$resp= array();
		$empresa= new Empresa();
		$datos =$empresa->getEmpresa($id);
		$resp['success']= (count($datos)==0)?false:true;
		$resp['resultado']= $datos;
		$this->renderText(json_encode($resp));
	}

	/**
	 * Elimina la empresa. Recibe un parametro a través del REQUEST
	 */
	public function eliminarEmpresaAction(){
		$this->setResponse('ajax');
		$id = $this->getRequestParam('pIdEmpresa');
		$nro= $this->contarPasantiasActivas($id);
		$resp= array();
		$resp['success']= false;
		$resp['errorMsj']= $nro;
		if ($nro==0){ // Si no tiene pasantias activas, es candidado a eliminar
			$empresa = new Empresa();
			if (!$empresa->eliminar($id)){
				$resp['errorMsj']= utf8_encode('Error al eliminar la empresa.');
			}else{
				$tutorE= new TutorEmpresarial();
				if ($tutorE->eliminarTutores($id)){
					$resp['success']=true;
				}else{
					$resp['errorMsj']= utf8_encode('Error al eliminar los tutores.');
				}
			}
		}else{
			$resp['errorMsj']= utf8_encode('La empresa tiene '.$nro.' pasantía(s) activa(s) asociada(s).');
		}
		$this->renderText(json_encode($resp));
	}


	/**
	 * Activa la empresa y su usuario asociado. Recibe un parametro a través del REQUEST
	 */
	public function aprobarAction(){
		$this->setResponse('ajax');
		$id = $this->getRequestParam('pIdEmpresa');
		$resp= array();
		$resp['success']= false;
		if ($id!=null){
			$empresa = new Empresa();
			$resp['success']=$empresa->activar($id);
			$datos =$empresa->getEmpresa($id);
			$this->noticarActivacion($datos['correo']);
		}
		$this->renderText(json_encode($resp));
	}

	/**
	 * Envia correo electronico a la cuenta del usuario para informar aprobacion
	 * @param string $correo
	 */
	protected function noticarActivacion($correo){
		$mailer = new Correo();
		$body ='Registro completado. <BR/>
		  		  Su registro ha sido aprobado por la coordinaci&oacute;n de pasant&iacute;as. Ya puede iniciar sesi&oacute;n  y realizar sus operaciones.<BR/>';
		$body .='Visite el siguiente enlace: http://'. $this->getServer('SERVER_NAME').'/SIGP';
		$mailer->enviarCorreo($correo, 'Registro en el sistema', $body);
	}

	public function actualizarEmpresaAction(){
		$success= false;
		$this->setResponse('ajax');
		$successEmpresa=$successUsuario=$successRegistro=false;

		$empresa = new Empresa();
		$id= $this->getParametro('pEmpresaId','numerico',-1);

		$razonSocial = utf8_decode($this->getParametro('pRazonSocial','string',''));
		$direccion =utf8_decode($this->getParametro('pDireccion','string',''));
		$estado=$this->getParametro('pEstado','numerico',-1);
		$ciudad=$this->getParametro('pCiudad','numerico',-1);
		$telefono= $this->getParametro('pTelefono','string','');
		$telefono2=$this->getParametro('pTelefono2','string','');
		$descripcion=utf8_decode($this->getParametro('pDescripcion','string',''));
		$web=utf8_decode($this->getParametro('pWeb','string',''));
		$representante=utf8_decode($this->getParametro('pRepresentante','string',''));
		$cargo=utf8_decode($this->getParametro('pCargo','string',''));
		$correo = utf8_decode($this->getParametro('pCorreo','string',''));
		if ($id!=-1 and $razonSocial!='' and $direccion!='' and $estado!=-1 and $ciudad!=-1 and
		$telefono!='' and $descripcion!='' and $representante!='' and  $cargo!='' ){
			$success = $empresa->actualizarEmpresa($id,$razonSocial,$direccion,$estado,$ciudad,$telefono,$telefono2,$descripcion,$web,$representante,$cargo, $correo);

		}

		$this->renderText(json_encode(array("success"=>$success)));
	}

}
?>
