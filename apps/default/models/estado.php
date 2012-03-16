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
	//-----------------------------------------------------------------------------------------
	public function getEstados(){
		$aux = array();
		$i=0;
		$estados = $this->find("estatus = 'A'","order: nombre");
		foreach($estados as $estado){
			$aux[$i]['id'] = $estado->getId();
			$aux[$i]['nombre'] = utf8_encode($estado->getNombre());
			$i++;
		}
		return $aux;
	}
	//-----------------------------------------------------------------------------------------
	public function getEstadosLimit(){
		$aux = array();
		$i=0;
		$total=0;
		$sql  = " SELECT * ";
		$sql .= " FROM estado e ";
		$sql .= " WHERE e.estatus = 'A' ";
		$sql .= " ORDER BY e.nombre ";
		//$sql .= " LIMIT 0,20 ";
		
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$i]['id'] = $row['id'];
			$aux[$i]['nombre'] = utf8_encode($row['nombre']);
			$aux[$i]['estatus'] = utf8_encode($row['estatus']);
			$i++;
		}
		return $aux;
		
	}

	//-----------------------------------------------------------------------------------------
	public function eliminar($vId) {
		$success = false;
		$estado = $this->findFirst("id = ".$vId." AND estatus = 'A'");
		if ($estado != null){
			$estado->setEstatus('E');
			$success = $estado->update();
		}
		return $success;
	}
	//-----------------------------------------------------------------------------------------
	public function registrar($vNombre) {
		$success = false;
		$this->setNombre($vNombre);
		$this->setEstatus('A');
		$success = $this->save();

		return $success;
	}
	//-----------------------------------------------------------------------------------------
	public function actualizar($vId, $vNombre) {
		$success = false;
		$estado = $this->findFirst("id = ".$vId);
		if($estado != null){
			$estado->setNombre($vNombre);
			$estado->setEstatus('A');
			$success = $estado->update();
		}
		return $success;
	}
	//-----------------------------------------------------------------------------------------
	public function buscar($id){
		$arrEstados = array();
		$i = 0;
		$estados = $this->find("id = ".$id." AND estatus = 'A'");
		$arrEstados['success'] = false;
		$resp['datos'] = array();
		foreach($estados as $estado){
			$arrEstados['datos']['id'] = $estado->id;
			$arrEstados['datos']['nombre'] = utf8_encode($estado->nombre);
			$arrEstados['datos']['estatus'] = $estado->estatus;
			$arrEstados['success'] = true;
			$i++;
		}
		return $arrEstados;
	}
	//-----------------------------------------------------------------------------------------
}
?>