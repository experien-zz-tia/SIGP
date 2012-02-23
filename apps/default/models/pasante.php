<?php
include_once 'Utils/Util.php';
class Pasante extends ActiveRecord{
	protected $id;
	protected $cedula;
	protected $nombre;
	protected $apellido;
	protected $fchNacimiento;
	protected $sexo;
	protected $telefono;
	protected $semestre;
	protected $indiceAcademico;
	protected $foto;
	protected $direccion;
	protected $ciudad_id;
	protected $estado_id;
	protected $email;
	protected $fchRegistro_at;
	protected $carrera_id;
	protected $lapsoAcademico_id;
	protected $modalidadPasantia_id;
	protected $tipoPasantia_id;
	protected $estatus;

	protected function initialize(){
		$this->belongsTo("carrera_id","carrera","id");
	}
	//-----------------------------------------------------------------------------------------
	public function setId($valor){$this->id = $valor;}
	public function getId(){return $this->id;}

	public function setCedula($valor){$this->cedula = $valor;}
	public function getCedula(){return $this->cedula;}

	public function setNombre($valor){$this->nombre = $valor;}
	public function getNombre(){return $this->nombre;}

	public function setApellido($valor){$this->apellido = $valor;}
	protected function getApellido(){return $this->apellido;}

	public function setFchNacimiento($valor){$this->fchNacimiento = $valor;}
	public function getFchNacimiento(){return $this->fchNacimiento;}

	public function setSexo($valor){$this->sexo = $valor;}
	public function getSexo(){return $this->sexo;}

	public function setTelefono($valor){$this->telefono = $valor;}
	public function getTelefono(){return $this->telefono;}

	public function setSemestre($valor){$this->semestre = $valor;}
	public function getSemestre(){return $this->semestre;}

	public function setIndiceAcademico($valor){$this->indiceAcademico = $valor;}
	public function getIndiceAcademico(){return $this->indiceAcademico;}

	public function setFoto($valor){$this->foto = $valor;}
	public function getFoto(){return $this->foto;}

	public function setDireccion($valor){$this->direccion = $valor;}
	public function getDireccion(){return $this->direccion;}

	public function setCiudadId($valor){$this->ciudad_id = $valor;}
	public function getCiudadId(){return $this->ciudad_id;}

	public function setEstadoId($valor){$this->estado_id = $valor;}
	public function getEstadoId(){return $this->estado_id;}

	public function setEmail($valor){$this->email = $valor;}
	public function getEmail(){return $this->email;}

	public function setFchRegistro_at($valor){$this->fchRegistro_at = $valor;}
	public function getFchRegistro_at(){return $this->fchRegistro_at;}

	public function setCarreraId($valor){$this->carrera_id = $valor;}
	public function getCarreraId(){return $this->carrera_id;}

	public function setLapsoAcademico_Id($valor){$this->lapsoAcademico_id = $valor;}
	public function getLapsoAcademico_Id(){return $this->lapsoAcademico_id;}

	public function setEstatus($valor){$this->estatus = $valor;}
	public function getEstatus(){return $this->estatus;}

	public function getModalidadPasantia_id() { return $this->modalidadPasantia_id; }
	public function getTipoPasantia_id() { return $this->tipoPasantia_id; }
	public function setModalidadPasantia_id($x) { $this->modalidadPasantia_id = $x; }
	public function setTipoPasantia_id($x) { $this->tipoPasantia_id = $x; }
	//-----------------------------------------------------------------------------------------

	/*
	 * NO USAR.. usar en su lgar la funcion q esta en Util
	 */
	function cambiarFechaMDYtoYMD($fecha,$separador='/'){
		$fechaExplode = explode($separador, $fecha);
		$lafecha = date("Y/m/d", mktime(0,0,0,$fechaExplode[1], $fechaExplode[0], $fechaExplode[2]));

		return $lafecha;
	}
	//-----------------------------------------------------------------------------------------
	public function registrarPasante($cedula,$fchNacimiento,$nombre,$apellido,$sexo,
	$carrera,$semestre,$indice,$tipoPasantia,
	$modalidad,$direccion,$estado,$ciudad,$telefono,$email){
			
		$success=false;
		$fecha = Util::cambiarFechaMDYtoYMD($fchNacimiento,'/');
		$pasante = new Pasante();
		$pasante->setCedula($cedula);
		$pasante->setNombre($nombre);
		$pasante->setApellido($apellido);
		$pasante->setSexo($sexo);
		$pasante->setTelefono($telefono);
		$pasante->setSemestre($semestre);
		$pasante->setFchNacimiento($fecha);
		$pasante->setIndiceAcademico($indice);
		$pasante->setDireccion($direccion);
		$pasante->setCiudadId($ciudad);
		$pasante->setEstadoId($estado);
		$pasante->setEmail($email);
		$pasante->setCarreraId($carrera);
		$pasante->setTipoPasantia_id($tipoPasantia);
		$pasante->setModalidadPasantia_id($modalidad);
		$pasante->setLapsoAcademico_Id(1);
		$pasante->setEstatus('P');
		$success = $pasante->save();

		return $success;
	}
	//-----------------------------------------------------------------------------------------
	public function actualizarPasante($cedula,$fchNacimiento,$nombre,$apellido,$sexo,
	$carrera,$semestre,$indice,$tipoPasantia,
	$modalidad,$direccion,$estado,$ciudad,$telefono,$email){
			
		$success=false;
		$fecha = Util::cambiarFechaMDYtoYMD($fchNacimiento,'/');
		$pasante = $this->findFirst("cedula = ".$cedula);
		if ($pasante != null){
			$pasante->setCedula($cedula);
			$pasante->setNombre($nombre);
			$pasante->setApellido($apellido);
			$pasante->setSexo($sexo);
			$pasante->setTelefono($telefono);
			$pasante->setSemestre($semestre);
			$pasante->setFchNacimiento($fecha);
			$pasante->setIndiceAcademico($indice);
			$pasante->setDireccion($direccion);
			$pasante->setCiudadId($ciudad);
			$pasante->setEstadoId($estado);
			$pasante->setEmail($email);
			$pasante->setCarreraId($carrera);
			$pasante->setTipoPasantia_id($tipoPasantia);
			$pasante->setModalidadPasantia_id($modalidad);
			$success = $pasante->update();

		}

		return $success;
	}
	//-----------------------------------------------------------------------------------------
	public function eliminarPasante($id){
		$success=false;
		$pas = $this->findFirst("id='$id'");
		if ($pas){
			$pas->setEstatus('E');
			$success = $pas->update();
		}
		return $success;

	}
	//-----------------------------------------------------------------------------------------
	public function buscarId($vCedula, $vFecha){
		$fecha = Util::cambiarFechaMDYtoYMD($vFecha,'/');
		$auxId = $this->findFirst("cedula = '".$vCedula."' AND fchNacimiento = '".$fecha."'");
		$id = 0;
		if ($auxId){
			$id = $auxId->getId();
		}
		return $id;
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
	public function buscarPasanteId($vCedula){
		//	$fecha = Util::cambiarFechaMDYtoYMD($vFecha,'/');
		//$fecha = $this->cambiarFechaMDYtoYMD($vFecha);
		$resp = array();

		$resp['success']= false;
		$resp['errorMsj']= '';
		$resp['datos']=array();
		$sw = false;
		$errorMsj ='';
		$i=0;

		$sql  = " SELECT p.*, c.id as ciudad, e.id as estado, ca.id as carr, d.id as decan ";
		$sql .= " FROM  pasante p, ciudad c, estado e, carrera ca, decanato d ";
		$sql .= " WHERE p.cedula = '".$vCedula."' ";
		$sql .= " AND c.id = p.ciudad_id AND c.estado_id = e.id AND e.id = p.estado_id ";
		$sql .= " AND ca.id = p.carrera_id AND ca.decanato_id = d.id ";
			
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$errorMsj ='Pasante encontrado.';
			$resp['datos']['nombre']=utf8_encode($row['nombre']);
			$resp['datos']['apellido']=utf8_encode($row['apellido']);
			$resp['datos']['sexo']=$row['sexo'];
			$resp['datos']['telefono']=$row['telefono'];
			$resp['datos']['semestre']=$row['semestre'];
			$resp['datos']['indiceAcademico']=$row['indiceAcademico'];
			$resp['datos']['direccion']=$row['direccion'];
			$resp['datos']['ciudad']=$row['ciudad'];
			$resp['datos']['estado']=$row['estado'];
			$resp['datos']['email']=$row['email'];
			$resp['datos']['fchNacimiento']=$row['fchNacimiento'];
			$resp['datos']['carrera']=$row['carr'];
			$resp['datos']['decanato']=$row['decan'];
			$resp['datos']['modalidadPasantia']=$row['modalidadPasantia_id'];
			$resp['datos']['tipoPasantia']=$row['tipoPasantia_id'];
			$resp['success']= true;
			$i++;
		}
			
		$resp['errorMsj']= $errorMsj;

		return ($resp);
	}
	//-----------------------------------------------------------------------------------------

	/**
	 * Obtiene las notas  totales por evaluacion por el tutor.
	 * El calculo de las notas esta dado por la formula: (Sumatoria(notas de cada aspecto)* porcentaje del tipo de evaluacion)/(cantidad de aspectos pro categoria* Nota maxima por aspecto)
	 * @param int $tutorId
	 * @param int $start
	 * @param int $limit
	 * @param string $tipoTutor
	 * @return array con_ array de notas y total de registros (general)
	 */
	public function getNotasPorTutor($tutorId,$cedulaPasante='',$start='*',$limit='*',$tipoTutor='*') {
		$aux = array();
		$i=0;
		$sqlTutor ="";
		$sqlNotas ="";
		$pasantia = new Pasantia();
		$total=0;
		$evaluacion = new Evaluacion();

		$datos=$evaluacion->getDetalleEvaluacion();
		$factores= array();
		for ($j = 1; $j <= count($datos); $j++) {
			$factores[$j]=(float)(($datos[$j]['porcentaje'])/($datos[$j]['nroItems']*NOTA_INDIVIDUAL_MAXIMA));
		}

		switch ($tipoTutor) {
			case 'A':
				$total= $pasantia->contarPasantiasActivasTutor($tutorId, $tipoTutor);
				$sqlTutor = " AND pa.tutorAcademico_id='$tutorId' ";
				$sqlNotas = ", SUM(IF(e.id=1,pe.nota,0))*$factores[1] AS notaInforme, SUM(IF(e.id=2,pe.nota,0))*$factores[2] AS notaEmpresaTA ";
				break;
			case 'E':
				$total=$pasantia->contarPasantiasActivasTutor($tutorId, $tipoTutor);
				$sqlTutor = " AND pa.tutorEmpresarial_id='$tutorId' ";
				$sqlNotas = ", SUM(IF(e.id=3,pe.nota,0))*$factores[3] AS notaEmpresaTE ";
				break;
			default:
				$total= $pasantia->contarPasantiasActivas();
				$sqlNotas = ", SUM(IF(e.id=1,pe.nota,0))*$factores[1] AS notaInforme, SUM(IF(e.id=2,pe.nota,0))*$factores[2] AS notaEmpresaTA ";
				$sqlNotas .=", SUM(IF(e.id=3,pe.nota,0))*$factores[3] AS notaEmpresaTE ";
				break;
		}
		$sql  = " SELECT  p.id AS pasanteId, cedula, nombre, apellido, razonSocial ";
		$sql .= $sqlNotas;
		$sql .= " FROM pasante p, pasanteevaluacion pe, evaluacion e, aspectoevaluacion ap, empresa em, pasantia pa ";
		$sql .= " WHERE p.id=pe.pasante_id  AND aspectoevaluacion_id=ap.id AND e.id=ap.evaluacion_id ";
		$sql .= " AND p.id=pa.pasante_id AND pa.empresa_id= em.id AND NOT (pa.estatus='F' AND pa.estatus='S') ";
		$sql .= $sqlTutor;
		if ($cedulaPasante!=''){
			$sql .= " AND p.cedula LIKE '$cedulaPasante%'";
		}
		$sql .= " GROUP BY p.id, cedula ";
		$sql .= " ORDER BY cedula ";
		if ($start!='*' && $limit!='*'){
			$sql .= " LIMIT ".$start.",".$limit." ";
		}

		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$i]['id'] = $row['pasanteId'];
			$aux[$i]['cedula'] = $row['cedula'];
			$aux[$i]['nombre'] = utf8_encode($row['nombre']);
			$aux[$i]['apellido'] = utf8_encode($row['apellido']);
			$aux[$i]['razonSocial'] = utf8_encode($row['razonSocial']);
			switch ($tipoTutor) {
				case 'A':
					$aux[$i]['notaInforme'] = $row['notaInforme'];
					$aux[$i]['notaEmpresaTA'] = $row['notaEmpresaTA'];
					break;
				case 'E':
					$aux[$i]['notaEmpresaTE'] = $row['notaEmpresaTE'];
					break;
				default:
					$aux[$i]['notaInforme'] = $row['notaInforme'];
					$aux[$i]['notaEmpresaTA'] = $row['notaEmpresaTA'];
					$aux[$i]['notaEmpresaTE'] = $row['notaEmpresaTE'];
					$aux[$i]['acumulado'] =$row['notaInforme']+  $row['notaEmpresaTA']+ $row['notaEmpresaTE'];
					break;
			}
			$i++;
		}

		return array('total'=>$total,
					'resultado' => $aux);

	}

	/**
	 * Obtiene el nombre y apellido de un pasante dado su id
	 * @param int $id
	 * @return string
	 */
	public function getNombreApellido($id) {
		$aux='';
		$pasante = $this->findFirst("id='$id'");
		if ($pasante){
			$aux= "{$pasante->getNombre()}, {$pasante->getApellido()}";
		}
		return $aux;
	}

	/**
	 * Busca la informacion del pasante en base a un id
	 * @param int $idPasante
	 * @return array
	 */
	public function getPasantebyId($idPasante) {
		$resultado=array();
		$pasante = $this->findFirst("id='$idPasante'");
		if ($pasante){
			$resultado['id']=$pasante->getId();
			$resultado['cedula']=$pasante->getCedula();
			$resultado['nombre']=utf8_encode($pasante->getNombre());
			$resultado['apellido']=utf8_encode($pasante->getApellido());
			$resultado['email']=$pasante->getEmail();
			$resultado['semestre']=$pasante->getSemestre();
			$resultado['telefono']=$pasante->getTelefono();
			$resultado['tipoPasantia']=$pasante->getTipoPasantia_id();
			$carrera= $pasante->getCarrera();
			$resultado['carrera']=utf8_encode($carrera->getNombre());
			$resultado['modalidadPasantia']=$pasante->getModalidadPasantia_id();
		}
		return $resultado;
	}


	public function inscribir($pasanteId) {
		$success=false;
		$pasante = $this->findFirst("id ='$pasanteId'");
		if ($pasante){
			$pasante->setEstatus('D');
			$success = $pasante->update();
		}
		return $success;
	}

	/**
	 * Retorna el número de pasantes que estan registrados ( en cualquiera de sus fases)
	 * @param int $idLapso
	 * @return int
	 */
	public function contarPasantesPorLapso($idLapso, $estatus) {
		return $this->count("lapsoAcademico_id='$idLapso' AND estatus='$estatus'");
	}

	public function contarTotalPasantesPorLapso($idLapso) {
		return $this->count("lapsoAcademico_id='$idLapso' AND estatus!='E'");
	}

	public function consultaPasantias($carreraId='',$cedulaPasante='',$start='*',$limit='*') {
		$aux = array();
		$i=0;
		$total=0;
		$pasantia = new Pasantia();
		$idDecanato= DECANATO_CIENCIAS;
		$lapso = new Lapsoacademico();
		$lapsoId=  $lapso->getLapsoActivobyDecanato($idDecanato);
		if ($lapsoId) {
			$lapsoId=$lapsoId['id'];
		}else{
			$lapsoId=0;
		}
		$total= $pasantia->contarPasantiabyLapsoActivo($idDecanato);
		$sql  = " SELECT  p.id AS pasanteId,pa.id AS pasantiaId, p.cedula AS cedula, p.nombre AS nombrePasante, p.apellido AS apellidoPasante, ";
		$sql  .= " c.nombre AS carrera, razonSocial, te.nombre AS nombreTE, te.apellido AS apellidoTE,ta.nombre AS nombreTA, ";
		$sql  .= " ta.apellido AS apellidoTA, pa.estatus AS estatusPasantia ";
		$sql  .= " FROM lapsoacademico la, carrera c, pasante p,pasantia pa ";
		$sql  .= " LEFT JOIN  empresa em ON ( pa.empresa_id= em.id) ";
		$sql  .= " LEFT JOIN  tutorempresarial te ON ( pa.tutorempresarial_id=te.id) ";
		$sql  .= " LEFT JOIN  tutoracademico ta ON ( pa.tutoracademico_id=ta.id) ";
		$sql  .= " WHERE  p.id=pa.pasante_id AND p.estatus!='E' ";
		$sql  .= " AND  c.id= p.carrera_id AND la.id=pa.lapsoacademico_id AND la.id='$lapsoId' ";
		if ($cedulaPasante!=''){
			$sql .= " AND p.cedula LIKE '$cedulaPasante%'";
		}
		if ($carreraId!=''){
			$sql .= " AND c.id = '$carreraId'";
		}
		$sql .= " ORDER BY p.cedula, c.id ";
		if ($start!='*' && $limit!='*'){
			$sql .= " LIMIT ".$start.",".$limit." ";
		}
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$i]['pasanteId'] = $row['pasanteId'];
			$aux[$i]['pasantiaId'] = $row['pasantiaId'];
			$aux[$i]['cedulaPasante'] = $row['cedula'];
			$aux[$i]['nombrePasante'] = utf8_encode($row['nombrePasante']);
			$aux[$i]['apellidoPasante'] = utf8_encode($row['apellidoPasante']);
			$aux[$i]['razonSocial'] = utf8_encode($row['razonSocial']);
			$aux[$i]['carrera'] = utf8_encode($row['carrera']);
			$aux[$i]['tutorEmp'] = utf8_encode($row['nombreTE'].' '.$row['apellidoTE']);
			$aux[$i]['tutorAcad'] = utf8_encode($row['nombreTA'].' '.$row['apellidoTA']);
			$aux[$i]['estatusPasantia'] = utf8_encode($pasantia->getTextoEstatus($row['estatusPasantia']));
			$i++;
		}

		return array('total'=>$total,
					'resultado' => $aux);

	}

	public function consultaPasantiasTA($idTutor,$carreraId='',$cedulaPasante='',$start='*',$limit='*') {
		$aux = array();
		$i=0;
		$total=0;
		$pasantia = new Pasantia();
		$idDecanato= DECANATO_CIENCIAS;
		$lapso = new Lapsoacademico();
		$lapsoId=  $lapso->getLapsoActivobyDecanato($idDecanato);
		if ($lapsoId) {
			$lapsoId=$lapsoId['id'];
		}else{
			$lapsoId=0;
		}
		$total= $pasantia->contarPasantiasActivasTutorbyLapso($idDecanato,$idTutor,'A');
		$sql  = " SELECT  p.id AS pasanteId,pa.id AS pasantiaId, p.cedula AS cedula, p.nombre AS nombrePasante, p.apellido AS apellidoPasante, ";
		$sql  .= " c.nombre AS carrera, razonSocial, te.nombre AS nombreTE, te.apellido AS apellidoTE,  te.id AS tutorId, ";
		$sql  .= "  pa.estatus AS estatusPasantia ";
		$sql  .= " FROM lapsoacademico la, carrera c, pasante p,pasantia pa ";
		$sql  .= " LEFT JOIN  empresa em ON ( pa.empresa_id= em.id) ";
		$sql  .= " LEFT JOIN  tutorempresarial te ON ( pa.tutorempresarial_id=te.id) ";
		$sql  .= " WHERE   pa.tutoracademico_id='$idTutor' AND p.id=pa.pasante_id AND p.estatus!='E' ";
		$sql  .= " AND  c.id= p.carrera_id AND la.id=pa.lapsoacademico_id AND la.id='$lapsoId' ";
		if ($cedulaPasante!=''){
			$sql .= " AND p.cedula LIKE '$cedulaPasante%'";
		}
		if ($carreraId!=''){
			$sql .= " AND c.id = '$carreraId'";
		}
		$sql .= " ORDER BY p.cedula, c.id ";
		if ($start!='*' && $limit!='*'){
			$sql .= " LIMIT ".$start.",".$limit." ";
		}
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$i]['pasanteId'] = $row['pasanteId'];
			$aux[$i]['pasantiaId'] = $row['pasantiaId'];
			$aux[$i]['cedulaPasante'] = $row['cedula'];
			$aux[$i]['tutorId'] = $row['tutorId'];
			$aux[$i]['nombrePasante'] = utf8_encode($row['nombrePasante']);
			$aux[$i]['apellidoPasante'] = utf8_encode($row['apellidoPasante']);
			$aux[$i]['razonSocial'] = utf8_encode($row['razonSocial']);
			$aux[$i]['carrera'] = utf8_encode($row['carrera']);
			$aux[$i]['tutorEmp'] = utf8_encode($row['nombreTE'].' '.$row['apellidoTE']);
			$aux[$i]['estatusPasantia'] = utf8_encode($pasantia->getTextoEstatus($row['estatusPasantia']));
			$i++;
		}

		return array('total'=>$total,
					'resultado' => $aux);

	}


	public function consultaPasantiasTE($idTutor,$carreraId='',$cedulaPasante='',$start='*',$limit='*') {
		$aux = array();
		$i=0;
		$total=0;
		$pasantia = new Pasantia();
		$idDecanato= DECANATO_CIENCIAS;
		$lapso = new Lapsoacademico();
		$lapsoId=  $lapso->getLapsoActivobyDecanato($idDecanato);
		if ($lapsoId) {
			$lapsoId=$lapsoId['id'];
		}else{
			$lapsoId=0;
		}
		$total= $pasantia->contarPasantiasActivasTutorbyLapso($idDecanato,$idTutor,'E');
		$sql  = " SELECT  p.id AS pasanteId,pa.id AS pasantiaId, p.cedula AS cedula, p.nombre AS nombrePasante, p.apellido AS apellidoPasante, ";
		$sql  .= " c.nombre AS carrera, razonSocial, ta.nombre AS nombreTA, ta.apellido AS apellidoTA,  ta.id AS tutorId, ";
		$sql  .= "  pa.estatus AS estatusPasantia ";
		$sql  .= " FROM lapsoacademico la, carrera c, pasante p,pasantia pa ";
		$sql  .= " LEFT JOIN  empresa em ON ( pa.empresa_id= em.id) ";
		$sql  .= " LEFT JOIN  tutoracademico ta ON ( pa.tutoracademico_id=ta.id) ";
		$sql  .= " WHERE   pa.tutorempresarial_id='$idTutor' AND p.id=pa.pasante_id AND p.estatus!='E' ";
		$sql  .= " AND  c.id= p.carrera_id AND la.id=pa.lapsoacademico_id AND la.id='$lapsoId' ";
		if ($cedulaPasante!=''){
			$sql .= " AND p.cedula LIKE '$cedulaPasante%'";
		}
		if ($carreraId!=''){
			$sql .= " AND c.id = '$carreraId'";
		}
		$sql .= " ORDER BY p.cedula, c.id ";
		if ($start!='*' && $limit!='*'){
			$sql .= " LIMIT ".$start.",".$limit." ";
		}
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$i]['pasanteId'] = $row['pasanteId'];
			$aux[$i]['pasantiaId'] = $row['pasantiaId'];
			$aux[$i]['cedulaPasante'] = $row['cedula'];
			$aux[$i]['tutorId'] = $row['tutorId'];
			$aux[$i]['nombrePasante'] = utf8_encode($row['nombrePasante']);
			$aux[$i]['apellidoPasante'] = utf8_encode($row['apellidoPasante']);
			$aux[$i]['razonSocial'] = utf8_encode($row['razonSocial']);
			$aux[$i]['carrera'] = utf8_encode($row['carrera']);
			$aux[$i]['tutorAcad'] = utf8_encode($row['nombreTA'].' '.$row['apellidoTA']);
			$aux[$i]['estatusPasantia'] = utf8_encode($pasantia->getTextoEstatus($row['estatusPasantia']));
			$i++;
		}

		return array('total'=>$total,
					'resultado' => $aux);

	}
}
?>