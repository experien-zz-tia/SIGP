<?php
class Departamento extends ActiveRecord {
	protected $id;
	protected $descripcion;
	protected $decanato_id;
	protected $estatus;
//-----------------------------------------------------------------------------------------
	protected function inicializate(){
		$this->belongsTo("decanato");
	}
//-----------------------------------------------------------------------------------------	
	public function setId($valor){
		$this->id = $valor;
	}
	public function getId(){
		return $this->id;
	}
//-----------------------------------------------------------------------------------------	
	public function setDecanatoId($valor){
		$this->decanato_id = $valor;
	}
	public function getDecanatoId(){
		return $this->decanato_id;
	}
//-----------------------------------------------------------------------------------------	
	public function setDescripcion($valor){
		$this->descripcion = $valor;
	}
	public function getDescripcion(){
		return $this->descripcion;
	}
//-----------------------------------------------------------------------------------------	
	public function setEstatus($valor){
		$this->estatus = $valor;
	}
	public function getEstatus(){
		return $this->estatus;
	}
//-----------------------------------------------------------------------------------------
	public function getDepartamentos(){
		$aux = array();
		$i=0;
		$departamentos = $this->find("order: descripcion");
		foreach($departamentos as $departamento){
			$aux[$i]['id'] = $departamento->getId();
			$aux[$i]['descripcion'] = utf8_encode($departamento->getDescripcion());
			$i++;
		}
		return $aux;
	}
//-----------------------------------------------------------------------------------------	
	public function getDepartamentosbyDecanato($id){
		$aux = array();
		$i=0;
		$departamentos = $this->find("decanato_id='$id'","order: descripcion");
		
		foreach($departamentos as $departamento){
			$aux[$i]['id'] = $departamento->getId();
			$aux[$i]['descripcion'] = utf8_encode($departamento->getDescripcion());
			$i++;
		}
		
		return $aux;

	}
//-----------------------------------------------------------------------------------------	
}
?>