<?php
include_once 'Utils/Util.php';
/**
 * Clase (Modelo) de la entidad oferta, mantiene la ifnromacion de las ofertas creadas por las empresas
 * para cubrir las vacantes de puestos laborales.
 * @author Robert Arrieche
 * @version 1.0
 */
class Oferta extends ActiveRecord {

	protected $id;
	protected $empresa_id;
	protected $fchPublicacion;
	protected $fchCierre;
	protected $titulo;
	protected $descripcion;
	protected $cupos;
	protected $vacantes;
	protected $tipoOferta;
	protected $areaPasantia_id;
	protected $fchActualizacion_in;
	protected $fchCreacion_at;
	protected $estatus;
	protected $fchInicioEst;
	protected $fchFinEst;

	protected function initialize(){
		$this->belongsTo("empresa_id","empresa","id");
		$this->belongsTo("areaPasantia_id","areapasantia","id");
	}
	public function getId() {
		return $this->id;
	}
	public function getEmpresa_id() {
		return $this->empresa_id;
	}
	public function getFchPublicacion() {
		return $this->fchPublicacion;
	}
	public function getFchCierre() {
		return $this->fchCierre;
	}
	public function getTitulo() {
		return $this->titulo;
	}
	public function getDescripcion() {
		return $this->descripcion;
	}
	public function getCupos() {
		return $this->cupos;
	}
	public function getVacantes() {
		return $this->vacantes;
	}
	public function getTipoOferta() {
		return $this->tipoOferta;
	}
	public function getAreaPasantia_id() {
		return $this->areaPasantia_id;
	}
	public function getFchActualizacion_in() {
		return $this->fchActualizacion_in;
	}
	public function getFchCreacion_at() {
		return $this->fchCreacion_at;
	}
	public function getEstatus() {
		return $this->estatus;
	}
	public function setId($x) {
		$this->id = $x;
	}
	public function setEmpresa_id($x) {
		$this->empresa_id = $x;
	}
	public function setFchPublicacion($x) {
		$this->fchPublicacion = $x;
	}
	public function setFchCierre($x) {
		$this->fchCierre = $x;
	}
	public function setTitulo($x) {
		$this->titulo = $x;
	}
	public function setDescripcion($x) {
		$this->descripcion = $x;
	}
	public function setCupos($x) {
		$this->cupos = $x;
	}
	public function setVacantes($x) {
		$this->vacantes = $x;
	}
	public function setTipoOferta($x) {
		$this->tipoOferta = $x;
	}
	public function setAreaPasantia_id($x) {
		$this->areaPasantia_id = $x;
	}
	public function setFchActualizacion_in($x) {
		$this->fchActualizacion_in = $x;
	}
	public function setFchCreacion_at($x) {
		$this->fchCreacion_at = $x;
	}
	public function setEstatus($x) {
		$this->estatus = $x;
	}
	public function getFchInicioEst() {
		return $this->fchInicioEst;
	}
	public function getFchFinEst() {
		return $this->fchFinEst;
	}
	public function setFchInicioEst($x) {
		$this->fchInicioEst = $x;
	}
	public function setFchFinEst($x) {
		$this->fchFinEst = $x;
	}
	/**
	 * Función para obtener las ofertas ( no cerradas) que posee una empresa dada.
	 * @param string $idEmpresa por defecto el comodin %
	 * @param string $start inicio del resultset a listar, por defecto '-1' => 0
	 * @param string $limit máximo del resulset a lista, por defecto sin limite( o definido por mysql)
	 * @return array asociativo con total de la consulta y resultado parcial (depende del start y limit)
	 */
	public function getOfertas($idEmpresa='%',$start='-1',$limit=-'1'){
		$aux = array();
		$i=0;

		$sqlTotal  = "SELECT  COUNT(o.id) as total ";
		$sqlTotal .= "FROM  oferta o ";
		$sqlTotal .= "WHERE o.estatus != 'E' ";
		$sqlTotal .= "AND empresa_id LIKE '".$idEmpresa."'";
		$db = Db::rawConnect();
		$total = $db->fetchOne($sqlTotal);
		$total = $total[0];
		if ($total!=0){
			$sql  = " SELECT razonSocial, e.id AS empresaId, o.id AS id, titulo, fchPublicacion,fchCreacion_at as fchCreacion, fchCierre, vacantes, ap.descripcion AS descripcion, ";
			$sql .=	" COUNT(p.oferta_id) AS postulados ";
			$sql .= " FROM empresa e, areapasantia ap, oferta o ";
			$sql .= " LEFT JOIN  postulacion p on (o.id=p.oferta_id AND p.estatus = 'P') ";
			$sql .= " WHERE o.estatus != 'E' AND ap.estatus = 'A' AND areapasantia_id = ap.id AND empresa_id LIKE '".$idEmpresa."'";
			$sql .= " AND empresa_id=e.id";
			$sql .= " GROUP BY o.id, p.oferta_id, titulo, fchPublicacion, fchCierre, vacantes, ap.descripcion " ;
			$sql .= " ORDER BY fchCreacion_at DESC ";
			if ($start!='-1' && $limit!='-1'){
				$sql .= " LIMIT ".$start.",".$limit." ";
			}
			$db = Db::rawConnect();
			$result = $db->query($sql);
			while($row = $db->fetchArray($result)){
				$aux[$i]['empresaId'] = $row['empresaId'];
				$aux[$i]['razonSocial'] = utf8_encode($this->adecuarTexto($row['razonSocial']));
				$aux[$i]['id'] = $row['id'];
				$aux[$i]['titulo'] = utf8_encode($this->adecuarTexto($row['titulo']));
				$aux[$i]['fchPublicacion'] = Util::cambiarFechaMDY($row['fchPublicacion']);
				$aux[$i]['fchCierre'] = Util::cambiarFechaMDY($row['fchCierre']);
				$aux[$i]['fchCreacion'] = Util::cambiarFechaMDY($row['fchCreacion']);
				$aux[$i]['vacantes'] = $row['vacantes'];
				$aux[$i]['postulados'] = $row['postulados'];
				$aux[$i]['area'] = utf8_encode($this->adecuarTexto($row['descripcion']));
				$i++;
			}
		}

		return array('total'=>$total,
					'resultado' => $aux);
	}


	/**
	 * Función para obtener las ofertas (publicadas y sin cerrar) que aun tienen disponibilidad
	 * @param string $start inicio del resultset a listar, por defecto '*' => 0
	 * @param string $limit máximo del resulset a lista, por defecto sin limite( o definido por mysql)
	 * @return array asociativo con total de la consulta y resultado parcial (depende del start y limit)
	 */
	public function getOfertasPasante($idEmpresa='%',$start='*',$limit='*'){
		$aux = array();
		$i=0;

		$sqlTotal  = " SELECT  IFNULL(COUNT(o.id),0) as total,o.cupos ";
		$sqlTotal  .= " FROM  oferta o ";
		$sqlTotal  .= " LEFT JOIN  postulacion p on (o.id=p.oferta_id AND p.estatus = 'P' ) ";
		$sqlTotal  .= " WHERE o.estatus = 'P' AND fchCierre>=CURDATE() ";
		$sqlTotal  .= " HAVING IF(o.cupos=0,TRUE,(o.cupos-COUNT(p.oferta_id)) >0 )";

		$db = Db::rawConnect();
		$total = $db->fetchOne($sqlTotal);
		$total = $total[0];
		if ($total!=0){
			$sql  = " SELECT razonSocial, e.id AS empresaId, o.id AS id, titulo, fchPublicacion , fchCierre, vacantes, ap.descripcion AS descripcion, o.tipoOferta AS tipoOferta, ";
			$sql .=	" COUNT(p.oferta_id) AS postulados, o.cupos AS cupos, IF(o.cupos=0,0,(o.cupos-COUNT(p.oferta_id)) ) AS disponible ";
			$sql .=	" FROM empresa e, areapasantia ap, oferta o ";
			$sql .=	" LEFT JOIN  postulacion p on (o.id=p.oferta_id AND p.estatus = 'P') ";
			$sql .=	" WHERE o.estatus = 'P' AND ap.estatus = 'A' AND areapasantia_id = ap.id ";
			$sql .=	" AND empresa_id=e.id AND  o.fchCierre>=CURDATE() ";
			$sql .=	" GROUP BY o.id, p.oferta_id, titulo, fchPublicacion, fchCierre, vacantes, ap.descripcion ";
			$sql .=	" HAVING IF(o.cupos=0,TRUE,(o.cupos-COUNT(p.oferta_id)) >0 ) ";
			$sql .=	" ORDER BY fchPublicacion DESC ";
			if ($start!='*' && $limit!='*'){
				$sql .= " LIMIT ".$start.",".$limit." ";
			}
			$db = Db::rawConnect();
			$result = $db->query($sql);
			while($row = $db->fetchArray($result)){
				$aux[$i]['empresaId'] = $row['empresaId'];
				$aux[$i]['razonSocial'] = utf8_encode($this->adecuarTexto($row['razonSocial']));
				$aux[$i]['id'] = $row['id'];
				$aux[$i]['titulo'] = utf8_encode($this->adecuarTexto($row['titulo']));
				$aux[$i]['fchPublicacion'] = Util::cambiarFechaMDY($row['fchPublicacion']);
				$aux[$i]['fchCierre'] = Util::cambiarFechaMDY($row['fchCierre']);
				$aux[$i]['vacantes'] = $row['vacantes'];
				$aux[$i]['cupos'] = $row['cupos'];
				$aux[$i]['disponible'] = $row['disponible'];
				$aux[$i]['postulados'] = $row['postulados'];
				$aux[$i]['area'] = utf8_encode($this->adecuarTexto($row['descripcion']));
				$aux[$i]['tipoOferta'] = utf8_encode($row['tipoOferta']);
				$i++;
			}
		}

		return array('total'=>$total,
					'resultado' => $aux);
	}



	public function publicarOferta($id){
		$success=false;
		$oferta = $this->findFirst("id='$id'");
		if ($oferta){
			$oferta->setEstatus('P');
			$oferta->setFchPublicacion(date("Y/m/d"));
			$success = $oferta->update();
		}
		return $success;

	}

	public function eliminarOferta($id){
		$success=false;
		$oferta = $this->findFirst("id='$id'");
		if ($oferta){
			$oferta->setEstatus('E');
			$success = $oferta->update();
		}
		return $success;

	}


	function guardarOferta($idEmpresa,$fechaC,$titulo,$descripcion,$cupos,$vacantes,$tipoOferta,$area,$estatus,$fechaI,$fechaF){
		$success=false;

		$this->setEmpresa_id($idEmpresa);
		if ($estatus=='saveAndPublish'){
			$this->setFchPublicacion(date("Y/m/d"));
			$this->setEstatus('P');
		}elseif ($estatus=='save'){
			$this->setEstatus('R');
		}
		$this->setFchInicioEst(Util::cambiarFechaMDYtoYMD($fechaI,'/'));
		$this->setFchFinEst(Util::cambiarFechaMDYtoYMD($fechaF,'/'));
		$this->setFchCierre(Util::cambiarFechaMDYtoYMD($fechaC,'/'));
		$this->setTitulo($titulo) ;
		$this->setDescripcion($descripcion) ;
		$this->setCupos($cupos);
		$this->setVacantes($vacantes) ;
		$this->setTipoOferta($tipoOferta);
		$this->setAreaPasantia_id($area);
		$success= $this->save();
		return $success;

	}

	/**
	 * Actualiza los campos de una oferta, si cumplen ciertas condiciones.
	 * @param int $idOferta
	 * @param date $fechaC
	 * @param string $titulo
	 * @param string $descripcion
	 * @param int $cupos
	 * @param int $vacantes
	 * @param string $tipoOferta
	 * @param int $area
	 * @return multitype:string boolean
	 */
	function actualizarOferta($idOferta, $fechaC,$titulo,$descripcion,$cupos,$vacantes,$tipoOferta,$area,$fechaI,$fechaF){
		$success=false;
		$errorMsj='';
		$oferta = $this->findFirst("id='$idOferta'");
		if ($oferta){
			$oferta->setTitulo($titulo) ;
			$oferta->setDescripcion($descripcion) ;
			$oferta->setAreaPasantia_id($area);
			$oferta->setTipoOferta($tipoOferta);
			$oferta->setVacantes($vacantes) ;
			$oferta->setFchInicioEst(Util::cambiarFechaMDYtoYMD($fechaI,'/'));
			$oferta->setFchFinEst(Util::cambiarFechaMDYtoYMD($fechaF,'/'));
			if (strtotime(Util::cambiarFechaMDYtoYMD($fechaC,'/'))< strtotime(date("Y/m/d"))){
				$errorMsj .='Fecha de Cierre: No puede ser inferior a la fecha actual.<BR>';
			}
			else {
				$oferta->setFchCierre(Util::cambiarFechaMDYtoYMD($fechaC,'/'));
			}
			$postulacion = new Postulacion();
			$nroPostulados = $postulacion->count("oferta_id='$idOferta' AND estatus='P'");
			if (($cupos !=0) and ($cupos < $nroPostulados)){
				$errorMsj .='M&aacute;ximo postulantes: No puede ser inferior a la cantidad actual de personas postuladas a la oferta ('.$nroPostulados.' persona(s)).<BR>';
			}else{
				$oferta->setCupos($cupos);
			}
			$success = $oferta->update();

		}
		return (array("success"=> $success, "errorMsj"=>utf8_encode($errorMsj)));

	}


	/**
	 * Obtiene la informacion de una oferta segun su id
	 * @param int $id
	 * @return array
	 */
	public function getOfertaById($id){
		$resultado=array();
		$oferta = $this->findFirst("id='$id'");
		if ($oferta){
			$resultado['id']=$oferta->getId();
			$resultado['empresaId']=$oferta->getEmpresa_id();
			$resultado['titulo']=utf8_encode($this->adecuarTexto($oferta->getTitulo()));
			$resultado['descripcion']=utf8_encode($this->adecuarTexto($oferta->getDescripcion()));
			$resultado['areaId']=$oferta->getAreaPasantia_Id();
			$resultado['tipoOferta']=$oferta->getTipoOferta();
			$resultado['vacantes']=$oferta->getVacantes();
			$resultado['cupos']=$oferta->getCupos();
			$resultado['fchCierre']=Util::cambiarFechaDMY($oferta->getFchCierre());
			$resultado['fchInicio']=Util::cambiarFechaDMY($oferta->getFchInicioEst());
			$resultado['fchCulminacion']=Util::cambiarFechaDMY($oferta->getFchFinEst());
		}
			
		return $resultado;
	}

	/**
	 * Obtiene la descripcion de la oferta asociada al id pasado.
	 * @param int $id
	 * @return string
	 */
	public function getDescripcionbyId($id) {
		$resultado='';
		$oferta = $this->findFirst("id='$id'");
		if ($oferta){
			$resultado['descripcion']=utf8_encode($this->adecuarTexto($oferta->getDescripcion()));
		}
		return $resultado;

	}

	/**
	 * Indica si la oferta esta publicada y su fecha de cierre no ha pasado
	 * @param int $idOferta
	 * @return boolean
	 */
	public function isAbierta($idOferta) {
		$flag=false;
		$oferta=$this->findFirst("id='$idOferta' AND estatus='P' AND fchCierre>=CURDATE()");
		if ($oferta){
			$flag=true;
		}
		return $flag;
	}

	public function getTipoOfertabyId($idOferta) {
		$tipo='';
		$oferta=$this->findFirst("id='$idOferta'");
		if ($oferta){
			$tipo=$oferta->getTipoOferta();
		}
		return $tipo;
	}
	public function contarRegistradosEnLapso($idLapso) {
		$cantidad=0;
		$sql = " SELECT COUNT(*) AS cantidad FROM lapsoacademico l, oferta o ";
		$sql .= " WHERE o.fchPublicacion BETWEEN l.fchInicio AND l.fchFin ";
		$sql .= " AND l.id='$idLapso' ";
		$db = Db::rawConnect();
		$result = $db->query($sql);
		if ($row = $db->fetchArray($result)){
			$cantidad = $row['cantidad'];
		}
		return $cantidad;
	}


	public function getOfertasReporte($inicio, $fin){
		$aux = array();
		$i=0;
		$sql  = " SELECT titulo,razonSocial, fchPublicacion , ";
		$sql  .= "  vacantes , o.tipoOferta AS tipoOferta ";
		$sql  .= " FROM empresa e, areapasantia ap, oferta o ";
		$sql  .= " WHERE  ap.estatus = 'A' AND areapasantia_id = ap.id ";
		$sql  .= " AND o.estatus = 'P' ";
		$sql  .= " AND empresa_id=e.id ";
		if ($inicio!='' && $fin!=''){
			$inicio=Util::cambiarFechaMDYtoYMD($inicio,'/');
			$fin=Util::cambiarFechaMDYtoYMD($fin,'/');
			$sql  .= " AND fchPublicacion BETWEEN '".$inicio."' AND '".$fin."' ";
		}
		$sql  .= " GROUP BY o.id, titulo, fchPublicacion, fchCierre, vacantes, ap.descripcion ";
		$sql  .= " ORDER BY fchPublicacion, razonSocial DESC";
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$i][0] = Util::cambiarFechaDMY($row['fchPublicacion']);
			$aux[$i][1] = utf8_encode($this->adecuarTexto($row['razonSocial']));
			$aux[$i][2] = utf8_encode($this->adecuarTexto($row['titulo']));
			$aux[$i][3] = $row['vacantes'];
			$aux[$i][4] = utf8_encode($row['tipoOferta']);
			$i++;
		}
		return  $aux;
	}

}
?>