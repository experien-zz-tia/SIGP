<?php
class Empresa extends ActiveRecord {
	protected $id;
	protected $rif;
	protected $razonSocial;
	protected $direccion;
	protected $estado_id;
	protected $ciudad_id;
	protected $telefono;
	protected $telefono2;
	protected $email;
	protected $descripcion;
	protected $logo;
	protected $web;
	protected $contacto;
	protected $cargo;
	protected $fchRegistro_at;
	protected $estatus;

	public function getId() {
		return $this->id;
	}
	public function getRif() {
		return $this->rif;
	}
	public function getRazonSocial() {
		return $this->razonSocial;
	}
	public function getDireccion() {
		return $this->direccion;
	}
	public function getEstado_id() {
		return $this->estado_id;
	}
	public function getCiudad_id() {
		return $this->ciudad_id;
	}
	public function getTelefono() {
		return $this->telefono;
	}
	public function getTelefono2() {
		return $this->telefono2;
	}
	public function getEmail() {
		return $this->email;
	}
	public function getDescripcion() {
		return $this->descripcion;
	}
	public function getLogo() {
		return $this->logo;
	}
	public function getWeb() {
		return $this->web;
	}
	public function getContacto() {
		return $this->contacto;
	}
	public function getCargo() {
		return $this->cargo;
	}
	public function getEstatus() {
		return $this->estatus; 
	}

	public function getFchRegistro_at() {
		return $this->$fchRegistro_at; 
}

	public function setFchRegistro_at($x) {
			$this->$fchRegistro_at = $x;
	 }
		
	public function setEstatus($x) {
			$this->estatus = $x;
	 }
	 public function setCargo($x) {
	 	$this->cargo = $x;
	 }
	 public function setContacto($x) {
	 	$this->contacto = $x;
	 }
	 public function setId($x) {
	 	$this->id = $x;
	 }
	 public function setRif($x) {
	 	$this->rif = $x;
	 }
	 public function setRazonSocial($x) {
	 	$this->razonSocial = $x;
	 }
	 public function setDireccion($x) {
	 	$this->direccion = $x;
	 }
	 public function setEstado_id($x) {
	 	$this->estado_id = $x;
	 }
	 public function setCiudad_id($x) {
	 	$this->ciudad_id = $x;
	 }
	 public function setTelefono($x) {
	 	$this->telefono = $x;
	 }
	 public function setTelefono2($x) {
	 	$this->telefono2 = $x;
	 }
	 public function setEmail($x) {
	 	$this->email = $x;
	 }
	 public function setDescripcion($x) {
	 	$this->descripcion = $x;
	 }
	 public function setLogo($x) {
	 	$this->logo = $x;
	 }
	 public function setWeb($x) {
	 	$this->web = $x;
	 }

	 /**
	  * Registra la empresa segun los datos suministrados.
	  * @param string $rif
	  * @param string $razonSocial
	  * @param string $direccion
	  * @param int $estado
	  * @param int $ciudad
	  * @param string $telefono
	  * @param string $telefono2
	  * @param string $descripcion
	  * @param string $web
	  * @param string $representante
	  * @param string $correo
	  * @param string $cargo
	  * @return boolean
	  */
	 public function registrarEmpresa($rif,$razonSocial,$direccion,$estado,$ciudad,$telefono,$telefono2,$descripcion,$web,$representante,$correo,$cargo,$estatusEmpresa){
	 	$flag=false;
	 	$empresa = new Empresa();
	 	$empresa->setRif($rif);
	 	$empresa->setRazonSocial($razonSocial);
	 	$empresa->setDireccion($direccion);
	 	$empresa->setEstado_id($estado);
	 	$empresa->setCiudad_id($ciudad);
	 	$empresa->setTelefono($telefono);
	 	$empresa->setTelefono2($telefono2);
	 	$empresa->setDescripcion($descripcion);
	 	$empresa->setWeb(utf8_decode($web));
	 	$empresa->setContacto($representante);
	 	$empresa->setEmail($correo);
	 	$empresa->setCargo($cargo);
	 	$empresa->setEstatus($estatusEmpresa);
	 	$flag= $empresa->save();
	 	if ($flag)
			return $empresa->getId();
		else
			return $flag;

	 }
 	public function actualizarEmpresa($id,$razonSocial,$direccion,$estado,$ciudad,$telefono,$telefono2,$descripcion,$web,$representante,$cargo,$correo){
	 	$success=false;
	 	
	 	$empresa = $this->findFirst("id='$id'");
		if ($empresa){
		 	$empresa->setRazonSocial($razonSocial);
		 	$empresa->setDireccion($direccion);
		 	$empresa->setEstado_id($estado);
		 	$empresa->setCiudad_id($ciudad);
		 	$empresa->setTelefono($telefono);
		 	$empresa->setTelefono2($telefono2);
		 	$empresa->setDescripcion($descripcion);
		 	$empresa->setWeb(utf8_decode($web));
		 	$empresa->setContacto($representante);
		 	$empresa->setCargo($cargo);
		 	if ($correo != ''){
		 		$empresa->setEmail($correo);
		 	}
		 	$success= $empresa->update();
		}
		return $success;

	 }

	 /**
	  * Genera lista de las empresas activas.
	  * @return array <multitype:, string>
	  */
	 public function getEmpresas(){
	 	$aux = array();
	 	$i=0;
	 	$empresas= $this->find("estatus='A'","order: razonSocial");
	 	foreach($empresas as $empresa){
	 		$aux[$i]['id'] = $empresa->getId();
	 		$aux[$i]['descripcion'] = utf8_encode($this->adecuarTexto($empresa->getRazonSocial()));
	 		$i++;

	 	}
	 	return $aux;
	 }

	 /**
	  * Genera lista de las empresas segun el estatus pasado.
	  * @param string $estatus
	  * @param string $start
	  * @param string $limit
	  * @return array <multitype:, string>
	  */
	 public function getEmpresasbyEstatus($estatus,$start, $limit){
	 	$aux = array();
	 	$i=0;
	 	if ($estatus=='T'){
	 		$estatus=" ('A','S') ";
	 	}else if ($estatus=='SV'){
	 		$estatus=" ('I') ";
	 	}else{
	 		$estatus= " ('A','S','R','I') ";
	 	}
		$total = $this->count("estatus IN ".$estatus);
	 	$sql = "SELECT id, rif, razonSocial, telefono, contacto, email, estatus ";
	 	$sql .= "FROM Empresa " ;
	 	$sql .= "WHERE estatus IN ".$estatus ;
	 	$sql .= "ORDER BY razonSocial ";
	 	$sql .= " LIMIT ".$start.",".$limit." ";
	 	$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$i]['id'] = $row['id'];
	 		$aux[$i]['rif'] = utf8_encode($row['rif']);
 			$aux[$i]['razonSocial'] = utf8_encode($this->adecuarTexto($row['razonSocial']));
 			$aux[$i]['telefono'] = utf8_encode($row['telefono']);
 			$aux[$i]['contacto'] = utf8_encode($row['contacto']);
 			$aux[$i]['email'] = utf8_encode($row['email']);
 			$aux[$i]['estatus'] = utf8_encode($this->retornarNombre($row['estatus']));
 			$i++;
		}

	 	return array('total'=>$total,
					'resultado' => $aux);
	 }
	 
	 
	 /**
	  * Retorna el nombre completo del estatus
	  * @param string $inicial
	  * @return string
	  */
	 private  function retornarNombre($inicial) {
	 	$nombre='';
	 	switch ($inicial) {
	 		case 'A':
	 			$nombre='Activa';
			 	break;
	 		case 'E':
	 			$nombre='Eliminada';
			 	break;
	 		case 'S':
	 			$nombre='Suspendida';
			 	break;
	 		case 'I':
	 			$nombre='Inactiva';
			 	break;
	 		case 'R':
	 			$nombre='Registrada';
			 	break;
	 	}
	 	return $nombre;
	 }
	 
	 /**
	  * Elimina de manera logica a la empresa y el usuario asociado a ella.
	  * @param int $id
	  * @return boolean
	  */
	 public function eliminar($id) {
		$success=false;
		$successUser =  false;
		$empresa = $this->findFirst("id='$id'");
		if ($empresa){
			$empresa->setEstatus('E');
			$usuario = new Usuario();
			$successUser =  $usuario->eliminar($id, CAT_USUARIO_EMPRESA);
			$success = $empresa->update();
		}
		return (($success AND $successUser)?true:false);
	 	
	 }
	 
	 
/**
	  * Activa la empresa y el usuario asociado a ella.
	  * @param int $id
	  * @return boolean
	  */
	 public function activar($id) {
		$success=false;
		$successUser =  false;
		$empresa = $this->findFirst("id='$id'");
		if ($empresa){
			$empresa->setEstatus('A');
			$usuario = new Usuario();
			$successUser =  $usuario->activar($id, CAT_USUARIO_EMPRESA);
			$success = $empresa->update();
		}
		return (($success AND $successUser)?true:false);
	 	
	 }
	 /**
	  * Obtiene todos los datos de la empresa a traves de su id
	  * @param int $id
	  * @return  <multitype:, string>
	  */
	 public function getEmpresa($id) {
	 	$aux = array();
	 	$empresa= $this->findFirst("id='$id'");
	 	if ($empresa){
	 		$aux['id'] = $empresa->getId();
	 		$aux['razonSocial'] = utf8_encode($this->adecuarTexto($empresa->getRazonSocial()));
	 		$aux['rif'] = utf8_encode($empresa->getRif());
	 		$aux['direccion'] = utf8_encode($this->adecuarTexto($empresa->getDireccion()));
	 		$aux['descripcion'] = utf8_encode($this->adecuarTexto($empresa->getDescripcion()));
	 		$aux['telefono'] = $empresa->getTelefono();
	 		$aux['telefono2'] = $empresa->getTelefono2();
	 		$aux['web'] = utf8_encode($empresa->getWeb());
	 		$aux['estadoId'] = utf8_encode($empresa->getEstado_id());
	 		$aux['ciudadId'] = utf8_encode($empresa->getCiudad_id());
	 		$aux['representante'] = utf8_encode($this->adecuarTexto($empresa->getContacto()));
	 		$aux['cargo'] = utf8_encode($this->adecuarTexto($empresa->getCargo()));
	 		$aux['correo'] = utf8_encode($empresa->getEmail());

	 	}
	 	return $aux;	
	  }
	  
	  
	public function getRazonSocialbyId($id) {
		$aux='';
		$empresa = $this->findFirst("id='$id'");
		if ($empresa){
			$aux= $this->adecuarTexto($empresa->getRazonSocial());
		}
		return $aux;
	}
	
	
	
	public function contarRegistradosEnLapso($idLapso) {
		$cantidad=0;
		$sql = " SELECT COUNT(*) AS cantidad FROM lapsoacademico l, empresa e ";
		$sql .= " WHERE e.fchRegistro_at BETWEEN l.fchInicio AND l.fchFin ";
		$sql .= " AND l.id='$idLapso' ";
	 	$db = Db::rawConnect();
	 	$result = $db->query($sql);
	 	if ($row = $db->fetchArray($result)){
			$cantidad = $row['cantidad'];
	 	}
	 	return $cantidad;
	}
	
	
 public function getEmpresasReporte($ciudad){
	 	$aux = array();
	 	$i=0;
	 	
	 	$sql = "SELECT  rif, razonSocial, telefono, contacto, email  ";
	 	$sql .= "FROM Empresa " ;
	 	$sql .= "WHERE estatus ='A' ";
	 	if ($ciudad!=''){
	 		$sql .= "AND ciudad_id=".$ciudad." ";
	 	}
	 	$sql .= "ORDER BY razonSocial ";
	 
	 	$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
	 		$aux[$i][0] = utf8_encode($row['rif']);
 			$aux[$i][1] = utf8_encode($this->adecuarTexto($row['razonSocial']));
 			$aux[$i][2] = utf8_encode($this->adecuarTexto($row['contacto']));
 			$aux[$i][3] = utf8_encode($row['telefono']);
 			$aux[$i][4] = utf8_encode($row['email']);
 			$i++;
		}
	 	return  $aux;
	 }
}
?>