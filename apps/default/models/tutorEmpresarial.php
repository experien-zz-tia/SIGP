<?php
/**
 * @author Robert Arrieche
 * @version 1.0
 */
class TutorEmpresarial extends Tutor {

	protected $empresa_id ;

	protected function initialize(){
		//$this->belongsTo("tutorEmpresarial_id","pasantia","id");
		//$this->belongsTo("areaPasantia_id","areapasantia","id");
	}

	public function getEmpresa_id() {
		return $this->empresa_id;
	}

	public function setEmpresa_id($x) {
		$this->empresa_id = $x;
	}

	/**
	 * Función para obtener los tutores empresariales ( no eliminados) que posee una empresa dada.
	 * @param string $idEmpresa por defecto el comodin %
	 * @param string $start inicio del resultset a listar, por defecto '-1' => 0
	 * @param string $limit máximo del resulset a lista, por defecto sin limite( o definido por mysql)
	 * @return array asociativo con total de la consulta y resultado parcial (depende del start y limit)
	 */
	public function getTutoresEmpresariales($idEmpresa='%',$start='-1',$limit=-'1'){
		$aux = array();
		$i=0;

		$sqlTotal  = "SELECT  COUNT(te.id) as total ";
		$sqlTotal .= "FROM  tutorEmpresarial te ";
		$sqlTotal .= "WHERE te.estatus != 'E' ";
		$sqlTotal .= "AND empresa_id LIKE '".$idEmpresa."'";
		$db = Db::rawConnect();
		$total = $db->fetchOne($sqlTotal);
		$total = $total[0];
		if ($total!=0){
			$sql  = " SELECT id, cedula, nombre, apellido, cargo, estatus ";
			$sql .= " FROM  tutorEmpresarial te ";
			$sql .= " WHERE te.estatus != 'E' AND  empresa_id LIKE '".$idEmpresa."'";
			$sql .= " ORDER BY cedula ";
			if ($start!='-1' && $limit!='-1'){
				$sql .= " LIMIT ".$start.",".$limit." ";
			}
			$db = Db::rawConnect();
			$result = $db->query($sql);
			while($row = $db->fetchArray($result)){
				$aux[$i]['id'] = $row['id'];
				$aux[$i]['cedula'] = $row['cedula'];
				$aux[$i]['nombre'] = utf8_encode($this->adecuarTexto($row['nombre']));
				$aux[$i]['apellido'] = utf8_encode($this->adecuarTexto($row['apellido']));
				$aux[$i]['cargo'] = utf8_encode($this->adecuarTexto($row['cargo']));
				$aux[$i]['estatus'] = ($row['estatus']=='A')?'Activo':'Inactivo';
				$i++;
			}
		}

		return array('total'=>$total,
					'resultado' => $aux);
	}


	/* (non-PHPdoc)
	 * @see Tutor::eliminarTutor()
	 */
	public function eliminarTutor($id){
		$success=false;
		$oferta = $this->findFirst("id='$id'");
		if ($oferta){
			$oferta->setEstatus('E');
			$success = $oferta->update();
		}
		return $success;

	}

	/**
	 * Elimina los tutores empresariales y sus usuarios asociados de una empresa dada.
	 * @param int $idEmpresa
	 * @return boolean
	 */
	public function eliminarTutores($idEmpresa){
		$success=true;
		$tutores= $this->find("empresa_id='$idEmpresa'");
		foreach($tutores as $tutor){
			$tutor->setEstatus('E');
			$idUsuario = $tutor->getId();
			$idCategoria = CAT_USUARIO_TUTOR_EMP;
			$success = ($success AND $tutor->update());
			$usuario = new Usuario();
			$success=($success AND $usuario->eliminar($idUsuario, $idCategoria))?true:false;
		}
		return $success;
	}


	/**
	 * Registra o actualiza un tutor academico de una empresa dada.
	 * @param int $idEmpresa
	 * @param string $cedula
	 * @param string $nombre
	 * @param string $apellido
	 * @param string $telefono
	 * @param string $correo
	 * @param string $cargo
	 * @return boolean
	 */
	function guardarTutorE($idEmpresa,$cedula,$nombre,$apellido,$telefono,$correo,$cargo){
		$success=false;
		$enviarCorreo=false;
		$id=0;
		$tutorE = $this->findFirst("empresa_id='$idEmpresa' AND cedula='$cedula'");
		if ($tutorE){
			$tutorE->setNombre($nombre);
			$tutorE->setApellido($apellido);
			$tutorE->setCargo($cargo);
			$tutorE->setEmail($correo);
			$tutorE->setTelefono($telefono);
			$id= $tutorE->getId();
			$success = $tutorE->update();
		}else{
			$this->setEmpresa_id($idEmpresa);
			$this->setCedula($cedula);
			$this->setNombre($nombre);
			$this->setApellido($apellido);
			$this->setCargo($cargo);
			$this->setEmail($correo);
			$this->setTelefono($telefono);
			$this->setEstatus('I');
			$enviarCorreo = true;
			$success= $this->save();
			$tutorE = $this->findFirst("empresa_id='$idEmpresa' AND cedula='$cedula'");
			$id= $tutorE->getId();
		}
		return array("success"=>$success,
					"correo"=> $enviarCorreo,
					"id"=>$id);

	}


	/**
	 * Busca los datos del tutor empresarial asociado al id. Indices:id, cedula,nombre,apellido,correo,cargo y telefono
	 * @param int $id
	 * @return array datos
	 */
	public function getTutorEmpresarialById($id){
		$resultado=array();
		$tutor = $this->findFirst("id='$id'");
		$resultado['success'] = false;
		if ($tutor){
			$resultado['id']=$tutor->getId();
			$resultado['cedula']=utf8_encode($tutor->getCedula());
			$resultado['nombre']=utf8_encode($this->adecuarTexto($tutor->getNombre()));
			$resultado['apellido']=utf8_encode($this->adecuarTexto($tutor->getApellido()));
			$resultado['correo']=$tutor->getEmail();
			$resultado['cargo']=utf8_encode($this->adecuarTexto($tutor->getCargo()));
			$resultado['telefono']=$tutor->getTelefono();
			$resultado['empresa']=$tutor->getEmpresa_id();
			$resultado['success'] = true;
		}
			
		return $resultado;

	}


	/**
	 * Busca al tutor empresarial de cedula pCedula, asociado a la empresa pasada como parametro.
	 * @param string $pCedula
	 * @param int $pEmpresa_id
	 * @return array asociativo, con indices: success (boolean), errorMsj(string) y datos(array)
	 */
	public function buscarTutorEmpresarial($pCedula,$pEmpresa_id){

		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$resp['datos']=array();
		$errorMsj ='';

		$tutorE = $this->findFirst("empresa_id='$pEmpresa_id' AND cedula='$pCedula'");
		if ($tutorE){
			$errorMsj ='Tutor ya registrado.';
			$resp['datos']['email']=$tutorE->getEmail();
			$resp['datos']['cargo']=utf8_encode($this->adecuarTexto($tutorE->getCargo()));
			$resp['datos']['telefono']=$tutorE->getTelefono();
			$resp['datos']['apellido']=utf8_encode($this->adecuarTexto($tutorE->getApellido()));
			$resp['datos']['nombre']=utf8_encode($this->adecuarTexto($tutorE->getNombre()));
		}
		$resp['errorMsj']= $errorMsj;
		$resp['success']= true;
		return ($resp);

	}

	/**
	 * Activa el tutor empresarial dado como parametro
	 * @param int $id
	 * @return boolean
	 */
	public function activarTutor($id) {
		$flag=false;
		$tutor= $this->findFirst("id='$id'");
		if ($tutor){
			$tutor->setEstatus('A');
			$flag=$tutor->update();
		}
		return $flag;
	}

	public function getNombreApellido($id) {
		$aux='';
		$tutor = $this->findFirst("id='$id'");
		if ($tutor){
			$aux= "{$tutor->getNombre()} {$tutor->getApellido()}";
		}
		return $aux;
	}

	public function getTutores($idEmpresa) {
		$aux = array();
		$i=0;
		$tutores= $this->find("empresa_id='$idEmpresa'","order: nombre");
		foreach($tutores as $tutor){
			$aux[$i]['id'] = $tutor->getId();
			$aux[$i]['nombreCompleto'] = utf8_encode("{$tutor->getCargo()} :: {$tutor->getNombre()}, {$tutor->getApellido()}");
			$i++;
		}
		return $aux;

	}
	public function contarRegistradosEnLapso($idLapso) {
		$cantidad=0;
		$sql = " SELECT COUNT(*) AS cantidad FROM lapsoacademico l, tutorempresarial e ";
		$sql .= " WHERE e.fchRegistro_at BETWEEN l.fchInicio AND l.fchFin ";
		$sql .= " AND l.id='$idLapso' ";
		$db = Db::rawConnect();
		$result = $db->query($sql);
		if ($row = $db->fetchArray($result)){
			$cantidad = $row['cantidad'];
		}
		return $cantidad;
	}



	public function getTutoresEmpresarialesReporte(){
		$aux = array();
		$i=0;

		$sql  = "SELECT  cedula, nombre, apellido, te.cargo AS cargo, razonSocial ";
		$sql  .= " FROM  tutorEmpresarial te, empresa e ";
		$sql  .= " WHERE te.estatus = 'A' AND empresa_id=e.id ";
		$sql  .= " ORDER BY razonSocial,cedula  ";

		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$i][0] = $row['cedula'];
			$aux[$i][1] = utf8_encode($this->adecuarTexto($row['nombre']));
			$aux[$i][2] = utf8_encode($this->adecuarTexto($row['apellido']));
			$aux[$i][3] = utf8_encode($this->adecuarTexto($row['razonSocial']));
			$aux[$i][4] = utf8_encode($this->adecuarTexto($row['cargo']));
			$i++;
		}

		return  $aux;
	}

}
?>