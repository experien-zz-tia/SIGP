<?php
class Menu extends ActiveRecord {
	protected $id;
	protected $categoriaMenu_id;
	protected $nombre;
	protected $descripcion;
	protected $ruta;
	protected $estatus;

	protected function initialize(){
		$this->belongsTo('id','categoriaMenu','menu_id');
	}
	public function getId() { 
		return $this->id; 
	}
	public function getCategoriaMenu_id() { 
		return $this->categoriaMenu_id; 
	}
	public function getNombre() { 
		return $this->nombre;
	 }
	public function getDescripcion() { 
		return $this->descripcion; 
	}
	public function getRuta() {
		return $this->ruta;
	}
	public function getEstatus() { 
		return $this->estatus; 
	}
	public function setId($x) {
		$this->id = $x;
	 }
	public function setCategoriaMenu_id($x) {
		$this->categoriaMenu_id = $x; 
	}
	public function setNombre($x) {
		$this->nombre = $x; 
	}
	public function setDescripcion($x) { 
		$this->descripcion = $x; 
	}
	public function setRuta($x) { 
		$this->ruta = $x; 
	}
	public function setEstatus($x) { 
		$this->estatus = $x;
	 }
}
?>