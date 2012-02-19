<?php
include_once 'Utils/Util.php';
class Lapsoacademico extends ActiveRecord{
	protected $id;
	protected $fchInicio;
	protected $fchFin; 
	protected $lapso; 
	protected $decanato_id;
	protected $estatus;
	
	protected function initialize(){
	
	}
	public function getId() { 
		return $this->id; 
	} 
	public function getFchInicio() { 
		return $this->fchInicio; 
	} 
	public function getFchFin() { 
		return $this->fchFin; 
	} 
	public function getLapso() { 
		return $this->lapso; 
	} 
	public function getDecanato_id() { 
		return $this->decanato_id; 
	} 
	public function getEstatus() { 
		return $this->estatus; 
	} 
	public function setId($x) { 
		$this->id = $x; 
	} 
	public function setFchInicio($x) {
		$this->fchInicio = $x; 
	} 
	public function setFchFin($x) {
		$this->fchFin = $x; 
	} 
	public function setLapso($x) { 
		$this->lapso = $x; 
	} 
	public function setDecanato_id($x) {
		$this->decanato_id = $x;
	 } 
	public function setEstatus($x) { 
		$this->estatus = $x; 
	} 
	
	public function getLapsoActivobyDecanato($idDecanato) {
		$resultado=array();
		$lapso = $this->findFirst("decanato_id='$idDecanato' AND estatus='A'");
		if ($lapso){
			$resultado['id']=$lapso->getId();
			$resultado['fchInicio']=Util::cambiarFechaDMY($lapso->getFchInicio());
			$resultado['fchFin']=Util::cambiarFechaDMY($lapso->getFchFin());
			$resultado['decanato_id']=$lapso->getDecanato_id();
			$resultado['lapso']=$lapso->getLapso();
		}
			
		return $resultado;
	}
	
	
/**
	 * Retorna array con el listado de los lapsos academicos no eliminados de un decanato
	 * @param int $start
	 * @param int $limit
	 * @param int $estatus
	 * @return  <multitype:, string>
	 */
	public function getLapsosAcademicosbyDecanato($start,$limit,$decanatoId) {
		$total = $this->count("estatus != 'E'  AND decanato_id=".$decanatoId);
		$aux = array();
		$i=0;
		$sql= " SELECT id, fchInicio, fchFin, lapso, decanato_id, estatus ";
		$sql.= " FROM lapsoAcademico ";
		$sql.= " WHERE decanato_id='$decanatoId' AND estatus!='E' ";
		$sql.= " ORDER BY fchInicio DESC";
		$sql.= " LIMIT ".$start.",".$limit." ";
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$i]['id'] = $row['id'];
			$aux[$i]['lapso'] =  utf8_encode($row['lapso']);
			$aux[$i]['estatus'] = utf8_encode($this->relacionarEstatus($row['estatus']));
			$aux[$i]['fchInicio'] = Util::cambiarFechaMDY($row['fchInicio']);
			$aux[$i]['fchFin'] = Util::cambiarFechaMDY($row['fchFin']);
			$i++;
		}
		return array('total'=>$total,
					'resultado' => $aux);
	}
	
	private function relacionarEstatus($estatus) {
		$texto='';
		switch (strtoupper($estatus)) {
			case 'A':
				$texto= 'Activo';
			break;
			case 'E':
				$texto= 'Eliminado';
			break;
			case 'R':
				$texto= 'Registrado';
			break;
			case 'F':
				$texto= 'Finalizado';
			break;
			
		}
		return $texto;
	}
	public function generarLapso($anio,$decanato_id) {
		$nro= $this->count("lapso LIKE '%$anio%' AND decanato_id=$decanato_id");
		$lapso=$anio.'-'.($nro+1);
		return $lapso;
		
	}
	public function guardar($lapso,$fechaInicio,$fechaFin,$decanato){
		$flag=false;
		$this->setLapso($lapso);
		$this->setFchInicio(Util::cambiarFechaMDYtoYMD($fechaInicio, '/'));
		$this->setFchFin(Util::cambiarFechaMDYtoYMD($fechaFin,'/'));
		$this->setDecanato_id($decanato);
		$this->setEstatus('R');
		$flag=$this->save();
		return $flag;
	}
	
	public function hayLapsoActivobyDecanato($decanato_id) {
		$nro= $this->count("estatus='A' AND decanato_id=$decanato_id");
		return $nro;
		
	}
	
	public function activarLapso($idLapso) {
		$success=false;
		$lapso = $this->findFirst("id='$idLapso'");
		if ($lapso){
			$lapso->setEstatus('A');
			$success = $lapso->update();
		}
		return $success;
	}
	public function getLapsoById($id) {
		$resultado=array();
		$lapso = $this->findFirst("id='$id'");
		if ($lapso){
			$resultado['id']=$lapso->getId();
			$resultado['lapso']=$lapso->getLapso();
			$resultado['fchInicio']=Util::cambiarFechaDMY($lapso->getFchInicio());
			$resultado['fchFin']=Util::cambiarFechaDMY($lapso->getFchFin());
			$resultado['estatus']=$lapso->getEstatus();
		}
		return $resultado;
	
	}
	
	public function modificarLapso($idLapso,$fechaInicio,$fechaFin) {
		$success=false;
		$lapso = $this->findFirst("id='$idLapso'");
		if ($lapso){
			$lapso->setFchInicio(Util::cambiarFechaMDYtoYMD($fechaInicio,'/'));
			$lapso->setFchFin(Util::cambiarFechaMDYtoYMD($fechaFin,'/'));
			$success = $lapso->update();
		}
		return $success;
	}
	
	public function eliminarLapso($idLapso) {
		$success=false;
		$lapso = $this->findFirst("id='$idLapso'");
		if ($lapso){
			$lapso->setEstatus('E');
			$success = $lapso->update();
		}
		return $success;
	}
	
	public function finalizarLapso($idLapso) {
		$success=false;
		$lapso = $this->findFirst("id='$idLapso'");
		if ($lapso){
			$lapso->setEstatus('F');
			$success = $lapso->update();
		}
		return $success;
	}
}
?>