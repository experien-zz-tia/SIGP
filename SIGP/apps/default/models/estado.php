<?php
class Estado extends ActiveRecord {
	protected $id;
	protected $nombre;
	protected $estatus;

	protected function initialize(){
		$this->hasMany("ciudad");
	}

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getNombre(){
		return $this->nombre;
	}

	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	public function getEstatus(){
		return $this->estatus;
	}

	public function setEstatus($estatus){
		$this->estatus = $estatus;
	}

	public function getEstados(){
		$aux = array();
		$i=0;
		$estados = $this->find("order: nombre");
		foreach($estados as $estado){
			$aux[$i]['id'] = $estado->getId();
			$aux[$i]['nombre'] = utf8_encode($estado->getNombre());
			$i++;

		}
		return $aux;
	}
}
?>