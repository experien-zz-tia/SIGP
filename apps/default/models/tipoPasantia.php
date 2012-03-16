<?php
class TipoPasantia extends ActiveRecord{
	protected $id;
	protected $descripcion;
	protected $estatus;
	//-----------------------------------------------------------------------------------------
	public function getId(){ return $this->id; }
	public function setId($valor){ $this->id = $valor; }

	public function getDescripcion(){ return $this->descripcion; }
	public function setDescripcion($valor){ $this->descripcion = $valor; }

	public function getEstatus(){ return $this->estatus; }
	public function setEstatus($valor){ $this->estatus = $valor; }
	//-----------------------------------------------------------------------------------------
	public function getTiposPasantia(){
		$arrTipos = array();
		$i = 0;
		$tipos = $this->find("estatus = 'A'","order: descripcion");
		foreach ($tipos as $tipo){
			$arrTipos[$i]['id'] = $tipo->id;
			$arrTipos[$i]['descripcion'] = utf8_encode($tipo->descripcion);
			$i++;
		}
		return $arrTipos;
	}
	//-----------------------------------------------------------------------------------------
	public function eliminar($vId) {
		$success = false;
		$tipo = $this->findFirst("id = ".$vId." AND estatus = 'A'");
		if ($tipo != null){
			$tipo->setEstatus('E');
			$success = $tipo->update();
		}
		return $success;
	}
	//-----------------------------------------------------------------------------------------
	public function registrar($vDescripcion) {
		$success = false;
		$this->setDescripcion($vDescripcion);
		$this->setEstatus('A');
		$success = $this->save();

		return $success;
	}
	//-----------------------------------------------------------------------------------------
	public function actualizar($vId, $vDescripcion) {
		$success = false;
		$tipo = $this->findFirst("id = ".$vId);
		if($tipo != null){
			$tipo->setDescripcion($vDescripcion);
			$tipo->setEstatus('A');
			$success = $tipo->update();
		}
		return $success;
	}
	//-----------------------------------------------------------------------------------------
	public function buscar($id){
		$arrTipos = array();
		$i = 0;
		$tipos = $this->find("id = ".$id." AND estatus = 'A'");
		$arrTipos['success'] = false;
		$resp['datos'] = array();
		foreach($tipos as $tipo){
			$arrTipos['datos']['id'] = $tipo->id;
			$arrTipos['datos']['descripcion'] = utf8_encode($tipo->descripcion);
			$arrTipos['datos']['estatus'] = $tipo->estatus;
			$arrTipos['success'] = true;
			$i++;
		}
		return $arrTipos;
	}
	//-----------------------------------------------------------------------------------------
}
?>
