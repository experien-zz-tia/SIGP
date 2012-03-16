<?php
class Carrera extends ActiveRecord{
	protected $id;
	protected $decanato_id;
	protected $nombre;
	protected $regimen;
	protected $duracion;
	protected $estatus;
	protected $plan;
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
	public function setDecanato_id($valor){
		$this->decanato_id = $valor;
	}
	public function getDecanato_id(){
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
	public function setPlan($valor){
		$this->plan = $valor;
	}
	public function getPlan(){
		return $this->plan;
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
			$auxCarreras[$i]['plan'] = $carrera->plan;
			$auxCarreras[$i]['estatus'] = $carrera->estatus;

			$i++;
		}
		return $auxCarreras;
	}
	//-----------------------------------------------------------------------------------------
	public function eliminar($vId) {
		$success = false;
		$carrera = $this->findFirst("id = ".$vId." AND estatus = 'A'");
		if ($carrera != null){
			$carrera->setEstatus('E');
			$success = $carrera->update();
		}
		return $success;
	}
	//-----------------------------------------------------------------------------------------
	public function getCarrerasFull($vId){
		$auxCarreras = array();
		$i = 0;
		if ($vId == '-1' or $vId == null){
			$carreras = $this->find("estatus = 'A' ","order: nombre");
		} else {
			$carreras = $this->find("decanato_id = '$vId'", "order: nombre");
		}

		foreach ($carreras as $carrera){
			$auxCarreras[$i]['id'] = $carrera->id;
			$auxCarreras[$i]['decanato_id'] = $carrera->decanato_id;
			$decanato = new Decanato();
			$dec = $decanato->findFirst("id = ".$carrera->decanato_id);
			if ($dec != null){
				$auxCarreras[$i]['decanato'] = utf8_encode($dec->getNombre());
			}
			$auxCarreras[$i]['nombre'] = utf8_encode($carrera->nombre);
			$auxCarreras[$i]['regimen_id'] = $carrera->regimen;
			if ($carrera->regimen == 'N'){
				$auxCarreras[$i]['regimen'] = utf8_encode('Nocturno');
			} else if ($carrera->regimen == 'D'){
				$auxCarreras[$i]['regimen'] = utf8_encode('Diurno');
			}
			$auxCarreras[$i]['duracion'] = $carrera->duracion;
			$auxCarreras[$i]['plan_id'] = $carrera->plan;
			if ($carrera->plan == 'A'){
				$auxCarreras[$i]['plan'] = utf8_encode('Anual');
			} else if ($carrera->plan == 'S'){
				$auxCarreras[$i]['plan'] = utf8_encode('Semestral');
			} else if ($carrera->plan == 'T'){
				$auxCarreras[$i]['plan'] = utf8_encode('Trimestral');
			}
			$auxCarreras[$i]['estatus'] = $carrera->estatus;

			$i++;
		}

		return $auxCarreras;
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
			$auxCarrerasxDecanato[$i]['plan'] = $carreraXdecanato->plan;
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
	public function getDuracionCarrera(){
		$arrDuracion = array();
		$tam = 10;
		for ($j = 0; $j < $tam; $j++) {
			$arrDuracion[$j]['id'] = $j+1;
			$arrDuracion[$j]['nombre'] = ($j+1)."";
		}
		return $arrDuracion;
	}
	//-----------------------------------------------------------------------------------------
	public function getRegimenCarrera(){
		$arrRegimen = array();
		$arrRegimen[0]['id'] = 'D';
		$arrRegimen[0]['nombre'] = 'Diurno';
		$arrRegimen[1]['id'] = 'N';
		$arrRegimen[1]['nombre'] = 'Nocturno';
		return $arrRegimen;
	}
	//-----------------------------------------------------------------------------------------
	public function getPlanCarrera(){
		$arrPlan = array();
		$arrPlan[0]['id'] = 'A';
		$arrPlan[0]['nombre'] = utf8_encode('Anual (Año)');
		$arrPlan[1]['id'] = 'S';
		$arrPlan[1]['nombre'] = utf8_encode('Semestral (Semestre)');
		$arrPlan[2]['id'] = 'T';
		$arrPlan[2]['nombre'] = utf8_encode('Trimestral (Trimestre)');
		return $arrPlan;
	}
	//-----------------------------------------------------------------------------------------
	public function registrar($vDecanatoId, $vNombre, $vRegimen, $vDuracion, $vPlan) {
		$success = false;
		$this->setDecanato_id($vDecanatoId);
		$this->setNombre($vNombre);
		$this->setRegimen($vRegimen);
		$this->setDuracion($vDuracion);
		$this->setPlan($vPlan);
		$this->setEstatus('A');
		$success = $this->save();

		return $success;
	}
	//-----------------------------------------------------------------------------------------
	public function actualizar($vId, $vDecanatoId, $vNombre, $vRegimen, $vDuracion, $vPlan){
		$success = false;
		$carrera = $this->findFirst("id = ".$vId);
		if($carrera != null){
			$carrera->setDecanato_id($vDecanatoId);
			$carrera->setNombre($vNombre);
			$carrera->setRegimen($vRegimen);
			$carrera->setDuracion($vDuracion);
			$carrera->setPlan($vPlan);
			$carrera->setEstatus('A');
			$success = $carrera->update();
		}
		return $success;
	}
	//-----------------------------------------------------------------------------------------
	public function buscar($id){
		$arrCarrera = array();
		$i = 0;
		$carrera = $this->find("id = ".$id." AND estatus = 'A'");
		$arrCarrera['success'] = false;
		$resp['datos'] = array();
		foreach($carrera as $carr){
			$arrCarrera['datos']['id'] = $carr->id;
			$arrCarrera['datos']['decanato_id'] = $carr->decanato_id;
			$arrCarrera['datos']['nombre'] = utf8_encode($carr->nombre);
			$arrCarrera['datos']['regimen'] = $carr->regimen;
			$arrCarrera['datos']['duracion'] = $carr->duracion;
			$arrCarrera['datos']['plan'] = utf8_encode($carr->plan);
			$arrCarrera['datos']['estatus'] = $carr->estatus;
			$arrCarrera['success'] = true;
			$i++;
		}
		return $arrCarrera;
	}
	//-----------------------------------------------------------------------------------------
}

?>
