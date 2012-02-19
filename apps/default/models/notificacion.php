<?php

class Notificacion extends ActiveRecord {

	protected $id;
	protected $mensaje;
	protected $remitenteId;
	protected $remitenteCatId;
	protected $destinatarioId;
	protected $destinatarioCatId;
	protected $fchEnvio_at;
	protected $tipo;
	protected $estatus;

	public function getId() { return $this->id; }
	public function getMensaje() { return $this->mensaje; }
	public function getRemitenteId() { return $this->remitenteId; }
	public function getRemitenteCatId() { return $this->remitenteCatId; }
	public function getDestinatarioId() { return $this->destinatarioId; }
	public function getDestinatarioCatId() { return $this->destinatarioCatId; }
	public function getFchEnvio_at() { return $this->fchEnvio_at; }
	public function getTipo() { return $this->tipo; }
	public function getEstatus() { return $this->estatus; }
	public function setId($x) { $this->id = $x; }
	public function setMensaje($x) { $this->mensaje = $x; }
	public function setRemitenteId($x) { $this->remitenteId = $x; }
	public function setRemitenteCatId($x) { $this->remitenteCatId = $x; }
	public function setDestinatarioId($x) { $this->destinatarioId = $x; }
	public function setDestinatarioCatId($x) { $this->destinatarioCatId = $x; }
	public function setFchEnvio_at($x) { $this->fchEnvio_at = $x; }
	public function setTipo($x) { $this->tipo = $x; }
	public function setEstatus($x) { $this->estatus = $x; }
	
	public function guardar ($remId, $remCat, $destId, $destCat,$mensaje,$tipo='A'){
		$flag=false;
	 	$this->setMensaje($mensaje);
	 	$this->setRemitenteId($remId);
	 	$this->setRemitenteCatId($remCat);
	 	$this->setDestinatarioId($destId);
	 	$this->setDestinatarioCatId($destCat);
	 	$this->setTipo($tipo);
	 	$this->setEstatus('A');
	 	$flag= $this->save();
	 	return $flag;

	}
	
	public function getNotificaciones($usuarioId,$catUsuario,$start,$limit){
		$aux = array();
		$i=0;
		$total= $this->count(" destinatarioId='$usuarioId' and destinatarioCatId='$catUsuario' AND estatus='A'");
		$sql= " SELECT n.id as id, nombre,apellido,mensaje, fchEnvio_at ";
		$sql.= " FROM notificacion  n LEFT JOIN empleado e ON ( n.remitenteId=e.id ) ";
		$sql.= " WHERE  n.destinatarioId='$usuarioId' and n.destinatarioCatId='$catUsuario' AND n.estatus='A'";
		$sql.= " ORDER BY fchEnvio_at DESC";
		$sql.= " LIMIT ".$start.",".$limit." ";
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$i]['id'] = $row['id'];
			$aux[$i]['remitente'] =  utf8_encode($row['nombre'].', '.$row['apellido']);
			$aux[$i]['mensaje'] = utf8_encode(html_entity_decode($row['mensaje']));
			$aux[$i]['fchEnvio'] = Util::cambiarFechaMDY($row['fchEnvio_at']);
			$i++;
		}
			
		return array('total'=>$total,
					'resultado' => $aux);
	}
	
	public function eliminar ($idDuenio,$id){
		$success=false;
		$mensaje = $this->findFirst("id='$id' AND destinatarioId='$idDuenio'");
		if ($mensaje){
			$mensaje->setEstatus('E');
			$success = $mensaje->update();
		}
		return $success;
		
		
	}

}

?>