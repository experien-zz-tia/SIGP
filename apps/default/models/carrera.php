<?php
class Carrera extends ActiveRecord{
	protected $id;
	protected $decanato_id;
	protected $nombre;
	protected $regimen;
	protected $duracion;
	protected $estatus;
	//-----------------------------------------------------------------------------------------
	protected function inicializate(){
		$this->belongsTo('decanato');
	}
	//-----------------------------------------------------------------------------------------
	public function setId($valor){
		$this->id = $valor;
	}
	public function getId(){
		return $this->id;
	}
	//-----------------------------------------------------------------------------------------
	public function setDecanatoId($valor){
		$this->decanato_id = $valor;
	}
	public function getDecanatoId(){
		return $this->decanato_id;
	}
	//-----------------------------------------------------------------------------------------
	public function setNombre($valor){
		$this->nombre = $valor;
	}
	public function getNombre(){
		return $this->nombre;
	}
	//-----------------------------------------------------------------------------------------
	public function setRegimen($valor){
		$this->regimen = $valor;
	}
	public function getRegimen(){
		return $this->regimen;
	}
	//-----------------------------------------------------------------------------------------
	public function setDuracion($valor){
		$this->duracion = $valor;
	}
	public function getDuracion(){
		return $this->duracion;
	}
	//-----------------------------------------------------------------------------------------
	public function setEstatus($valor){
		$this->estatus = $valor;
	}
	public function getEstatus(){
		return $this->estatus;
	}
	//-----------------------------------------------------------------------------------------
	public function getCarreras(){
		$auxCarreras = array();
		$i = 0;
		$carreras = $this->find("order: nombre");
		foreach ($carreras as $carrera){
			$auxCarreras[$i]['id'] = $carrera->id;
			$auxCarreras[$i]['decanato_id'] = $carrera->decanato_id;
			$auxCarreras[$i]['nombre'] = $carrera->nombre;
			$auxCarreras[$i]['regimen'] = $carrera->regimen;
			$auxCarreras[$i]['duracion'] = $carrera->duracion;
			$auxCarreras[$i]['estatus'] = $carrera->estatus;

			$i++;
		}
	}
	//-----------------------------------------------------------------------------------------
	public function getCarrerasbyDecanato($decan){
		$auxCarrerasxDecanato = array();
		$i = 0;
		$carrerasXdecanato = $this->find("decanato_id = '$decan'", "order: nombre");
		foreach ($carrerasXdecanato as $carreraXdecanato){
			$auxCarrerasxDecanato[$i]['id'] = $carreraXdecanato->id;
			$auxCarrerasxDecanato[$i]['decanato_id'] = $carreraXdecanato->decanato_id;
			$auxCarrerasxDecanato[$i]['nombre'] = utf8_encode($carreraXdecanato->nombre);
			$auxCarrerasxDecanato[$i]['regimen'] = $carreraXdecanato->regimen;
			$auxCarrerasxDecanato[$i]['duracion'] = $carreraXdecanato->duracion;
			$auxCarrerasxDecanato[$i]['estatus'] = $carreraXdecanato->estatus;
			$i++;
		}
		return $auxCarrerasxDecanato;
	}
	//-----------------------------------------------------------------------------------------
	public function getSemestres($decan){
		$auxCarrerasxDecanato = array();
		
		$tam = 0;
		$carrerasXdecanato = $this->find("id = '$decan'");
		foreach ($carrerasXdecanato as $carreraXdecanato){
			$tam = $carreraXdecanato->duracion;
			for ($j = 0; $j < $tam; $j++) {
				$auxCarrerasxDecanato[$j]['semestre'] = $j+1;
			}
		}
		return $auxCarrerasxDecanato;
	}
	//-----------------------------------------------------------------------------------------
	public function getCarrerasbyNombre($nomb){
		$id = 0;
		$carrerasXNombre = $this->find("nombre = '$nomb'", "order: nombre");
		$i = 0;
		foreach ($carrerasXNombre as $carreraXnombre){
			$id = $carreraXnombre->id;
			$i++;
		}
		return $id;
	}

	//-----------------------------------------------------------------------------------------
}

?>
