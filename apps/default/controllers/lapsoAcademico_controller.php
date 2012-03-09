<?php
require_once('utilities/constantes.php');
require_once('utilities/libreria.php');
class LapsoAcademicoController extends ApplicationController {
	protected   $auth;
	protected function initialize(){
		$this->setTemplateAfter("menu");
		$this->auth=Auth::getActiveIdentity();
	}

	public function indexAction(){


	}
	/**
	 * Obtiene la informacion de los lapsos academicos por partes ( basado en parametros start y limit ).
	 *
	 */
	public function getLapsosAcademicosAction(){
		$start = $this->obtenerParametroRequest('start');
		$limit = $this->obtenerParametroRequest('limit');
		$decanato=$this->auth['decanato_id'];
		$this->setResponse('ajax');
		$lapsos = new Lapsoacademico();
		$resultado = array();
		$detalles= $lapsos->getLapsosAcademicosbyDecanato($start, $limit,$decanato);
		$this->renderText(json_encode($detalles));
	}

	/**
	 * Crea  un lapso academico.
	 */
	public function registrarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$this->setResponse('ajax');
		$catUsuario=$this->auth['categoriaUsuario_id'];
		if ($catUsuario==CAT_USUARIO_COORDINADOR){
			$lapsoAcad = new Lapsoacademico();
			$fechaFin = $this->getParametro('dateFechaFin', 'string', '');
			$fechaInicio= $this->getParametro('dateFechaInicio', 'string', '');
			$lapso= $this->getParametro('txtLapso', 'string', '');
			if ($fechaFin!='' and $fechaInicio!='' and $lapso!=''){
				if (Libreria::compararFechas($fechaInicio, $fechaFin)>0 ){
					$decanato=$this->auth['decanato_id'];
					$resp['success'] = $lapsoAcad->guardar($lapso,$fechaInicio,$fechaFin,$decanato);
					if (!$resp['success']){
						$resp['errorMsj']='No se ha podido registrar el lapso académico.';
					}
				}else{
					$resp['errorMsj']= 'Fechas no válidas, la fecha de finalización no puede ser menor a la fecha de inicio.';
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

	public function activarLapsoAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$this->setResponse('ajax');
		$catUsuario=$this->auth['categoriaUsuario_id'];
		if ($catUsuario==CAT_USUARIO_COORDINADOR){
			$lapsoAcad = new Lapsoacademico();
			$idLapso = $this->getParametro('idLapso', 'number', -1);
			if ( $idLapso!=-1){
				$decanato=$this->auth['decanato_id'];
				if ($lapsoAcad->hayLapsoActivobyDecanato($decanato)==0){
					$resp['success'] = $lapsoAcad->activarLapso($idLapso);
					if (!$resp['success']){
						$resp['errorMsj']='No se ha podido activar el lapso académico.';
					}
				}else{
					$resp['errorMsj']= 'Solamente puede haber un lapso activo de manera simultanea.';
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

	public function solicitarLapsoAction() {
		$resp=array();
		$resp['success']= false;
		$resp['lapso']= '';
		$this->setResponse('ajax');
		$catUsuario=$this->auth['categoriaUsuario_id'];
		if ($catUsuario==CAT_USUARIO_COORDINADOR){
			$lapsoAcad = new Lapsoacademico();
			$fechaInicio= $this->getParametro('dateFechaInicio', 'string', '');
			if ($fechaInicio!=''){
				$decanato=$this->auth['decanato_id'];
				$anio= Libreria::obtenerAnio($fechaInicio);
				$resp['lapso'] = $lapsoAcad->generarLapso($anio, $decanato);
				if ($resp['lapso']!='')
				$resp['success']= true;
			}else{
				$resp['errorMsj']= 'Parámetros incompletos.';
			}
		}else{
			$resp['errorMsj']= 'Ud. no posee la permisologia para realizar esta operaci&oacute;n.';
		}
		$resp['lapso']=utf8_encode($resp['lapso']);
		$this->renderText(json_encode($resp));
	}

	public function getLapsoByIdAction() {
		$resp=array();
		$resp['success']= false;
		$resp['resultado']= array();
		$this->setResponse('ajax');
		$lapsoAcad = new Lapsoacademico();
		$id= $this->getParametro('pLapsoId', 'numeber', -1);
		if ($id!=-1){
			$resp['resultado'] = $lapsoAcad->getLapsoById($id);
			if ($resp['resultado'])
				$resp['success']= true;
		}else{
			$resp['errorMsj']= 'Parámetros incompletos.';
		}
		$this->renderText(json_encode($resp));
	}
	
	public function modificarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$this->setResponse('ajax');
		$catUsuario=$this->auth['categoriaUsuario_id'];
		if ($catUsuario==CAT_USUARIO_COORDINADOR){
			$lapsoAcad = new Lapsoacademico();
			$fechaFin = $this->getParametro('dateFechaFin', 'string', '');
			$fechaInicio= $this->getParametro('dateFechaInicio', 'string', '');
			$id= $this->getParametro('txtIdLapso', 'number', -1);
			if ($fechaFin!='' and $fechaInicio!='' and $id!=-1){
				if (Libreria::compararFechas($fechaInicio, $fechaFin)>0 ){
					$resp['success'] = $lapsoAcad->modificarLapso($id,$fechaInicio,$fechaFin);
					if (!$resp['success']){
						$resp['errorMsj']='No se ha actualizado el lapso académico.';
					}
				}else{
					$resp['errorMsj']= 'Fechas no válidas, la fecha de finalización no puede ser menor a la fecha de inicio.';
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
	
	
	public function eliminarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$this->setResponse('ajax');
		$catUsuario=$this->auth['categoriaUsuario_id'];
		if ($catUsuario==CAT_USUARIO_COORDINADOR){
			$lapsoAcad = new Lapsoacademico();
			$id= $this->getParametro('pLapsoId', 'number', -1);
			if ($id!=-1){
				$nroPasantesActivos=0;
				$datosLapso= $lapsoAcad->getLapsoById($id);
				$pasante = new Pasante();
				$nroPasantesActivos = $pasante->contarTotalPasantesPorLapso($id);
				if ($nroPasantesActivos==0){
					if (strtoupper($datosLapso['estatus'])!='F'){
					$resp['success'] = $lapsoAcad->eliminarLapso($id);
					if (!$resp['success']){
						$resp['errorMsj']='No se ha eliminado el lapso académico.';
					}
					}else{
						$resp['errorMsj']= 'No se puede eliminar un lapso finalizado.';
				}
				}else{
					$resp['errorMsj']= 'No se puede eliminar. Hay '.$nroPasantesActivos.' pasante(s) activo(s) en el lapso seleccionado.';
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
	
	
	public function obtenerEstadisticasAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$resp['resultado']= array();
		$datos=array();
		$this->setResponse('ajax');
		$catUsuario=$this->auth['categoriaUsuario_id'];
		if ($catUsuario==CAT_USUARIO_COORDINADOR){
			$lapsoAcad = new Lapsoacademico();
			$id= $this->getParametro('pLapsoId', 'number', -1);
			if ($id!=-1){
				$pasante = new Pasante();
				$pasantia = new Pasantia();
				$tutorE = new TutorEmpresarial();
				$tutorA = new TutorAcademico();
				$empresa = new Empresa();
				$oferta = new Oferta();
				$datos['pasantesActivos']= $pasante->contarTotalPasantesPorLapso($id);
				$datos['pasantiasActivas']= $pasantia->contarPasantiasPorLapso($id);
				$datos['tutoresEReistrados']= $tutorE->contarRegistradosEnLapso($id);
				$datos['tutoresAReistrados']= $tutorA->contarRegistradosEnLapso($id);
				$datos['ofertasPublicadas']= $oferta->contarRegistradosEnLapso($id);
				$datos['empresasRegistradas']= $empresa->contarRegistradosEnLapso($id);
				$resp['resultado']=$datos;
				if ($resp['resultado'])
					$resp['success']= true;
				
			}else{
				$resp['errorMsj']= 'Parámetros incompletos.';
			}
		}else{
			$resp['errorMsj']= 'Ud. no posee la permisologia para realizar esta operaci&oacute;n.';
		}
		$resp['errorMsj']=utf8_encode($resp['errorMsj']);
		$this->renderText(json_encode($resp));
	}
	
public function finalizarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$this->setResponse('ajax');
		$catUsuario=$this->auth['categoriaUsuario_id'];
		if ($catUsuario==CAT_USUARIO_COORDINADOR){
			//$decanatoId= DECANATO_CIENCIAS;
			$decanatoId=$this->auth['decanato_id'];
			$lapsoAcad = new Lapsoacademico();
			$id= $this->getParametro('pLapsoId', 'number', -1);
			$omitirSinEvaluar= $this->getParametro('pOmitirSE', 'string', '');
			$enviarNotif= $this->getParametro('pEnviarNotif', 'string', '');
			if ($id!=-1 AND $omitirSinEvaluar!='' AND $enviarNotif!='' ){
				$datosLapso= $lapsoAcad->getLapsoById($id);
				if (strtoupper($datosLapso['estatus'])=='A'){
					$nroPasantias=0;
					$nroEvaluados=0;
					$pasantia = new Pasantia();
					$evaluacion = new Pasanteevaluacion();
					$nroPasantias = $pasantia->contarPasantiasPorLapso($id);
					$nroEvaluados= $evaluacion->contarPasantesEvaluadosPorLapso($id);
					if ($nroPasantias==$nroEvaluados OR $omitirSinEvaluar){
						$successP=$pasantia->finalizarPasantias($id);
						$successL = $lapsoAcad->finalizarLapso($id);
						$configuracion = new Configuracion();
						$successI= $configuracion->cerrarInscripciones($decanatoId);
						if ($successL and $successP and $successI){
							$resp['success'] = true;
						}else{
							if (!$successL){
								$resp['errorMsj']= 'No se ha finalizado el lapso.<BR>';
							}
							if (!$successP){
								$resp['errorMsj']= 'No se han finalizado las pasantías asociadas al lapso.<BR>';
							}
						}
					}else{
						$resp['errorMsj']= 'Aún existen pasantes sin evaluar.';
					}
				}else{
					$resp['errorMsj']= 'Sólo se pueden finalizar lapsos activos.';
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
	


}