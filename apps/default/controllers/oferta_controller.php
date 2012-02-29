<?php
require_once('utilities/constantes.php');
/**
 * Clase controladora para el modelo Oferta
 * @author Robert A
 *
 */
class OfertaController extends ApplicationController{
	protected   $auth;

	protected function initialize(){
		$this->setTemplateAfter("menu");
		$this->auth=Auth::getActiveIdentity();
	}

	public function indexAction(){

	}

	public function gestionarAction(){
		$this->setParamToView('auth', $this->auth);

	}

	/**
	 * Publica la oferta pasada en el request, envia confirmacionvia Ajax usando Json
	 */
	public function publicarOfertaAction(){
		$this->setResponse('ajax');
		$id = $this->getRequestParam('pIdOferta');
		$oferta = new Oferta();
		$flag = $oferta->publicarOferta($id);
		$respuesta= array();
		if ($flag){
			$respuesta['success']=true;
		}
		else{
			$respuesta['success']=false;
		}
		$this->renderText(json_encode($respuesta));
	}

	/**
	 * Elimina la oferta pasada en el request, envia conformacion de la operacion via Ajax en JSON
	 */
	public function eliminarOfertaAction(){
		$this->setResponse('ajax');
		$id = $this->getRequestParam('pIdOferta');
		$oferta = new Oferta();
		$flag = $oferta->eliminarOferta($id);
		$respuesta= array();
		if ($flag){
			$respuesta['success']=true;
		}
		else{
			$respuesta['success']=false;
		}
		$this->renderText(json_encode($respuesta));
	}


	/**
	 * Crea / actualiza una oferta. Action solo disponible para usuarios tipo Empresa
	 */
	public function registrarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$this->setResponse('ajax');
		$oferta = new Oferta();
		if ($this->auth['categoriaUsuario_id']==CAT_USUARIO_EMPRESA){
			$idEmpresa =$this->auth['idUsuario'];
		}else{
			$idEmpresa=$this->getParametro('pEmpresaId','numerico',-1);
		}
		$fechaC=$this->getParametro('dateFechaCierre','string','');
		$fechaCulEst=$this->getParametro('dateFechaCulminacionEst','string','');
		$fechaIniEst=$this->getParametro('dateFechaInicioEst','string','');
		$titulo=utf8_decode($this->getParametro('txtTitulo','string',''));
		$descripcion=utf8_decode($this->getParametro('txtDescripcion','string',''));
		$cupos= $this->getParametro('txtCupos','numerico',0);
		$vacantes = $this->getParametro('txtVacantes','numerico',-1);
		$tipoOferta= $this->getParametro('pTipoOferta','string','');
		$area = $this->getParametro('pArea','numerico',-1);
		$tipoEvento = $this->getParametro('pTipoEvento','string','');
		if ($fechaC!='' and $fechaCulEst!='' and $fechaIniEst!='' and $titulo!=''
		and $descripcion!='' and $vacantes!=-1 and $tipoOferta!='' and $area!=-1 and $tipoEvento!=''){
			if ($tipoEvento!='update'){
				if ($idEmpresa!=-1){
					$resp['success']=$oferta->guardarOferta($idEmpresa,$fechaC,$titulo,$descripcion,$cupos,$vacantes,$tipoOferta,$area,$tipoEvento,$fechaIniEst,$fechaCulEst);
				}else{
					$resp['errorMsj']= utf8_encode('Identificador de la empresa no válido.');
				}
			}else{
				$idOferta = $this->getParametro('txtIdOfertaHidden','numerico',-1);
				if ($idOferta!=-1){
					$aux = $oferta->actualizarOferta($idOferta,$fechaC,$titulo,$descripcion,$cupos,$vacantes,$tipoOferta,$area,$fechaIniEst,$fechaCulEst);
					$resp['success']= $aux['success'];
					$resp['errorMsj']= $aux['errorMsj'];
				}else{
					$resp['errorMsj']= utf8_encode('Identificador de la oferta no válido.');
				}
			}
		}else{
			$resp['errorMsj']= utf8_encode('Parámetros incorrectos.');
		}
		
		$this->renderText(json_encode($resp));
	}


	/**
	 * Obtiene las ofertas por partes ( basado en parametros start y limit ). Puede retornar las ofertas de todas las empresas o de una sola dependiendo de
	 * la categoria del usuario
	 */
	public function getOfertasAction(){
		$id = ($this->auth['categoriaUsuario_id']==CAT_USUARIO_EMPRESA)?$this->auth['idUsuario']:'%';
		$start = $this->obtenerParametroRequest('start');
		$limit = $this->obtenerParametroRequest('limit');
		$this->setResponse('ajax');
		$ofertas = new Oferta();
		$resultado = $ofertas->getOfertas($id,$start,$limit);
		$this->renderText(json_encode($resultado));
	}

	/**
	 * Obtiene las ofertas por partes ( basado en parametros start y limit ).
	 */
	public function getOfertasbyEmpresaAction(){
		$id = $this->getRequestParam('pEmpresa_id');
		$start = $this->obtenerParametroRequest('start');
		$limit = $this->obtenerParametroRequest('limit');
		$this->setResponse('ajax');
		$ofertas = new Oferta();
		$resultado = $ofertas->getOfertas($id,$start,$limit);
		$this->renderText(json_encode($resultado));
	}

	/**
	 * Obtiene las ofertas por partes ( basado en parametros start y limit ). Basado en las condiciones que apliquen a los pasantes.
	 */
	public function getOfertasPasanteAction(){
		$start = $this->obtenerParametroRequest('start');
		$limit = $this->obtenerParametroRequest('limit');
		$this->setResponse('ajax');
		$ofertas = new Oferta();
		$resultado = $ofertas->getOfertasPasante($start,$limit);
		$this->renderText(json_encode($resultado));
	}


	/**
	 * Obtiene los datos de la oferta solicitada por el request, identificada por su id.
	 */
	public function getOfertaByIdAction(){
		$success= false;
		$this->setResponse('ajax');
		$id=$this->getRequestParam('pOfertaId');
		$oferta = new Oferta();
		$resultado = $oferta->getOfertaById($id);
		if ($resultado){
			$success =  true;
		}
		$this->renderText(json_encode(array("success"=>$success,
											"resultado"=> $resultado)));

	}

	/**
	 * Obtiene las areas de las pasantias, en las cuales se puede crear una oferta.
	 */
	public function getAreasAction(){
		$this->setResponse('ajax');
		$area = new Areapasantia();
		$this->renderText(json_encode($area->getAreas()));
	}


	public function getDescripcionbyIdAction() {
		$success= false;
		$this->setResponse('ajax');
		$id=$this->getRequestParam('pOfertaId');
		$oferta = new Oferta();
		$resultado = $oferta->getDescripcionbyId($id);
		if ($resultado){
			$success =  true;
		}
		$this->renderText(json_encode(array("success"=>$success,
											"resultado"=> $resultado)));

	}
}
?>