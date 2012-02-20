<?php
class Universidad extends ActiveRecord{
	protected $id;
	protected $nombre;
	protected $direccion;
	protected $telefono;
	protected $estado_id;
	protected $ciudad_id;
	protected $logo;
	protected $estatus;
//-----------------------------------------------------------------------------------------
	protected function inicializate(){
		$this->hasMany('universidad');
	}
//-----------------------------------------------------------------------------------------	
	public function setId($valor){
		$this->id = $valor;
	}
	public function getId(){
		return $this->id;
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
	public function setCiudadId($valor){
		$this->ciudad_id = $valor;
	}
	public function getCiudadId(){
		return $this->ciudad_id;
	}
//-----------------------------------------------------------------------------------------	
	public function setEstadoId($valor){
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
	public function getUniversidades(){
		$auxUniversidades = array();
		$i = 0;
		$Universidades = $this->find("order: nombre");
		foreach($Universidades as $universidad){
			$auxUniversidades[$i]['id'] = $universidad->getId();
			$auxUniversidades[$i]['nombre'] = utf8_encode($universidad->getNombre());	
			$i++;
		}
		
		return $auxUniversidades;
	}
//-----------------------------------------------------------------------------------------
}

?>