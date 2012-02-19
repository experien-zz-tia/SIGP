<?php
include_once 'Utils/Util.php';
/**
 * @author Robert A
 *
 */
class Pasanteevaluacion extends ActiveRecord {


	protected $id;
	protected $aspectoEvaluacion_id;
	protected $pasante_id;
	protected $nota;


	public function getId() { return $this->id; }
	public function getAspectoEvaluacion_id() { return $this->aspectoEvaluacion_id; }
	public function getPasante_id() { return $this->pasante_id; }
	public function getNota() { return $this->nota; }
	public function setId($x) { $this->id = $x; }
	public function setAspectoEvaluacion_id($x) { $this->aspectoEvaluacion_id = $x; }
	public function setPasante_id($x) { $this->pasante_id = $x; }
	public function setNota($x) { $this->nota = $x; }
	
	
	public function getDetalleNotas($idPasante,$idTipoTutor='*') {
		$aux = array();
		$i=0;
		$sql  = " SELECT  e.descripcion AS evalDescripcion, e.id AS evaluacionId,ap.id AS aspectoId, IFNULL(pe.nota,0) AS nota, item, ap.descripcion AS descripcion ";
		$sql .= " FROM  evaluacion e , ";
		$sql .= " aspectoevaluacion ap LEFT JOIN pasanteevaluacion pe ON ( pe.pasante_id='$idPasante' AND ap.id=pe.aspectoEvaluacion_id) ";
		$sql .= " WHERE  e.id=ap.evaluacion_id  AND "; 
		$sql .= " e.tipoTutor=( CASE WHEN '$idTipoTutor' = '*' THEN e.tipoTutor ELSE '$idTipoTutor' END) ";
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$i]['evalDescripcion'] = utf8_encode($row['evalDescripcion']);
			$aux[$i]['evaluacionId'] = $row['evaluacionId'];
			$aux[$i]['aspectoId'] = $row['aspectoId'];
			$aux[$i]['nota'] = $row['nota'];
			$aux[$i]['item'] = utf8_encode($row['item']);
			$aux[$i]['descripcion'] = utf8_encode($row['descripcion']);
			$i++;
		
		}
		return $aux;
		
	}
	
	/**
	 * Registra/ actualiza la nota de un aspecto de evaluacion del pasante
	 * @param int $pasanteId
	 * @param int $aspectoId
	 * @param int $nota
	 * @return boolean
	 */
	public function registrarNota($pasanteId, $aspectoId, $nota) {
		$flag = false;
		$registroNota=$this->findFirst("aspectoEvaluacion_id='$aspectoId' AND pasante_id='$pasanteId'");
		if ($registroNota){
			$registroNota->setNota($nota);
			$flag=$registroNota->update();
		}else{
			$this->setPasante_id($pasanteId);
			$this->setAspectoEvaluacion_id($aspectoId);
			$this->setNota($nota);
			$flag = $this->save();
		}
		return $flag;
	}
	
	/**
	 * Cuenta los pasantes que al menos tienen un registro de evaluacion en alguna de las diferentes categorias. 
	 * Puede contar pasantes que no hayan sido totalmente evaluados.
	 * @param int $idLapso
	 */
	public function contarPasantesEvaluadosPorLapso($idLapso) {
		$sql  = " SELECT COUNT(DISTINCT pasante_id) AS registros ";
		$sql .= " FROM pasanteevaluacion pe,pasante p ";
		$sql .= "  WHERE pe.pasante_id=p.id AND p.lapsoAcademico_id='$idLapso' ";
		$db = Db::rawConnect();
	 	$result = $db->query($sql);
	 	if ($row = $db->fetchArray($result)){
			$cantidad = $row['registros'];
	 	}
	 	return $cantidad;
		
	}

	

}

?>
