<?php
class Perfil extends ActiveRecord {
	protected $id;
	protected $pasante_id;
	protected $descripcion;
	protected $experiencia;
	protected $cursos;
	protected $estatus;

	public function getId() {
		return $this->id; 
	}
	public function setId($valor) {
		$this->id = $valor; 
	}
	public function getPasanteId() { 
		return $this->pasante_id; 
	}
	public function setPasanteId($valor) {
		$this->pasante_id = $valor; 
	}
	public function getDescripcion() { 
		return $this->descripcion; 
	}
	public function setDescripcion($valor) {
		$this->descripcion = $valor; 
	}
	public function getExperiencia() { 
		return $this->experiencia; 
	}
	public function setExperiencia($valor) {
		$this->experiencia = $valor; 
	}
	public function getCursos() { 
		return $this->cursos; 
	}
	public function setCursos($valor) {
		$this->cursos = $valor; 
	}
	public function getEstatus() {
		return $this->estatus; 
	}
	public function setEstatus($valor) {
		$this->estatus = $valor; 
	}
//-----------------------------------------------------------------------------------------	
	public function registrarPerfil($vPasanteId,$vDescripcion,$vExperiencia,$vCursos){
			
			$flag = false;
			$perfil = new Perfil();
			$perfil->setPasanteId($vPasanteId);
			$perfil->setDescripcion($vDescripcion);
			$perfil->setExperiencia($vExperiencia);
			$perfil->setCursos($vCursos);
			$perfil->setEstatus("A");
			$flag = $perfil->save();
			return $flag;
		}
//-----------------------------------------------------------------------------------------	
		public function actualizarPerfil($vPasanteId,$vDescripcion,$vExperiencia,$vCursos){
		$success=false;
		$perfil = $this->findFirst("pasante_id ='$vPasanteId'");
		if ($perfil){
			$perfil->setDescripcion($vDescripcion);
			$perfil->setExperiencia($vExperiencia);
			$perfil->setCursos($vCursos);
			$perfil->setEstatus("A");
			$success = $perfil->update();
		}
		return $success;
	}		
//-----------------------------------------------------------------------------------------
public function buscarPerfil($pId){		
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$resp['datos']=array();
		$errorMsj ='';
		
		$perfilP = $this->findFirst("pasante_id = '$pId'");
		if ($perfilP){
			$errorMsj ='Perfil encontrado.';
			$resp['datos']['descripcion']=utf8_encode($perfilP->getDescripcion());
			$resp['datos']['experiencia']=utf8_encode($perfilP->getExperiencia());
			$resp['datos']['cursos']=utf8_encode($perfilP->getCursos());
		}
		$resp['errorMsj']= $errorMsj;
		$resp['success']= true;
		return ($resp);
	}
//-----------------------------------------------------------------------------------------	
}

?>