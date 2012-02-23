<?php
include_once 'Utils/Util.php';
class Solicitudtutoracademico extends ActiveRecord{
	protected $id;
	protected $pasante_id;
	protected $tutorAcademico_id;
	protected $dependencia_id;
	protected $fchSolicitud;
	protected $fchRespuesta;
	protected $estatus;

	protected function initialize(){

	}

	public function getId() {
		return $this->id;
	}
	public function getPasante_id() {
		return $this->pasante_id;
	}
	public function getTutorAcademico_id() {
		return $this->tutorAcademico_id;
	}
	public function getFchSolicitud() {
		return $this->fchSolicitud;
	}
	public function getFchRespuesta() {
		return $this->fchRespuesta;
	}
	public function getEstatus() {
		return $this->estatus;
	}
	public function getDependencia_id() {
		return $this->dependencia_id;
	}
	public function setDependencia_id($x) {
		$this->dependencia_id = $x;
	}
	public function setId($x) {
		$this->id = $x;
	}
	public function setPasante_id($x) {
		$this->pasante_id = $x;
	}
	public function setTutorAcademico_id($x) {
		$this->tutorAcademico_id = $x;
	}
	public function setFchSolicitud($x) {
		$this->fchSolicitud = $x;
	}
	public function setFchRespuesta($x) {
		$this->fchRespuesta = $x;
	}
	public function setEstatus($x) {
		$this->estatus = $x;
	}

	public function obtenerTutorAsignado($idPasante) {
		$resultado=array();
		$solicitud = $this->findFirst("pasante_id='$idPasante' AND estatus='A'");
		if ($solicitud){
			$resultado['idTutorAcademico']=$solicitud->getTutorAcademico_id();
		}
			
		return $resultado;
	}
	public function getSolicitudesTutor($pasanteId,$start='*',$limit='*') {
		$aux = array();
		$i=0;
		$tutor = new Solicitudtutoracademico();
		$total = $tutor->count("pasante_id='$pasanteId' AND estatus!='A'");
		$sql  = " SELECT s.id AS id, nombre, apellido, cargo, IFNULL(descripcion,'-') AS departamento, fchSolicitud, fchRespuesta, ";
		$sql .= " s.estatus AS estatus ";
		$sql .= " FROM solicitudtutoracademico s, ";
		$sql .= " tutorAcademico t LEFT JOIN departamento d ON (departamento_id=d.id) ";
		$sql .= " WHERE pasante_id='$pasanteId' AND tutorAcademico_id=t.id AND s.estatus!='E' ";
		$sql .= " ORDER BY fchSolicitud DESC, fchRespuesta DESC";
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
			$aux[$i]['fchSolicitud'] = Util::cambiarFechaMDY($row['fchSolicitud']);
			$aux[$i]['fchRespuesta'] = Util::cambiarFechaMDY($row['fchRespuesta']);
			$aux[$i]['estatus'] = utf8_encode($this->adecuarTexto($this->asociarEstatus($row['estatus'])));
			$i++;
		}
		return array('total'=>$total,
					'resultado' => $aux);

	}

	/**
	 * Retorna el texto asociado a un estatus en particular de una solicitud
	 * @param string $estatus
	 * @return string
	 */
	private function asociarEstatus($estatus) {
		$aux='No catalogado.';
		switch (strtoupper($estatus)) {
			case 'A':
				$aux='Asignado.';
				break;
			case 'E':
				$aux='Eliminado.';
				break;
			case 'R':
				$aux='Rechazado.';
				break;
			case 'P':
				$aux='En espera.';
				break;
			case 'C':
				$aux='Cancelada.';
				break;
		}
		return $aux;
	}

	public function contarSolicitudesPasante($idPasante){
		$nro=0;
		$nro= $this->count("pasante_id='$idPasante' AND estatus='P'");
		return $nro;
	}
	public function contarSolicitudesTutor($idTutor){
		$nro=0;
		$nro= $this->count("tutorAcademico_id='$idTutor' AND estatus='P'");
		return $nro;

	}

	public function solicitarTutor($idTutor,$idPasante){
		$success= false;
		$this->setPasante_id($idPasante);
		$this->setTutorAcademico_id($idTutor);
		$this->setFchSolicitud(date("Y/m/d"));
		$this->setEstatus('P');
		$success = $this->save();
		return  $success;

	}
	/**
	 * Indica si el pasante tiene registrada una solicitud previa ( en estado pendiente- sin responder-) a un tutor dado
	 * @param int $idPasante
	 * @param int $idTutor
	 */
	public function existeSolicitudPrevia($idPasante,$idTutor) {
		$flag=false;
		$solicitud = $this->findFirst("pasante_id='$idPasante'AND tutorAcademico_id='$idTutor'  AND estatus='P'");
		if ($solicitud){
			$flag=true;
		}
		return $flag;
	}


	public function getSolicitudesbyTutor($idTutor,$start='*',$limit='*'){
		$aux = array();
		$i=0;
		$sql   = " SELECT s.id AS id,p.id AS idPasante, cedula,p.nombre, apellido, c.nombre AS carrera, fchSolicitud ";
		$sql  .= " FROM solicitudtutoracademico s,pasante p, carrera c ";
		$sql  .= " WHERE c.id=p.carrera_id AND tutorAcademico_id='$idTutor' AND s.estatus='P' ";
		$sql  .= " ORDER BY fchSolicitud DESC  ";
		if ($start!='*' && $limit!='*'){
			$sql .= " LIMIT ".$start.",".$limit." ";
		}
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$i]['id'] = $row['id'];
			$aux[$i]['idPasante'] = $row['idPasante'];
			$aux[$i]['cedula'] = $row['cedula'];
			$aux[$i]['nombre'] = utf8_encode($this->adecuarTexto($row['nombre']));
			$aux[$i]['apellido'] = utf8_encode($this->adecuarTexto($row['apellido']));
			$aux[$i]['carrera'] = utf8_encode($this->adecuarTexto($row['carrera']));
			$aux[$i]['fchSolicitud'] = Util::cambiarFechaMDY($row['fchSolicitud']);
			$i++;
		}
		return array('resultado' => $aux);
	}

	public function rechazar($id) {
		$success=false;
		$solicitud = $this->findFirst("id='$id'");
		if ($solicitud){
			$solicitud->setFchRespuesta(date("Y/m/d"));
			$solicitud->setEstatus('R');
			$success=$solicitud->update();
		}
		return $success;
	}

	public function getSolicitudbyId($id) {
		$resultado=array();
		$solicitud = $this->findFirst("id='$id'");
		if ($solicitud){
			$resultado['id']=$solicitud->getId();
			$resultado['tutorAcademicoId']=$solicitud->getTutorAcademico_id();
			$resultado['pasanteId']=$solicitud->getTutorAcademico_id();
			$resultado['fchSolicitud']=Util::cambiarFechaDMY($solicitud->getFchSolicitud());
			$resultado['fchRespuesta']=Util::cambiarFechaDMY($solicitud->getFchRespuesta());
			$resultado['estatus']=$solicitud->getEstatus();
		}
			
		return $resultado;
	}


	public function aceptar($id, $pasanteId) {
		$success=true;
		$solicitudes = $this->find("pasante_id='$pasanteId'");
		foreach ($solicitudes as $solicitud){
			if ($solicitud->getId()==$id){
				$solicitud->setEstatus('A');
			}else{
				$solicitud->setEstatus('R');
			}
			$solicitud->setFchRespuesta(date("Y/m/d"));
			$flag=$solicitud->update();
			if (!$flag){
				$success=false;
			}
		}
		return $success;
	}

	public function cancelar($id) {
		$success=false;
		$solicitud = $this->findFirst("id='$id'");
		if ($solicitud){
			if ($solicitud->getEstatus()=='P'){
				$solicitud->setEstatus('C');
				$success=$solicitud->update();
			}
		}
		return $success;
	}
	public function perteneceSolicitud($idPasante,$id) {
		$flag=false;
		$solicitud = $this->findFirst("pasante_id='$idPasante'AND id='$id' ");
		if ($solicitud){
			$flag=true;
		}
		return $flag;
	}

	public function retirarSolicitudes($pasanteId) {
		$success = true;
		$resp = array();
		$solicitudes = $this->find("pasante_id='$pasanteId' ");
		$pasante = new Pasante();
		$resp = $pasante->buscarPasanteId($pasante->buscarCedulaById($pasanteId));

		foreach ($solicitudes as $solicitud){
			if ($solicitud->getEstatus() == 'P'){
				$correo = new Correo();
				$body ='Notificación. <BR/>
			  	Le informamos que el estudiante '.$resp['datos']['nombre'].' '.$resp['datos']['apellido'].' ya no se encuentra registrado
			  	como pasante por lo que la solicitud realizada a su persona el '.$solicitud->getFchSolicitud().' será descartada del sistema. Gracias por su atención.<BR/>';
				$correo->enviarCorreo($resp['datos']['email'], 'Eliminación de Pasante', $body);
			}
			if ($solicitud->getEstatus() == 'R'){
				$correo = new Correo();
				$body ='Notificación. <BR/>
			  	Le informamos que el estudiante '.$resp['datos']['nombre'].' '.$resp['datos']['apellido'].' ya no se encuentra registrado
			  	como pasante por lo que no debe realizar evaluaciones relacionadas. Cualquier inquietud puede escribir a coord.pasantias@gmail.com. Gracias por su atención.<BR/>';
				$correo->enviarCorreo($resp['datos']['email'], 'Eliminación de Pasante', $body);
			}
			$solicitud->setEstatus('E');
			$success = $solicitud->update();
		}
		return $success;
	}

}
?>