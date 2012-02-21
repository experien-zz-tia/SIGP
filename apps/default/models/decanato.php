<?php
/**
 * NO USAR. Hasta que este actualizada.
 * @deprecated
 *
 */
class Decanato extends ActiveRecord{
	protected $id;
	protected $universidad_id;
	protected $cedulaCoord;
	protected $coordinador;
	protected $email;
	protected $nombre;
	protected $direccion;
	protected $oficina;
	protected $ciudad_id;
	protected $ciudad_estado_id;
	protected $telefono;
	protected $telefonoOficina;
	protected $logo;
	protected $estatus;
//-----------------------------------------------------------------------------------------
	protected function inicializate(){
		$this->hasMany('carrera');
	}
//-----------------------------------------------------------------------------------------	
	public function setId($valor){
		$this->id = $valor;
	}
	public function getId(){
		return $this->id;
	}
//-----------------------------------------------------------------------------------------	
	public function setUniversidadId($valor){
		$this->universidad_id = $valor;
	}
	public function getUniverdidadId(){
		return $this->universidad_id;
	}
//-----------------------------------------------------------------------------------------	
	public function setCedulaCoord($valor){
		$this->cedulaCoord = $valor;
	}
	public function getCedulaCoord(){
		return $this->cedulaCoord;
	}
//-----------------------------------------------------------------------------------------	
	public function setCoordinador($valor){
		$this->coordinador = $valor;
	}
	public function getCoordinador(){
		return $this->coordinador;
	}
//-----------------------------------------------------------------------------------------	
	public function setEmail($valor){
		$this->email = $valor;
	}
	public function getEmail(){
		return $this->email;
	}
//-----------------------------------------------------------------------------------------	
	public function setNombre($valor){
		$this->nombre = $valor;
	}
	public function getNombre(){
		return $this->nombre;
	}
//-----------------------------------------------------------------------------------------	
	public function setDireccion($valor){
		$this->direccion = $valor;
	}
	public function getDireccion(){
		return $this->direccion;
	}
//-----------------------------------------------------------------------------------------	
	public function setOficina($valor){
		$this->oficina = $valor;
	}
	public function getOficina(){
		return $this->oficina;
	}
//-----------------------------------------------------------------------------------------	
	public function setCiudadId($valor){
		$this->ciudad_id = $valor;
	}
	public function getCiudadId(){
		return $this->ciudad_id;
	}
//-----------------------------------------------------------------------------------------	
	public function setCiudadEstadoId($valor){
		$this->ciudad_estado_id = $valor;
	}
	public function getCiudadEstadoId(){
		return $this->ciudad_estado_id;
	}
//-----------------------------------------------------------------------------------------	
	public function setTelefono($valor){
		$this->telefono = $valor;
	}
	public function getTelefono(){
		return $this->telefono;
	}
//-----------------------------------------------------------------------------------------	
	public function setTelefonoOficina($valor){
		$this->telefonoOficina = $valor;
	}
	public function getTelefonoOficina(){
		return $this->telefonoOficina;
	}
//-----------------------------------------------------------------------------------------	
	public function setLogo($valor){
		$this->logo = $valor;
	}
	public function getLogo(){
		return $this->logo;
	}
//-----------------------------------------------------------------------------------------	
	public function setEstatus($valor){
		$this->estatus = $valor;
	}
	public function getEstatus(){
		return $this->estatus;
	}
//-----------------------------------------------------------------------------------------
	public function getDecanatos(){
		$auxDecanatos = array();
		$i = 0;
		$decanatos = $this->find("order: nombre");
		foreach($decanatos as $decanato){
			$auxDecanatos[$i]['id'] = $decanato->getId();
			/*$auxDecanatos[$i]['universidad_id'] = $decanato->getUniverdidadId();
			$auxDecanatos[$i]['cedulaCoord'] = $decanato->getCedulaCoord();
			$auxDecanatos[$i]['coordinador'] = $decanato->getCoordinador();
			$auxDecanatos[$i]['email'] = $decanato->getEmail();*/
			$auxDecanatos[$i]['nombre'] = utf8_encode($decanato->getNombre());
	/*		$auxDecanatos[$i]['direccion'] = $decanato->getDireccion();
			$auxDecanatos[$i]['oficina'] = $decanato->getOficina();
			$auxDecanatos[$i]['ciudad_id'] = $decanato->getCiudadId();
			$auxDecanatos[$i]['ciudad_estado_id'] = $decanato->getCiudadEstadoId();
			$auxDecanatos[$i]['telefono'] = $decanato->getTelefono();
			$auxDecanatos[$i]['telefonoOficina'] = $decanato->getTelefonoOficina();
			$auxDecanatos[$i]['logo'] = $decanato->getLogo();
			$auxDecanatos[$i]['estatus'] = $decanato->getEstatus();
		*/	
			$i++;
		}
		
		return $auxDecanatos;
	}
//-----------------------------------------------------------------------------------------
	public function getUniversidadByDecanato($idDecanato){
		$auxDecanatos = array();
		$encontrado = 0;
		$i = 0;
		$decanatos = $this->findFirst("id = '$idDecanato'");
		foreach($decanatos as $decanato){
			$encontrado = $decanato->getUniverdidadId();
		}
		
		return $encontrado;
	}	
//-----------------------------------------------------------------------------------------
}

?>