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
	public function setDecanato_id($valor){
		$this->decanato_id = $valor;
	}
	public function getDecanato_id(){
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
	public function getDepartamentosFull($vId){
		$arrDepartamentos = array();
		$i = 0;
		if ($vId == '-1' or $vId == null){
			$departamentos = $this->find("estatus = 'A' ","order: descripcion");
		} else {
			$departamentos = $this->find("decanato_id = '$vId'", "order: descripcion");
		}

		foreach ($departamentos as $departamento){
			$arrDepartamentos[$i]['id'] = $departamento->id;
			$arrDepartamentos[$i]['decanato_id'] = $departamento->decanato_id;
			$decanato = new Decanato();
			$dec = $decanato->findFirst("id = ".$departamento->decanato_id);
			if ($dec != null){
				$arrDepartamentos[$i]['decanato'] = utf8_encode($dec->getNombre());
			}
			$arrDepartamentos[$i]['descripcion'] = utf8_encode($departamento->descripcion);
			$arrDepartamentos[$i]['estatus'] = $departamento->estatus;
			$i++;
		}

		return $arrDepartamentos;
	}
	//-----------------------------------------------------------------------------------------
	public function eliminar($vId) {
		$success = false;
		$departamento = $this->findFirst("id = ".$vId." AND estatus = 'A'");
		if ($departamento != null){
			$departamento->setEstatus('E');
			$success = $departamento->update();
		}
		return $success;
	}
	//-----------------------------------------------------------------------------------------
	public function registrar($vDecanatoId, $vDescripcion) {
		$success = false;
		$this->setDecanato_id($vDecanatoId);
		$this->setDescripcion($vDescripcion);
		$this->setEstatus('A');
		$success = $this->save();

		return $success;
	}
	//-----------------------------------------------------------------------------------------
	public function actualizar($vId, $vDecanatoId, $vDescripcion) {
		$success = false;
		$departamento = $this->findFirst("id = ".$vId);
		if($departamento != null){
			$departamento->setDecanato_id($vDecanatoId);
			$departamento->setDescripcion($vDescripcion);
			$departamento->setEstatus('A');
			$success = $departamento->update();
		}
		return $success;
	}
	//-----------------------------------------------------------------------------------------
	public function buscar($id){
		$arrDepartamentos = array();
		$i = 0;
		$departamentos = $this->find("id = ".$id." AND estatus = 'A'");
		$arrDepartamentos['success'] = false;
		$resp['datos'] = array();
		foreach($departamentos as $departamento){
			$arrDepartamentos['datos']['id'] = $departamento->id;
			$arrDepartamentos['datos']['decanato_id'] = $departamento->decanato_id;
			$arrDepartamentos['datos']['descripcion'] = utf8_encode($departamento->descripcion);
			$arrDepartamentos['datos']['estatus'] = $departamento->estatus;
			$arrDepartamentos['success'] = true;
			$i++;
		}
		return $arrDepartamentos;
	}
	//-----------------------------------------------------------------------------------------
}
?>