<?php

class Areapasantia extends ActiveRecord {

	protected $id;
	protected $descripcion;
	protected $estatus;

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

	public function getAreas(){
		$aux = array();
		$i=0;
		$areas= $this->find("order: descripcion");
		foreach($areas as $area){
			$aux[$i]['id'] = $area->getId();
			$aux[$i]['descripcion'] = utf8_encode($area->getDescripcion());
			$i++;

		}
		return $aux;
	}
}
?>