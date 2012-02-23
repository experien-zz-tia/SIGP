<?php
/**
 * @author Yajaira Suescún
 * @author Robert Arrieche
 * @version 1.0
 */
class TutorAcademico extends Tutor {

	protected $departamento_id ;
	protected $dependencia_id;

	protected function initialize(){
	}
	//-----------------------------------------------------------------------------------------
	public function getDepartamento_id() {
		return $this->departamento_id;
	}

	public function setDepartamento_id($x) {
		$this->departamento_id = $x;
	}

	public function getDependencia_id() {
		return $this->dependencia_id;
	}

	public function setDependencia_id($x) {
		$this->dependencia_id = $x;
	}
	//-----------------------------------------------------------------------------------------
	public function buscarCedulaById($vId){
		$auxId = $this->findFirst("id = '".$vId."' ");
		$cedula = 0;
		if ($auxId){
			$cedula = $auxId->getCedula();
		}
		return $cedula;
	}
	//-----------------------------------------------------------------------------------------
	public function getTutoresAcademicos($idDepartamento='%',$start='-1',$limit=-'1'){
		$aux = array();
		$i=0;

		$sqlTotal  = "SELECT  COUNT(ta.id) as total ";
		$sqlTotal .= "FROM  tutorAcademico ta ";
		$sqlTotal .= "WHERE ta.estatus = 'A' ";
		$sqlTotal .= "AND departamento_id LIKE '".$idDepartamento."'";
		$db = Db::rawConnect();
		$total = $db->fetchOne($sqlTotal);
		$total = $total[0];
		if ($total!=0){
			$sql  = " SELECT ta.id as id, cedula, nombre, apellido, cargo, ta.estatus as status, d.descripcion as departamento, d.id as idDep";
			$sql .= " FROM  tutorAcademico ta, departamento d ";
			$sql .= " WHERE ta.estatus = 'A' AND  d.id = departamento_id ";
			$sql .= " ORDER BY d.descripcion, cedula ";
			if ($start!='-1' && $limit!='-1'){
				$sql .= " LIMIT ".$start.",".$limit." ";
			}
			$db = Db::rawConnect();
			$result = $db->query($sql);
			while($row = $db->fetchArray($result)){
				$aux[$i]['id'] = $row['id'];
				$aux[$i]['cedula'] = $row['cedula'];
				$aux[$i]['nombre'] = utf8_encode($row['nombre']);
				$aux[$i]['apellido'] = utf8_encode($row['apellido']);
				$resp['datos']['departamentoId']= $row['idDep'];
				$aux[$i]['departamento'] = utf8_encode($row['departamento']);
				$aux[$i]['cargo'] = utf8_encode($row['cargo']);
				$aux[$i]['estatus'] = ($row['status']=='A')?'Activo':'Inactivo';
				$i++;
			}
		}

		return array('total'=>$total,
					'resultado' => $aux);
	}
	//-----------------------------------------------------------------------------------------
	public function eliminarTutor($id){
		$success=false;
		$oferta = $this->findFirst("id='$id'");
		if ($oferta){
			$oferta->setEstatus('E');
			$success = $oferta->update();
		}
		return $success;

	}
	//-----------------------------------------------------------------------------------------
	public function eliminarTutores($idDepartamento){
		$success=true;
		$tutorAs= $this->find("departamento_id='$idDepartamento'");
		foreach($tutorAs as $tutor){
			$tutor->setEstatus('E');
			$idUsuario = $tutor->getId();
			$idCategoria = CAT_USUARIO_TUTOR_EMP;
			$success = ($success AND $tutor->update());
			$usuario = new Usuario();
			$success=($success AND $usuario->eliminar($idUsuario, $idCategoria))?true:false;
		}
		return $success;
	}
	//-----------------------------------------------------------------------------------------
	/**
	 * Registra o actualiza un tutor academico de un Departamento dado.
	 * @param int $idDepartamento
	 * @param string $cedula
	 * @param string $nombre
	 * @param string $apellido
	 * @param string $telefono
	 * @param string $correo
	 * @param string $cargo
	 * @return boolean
	 */
	function actualizarTutorA($idDepartamento,$cedula,$nombre,$apellido,$telefono,$correo,$cargo,$dependencia){
		$success = false;
		$enviarCorreo = false;
		$id = 0;
		$paso = '';
		$tutorA = $this->findFirst("cedula='$cedula'");
		if ($tutorA){
			$tutorA->setNombre($nombre);
			$tutorA->setApellido($apellido);
			$tutorA->setCargo($cargo);
			$tutorA->setEmail($correo);
			$tutorA->setDepartamento_id($idDepartamento);
			$tutorA->setDependencia_id($dependencia);

			if ($telefono!=''){
				$tutorA->setTelefono($telefono);
			}
			$enviarCorreo = false;
			$id = $tutorA->getId();
			$success = $tutorA->update();
			$paso='actualizado';
		}
		return array("success"=>$success,
					"correo"=> $enviarCorreo,
					"id"=>$id,
					"pasaPor"=>$paso);
	}
	//-----------------------------------------------------------------------------------------
	function guardarTutorA($idDepartamento,$cedula,$nombre,$apellido,$telefono,$correo,$cargo,$dependencia){
		$success = false;
		$enviarCorreo = false;
		$id = 0;
		$paso = '';
		//$tutorA = $this->findFirst("cedula='.$cedula.'");
		$tutorAcad = new TutorAcademico();
		$tutorAcad->setDepartamento_id($idDepartamento);
		$tutorAcad->setCedula($cedula);
		$tutorAcad->setNombre($nombre);
		$tutorAcad->setApellido($apellido);
		$tutorAcad->setCargo($cargo);
		$tutorAcad->setEmail($correo);
		$tutorAcad->setDependencia_id($dependencia);

		if ($telefono!=''){
			$tutorAcad->setTelefono($telefono);
		}

		$tutorAcad->setEstatus('I');
		$enviarCorreo = true;
		$success = $tutorAcad->save();
		$paso='guardando';
		if ($success){
			$tutorA = $this->findFirst("cedula='$cedula'");
			if ($tutorA){
				$id = $tutorA->getId();
			}
		}
		return array("success"=>$success,
					"correo"=> $enviarCorreo,
					"id"=>$id,
					"pasaPor"=>$tutorAcad->getNombre().' '.$tutorAcad->getApellido()
		.' '.$tutorAcad->getCedula().' '.$tutorAcad->getCargo().' '.$tutorAcad->getDepartamento_id()
		.' '.$tutorAcad->getDependencia_id());
	}
	//-----------------------------------------------------------------------------------------
	public function getTutorAcademicoById($id){
		$resultado=array();
		$tutor = $this->findFirst("id='$id'");
		if ($tutor){
			$resultado['id']=$tutor->getId();
			$resultado['cedula']=utf8_encode($tutor->getCedula());
			$resultado['nombre']=utf8_encode($tutor->getNombre());
			$resultado['apellido']=utf8_encode($tutor->getApellido());
			$resultado['correo']=$tutor->getEmail();
			$resultado['cargo']=utf8_encode($tutor->getCargo());
			$resultado['telefono']=$tutor->getTelefono();

			$id = $tutor->getDepartamento_id();
			$dep = new Departamento();
			$departamento = $dep->findFirst("id = '$id'");
			if ($departamento){
				$resultado['datos']['departamentoId']= $id;
				$resultado['datos']['departamento']= utf8_encode($departamento->getDescripcion());
				$resultado['datos']['decanatoId'] = $departamento->getDecanatoId();
			}
		}
		return $resultado;
	}
	//-----------------------------------------------------------------------------------------
	/**
	 * Busca al tutor Academico de cedula pCedula, asociado a la Departamento pasada como parametro.
	 * @param string $pCedula
	 * @param int $pDepartamento_id
	 * @return array asociativo, con indices: success (boolean), errorMsj(string) y datos(array)
	 */
	public function buscarTutorAcademico($pCedula,$pDepartamento_id){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$resp['datos']=array();
		$errorMsj ='';

		$tutorA = $this->findFirst("departamento_id='$pDepartamento_id' AND cedula='$pCedula'");
		if ($tutorA){
			$errorMsj ='Tutor ya registrado.';
			$resp['datos']['email']=$tutorA->getEmail();
			$resp['datos']['cargo']=utf8_encode($tutorA->getCargo());
			$resp['datos']['telefono']=$tutorA->getTelefono();
			$resp['datos']['apellido']=utf8_encode($tutorA->getApellido());
			$resp['datos']['nombre']=utf8_encode($tutorA->getNombre());
		}
		$resp['errorMsj']= $errorMsj;
		$resp['success']= true;
		return ($resp);

	}
	//-----------------------------------------------------------------------------------------
	public function buscarTutorAcad($pCedula){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$resp['datos']=array();
		$errorMsj ='';

		$tutorA = $this->findFirst("cedula = '$pCedula'");
		if ($tutorA){
			$errorMsj ='Tutor encontrado.';
			$resp['datos']['cedula'] = $pCedula;
			$resp['datos']['email'] = $tutorA->getEmail();
			$resp['datos']['cargo'] = utf8_encode($tutorA->getCargo());
			$resp['datos']['telefono'] = $tutorA->getTelefono();
			$resp['datos']['apellido'] = utf8_encode($tutorA->getApellido());
			$resp['datos']['nombre'] = utf8_encode($tutorA->getNombre());
			$id = $tutorA->getDepartamento_id();
			$dep = new Departamento();
			$departamento = $dep->findFirst("id = '$id'");
			if ($departamento){
				$resp['datos']['departamentoId']= $id;
				$resp['datos']['departamento']= $departamento->getDescripcion();
				$resp['datos']['decanatoId'] = $departamento->getDecanatoId();
			}
			$resp['success']= true;
		}
		$resp['errorMsj']= $errorMsj;
		$resp['cedula']= $pCedula;
		return ($resp);

	}
	//-----------------------------------------------------------------------------------------
	public function activarTutor($id) {
		$flag=false;
		$tutor= $this->findFirst("id='$id'");
		if ($tutor){
			$tutor->setEstatus('A');
			$flag=$tutor->update();
		}
		return $flag;
	}
	//-----------------------------------------------------------------------------------------

	public function getNombreApellido($id) {
		$aux='';
		$tutor = $this->findFirst("id='$id'");
		if ($tutor){
			$aux= "{$tutor->getNombre()}, {$tutor->getApellido()}";
		}
		return $aux;
	}


	public function getTutoresAcademicosLight($nombre='',$start='*',$limit='*'){
		$aux = array();
		$i=0;

		$tutor = new TutorAcademico();
		$total = $tutor->count("estatus='A'");
		if ($total!=0){
			$sql  = " SELECT t.id AS id,nombre, apellido, cargo, IFNULL(descripcion,'-') AS departamento ";
			$sql  .= " FROM tutoracademico t LEFT JOIN departamento d ON (departamento_id=d.id) ";
			$sql  .= " WHERE t.estatus='A' ";
			if ($nombre!=''){
				$sql .= " AND nombre LIKE '%$nombre%'";
			}
			$sql  .= " ORDER BY descripcion ";
			if ($start!='*' && $limit!='*'){
				$sql .= " LIMIT ".$start.",".$limit." ";
			}
			$db = Db::rawConnect();
			$result = $db->query($sql);
			while($row = $db->fetchArray($result)){
				$aux[$i]['id'] = $row['id'];
				$aux[$i]['nombre'] = utf8_encode($this->adecuarTexto($row['nombre']));
				$aux[$i]['apellido'] = utf8_encode($this->adecuarTexto($row['apellido']));
				$aux[$i]['cargo'] = utf8_encode($this->adecuarTexto($row['cargo']));
				$aux[$i]['departamento'] = utf8_encode($this->adecuarTexto($row['departamento']));
				$i++;
			}
		}

		return array('total'=>$total,
					'resultado' => $aux);
	}

	public function contarRegistradosEnLapso($idLapso) {
		$cantidad=0;
		$sql = " SELECT COUNT(*) AS cantidad FROM lapsoacademico l, tutoracademico e ";
		$sql .= " WHERE e.fchRegistro_at BETWEEN l.fchInicio AND l.fchFin ";
		$sql .= " AND l.id='$idLapso' ";
		$db = Db::rawConnect();
		$result = $db->query($sql);
		if ($row = $db->fetchArray($result)){
			$cantidad = $row['cantidad'];
		}
		return $cantidad;
	}


	public function getTutoresAcademicosReporte(){
		$aux = array();
		$i=0;

		$sql  = " SELECT  cedula, nombre, apellido, cargo, d.descripcion as departamento ";
		$sql  .= " FROM  tutorAcademico ta, departamento d ";
		$sql  .= " WHERE ta.estatus = 'A' AND  d.id = departamento_id ";
		$sql  .= " ORDER BY d.descripcion, cedula ";

		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$i][0] = $row['cedula'];
			$aux[$i][1] = utf8_encode($row['nombre']);
			$aux[$i][2] = utf8_encode($row['apellido']);
			$aux[$i][3] = utf8_encode($row['departamento']);
			$aux[$i][4] = utf8_encode($row['cargo']);
			$i++;
		}

		return $aux;
	}

}
?>
