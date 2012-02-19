<?php
class CategoriaMenu extends ActiveRecord {
	protected $id;
	protected $descripcion;
	protected $estatus;

	protected function initialize(){
		$this->hasMany('id','menu','id');
	}
	public function getId() {
		return $this->id; 
	}
	public function getDescripcion() {
		return $this->descripcion; 
	}
	public function getEstatus() { 
		return $this->estatus;
	 }
	public function setId($x) { 
		$this->id = $x; 
	}
	public function setDescripcion($x) { 
		$this->descripcion = $x;
	 }
	public function setEstatus($x) { 
		$this->estatus = $x; 
	}

}
?>