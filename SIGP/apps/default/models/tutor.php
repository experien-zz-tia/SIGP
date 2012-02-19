<?php
class Tutor extends ActiveRecord {

	protected $id;
	protected $cedula;
	protected $nombre;
	protected $apellido;
	protected $telefono;
	protected $email;
	protected $cargo;
	protected $fchRegistro_at;
	protected $estatus;


	public function getId() { return $this->id; }
	public function getCedula() { return $this->cedula; }
	public function getNombre() { return $this->nombre; }
	public function getApellido() { return $this->apellido; }
	public function getTelefono() { return $this->telefono; }
	public function getEmail() { return $this->email; }
	public function getCargo() { return $this->cargo; }
	public function getFchRegistro_at() { return $this->fchRegistro_at; }
	public function getEstatus() { return $this->estatus; }
	public function setId($x) { $this->id = $x; }
	public function setCedula($x) { $this->cedula = $x; }
	public function setNombre($x) { $this->nombre = $x; }
	public function setApellido($x) { $this->apellido = $x; }
	public function setTelefono($x) { $this->telefono = $x; }
	public function setEmail($x) { $this->email = $x; }
	public function setCargo($x) { $this->cargo = $x; }
	public function setFchRegistro_at($x) { $this->fchRegistro_at = $x; }
	public function setEstatus($x) { $this->estatus = $x; }
	
/**
	 * Elimina de manera lógica el tutor indicado.
	 * @param int $idTutor
	 * @return boolean
	 */
	protected function eliminarTutor($idTutor){
		$flag=false;
		$tutor = $this->findFirst("id='$idTutor'");
		if ($tutor){
			$tutor->setEstatus('E');
			$flag = $tutor->update();
		}
		
		return $flag;
		
	}
	
}
?>