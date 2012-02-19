<?php
include_once 'Utils/Util.php';
/**
 * Enter description here ...
 * @author Robert A
 *
 */
class Postulacion extends ActiveRecord {
	protected $id;
	protected $pasante_id;
	protected $oferta_id;
	protected $fchPostulacion_at;
	protected $estatus;
 

	public function getId() { 
		return $this->id; 
	}
	public function getPasante_id() { return $this->pasante_id; }
	public function getOferta_id() { return $this->oferta_id; }
	public function getFchPostulacion_at() { return $this->fchPostulacion_at; }
	public function getEstatus() { return $this->estatus; }
	public function setId($x) { $this->id = $x; }
	public function setPasante_id($x) { $this->pasante_id = $x; }
	public function setOferta_id($x) { $this->oferta_id = $x; }
	public function setFchPostulacion_at($x) { $this->fchPostulacion_at = $x; }
	public function setEstatus($x) { $this->estatus = $x; }
	
	
	/**
	 * Cuenta la cantidad de pasantes postulados a una oferta dada.
	 * @param int $idOferta
	 * @return int
	 */
	public function contarPostulaciones($idOferta){
		$nro=0;
		$nro=$this->count("oferta_id='$idOferta' AND estatus='P'");
		return $nro; 
	}
	
	/**
	 * Cuenta la cantidad de postulaciones que un  pasantes tiene activa
	 * @param int $idPasante
	 * @return int
	 */
	public function contarPostulacionesPasante($idPasante){
		$nro=0;
		$nro=$this->count("pasante_id='$idPasante' AND estatus='P'");
		return $nro; 
	}
	
	/**
	 * Registra la postulacion de un pasante a una oferta dada.
	 * @param int $idPasante
	 * @param int $idOferta
	 * @return boolean
	 */
	public function registrarPostulacion($idPasante, $idOferta){
		$flag=false;
		$this->setOferta_id($idOferta);
		$this->setPasante_id($idPasante);
		$this->setEstatus('P');
		$flag=$this->save();
		return $flag;
		
	}
	
	/**
	 * Busca la informacion de una postulacion
	 * @param int $idPasante
	 * @param int $idOferta
	 * @return array:
	 */
	public function buscarPostulacion($idPasante,$idOferta) {
		$aux = array(); 
		$postulacion = $this->findFirst("oferta_id='$idOferta' AND pasante_id='$idPasante' AND estatus='P'");
		if ($postulacion){
			$aux['id']=$postulacion->getId();
			$aux['ofertaId']=$postulacion->getOferta_id();
			$aux['pasanteId']=$postulacion->getPasante_id();
			$aux['estatus']=$postulacion->getEstatus();
		}
		return $aux;
	}
	
	/**
	 * Determina si un pasante esta postulado a una oferta dada
	 * @param int $idPasante
	 * @param int $idOferta
	 * @return boolean
	 */
	public function estaPostulado($idPasante,$idOferta) {
		$flag = false; 
		$postulacion = $this->findFirst("oferta_id='$idOferta' AND pasante_id='$idPasante' AND estatus='P'");
		if ($postulacion){
			$flag=true;
		}
		return $flag;
	}
	
	/**
	 * Lista las postulaciones realizadas a las ofertas de una empresa dada
	 * @param int $empresaId
	 * @return array 
	 */
	public function getPostulacionesbyEmpresa($empresaId) {
		$aux = array();
		$i=0;
		$sql = " SELECT po.id AS id, o.id AS ofertaId,p.id AS pasanteId ,cedula, p.nombre AS nombre, apellido, titulo, fchPostulacion_at AS fchPostulacion, c.nombre AS carrera ";
		$sql .= " FROM oferta o, pasante p, postulacion po, carrera c ";
		$sql .= " WHERE  o.empresa_id=$empresaId AND po.oferta_id=o.id AND p.id=po.pasante_id AND po.estatus='P' AND p.carrera_id=c.id ";
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$i]['id'] = $row['id'];
			$aux[$i]['ofertaId'] = $row['ofertaId'];
			$aux[$i]['pasanteId'] = $row['pasanteId'];
			$aux[$i]['cedula'] = $row['cedula'];
			$aux[$i]['nombre'] = utf8_encode($row['nombre']);
			$aux[$i]['apellido'] = utf8_encode($row['apellido']);
			$aux[$i]['titulo'] = utf8_encode($row['titulo']);
			$aux[$i]['carrera'] = utf8_encode($row['carrera']);
			$aux[$i]['fchPostulacion'] =Util::cambiarFechaMDY($row['fchPostulacion']);
			$i++;
		}
		return $aux;
	}
	
	/**
	 * Obtiene los datos de una postulacion dado su id
	 * @param int $id
	 * @return array:
	 */
	public function getPostulacionbyId($id) {
		$aux = array(); 
		$postulacion = $this->findFirst("id='$id'");
		if ($postulacion){
			$aux['id']=$postulacion->getId();
			$aux['ofertaId']=$postulacion->getOferta_id();
			$aux['pasanteId']=$postulacion->getPasante_id();
			$aux['fchPostulacion']=Util::cambiarFechaMDY($postulacion->getFchPostulacion_at());
			$aux['estatus']=$postulacion->getEstatus();
		}
		return $aux;
	}
	
	public function rechazar($id) {
		$flag=false;  
		$postulacion = $this->findFirst("id='$id'");
		if ($postulacion){
			$postulacion->setEstatus('R');
			$flag =$postulacion->update();
		}
		return $flag;
	}
	
	public function buscarDatosPostulacion($idPostulacion) {
		$aux = array();
		$sql =" SELECT fchCierre,fchInicioEst,fchFinEst, mp.descripcion AS modalidadPasantia, tp.descripcion AS tipoPasantia ";
		$sql .=" FROM postulacion po, pasante p, oferta o, modalidadPasantia mp, tipoPasantia tp";
		$sql .=" WHERE po.id='$idPostulacion' AND po.pasante_id=p.id AND po.oferta_id=o.id ";
		$sql .=" AND  mp.id=p.modalidadPasantia_id AND tp.id=p.tipoPasantia_id";
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux['modalidadPasantia'] = utf8_encode($row['modalidadPasantia']);
			$aux['tipoPasantia'] = utf8_encode($row['tipoPasantia']);
			$aux['fchCierre'] =Util::cambiarFechaDMY($row['fchCierre']);
			$aux['fchInicioEst'] =Util::cambiarFechaDMY($row['fchInicioEst']);
			$aux['fchFinEst'] =Util::cambiarFechaDMY($row['fchFinEst']);
		}
		return $aux;
	}
	
	/**
	 * Actualiza el estatus de la postulacion a Aceptada, el resto de las postulaciones pendientes se despostulan automaticamente.
	 * @param int $idPasante
	 * @param int $idPostulacion
	 */
	public function aprobarPostulacion($idPasante,$idPostulacion) {
		$aux = array(); 
		$postulaciones = $this->find("pasante_id='$idPasante'");
		foreach ($postulaciones as $postulacion){
			if($postulacion->getId()==$idPostulacion){
				$postulacion->setEstatus('A');
			}else{
				if ($postulacion->getEstatus()!='E' and $postulacion->getEstatus()!='D'){
					$postulacion->setEstatus('D');
				}
			}
			$postulacion->update();
		}	
	}
	
}
?>