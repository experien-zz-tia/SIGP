<?php
class Ciudad extends ActiveRecord {
	protected $id;
	protected $estado_id;
	protected $nombre;
	protected $estatus;

	protected function initialize(){
		$this->belongsTo("estado");
	}

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getEstado_id(){
		return $this->estado_id;
	}

	public function setEstado_id($estado_id){
		$this->estado_id = $estado_id;
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
	/**
	 * Lista todas las ciudades y sus Ids. En un arreglo asociativo, de indices id y nombre.
	 * @return array de las ciudades.
	 */
	public function getCiudades(){
		$aux = array();
		$i=0;
		$ciudades = $this->find("order: nombre");
		foreach($ciudades as $ciudad){
			$aux[$i]['id'] = $ciudad->getId();
			$aux[$i]['nombre'] = utf8_encode($ciudad->getNombre());
			$i++;

		}
		return $aux;
	}

	/**
	 *  Lista las ciudades y sus Ids de un estado dado. En un arreglo asociativo, de indices id y nombre.
	 * @param int $id id del estado
	 * @return array de las ciudades.
	 */
	public function getCiudadesbyEstado($id){
		$aux = array();
		$i=0;
		$ciudades = $this->find("estado_id='$id'","order: nombre");
		foreach($ciudades as $ciudad){
			$aux[$i]['id'] = $ciudad->getId();
			$aux[$i]['nombre'] = utf8_encode($ciudad->getNombre());
			$i++;

		}
		return $aux;

	}
}
?>