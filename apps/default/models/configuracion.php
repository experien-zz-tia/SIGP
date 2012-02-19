<?php

class Configuracion extends ActiveRecord{
	/**
	 * Numero maximo de solicitudes simultaneas que se pueden realizar un pasante a un tutor
	 * @var int
	 */
	const SOLIC_TUTOR =1;
	
	/**
	 * Numero maximo de postulaciones simultaneas que se pueden realizar
	 * @var int
	 */
	const POSTULACIONES_SIMULTANEAS =1;
	/**
	 * Numero maximo de solicitudes que pueden realizar a un tutor academico (grupo de solicitudes)
	 * @var int
	 */
	const SOLIC_RECIBIDAS_TUTOR = 30;
	
	const MAX_SOLIC_TUTOR =5;
	
	const MAX_POSTULACIONES_SIMULTANEAS =10;
	
	const MAX_SOLIC_RECIBIDAS_TUTOR = 50;
	const MENSAJES_ALMACENADOS =10;
	const MAX_MENSAJES_ALMACENADOS =20;
	
	
	protected $id;
	protected $decanato_id;
	protected $nroMaxSolicitudTutor;
	protected $nroMaxPostulacionOferta;
	protected $nroMaxSolitudRecibidasTutor;
	protected $inscripcionesAbiertas;
	protected $consultaCalificaciones;
	protected $nroMaxMensajesAlmacenados;
	protected $actualizacionCalificaciones;

	
	public function getId() { 
		return $this->id; 
	}
	public function getDecanato_id() { 
		return $this->decanato_id; 
	}
	public function getNroMaxSolicitudTutor() { 
		return $this->nroMaxSolicitudTutor; 
	}
	public function getNroMaxPostulacionOferta() { 
		return $this->nroMaxPostulacionOferta; 
	}
	public function getNroMaxSolitudRecibidasTutor() { 
		return $this->nroMaxSolitudRecibidasTutor; 
	}
	public function getInscripcionesAbiertas() { 
		return $this->inscripcionesAbiertas; 
	}
	public function getConsultaCalificaciones() { 
		return $this->consultaCalificaciones; 
	}
	public function getNroMaxMensajesAlmacenados() { 
		return $this->nroMaxMensajesAlmacenados; 
	}
	public function getActualizacionCalificaciones() { 
		return $this->actualizacionCalificaciones; 
	}
	public function setActualizacionCalificaciones($x) { 
		$this->actualizacionCalificaciones = $x; 
	}
	public function setNroMaxMensajesAlmacenados($x) { 
		$this->nroMaxMensajesAlmacenados = $x; 
	}
	public function setConsultaCalificaciones($x) { 
		$this->consultaCalificaciones = $x; 
	}
	public function setInscripcionesAbiertas($x) { 
		$this->inscripcionesAbiertas = $x; 
	}
	public function setNroMaxSolitudRecibidasTutor($x) { 
		$this->nroMaxSolitudRecibidasTutor = $x; 
	}
	public function setId($x) { 
		$this->id = $x; 
	}
	public function setDecanato_id($x) { 
		$this->decanato_id = $x; 
	}
	public function setNroMaxSolicitudTutor($x) { 
		$this->nroMaxSolicitudTutor = $x; 
	}
	public function setNroMaxPostulacionOferta($x) { 
		$this->nroMaxPostulacionOferta = $x; 
	}
	
	/**
	 * Obtiene el numero maximo de solicitudes que un pasante puede realizar de manera simultanea a un tutor academico en un decanato 
	 * @param int $decanatoId
	 * @return number
	 */
	public function getNroMaxSolicTutorbyDecanato($decanatoId) {
		$nro = Configuracion::SOLIC_TUTOR; 
		$config = $this->findFirst("decanato_id='$decanatoId'");
		if ($config){
			$nro=$config->getNroMaxSolicitudTutor();
		}
		return $nro;
	}
	
	/**
	 * Obtiene el numero maximo de postulaciones que un postulante puede realizar de manera simultanea en un decanato
	 * @param int $decanatoId
	 * @return number
	 */
	public function getNroMaxPostulacionesbyDecanato($decanatoId) {
		$nro = Configuracion::POSTULACIONES_SIMULTANEAS; 
		$config = $this->findFirst("decanato_id='$decanatoId'");
		if ($config){
			$nro=$config->getNroMaxPostulacionOferta();
		}
		return $nro;
	}
	
	/**
	 * Obtiene el numero maximo de postulaciones que puede recibir un tutor de manera simultanea en un decanato
	 * @param int $decanatoId
	 * @return number
	 */
	public function getNroMaxSolitudesRecibidasbyDecanato($decanatoId) {
		$nro = Configuracion::SOLIC_RECIBIDAS_TUTOR; 
		$config = $this->findFirst("decanato_id='$decanatoId'");
		if ($config){
			$nro=$config->getNroMaxSolitudRecibidasTutor();
		}
		return $nro;
	}
	public function getNroMaxMensajesAlmacenadosbyDecanato($decanatoId) {
		$nro = Configuracion::MENSAJES_ALMACENADOS; 
		$config = $this->findFirst("decanato_id='$decanatoId'");
		if ($config){
			$nro=$config->getNroMaxMensajesAlmacenados();
		}
		return $nro;
	}
	
	public function getInscripcionesAbiertasbyDecanato($decanatoId) {
		$default = 'C'; 
		$config = $this->findFirst("decanato_id='$decanatoId'");
		if ($config){
			$default=$config->getInscripcionesAbiertas();
		}
		return $default;
	}
	public function cerrarInscripciones($decanatoId) {
		$flag = false; 
		$config = $this->findFirst("decanato_id='$decanatoId'");
		if ($config){
			$config->setInscripcionesAbiertas('C');
			$flag=$config->update();
		}
		return $flag;
	}
	
	public function abrirInscripciones($decanatoId) {
		$flag = false; 
		$config = $this->findFirst("decanato_id='$decanatoId'");
		if ($config){
			$config->setInscripcionesAbiertas('A');
			$flag=$config->update();
		}
		return $flag;
	}
	
	public function getConsultaCalificacionesbyDecanato($decanatoId) {
		$default = 'N'; 
		$config = $this->findFirst("decanato_id='$decanatoId'");
		if ($config){
			$default=$config->getConsultaCalificaciones();
		}
		return $default;
	}
	public function cerrarConsultaCalificaciones($decanatoId) {
		$flag = false; 
		$config = $this->findFirst("decanato_id='$decanatoId'");
		if ($config){
			$config->setInscripcionesAbiertas('N');
			$flag=$config->update();
		}
		return $flag;
	}
	
	public function abrirConsultaCalificaciones($decanatoId) {
		$flag = false; 
		$config = $this->findFirst("decanato_id='$decanatoId'");
		if ($config){
			$config->setInscripcionesAbiertas('S');
			$flag=$config->update();
		}
		return $flag;
	}
	
	public function getActualizacionCalificacionesbyDecanato($decanatoId) {
		$default = 'N'; 
		$config = $this->findFirst("decanato_id='$decanatoId'");
		if ($config){
			$default=$config->getActualizacionCalificaciones();
		}
		return $default;
	}
	
	public function getConfiguracionbyDecanato($decanatoId){
		$aux=array();
		$aux['maxSolicTutor']=$this->getNroMaxSolicTutorbyDecanato($decanatoId);
		$aux['maxSolicSimul']=$this->getNroMaxPostulacionesbyDecanato($decanatoId);
		$aux['maxSolicRecibidasTutor']=$this->getNroMaxSolitudesRecibidasbyDecanato($decanatoId);
		$aux['maxMensajesAlmacenados']=$this->getNroMaxMensajesAlmacenadosbyDecanato($decanatoId);
		$aux['inscripciones']=strtoupper($this->getInscripcionesAbiertasbyDecanato($decanatoId));
		$aux['calificaciones']=strtoupper($this->getConsultaCalificacionesbyDecanato($decanatoId));
		$aux['actCalif']=strtoupper($this->getActualizacionCalificacionesbyDecanato($decanatoId));
		return $aux;
	
	}
	public function guardarConfiguracion($decanatoId,$pMaxRecSolicTutor,$pMaxSolicOferta,$pMaxSolicTutor,$pRadioInscrip,$pRadioCalif,$pMaxMensajes,$pRadioActCalif){
		$flag = false; 
		$error='';
		$config = $this->findFirst("decanato_id='$decanatoId'");
		if ($config){
			if ($pMaxRecSolicTutor<= Configuracion::MAX_SOLIC_RECIBIDAS_TUTOR and $pMaxRecSolicTutor>0){
				$config->setNroMaxSolitudRecibidasTutor($pMaxRecSolicTutor);	
			}else{
				$error .= "Solicitudes recibidas por tutor: Debe estar en el rango [1,".Configuracion::MAX_SOLIC_RECIBIDAS_TUTOR."] <BR>";
			}
			if ($pMaxSolicOferta<= Configuracion::MAX_POSTULACIONES_SIMULTANEAS and $pMaxSolicOferta>0){
				$config->setNroMaxPostulacionOferta($pMaxSolicOferta);	
			}else{
				$error .= "Postulaciones por pasante: Debe estar en el rango [1,".Configuracion::MAX_POSTULACIONES_SIMULTANEAS."]<BR>";
			}
			if ($pMaxSolicTutor<= Configuracion::MAX_SOLIC_TUTOR and $pMaxSolicTutor>0){
				$config->setNroMaxSolicitudTutor($pMaxSolicTutor);	
			}else{
				$error .= "Solicitudes por pasante a tutor: Debe estar en el rango [1,".Configuracion::MAX_SOLIC_TUTOR."]<BR>";
			}
			if ($pMaxMensajes<= Configuracion::MAX_MENSAJES_ALMACENADOS and $pMaxMensajes>0){
				$config->setNroMaxMensajesAlmacenados($pMaxMensajes);	
			}else{
				$error .= "Mensajes: Debe estar en el rango [1,".Configuracion::MAX_MENSAJES_ALMACENADOS."]<BR>";
			}
			$lapso = new Lapsoacademico();
			$lapsoActivo= $lapso->hayLapsoActivobyDecanato($decanatoId);
			$pRadioInscrip= strtoupper($pRadioInscrip);
			if ($pRadioInscrip=='A' OR $pRadioInscrip=='C'){
				if ($lapsoActivo==1){
					$config->setInscripcionesAbiertas($pRadioInscrip);	
				}else{
					$error .= "Inscripciones: No hay lapso académico activo, debe activar un lapso para poder habilitar las inscripciones.";
				}
			}else{
				$error .= "Inscripciones: Valor no válido.";
			}
			$pRadioCalif= strtoupper($pRadioCalif);
			if ($pRadioCalif=='S' OR $pRadioCalif=='N'){
				if ($lapsoActivo==1){
					$config->setConsultaCalificaciones($pRadioCalif);	
				}else{
					$error .= "Calificaciones: No hay lapso académico activo, debe activar un lapso para poder habilitar la consulta de Calificaciones.";
				}
			}else{
				$error .= "Calificaciones: Valor no válido.";
			}
			$pRadioActCalif= strtoupper($pRadioActCalif);
			if ($pRadioActCalif=='S' OR $pRadioActCalif=='N'){
				if ($lapsoActivo==1){
					$config->setActualizacionCalificaciones($pRadioActCalif);	
				}else{
					$error .= "Actualización Calificaciones: No hay lapso académico activo, debe activar un lapso para poder habilitar las inscripciones.";
				}
			}else{
				$error .= "Actualización Calificaciones: Valor no válido.";
			}
			$flag=$config->update();
		}
		$aux['success']=($error=='')?$flag:false;
		$aux['errores']=$error;
		return $aux;
	}
}

?>