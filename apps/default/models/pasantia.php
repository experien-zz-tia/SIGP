<?php
include_once 'Utils/Util.php';
/**
 * Clase Pasantia: Almacena y permite operar sobre la información relacionada con las pasantias de los pasantes.
 * @author Robert A
 *
 */
class Pasantia extends ActiveRecord {

	protected $id;
	protected $lapsoAcademico_id;
	protected $empresa_id;
	protected $pasante_id;
	protected $tutorEmpresarial_id;
	protected $tutorAcademico_id;
	protected $oferta_id;
	protected $fchInicioEst;
	protected $fchFinEst;
	protected $fchInicio;
	protected $fchFin;
	protected $tipoPasantia;
	protected $areaPasantia_id;
	protected $modalidadPasantia;
	protected $nota;
	protected $estatus;

	protected function initialize(){

	}

	public function getId() {
		return $this->id;
	}
	public function getLapsoAcademico_id() {
		return $this->lapsoAcademico_id;
	}
	public function getEmpresa_id() {
		return $this->empresa_id;
	}
	public function getOferta_id() {
		return $this->oferta_id;
	}
	public function getPasante_id() {
		return $this->pasante_id;
	}
	public function getTutorEmpresarial_id() {
		return $this->tutorEmpresarial_id;
	}
	public function getTutorAcademico_id() {
		return $this->tutorAcademico_id;
	}
	public function getFchInicioEst() {
		return $this->fchInicioEst;
	}
	public function getFchFinEst() {
		return $this->fchFinEst;
	}
	public function getFchInicio() {
		return $this->fchInicio;
	}
	public function getFchFin() {
		return $this->fchFin;
	}
	public function getTipoPasantia() {
		return $this->tipoPasantia;
	}
	public function getAreaPasantia_id() {
		return $this->areaPasantia_id;
	}
	public function getModalidadPasantia() {
		return $this->modalidadPasantia;
	}
	public function getNota() {
		return $this->nota;
	}
	public function getEstatus() {
		return $this->estatus;
	}
	public function setId($x) {
		$this->id = $x;
	}
	public function setLapsoAcademico_id($x) {
		$this->lapsoAcademico_id = $x;
	}
	public function setEmpresa_id($x) {
		$this->empresa_id = $x;
	}
	public function setOferta_id($x) {
		$this->oferta_id = $x;
	}
	public function setPasante_id($x) {
		$this->pasante_id = $x;
	}
	public function setTutorEmpresarial_id($x) {
		$this->tutorEmpresarial_id = $x;
	}
	public function setTutorAcademico_id($x) {
		$this->tutorAcademico_id = $x;
	}
	public function setFchInicioEst($x) {
		$this->fchInicioEst = $x;
	}
	public function setFchFinEst($x) {
		$this->fchFinEst = $x;
	}
	public function setFchInicio($x) {
		$this->fchInicio = $x;
	}

	public function setFchFin($x) {
		$this->fchFin = $x;
	}
	public function setTipoPasantia($x) {
		$this->tipoPasantia = $x;
	}
	public function setAreaPasantia_id($x) {
		$this->areaPasantia_id = $x;
	}
	public function setModalidadPasantia($x) {
		$this->modalidadPasantia = $x;
	}
	public function setNota($x) {
		$this->nota = $x;
	}
	public function setEstatus($x) {
		$this->estatus = $x;
	}

	/**
	 * Cuenta el número de pasantías activas ( en estado no finalizado) a las cuales el tutor pasado como
	 * parametro está asignado.
	 * @param int $idTutor
	 * @param string $tipoTutor 'A': Academico, 'E':Empresarial
	 * @return int
	 */
	public function contarPasantiasActivasTutor($idTutor,$tipoTutor){
		$condicion="estatus !='F' AND ";

		switch ($tipoTutor) {
			case 'A':$condicion .=" tutorAcademico_id";
			break;
			case 'E':$condicion .=" tutorEmpresarial_id";
			break;
		}
		$condicion .= "='$idTutor'";

		return $this->count($condicion);

	}
	public function contarPasantiasActivasTutorbyLapso($idDecanato,$idTutor,$tipoTutor){
		$nro=0;
		$condicion ='';
		$lapso = new Lapsoacademico();
		$lapsoId=  $lapso->getLapsoActivobyDecanato($idDecanato);
		if ($lapsoId) {
			$lapsoId=$lapsoId['id'];
			switch ($tipoTutor) {
				case 'A':$condicion .=" tutorAcademico_id";
				break;
				case 'E':$condicion .=" tutorEmpresarial_id";
				break;
			}
			$condicion .= "='$idTutor' AND lapsoacademico_id ='$lapsoId'";	
			$nro =$this->count($condicion);
		}
		return $nro;

	}
	/**
	 * Cuenta las pasantias no finalizadas registradas en el sistema
	 * @return int 
	 */
	public function contarPasantiasActivas(){
		$condicion="estatus !='F'";
		return $this->count($condicion);
	}
	
	public function contarPasantiabyLapsoActivo($idDecanato){
		$nro=0;
		$lapso = new Lapsoacademico();
		$lapsoId=  $lapso->getLapsoActivobyDecanato($idDecanato);
		if ($lapsoId) {
			$lapsoId=$lapsoId['id'];
			$condicion="lapsoacademico_id ='$lapsoId'";
			$nro =$this->count($condicion);
		}
		return $nro;
	}

	/**
	 * Cuenta el número de pasantias actiavs que estan asociadas a la empresa.
	 * @param int  $idEmpresa
	 * @return int
	 */
	public function  contarPasantiasActivasEmpresa($idEmpresa){
		return $this->count("estatus !='F' AND empresa_id =".$idEmpresa);
	}

	/**
	 * Genera lista de las pasantias asociadadas a un empresa en particular y filtradas por su estatus.
	 * @param int $id
	 * @param string $start
	 * @param string $limit
	 * @param string $estatus default 'D'
	 * @return array <multitype:, string>
	 */
	public function getPasantias($id,$start,$limit,$estatus="'D'") {
		$aux = array();
		$i=0;
		 
		$total = $this->count("estatus IN (".$estatus.")  AND empresa_id=".$id);
		$sql = " SELECT p.id as id, titulo,cedula,nombre,apellido,p.fchInicioEst AS fchInicioEst ,p.fchFinEst AS fchFinEst,mp.descripcion AS modalidadPasantia, tp.descripcion AS tipoPasantia";
		$sql .= " FROM pasantia p,pasante pa,oferta o ,modalidadPasantia mp, tipoPasantia tp";
		$sql .= " WHERE oferta_id=o.id AND pasante_id=pa.id AND p.estatus IN (".$estatus.") AND p.empresa_id=".$id;
		$sql .= " AND mp.id=p.modalidadPasantia_id AND tp.id=p.tipoPasantia_id  ";
		$sql .= " ORDER BY o.id,pa.cedula ";
		$sql .= " LIMIT ".$start.",".$limit." ";
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$i]['id'] = $row['id'];
			$aux[$i]['titulo'] = utf8_encode($row['titulo']);
			$aux[$i]['cedula'] = utf8_encode($row['cedula']);
			$aux[$i]['nombre'] = utf8_encode($row['nombre']);
			$aux[$i]['apellido'] = utf8_encode($row['apellido']);
			$aux[$i]['fchInicioEst'] = Util::cambiarFechaMDY($row['fchInicioEst']);
			$aux[$i]['fchFinEst'] =  Util::cambiarFechaMDY($row['fchFinEst']);
			$aux[$i]['modalidad'] = utf8_encode($row['modalidadPasantia']);
			$aux[$i]['tipoPasantia'] = utf8_encode($row['tipoPasantia']);
			$i++;
		}
		return array('total'=>$total,
					'resultado' => $aux);
	}


	
	/**
	 * Busca si el pasante esta realizando pasantias, o ya la ha realizado, en caso afrimativo retorna informacion realcionada a la misma, en caso contrario arreglo vacio
	 * @param int $idPasante
	 * @return array:
	 */
	public function estaEnPasantia($idPasante) {
		$resultado=array();
		$pasantia = $this->findFirst("pasante_id='$idPasante'");
		if ($pasantia){
			$resultado['id']=$pasantia->getId();
			$resultado['estatus']=$pasantia->getEstatus();
		}
			
		return $resultado;
	}
	
	/**
	 * Guarda los datos de una pasantia en modo 'inscrita'
	 * @param int $lapsoAcademico_id
	 * @param int $empresa_id
	 * @param int $pasante_id
	 * @param int $tutorEmpresarial_id
	 * @param int $tutorAcademico_id
	 * @param int $oferta_id
	 * @param date $fchInicioEst
	 * @param date $fchFinEst
	 * @param string $tipoPasantia
	 * @param int $areaPasantia_id
	 * @param string $modalidadPasantia
	 * @return boolean $success
	 */
	public function inscribirPasantia( $lapsoAcademico_id, $empresa_id, $pasante_id, $tutorEmpresarial_id, $tutorAcademico_id, $oferta_id, $fchInicioEst, $fchFinEst, $tipoPasantia, $areaPasantia_id, $modalidadPasantia) {
		$success= false;
		$this->setLapsoAcademico_id($lapsoAcademico_id);
		$this->setEmpresa_id($empresa_id);
		$this->setPasante_id($pasante_id);
		$this->setTutorAcademico_id($tutorAcademico_id);
		$this->setTutorEmpresarial_id($tutorEmpresarial_id);
		$this->setOferta_id($oferta_id);
		$this->setFchInicioEst(Util::cambiarFechaMDYtoYMD($fchInicioEst,'/'));
		$this->setFchFinEst(Util::cambiarFechaMDYtoYMD($fchFinEst,'/'));
		$this->setModalidadPasantia($modalidadPasantia);
		$this->setTipoPasantia($tipoPasantia);
		$this->setAreaPasantia_id($areaPasantia_id);
		$this->setEstatus('I');
		$success= $this->save();
		return $success;

	}
	
	public function registrarTutor($pasanteId,$idTutor) {
		$success=false;
		$pasantia = $this->findFirst("pasante_id='$pasanteId' AND estatus!='E'");
		if ($pasantia){
			$pasantia->setTutorAcademico_id($idTutor);
			$success=$pasantia->update();
		}
		return $success;
	}
	
	public function contarPasantiasPorLapso($idLapso) {
		$sql = " SELECT COUNT(*) AS rowcount FROM pasantia p ,pasante t "; 
		$sql .= " WHERE t.lapsoAcademico_id='$idLapso' AND p.estatus!='E'  AND p.pasante_id=t.id";
		$db = Db::rawConnect();
	 	$result = $db->query($sql);
	 	if ($row = $db->fetchArray($result)){
			$cantidad = $row['rowcount'];
	 	}
	 	return $cantidad;
	}
	
	public function finalizarPasantias($idLapso){
		return $this->updateAll("estatus='F'","lapsoAcademico_id='$idLapso'");
	}
	
	public function buscarPasantiasSupervizadas($idTutorAcademico) {
		$aux = array();
		$auxDatos = array();
		$lapsoAnterior=-1;
		$i=-1;
		$sql = " SELECT  l.id AS lapsoId,l.fchInicio AS fchInicio, l.fchFin AS fchFin,l.lapso AS lapso, o.titulo AS titulo, e.razonSocial AS razonSocial, CONCAT (p.nombre, ', ',p.apellido) AS pasante ";
		$sql .= " FROM oferta o, empresa e, pasante p, pasantia pa, tutoracademico ta, lapsoacademico l ";
		$sql .= " WHERE e.id=o.empresa_id AND e.id= pa.empresa_id AND pa.tutoracademico_id=ta.id ";
		$sql .= " AND p.id=pa.pasante_id AND l.id= pa.lapsoacademico_id AND ta.id='$idTutorAcademico' ";
		$sql .= " ORDER BY l.lapso, razonSocial";
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			if ($row['lapsoId']!=$lapsoAnterior){
				if ($i!=-1){
					$aux[$i]['datos'] = $auxDatos;
					$auxDatos=array();
				}	
				$i++;
				$aux[$i]['lapso'] = $row['lapso'];
				$aux[$i]['fchInicio'] = Util::cambiarFechaDMY($row['fchInicio']);
				$aux[$i]['fchFin'] =  Util::cambiarFechaDMY($row['fchFin']);
			}
			$auxDatos[] = array("titulo"=>utf8_encode($this->adecuarTexto($row['titulo'])),
								"razonSocial"=>utf8_encode($row['razonSocial']),
								"pasante"=>utf8_encode($row['pasante']));
			$lapsoAnterior=$row['lapsoId'];
		}
		if ($auxDatos){
			$aux[$i]['datos'] = $auxDatos;
		}
		return $aux;
	}
	
	public function getTextoEstatus($estatus){
		$estatus= strtoupper($estatus);
		$texto='';
		switch ($estatus) {
			case 'P':
				$texto='Pre-Inscrita';
				break;
			case 'I':
				$texto='Inscrita';
				break;
			case 'D':
				$texto='En desarrollo';
				break;
			case 'F':
				$texto='Finalizada';
				break;
			case 'S':
				$texto='Suspendida';
				break;
			}
		return $texto;
	}
	
	public function getDetallePasantias($id) {
		$aux = array();
		$sql = " SELECT p.id as id,razonSocial, lapso,titulo,cedula,nombre,apellido,p.fchInicioEst AS fchInicioEst ,p.fchFinEst AS fchFinEst,mp.descripcion AS modalidadPasantia, tp.descripcion AS tipoPasantia";
		$sql .= " FROM pasante pa,modalidadPasantia mp, tipoPasantia tp, lapsoacademico la,pasantia p ";
		$sql .= "  LEFT JOIN empresa e ON (e.id=p.empresa_id) ";
		$sql .= "  LEFT JOIN oferta o ON (oferta_id=o.id ) ";
		$sql .= " WHERE  pasante_id=pa.id  AND  p.id=".$id;
		$sql .= " AND mp.id=p.modalidadPasantia_id AND tp.id=p.tipoPasantia_id  AND la.id=p.lapsoacademico_id ";
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux['id'] = $row['id'];
			$aux['titulo'] = utf8_encode($row['titulo']);
			$aux['lapso'] = utf8_encode($row['lapso']);
			$aux['razonSocial'] = utf8_encode($row['razonSocial']);
			$aux['cedula'] = utf8_encode($row['cedula']);
			$aux['nombre'] = utf8_encode($row['nombre']);
			$aux['apellido'] = utf8_encode($row['apellido']);
			$aux['fchInicioEst'] = Util::cambiarFechaDMY($row['fchInicioEst']);
			$aux['fchFinEst'] =  Util::cambiarFechaDMY($row['fchFinEst']);
			$aux['modalidad'] = utf8_encode($row['modalidadPasantia']);
			$aux['tipoPasantia'] = utf8_encode($row['tipoPasantia']);
		}
		return  $aux;
	}
}
?>