<?php
class Universidad extends ActiveRecord{
	protected $id;
	protected $nombre;
	protected $direccion;
	protected $telefono;
	protected $estado_id;
	protected $ciudad_id;
	protected $logo;
	protected $estatus;
	//-----------------------------------------------------------------------------------------
	protected function inicializate(){
		$this->hasMany('universidad');
	}
	//-----------------------------------------------------------------------------------------
	public function setId($valor){
		$this->id = $valor;
	}
	public function getId(){
		return $this->id;
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
	public function getUniversidades(){
		$auxUniversidades = array();
		$i = 0;
		$Universidades = $this->find("estatus = 'A'","order: nombre");
		foreach($Universidades as $universidad){
			$auxUniversidades[$i]['id'] = $universidad->getId();
			$auxUniversidades[$i]['nombre'] = utf8_encode($universidad->getNombre());
			$auxUniversidades[$i]['direccion'] = utf8_encode($universidad->getDireccion());
			$auxUniversidades[$i]['telefono'] = utf8_encode($universidad->getTelefono());
			$auxUniversidades[$i]['estado_id'] = utf8_encode($universidad->getEstadoId());
			$auxUniversidades[$i]['ciudad_id'] = utf8_encode($universidad->getCiudadId());
			//$auxUniversidades[$i]['logo'] = utf8_encode($universidad->getLogo());
			$auxUniversidades[$i]['estatus'] = utf8_encode($universidad->getEstatus());
			$i++;
		}

		return $auxUniversidades;
	}
	//-----------------------------------------------------------------------------------------
	public function getUniversidadFull(){
		$auxUniversidades = array();
		$i = 0;
		$Universidades = $this->find("estatus = 'A'","order: nombre");
		foreach($Universidades as $universidad){
			$auxUniversidades[$i]['id'] = $universidad->getId();
			$auxUniversidades[$i]['nombre'] = utf8_encode($universidad->getNombre());
			$auxUniversidades[$i]['direccion'] = utf8_encode($universidad->getDireccion());
			$auxUniversidades[$i]['telefono'] = $universidad->getTelefono();
			$auxUniversidades[$i]['ciudad_id'] = $universidad->getCiudadId();
			$ciudad = new Ciudad();
			$ciu = $ciudad->findFirst("id = ".$universidad->getCiudadId());
			if ($ciu != null){
				$auxUniversidades[$i]['ciudad'] = utf8_encode($ciu->getNombre());
			}

			$auxUniversidades[$i]['estado_id'] = $universidad->getEstadoId();
			$estado = new Estado();
			$est = $estado->findFirst("id = ".$universidad->getEstadoId());
			if ($est != null){
				$auxUniversidades[$i]['estado'] = utf8_encode($est->getNombre());
			}
			//$auxUniversidades[$i]['logo'] = utf8_encode($universidad->getLogo());
			$auxUniversidades[$i]['estatus'] = utf8_encode($universidad->getEstatus());
			$i++;
		}

		return $auxUniversidades;
	}
	//-----------------------------------------------------------------------------------------
	public function registrar($vCiudad, $vDireccion, $vEstado, $vNombre, $vTelefono) {
		$success = false;
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
	public function actualizar($vId, $vCiudad, $vDireccion, $vEstado, $vNombre, $vTelefono) {
		$success = false;
		$universidad = $this->findFirst("id = ".$vId);
		if ($universidad != null){
			$universidad->setCiudadId($vCiudad);
			$universidad->setDireccion($vDireccion);
			$universidad->setEstadoId($vEstado);
			//$this->setLogo($valor);
			$universidad->setNombre($vNombre);
			$universidad->setTelefono($vTelefono);
			$success = $universidad->update();
		}
		return $success;
	}

	//-----------------------------------------------------------------------------------------
	public function buscar($id){
		$auxuniversidad = array();
		$i = 0;
		$universidad = $this->find("id = ".$id." AND estatus = 'A'");
		$auxuniversidad['success'] = false;
		$resp['datos'] = array();
		foreach($universidad as $univer){
			$auxuniversidad['datos']['id'] = $id;
			$auxuniversidad['datos']['nombre'] = utf8_encode($univer->getNombre());
			$auxuniversidad['datos']['direccion'] = utf8_encode($univer->getDireccion());
			$auxuniversidad['datos']['ciudad_id'] = $univer->getCiudadId();
			$auxuniversidad['datos']['estado_id'] = $univer->getEstadoId();
			$auxuniversidad['datos']['telefono'] = $univer->getTelefono();
			$auxuniversidad['datos']['logo'] = $univer->getLogo();
			$auxuniversidad['datos']['estatus'] = $univer->getEstatus();
			$auxuniversidad['success'] = true;

			$i++;
		}

		return $auxuniversidad;
	}
	//-----------------------------------------------------------------------------------------
	public function contar(){
		$auxuniversidad = array();
		$auxuniversidad['success'] = true;
		$cont = $this->count("estatus = 'A'");
		if ($cont != null){
			$auxuniversidad['success'] = false;
		}
		return $auxuniversidad;
	}
	//----------------------------------------------------------------------------------------
}

?>