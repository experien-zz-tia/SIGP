<?php
include_once 'Utils/Util.php';
/**
 * @author Robert A
 *
 */
class Aspectoevaluacion extends ActiveRecord {


	protected $id;
	protected $evaluacion_id;
	protected $item;
	protected $descripcion;
	protected $porcentaje;
	protected $estatus;

	public function getId() { return $this->id; }
	public function getEvaluacion_id() { return $this->evaluacion_id; }
	public function getItem() { return $this->item; }
	public function getDescripcion() { return $this->descripcion; }
	public function getPorcentaje() { return $this->porcentaje; }
	public function getEstatus() { return $this->estatus; }
	public function setId($x) { $this->id = $x; }
	public function setEvaluacion_id($x) { $this->evaluacion_id = $x; }
	public function setItem($x) { $this->item = $x; }
	public function setDescripcion($x) { $this->descripcion = $x; }
	public function setPorcentaje($x) { $this->porcentaje = $x; }
	public function setEstatus($x) { $this->estatus = $x; }
}

?>
