<?php
class Coordinacion extends ActiveRecord{

	protected $id;
	protected $descripcion;
	protected $ubicacion;
	protected $telefono;
	protected $email;
	protected $decanato_id;
	protected $empleado_id;
	protected $estatus;

	protected function initialize(){
		$this->belongsTo("empleado_id","empleado","id");
		
	}
	public function getId() { 
		return $this->id; 
	}
	public function getDescripcion() { 
		return $this->descripcion; 
	}
	public function getUbicacion() { 
		return $this->ubicacion; 
	}
	public function getTelefono() { 
		return $this->telefono; 
	}
	public function getEmail() {
		return $this->email; 
	}
	public function getDecanato_id() { 
		return $this->decanato_id; 
	}
	public function getEmpleado_id() { 
		return $this->empleado_id; 
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
	public function setUbicacion($x) { 
		$this->ubicacion = $x; 
	}
	public function setTelefono($x) { 
		$this->telefono = $x; 
	}
	public function setEmail($x) {
		$this->email = $x; 
	}
	public function setDecanato_id($x) { 
		$this->decanato_id = $x; 
	}
	public function setEmpleado_id($x) { 
		$this->empleado_id = $x; 
	}
	public function setEstatus($x) {
		$this->estatus = $x; }
		
	public function getDatosCoordinador($idDecanato) {
		$aux= array();
		$coordinacion=$this->findFirst("decanato_id='$idDecanato'");
		if ($coordinacion){
			$coordinador= $coordinacion->getEmpleado();
			if ($coordinador){
				$aux['id']=$coordinador->getId();
				$aux['nombre']=utf8_decode($coordinador->getNombre());
				$aux['apellido']=utf8_decode($coordinador->getApellido());
				$aux['cedula']=$coordinador->getCedula();
			}
		}
		return $aux;
	}
	
	public function asignarCoordinador($decanatoId,$idEmpleado) {
		$flag = false; 
		$coord = $this->findFirst("decanato_id='$decanatoId'");
		if ($coord){
			$coord->setEmpleado_id($idEmpleado);
			$flag=$coord->update();
		}
		return $flag;
	}
}

?>