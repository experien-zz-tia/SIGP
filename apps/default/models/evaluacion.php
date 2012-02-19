<?php
include_once 'Utils/Util.php';
/**
 * @author Robert A
 *
 */
class Evaluacion extends ActiveRecord {

	protected $id;
	protected $descripcion;
	protected $porcentaje;
	protected $tipoTutor;
	protected $estatus;


	public function getId() { return $this->id; }
	public function getDescripcion() { return $this->descripcion; }
	public function getPorcentaje() { return $this->porcentaje; }
	public function getTipoTutor() { return $this->tipoTutor; }
	public function getEstatus() { return $this->estatus; }
	public function setId($x) { $this->id = $x; }
	public function setDescripcion($x) { $this->descripcion = $x; }
	public function setPorcentaje($x) { $this->porcentaje = $x; }
	public function setTipoTutor($x) { $this->tipoTutor = $x; }
	public function setEstatus($x) { $this->estatus = $x; }
	
	
	/**
	 * Obtiene el numero de items de cada tipo de evaluacion y el porncejate total de ese tipo de evaluacion
	 * @return array
	 */
	public function getDetalleEvaluacion() {
		$aux = array();
				
		$sql  = " SELECT e.id  AS evaluacionId,COUNT(*) AS nroItems ,e.porcentaje AS porcentaje ";
		$sql .= " FROM evaluacion e, aspectoevaluacion ae ";
		$sql .= " WHERE  e.id=ae.evaluacion_id ";
		$sql .= " GROUP BY e.id ";
		$sql .= " ORDER BY e.id ASC";
		
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$row['evaluacionId']]['nroItems'] = $row['nroItems'];
			$aux[$row['evaluacionId']]['porcentaje'] = $row['porcentaje'];
		}
		
		return $aux;
		
	}
}

?>