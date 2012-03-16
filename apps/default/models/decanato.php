<?php

class Decanato extends ActiveRecord{
	protected $id;
	protected $universidad_id;
	protected $nombre;
	protected $direccion;
	protected $ciudad_id;
	protected $estado_id;
	protected $telefono;
	protected $logo;
	protected $estatus;
	//-----------------------------------------------------------------------------------------
	protected function inicializate(){
		$this->hasMany('carrera');
	}
	//-----------------------------------------------------------------------------------------
	public function setId($valor){
		$this->id = $valor;
	}
	public function getId(){
		return $this->id;
	}
	//-----------------------------------------------------------------------------------------
	public function setUniversidadId($valor){
		$this->universidad_id = $valor;
	}
	public function getUniverdidadId(){
		return $this->universidad_id;
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
		$this->estado_id = $valor;
	}
	public function getEstadoId(){
		return $this->estado_id;
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
	public function getDecanatos(){
		$auxDecanatos = array();
		$i = 0;
		$decanatos = $this->find("estatus='A'","order: nombre");
		foreach($decanatos as $decanato){
			$auxDecanatos[$i]['id'] = $decanato->getId();
			$auxDecanatos[$i]['universidad_id'] = $decanato->getUniverdidadId();
			$auxDecanatos[$i]['nombre'] = utf8_encode($decanato->getNombre());
			$auxDecanatos[$i]['direccion'] = $decanato->getDireccion();
			$auxDecanatos[$i]['oficina'] = $decanato->getOficina();
			$auxDecanatos[$i]['ciudad_id'] = $decanato->getCiudadId();
			$auxDecanatos[$i]['estado_id'] = $decanato->getCiudadEstadoId();
			$auxDecanatos[$i]['telefono'] = $decanato->getTelefono();
			$auxDecanatos[$i]['logo'] = $decanato->getLogo();
			$auxDecanatos[$i]['estatus'] = $decanato->getEstatus();
			
			$i++;
		}

		return $auxDecanatos;
	}
	//-----------------------------------------------------------------------------------------
	public function getDecanatosFull(){
		$auxDecanatos = array();
		$i = 0;
		$decanatos = $this->find("estatus='A'","order: nombre");
		foreach($decanatos as $decanato){
			$auxDecanatos[$i]['id'] = $decanato->getId();
			$auxDecanatos[$i]['universidad_id'] = $decanato->getUniverdidadId();
			$universidad = new  Universidad();
			$uni = $universidad->findFirst("id = ".$decanato->getUniverdidadId());
			if ($uni != null){
				$auxDecanatos[$i]['universidad'] = utf8_encode($uni->getNombre());
			}
			$auxDecanatos[$i]['nombre'] = utf8_encode($decanato->getNombre());
			$auxDecanatos[$i]['direccion'] = utf8_encode($decanato->getDireccion());
			$auxDecanatos[$i]['ciudad_id'] = $decanato->getCiudadId();
			$ciudad = new Ciudad();
			$ciu = $ciudad->findFirst("id = ".$decanato->getCiudadId());
			if ($ciu != null){
				$auxDecanatos[$i]['ciudad'] = utf8_encode($ciu->getNombre());
			}
			$auxDecanatos[$i]['estado_id'] = $decanato->getEstadoId();
			$estado = new Estado();
			$est = $estado->findFirst("id = ".$decanato->getEstadoId());
			if ($est != null){
				$auxDecanatos[$i]['estado'] = utf8_encode($est->getNombre());
			}

			$auxDecanatos[$i]['telefono'] = $decanato->getTelefono();
			$auxDecanatos[$i]['logo'] = $decanato->getLogo();
			//$auxDecanatos[$i]['estatus'] = $decanato->getEstatus();

			$i++;
		}

		return $auxDecanatos;
	}
	//-----------------------------------------------------------------------------------------

	public function getUniversidadByDecanato($idDecanato){
		$auxDecanatos = array();
		$encontrado = 0;
		$i = 0;
		$decanatos = $this->find("id = '$idDecanato'");
		foreach($decanatos as $decanato){
			$encontrado = $decanato->getUniverdidadId();
		}
		return $encontrado;
	}
	//-----------------------------------------------------------------------------------------
	public function buscarDecanato($idDecanato){
		$decanato = $this->findFirst("id = '$idDecanato'");
		return $decanato;
	}
	//-----------------------------------------------------------------------------------------
	public function registrar($vUniverdad, $vCiudad, $vDireccion, $vEstado, $vNombre, $vTelefono) {
		$success = false;

		$this->setUniversidadId($vUniverdad);
		$this->setCiudadId($vCiudad);
		$this->setDireccion($vDireccion);
		$this->setEstadoId($vEstado);
		//$this->setLogo($valor);
		$this->setNombre($vNombre);
		$this->setTelefono($vTelefono);
		$this->setEstatus('A');

		$success = $this->save();

		return $success;
	}
	//-----------------------------------------------------------------------------------------
	public function actualizar($vId, $vUniverdad, $vCiudad, $vDireccion, $vEstado, $vNombre, $vTelefono) {
		$success = false;
		$decanato = $this->findFirst("id = ".$vId);
		if ($decanato != null){
			$decanato->setUniversidadId($vUniverdad);
			$decanato->setCiudadId($vCiudad);
			$decanato->setDireccion($vDireccion);
			$decanato->setEstadoId($vEstado);
			//$this->setLogo($valor);
			$decanato->setNombre($vNombre);
			$decanato->setTelefono($vTelefono);
			$success = $decanato->update();

		}

		return $success;
	}

	//-----------------------------------------------------------------------------------------
	public function eliminar($vId) {
		$success = false;
		$decanato = $this->findFirst("id = ".$vId." AND estatus = 'A'");
		if ($decanato != null){
			$decanato->setEstatus('E');
			$success = $decanato->update();
		}
		return $success;
	}
	//-----------------------------------------------------------------------------------------
	public function buscar($id){
		$auxDecanatos = array();
		$i = 0;
		$decanatos = $this->find("id = ".$id." AND estatus='A'");
		$auxDecanatos['success'] = false;
		$resp['datos'] = array();
		foreach($decanatos as $decanato){
			$auxDecanatos['datos']['id'] = $id;
			$auxDecanatos['datos']['universidad_id'] = $decanato->getUniverdidadId();
			$auxDecanatos['datos']['nombre'] = utf8_encode($decanato->getNombre());
			$auxDecanatos['datos']['direccion'] = utf8_encode($decanato->getDireccion());
			$auxDecanatos['datos']['ciudad_id'] = $decanato->getCiudadId();
			$auxDecanatos['datos']['estado_id'] = $decanato->getEstadoId();
			$auxDecanatos['datos']['telefono'] = $decanato->getTelefono();
			$auxDecanatos['datos']['logo'] = $decanato->getLogo();
			$auxDecanatos['datos']['estatus'] = $decanato->getEstatus();
			$auxDecanatos['success'] = true;

			$i++;
		}

		return $auxDecanatos;
	}
	//-----------------------------------------------------------------------------------------
}
?>
