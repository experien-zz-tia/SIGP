<?php
/**
 * Clase Usuario para el mapeo con el ORM de Kumbia.
 * @author Robert Arrieche
 *
 */
class Usuario extends ActiveRecord {
	protected $id;
	protected $nombreUsuario;
	protected $idUsuario;
	protected $clave;
	protected $fchCreacion_at;
	protected $estatus;
	protected $categoriaUsuario_id;

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getNombreUsuario(){
		return $this->nombreUsuario;
	}

	public function setNombreUsuario($nombreUsuario){
		$this->nombreUsuario = mysql_real_escape_string($nombreUsuario);
	}

	public function getIdUsuario() {
		return $this->idUsuario;
	}

	public function getClave() {
		return $this->clave;
	}
	public function getfchCreacion_at() {
		return $this->fchCreacion_at;
	}
	public function getEstatus() {
		return $this->estatus;
	}
	public function getCategoriaUsuario_id() {
		return $this->categoriaUsuario_id;
	}
	public function setIdUsuario($x) {
		$this->idUsuario = $x;
	}
	public function setClave($x) {
		$this->clave = mysql_real_escape_string($x);
	}
	public function setfchCreacion_at($x) {
		$this->fchCreacion_at = $x;
	}
	public function setEstatus($x) {
		$this->estatus = $x;
	}
	public function setCategoriaUsuario_id($x) {
		$this->categoriaUsuario_id = $x;
	}

	/**
	 * Busca si existe el nombre de usuario especificado. retorna 0 cuando no lo encuentra
	 *  y 1 en caso contrario.
	 * @param string $username
	 * @return number
	 */
	public function existebyUsername($username){
		$resp=0;// no encontrado
		$encontrado = $this->find("nombreUsuario ='$username'");
		if (count($encontrado)!=0)
		$resp=1;
		return $resp;
	}
	/**
	 * Registra un usuario
	 * @param  $nombreUsuario
	 * @param  $clave
	 * @param  $categoria
	 * @param  $idUsuario
	 * @param  $estatusUsuario
	 * @return boolean
	 */
	public function registrarUsuario($nombreUsuario,$clave,$categoria,$idUsuario,$estatusUsuario){
		$flag=false;
		$usuario = new Usuario();
		$usuario->setNombreUsuario($nombreUsuario);
		$usuario->setClave($clave);
		$usuario->setCategoriaUsuario_id($categoria);
		$usuario->setIdUsuario($idUsuario);
		$usuario->setEstatus($estatusUsuario);
		$flag= $usuario->save();
		return $flag;

	}

	/**
	 * Activa el usuario asociado con los parametros pasados y asigna un nuevo usuario y clave. Retorna  el id del usuario modificado y su categoria
	 * @param string $usuarioAnterior
	 * @param string $claveAnterior
	 * @param string $usuario
	 * @param string $clave en md5
	 * @return multitype:number boolean
	 */
	public function activarUsuario($usuarioAnterior, $claveAnterior,$usuario,$clave){
		$flag=false;
		$idTutor=0;
		$idCategoria=0;
		$aux=$this->findFirst("nombreUsuario='$usuarioAnterior' AND clave='$claveAnterior'");
		if ($aux){
			$aux->setNombreUsuario($usuario);
			$aux->setClave($clave);
			$aux->setEstatus('A');
			$flag=$aux->update();
			$idTutor= $aux->getIdUsuario();
			$idCategoria=  $aux->getCategoriaUsuario_id();
		}
		return array("success"=>$flag,
					"idUsuario"=>$idTutor,
					"idCategoria"=>$idCategoria);

	}


	/**
	 * Elimina de manera logica el registro de usuario de un tipo de usuario dado
	 * @param int $idUsuario
	 * @param int $idCategoria
	 * @return boolean
	 */
	public function eliminar($idUsuario,$idCategoria){
		$success=false;
		$aux=$this->findFirst("idUsuario='$idUsuario' AND categoriaUsuario_id='$idCategoria'");
		if ($aux){
			$aux->setEstatus('E');
			$success= $aux->update();
		}
		return $success;
	}

	/**
	 * Activa un usuario para operar en el sistema
	 * @param int $idUsuario
	 * @param int $idCategoria
	 * @return boolean
	 */
	public function activar($idUsuario,$idCategoria){
		$success=false;
		$aux=$this->findFirst("idUsuario='$idUsuario' AND categoriaUsuario_id='$idCategoria'");
		if ($aux){
			$aux->setEstatus('A');
			$success= $aux->update();
		}
		return $success;
	}

	public function validarCredenciales($user,$pass){
		$success=false;
		$aux=$this->findFirst("nombreUsuario='$user' AND clave='$pass' AND estatus='A'");
		if ($aux){
			$success= true;
		}
		return $success;
	}

	public function  coincideClave($id,$claveActual){
		$success=false;
		$aux=$this->findFirst("id='$id' ");
		if ($aux){
			if ($aux->getClave()==$claveActual)
			$success= true;
		}
		return $success;

	}
	public function  actualizarClave($id,$clave){
		$success=false;
		$aux=$this->findFirst("id='$id' ");
		if ($aux){
			$aux->setClave($clave);
			$success= $aux->update();
		}
		return $success;

	}

	public function consultarUsuarios($start='*',$limit='*') {
		$aux = array();
		$i=0;
		$total=0;
		$total= $this->count();

		$sql  = " SELECT u.id AS id, u.nombreUsuario AS nombreUsuario, c.descripcion AS categoria, u.estatus AS estatus ";
		$sql  .= " FROM usuario u, categoriausuario c";
		$sql  .= " WHERE c.id= u.categoriausuario_Id";
		$sql .= " ORDER BY c.id ";
		if ($start!='*' && $limit!='*'){
			$sql .= " LIMIT ".$start.",".$limit." ";
		}
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$i]['usuarioId'] = $row['id'];
			$aux[$i]['nombreUsuario'] = utf8_encode($this->adecuarTexto($row['nombreUsuario']));
			$aux[$i]['categoria'] = utf8_encode($this->adecuarTexto($row['categoria']));
			$aux[$i]['estatus'] = utf8_encode(($row['estatus']));
			$i++;
		}
		return array('total'=>$total,
					'resultado' => $aux);
	}

	public function cambiarClave($id){
		$claveAsignada="";
		$aux=$this->findFirst("id='$id' ");
		if ($aux){
			$claveAsignada= $this->generarClave(6);
			$aux->setClave(md5($claveAsignada));
			$success= $aux->update();
		}
		return $claveAsignada;
	}



	function generarClave($longitud){
		$cadena="[^A-Z0-9]";
		return substr(eregi_replace($cadena, "", md5(rand())) .
		eregi_replace($cadena, "", md5(rand())) .
		eregi_replace($cadena, "", md5(rand())),
		0, $longitud);
	}


	public function eliminarUser($id){
		$success=false;
		$aux=$this->findFirst("id='$id' ");
		if ($aux){
			$aux->setEstatus('E');
			$success= $aux->update();
		}
		return $success;
	}
	
	public function reactivarUser($id){
		$success=false;
		$aux=$this->findFirst("id='$id' ");
		if ($aux){
			if ($aux->getEstatus()=='E'){
				$aux->setEstatus('A');
				$success= $aux->update();	
			}
		}
		return $success;
	}

}

?>