<?php
class Registro extends ActiveRecord {
	protected $id;
	protected $hash;
	protected $usuario;
	protected $email;
	protected $fchRegistro_at;
	protected $estatus;

	public function getId() {
		return $this->id; 
	}
	public function getHash() { 
		return $this->hash; 
	}
	public function getUsuario() { 
		return $this->usuario; 
	}
	public function getEmail() { 
		return $this->email; 
	}
	public function getFchRegistro_at() { 
		return $this->fchRegistro_at; 
	}
	public function getEstatus() {
		return $this->estatus; 
	}
	public function setId($x) {
		$this->id = $x; 
	}
	public function setHash($x) {
		$this->hash = $x; 
	}
	public function setUsuario($x) {
		$this->usuario = $x; 
	}
	public function setEmail($x) {
		$this->email = $x; 
	}
	public function setFchRegistro_at($x) { 
		$this->fchRegistro_at = $x; 
	}
	public function setEstatus($x) {
		$this->estatus = $x; 
	}
	
	/**
	 * Guarda informacion del registro de un usuario
	 * @param  string $hash
	 * @param  string $registroUsuario
	 * @param  string $registroEmail
	 * @param  char $registroEstatus
	 * @return boolean
	 */
	public function guardarRegistro($hash,$registroUsuario,$registroEmail,$registroEstatus){
		$flag=false;
		$registro = new Registro();
		$registro->setHash($hash);
		$registro->setUsuario($registroUsuario);
		$registro->setEmail($registroEmail);
		$registro->setEstatus($registroEstatus);
		$flag= $registro->save();
		return $flag; 
		
	}
	
	/**
	 *  Activa el registro de un usuario. A:Activo
	 * @param string $hash
	 * @param string $userName
	 * @return boolean
	 */
	public function activarRegistro($hash,$userName){
		$flag=false;
		$registroAux = new Registro();
		$registro = $registroAux->findFirst("hash='$hash'");
		if ($registro){
			$registro->setEstatus('A');
			$registro->setUsuario($userName);
			$flag=$registro->update();
		}
		return $flag;
	}

	/**
	 * Obtiene el usuario asociado un registro a traves del hash generado al momento de crear el registro.
	 * Si no lo encuentra retornará una cadena vacia.
	 * @param string $hash
	 * @return string
	 */
	public function getUsuariobyHash($hash){
		$usuario='';
		$registro = $this->findFirst("hash='$hash'");
		if ($registro){
			$usuario=$registro->getUsuario();
		}
		return $usuario;
		
	}
	
	/**
	 * Obtiene la cuenta de correo electronica  asociado un registro a traves del hash generado al momento de crear el registro.
	 * Si no lo encuentra retornará una cadena vacia.
	 * @param string $hash
	 * @return string
	 */
	public function getEmailbyHash($hash){
		$correo='';
		$registro = $this->findFirst("hash='$hash'");
		if ($registro){
			$correo=$registro->getEmail();
		}
		return $correo;
	}
}

?>