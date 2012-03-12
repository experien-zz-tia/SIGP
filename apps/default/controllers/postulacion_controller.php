<?php
require_once('utilities/constantes.php');
require_once('utilities/libreria.php');
require_once('correo.php');
class PostulacionController extends ApplicationController{
	protected   $auth;

	protected function initialize(){
		$this->setTemplateAfter("menu");
		$this->auth=Auth::getActiveIdentity();

	}


	public function registrarAction() {
		$success=false;
		$errorMsj='';
		$this->setResponse('ajax');
		$ofertaId= $this->getParametro('pOfertaId', 'numerico', -1);
		if ($ofertaId!=-1){
			$pasanteId =$this->auth['idUsuario'];
			$pasantia = new Pasantia();
			if (!$pasantia->estaEnPasantia($pasanteId)){
				$conf = new Configuracion();
				$decanato=Session::getData('decanato_id');
				$nroMaximo= $conf->getNroMaxPostulacionesbyDecanato($decanato);
				//$nroMaximo= $conf->getNroMaxPostulacionesbyDecanato(DECANATO_CIENCIAS);
				$postulacion= new Postulacion();
				$nroPostulaciones = $postulacion->contarPostulacionesPasante($pasanteId);
				if ($nroPostulaciones< $nroMaximo){
					if (!$postulacion->estaPostulado($pasanteId,$ofertaId)){
						$oferta = new Oferta();
						$datos=$oferta->getOfertaById($ofertaId);
						if ($datos){
							if (strtoupper($datos['tipoOferta'])=='A'){
								if($oferta->isAbierta($ofertaId)){
									if ($datos['cupos']==0){
										$flag=$postulacion->registrarPostulacion($pasanteId,$ofertaId);
										if ($flag) {
											$success=true;
										}else{
											$errorMsj='No se ha completado la operaci�n.';
										}
									}else{
										$nroPostulaciones= $postulacion->contarPostulaciones($ofertaId);
										if(($datos['cupos']-$nroPostulaciones)>0){
											$flag=$postulacion->registrarPostulacion($pasanteId,$ofertaId);
											if ($flag) {
												$success=true;
											}else{
												$errorMsj='No se ha completado la operaci�n.';
											}
										}else{
											$errorMsj='No hay cupos disponibles.';
										}
									}
								}else{
									$errorMsj='La oferta est� cerrada.';
								}
							}else{
								$errorMsj='Esta oferta no admite postulaciones directas.';
							}
						}else{
							$errorMsj='No se encontr� la oferta.';
						}
					}else{
						$errorMsj='Ud. ya est� postulado a esta oferta.';
					}
				}else{
					$errorMsj='Ud. ha alcanzado el n�mero m�ximo de postulaciones aceptadas.';
				}
			}else{
				$errorMsj='Ud. ya tiene una pasant�a asociada.';
			}
		}else{
			$errorMsj='Par�metro incorrecto.';
		}
		$errorMsj=utf8_encode($errorMsj);
		$this->renderText(json_encode(array("success"=>$success,
											"errorMsj"=> $errorMsj)));
	}


	public function getPostulacionesAction() {
		$resultado = array();
		$this->setResponse('ajax');
		$categoria =$this->auth['categoriaUsuario_id'];
		if ($categoria==CAT_USUARIO_EMPRESA){
			$idEmpresa =$this->auth['idUsuario'];
			$postulacion = new Postulacion();
			$resultado = $postulacion->getPostulacionesbyEmpresa($idEmpresa);

		}
		$this->renderText(json_encode(array('resultado'=>$resultado)));
	}

	public function administrarAction() {

	}

	public function rechazarAction() {
		$success=false;
		$errorMsj='';
		$id=$this->getParametro('txtIdPostulacion', 'numerico', -1);
		if ($id!=-1){
			$rechazo=$this->getParametro('txtRechazo', 'string', 'No se especific� un motivo.');
			$errorMsj .= $rechazo;
			$postulacion = new Postulacion();
			$datosPostu =$postulacion->getPostulacionbyId($id);
			if ($datosPostu){
				$success= $postulacion->rechazar($id);
				if ($success){
					$pasante= new Pasante();
					$datos = $pasante->getPasantebyId($datosPostu['pasanteId']);
					$oferta = new Oferta();
					$datosOferta = $oferta->getOfertaById($datosPostu['ofertaId']);
					$this->enviarNotificacion($datos['nombre'], $datos['apellido'], $datos['email'], $rechazo, $datosOferta['titulo']);
				}else{
					$errorMsj='No se ha rechazado la postulaci�n. Intente de nuevo.';
				}
			}
			else{
				$errorMsj='No se encontr� elregistro indicado.';
			}
		}else {
			$errorMsj='Par�metros incorrectos.';
		}

		$this->setResponse('ajax');
		$errorMsj=utf8_encode($errorMsj);
		$this->renderText(json_encode(array("success"=>$success,
											"errorMsj"=> $errorMsj)));

	}

	/**
	 * Envia correo electronico a la cuenta del pasante  para solicitar el rechazo en su postulacion
	 * @param string $nombre
	 * @param string $apellido
	 * @param string $motivo
	 * @param string oferta
	 * @param string $correo
	 */
	private function enviarNotificacion($nombre, $apellido, $correo, $motivo, $oferta){
		$mailer = new Correo();
		$body = " $nombre, $apellido , su solicitud de postulaci&oacute;n a la oferta: $oferta. Ha sido <B>Rechazada</B>.<BR/>
		  		  El motivo de su rechazo es el siguiente: <BR/>".html_entity_decode($motivo);
		$body .="<BR/> Ud. puede postularse en otra oferta disponible, para ello ingrese a Expetientia.";
		$mailer->enviarCorreo($correo, 'Notificaci�n rechazo', $body);
	}

	public function getDatosPostulacionAction() {
		$errorMsj='';
		$datos =array();
		$id=$this->getParametro('pIdPostulacion', 'numerico', -1);
		if ($id!=-1){
			$postulacion = new Postulacion();
			$datos = $postulacion->buscarDatosPostulacion($id);
		}
		else {
			$errorMsj='Par�metros incorrectos.';
		}
		$this->setResponse('ajax');
		$errorMsj=utf8_encode($errorMsj);
		$this->renderText(json_encode(array("success"=>$datos?true:false,
											"datos"=>$datos,
											"errorMsj"=> $errorMsj)));
	}


	public function aceptarAction() {
		$success=false;
		$errorMsj='';
		$idPostulacion=$this->getParametro('txtIdPostulacion', 'numerico', -1);
		$idTutorEmp=$this->getParametro('pTutor', 'numerico', -1);
		$fechaInicioEst=$this->getParametro('dateFechaInicioEst', 'string', '');
		$fechaFinEst=$this->getParametro('dateFechaCulminacionEst', 'string', '');
		if ($idPostulacion!=-1 and $idTutorEmp!=-1 and $fechaFinEst!='' and $fechaInicioEst!=''){
			if (Libreria::compararFechas($fechaInicioEst, $fechaFinEst)>0 ){
				$postulacion = new Postulacion();
				$datos =$postulacion->getPostulacionbyId($idPostulacion);
				if ($datos){
					$idPasante=$datos['pasanteId'];
					$idOferta=$datos['ofertaId'];
					$categoria =$this->auth['categoriaUsuario_id'];
					if ($categoria==CAT_USUARIO_EMPRESA){
						$idEmpresa =$this->auth['idUsuario'];
						$pasantia = new Pasantia();
						if (!$pasantia->estaEnPasantia($idPasante)){
							$lapso = new Lapsoacademico();
							$decanato=Session::getData('decanato_id');
							//$datosLapso=$lapso->getLapsoActivobyDecanato(DECANATO_CIENCIAS);
							$datosLapso=$lapso->getLapsoActivobyDecanato($decanato);
							$lapsoActivo = $datosLapso['id'];
							$solicitud = new Solicitudtutoracademico();
							$datosSolicitud = $solicitud->obtenerTutorAsignado($idPasante);
							$idTutorAcad =0;
							if ($datosSolicitud){
								$idTutorAcad =$datosSolicitud['idTutorAcademico'];
							}
							$oferta = new Oferta();
							$idArea=0;
							$titulo='';
							$datosOferta= $oferta->getOfertaById($idOferta);
							if ($datosOferta){
								$idArea=$datosOferta['areaId'];
								$titulo=$datosOferta['titulo'];
							}
							$modalidadP=0;
							$tipoP=0;
							$pasante= new Pasante();
							$datosPasante = $pasante->getPasantebyId($idPasante);
							if ($datosPasante){
								$modalidadP=$datosPasante['modalidadPasantia'];
								$tipoP=$datosPasante['tipoPasantia'];
								$nombre=$datosPasante['nombre'];
								$apellido=$datosPasante['apellido'];
								$correo=$datosPasante['email'];
								$success=$pasantia->inscribirPasantia($lapsoActivo, $idEmpresa, $idPasante, $idTutorEmp, $idTutorAcad, $idOferta, $fechaInicioEst, $fechaFinEst, $tipoP, $idArea, $modalidadP);
								if($success){
									$postulacion->aprobarPostulacion($idPasante,$idPostulacion);
									$successIns = $pasante->inscribir($idPasante);
									if ($successIns){
										$this->enviarNotificacionAceptacion($nombre, $apellido, $correo, $titulo);		
									}
									
								}else{
									$errorMsj="No se ha podido registrar la pasant�a.";
								}
							}else{
								$errorMsj="No hay datos registrados del pasante.";
							}
						}
						else{
							$errorMsj="El pasante, ya est� inscrito en una pasant�a.";
						}
					}
					else{
						$errorMsj="Usuario sin permiso para  realizar la operaci�n.";
					}
				}else{
					$errorMsj='No se encuentra la postulaci�n.';
				}
			}else{
				$errorMsj='Fechas no v�lidas, la fecha de culminaci�n no puede ser menor a la fecha de inicio.';
			}
		}else{
			$errorMsj='Par�metros incorrectos.';
		}

		$this->setResponse('ajax');
		$errorMsj=utf8_encode($errorMsj);
		$this->renderText(json_encode(array("success"=>$success,
											"errorMsj"=> $errorMsj)));
	}


	private function enviarNotificacionAceptacion($nombre, $apellido, $correo, $oferta){
		$mailer = new Correo();
		$body =  " $nombre, $apellido , su solicitud de postulaci&oacute;n a la oferta: $oferta. Ha sido <B>Aceptada</B>.<BR/>";
		$body .= " Para mayor informaci&oacute;n se recomienda  revisar el men&uacute; <K>Pasant&iacute;as en el sistema.";
		$mailer->enviarCorreo($correo, 'Notificaci�n asignaci�n de pasant�as', $body);
	}

}

?>