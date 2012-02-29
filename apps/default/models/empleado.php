<?php
class Empleado extends ActiveRecord{

	protected $id;
	protected $cedula;
	protected $nombre;
	protected $apellido;
	protected $email;
	protected $tipo;
	protected $decanato_id;
	protected $estatus;


	public function getId() { return $this->id; }
	public function getCedula() { return $this->cedula; }
	public function getNombre() { return $this->nombre; }
	public function getApellido() { return $this->apellido; }
	public function getEmail() { return $this->email; }
	public function getTipo() { return $this->tipo; }
	public function getEstatus() { return $this->estatus; }
	public function getDecanato_id() { return $this->decanato_id; }
	public function setDecanato_id($x) { $this->decanato_id = $x; }
	public function setId($x) { $this->id = $x; }
	public function setCedula($x) { $this->cedula = $x; }
	public function setNombre($x) { $this->nombre = $x; }
	public function setApellido($x) { $this->apellido = $x; }
	public function setEmail($x) { $this->email = $x; }
	public function setTipo($x) { $this->tipo = $x; }
	public function setEstatus($x) { $this->estatus = $x; }

	public function getNombreApellido($id) {
		$aux='';
		$empleado = $this->findFirst("id='$id'");
		if ($empleado){
			$aux= "{$empleado->getNombre()} {$empleado->getApellido()}";
		}
		return $aux;
	}

	public function consultaEmpleados($cedula='',$start='*',$limit='*') {
		$aux = array();
		$i=0;
		$total=0;
		$total= $this->count("estatus !='E'");

		$sql  = " SELECT e.id AS empleadoId,cedula, e.nombre AS nombre ,apellido, email, ";
		$sql .= " e.estatus AS estatus,d.nombre AS decanato, tipo ";
		$sql .= " FROM empleado e, decanato d ";
		$sql .= " WHERE e.decanato_id=d.id AND e.estatus!='E' ";
		if ($cedula!=''){
			$sql .= " AND e.cedula LIKE '$cedula%'";
		}
		$sql .= " ORDER BY e.cedula, e.tipo ";
		if ($start!='*' && $limit!='*'){
			$sql .= " LIMIT ".$start.",".$limit." ";
		}
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$i]['empleadoId'] = $row['empleadoId'];
			$aux[$i]['cedula'] = $row['cedula'];
			$aux[$i]['nombre'] = utf8_encode($this->adecuarTexto($row['nombre']));
			$aux[$i]['apellido'] = utf8_encode($this->adecuarTexto($row['apellido']));
			$aux[$i]['correo'] = utf8_encode($row['email']);
			$aux[$i]['decanato'] = utf8_encode($row['decanato']);
			$aux[$i]['estatus'] = utf8_encode($this->getTextoEstatus($row['estatus']));
			$aux[$i]['tipo'] = utf8_encode($this->getTextoTipo($row['tipo']));
			$i++;
		}

		return array('total'=>$total,
					'resultado' => $aux);

	}


	protected function getTextoEstatus($valor){
		$texto='';
		switch (strtoupper($valor)) {
			case 'A':
				$texto='Activo';
				break;
			case 'E':
				$texto='Eliminado';
				break;
			case 'I':
				$texto='Inactivo';
				break;
		}
		return $texto;
	}

	protected function getTextoTipo($valor){
		$texto='';
		switch (strtoupper($valor)) {
			case 'A':
				$texto='Administrador';
				break;
			case 'C':
				$texto='Coordinador';
				break;
			case 'S':
				$texto='Analista';
				break;
		}
		return $texto;
	}

	public function buscar($pCedula){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$resp['datos']=array();
		$errorMsj ='';

		$emp = $this->findFirst("cedula='$pCedula'");
		if ($emp){
			$errorMsj ='Empleado ya registrado.';
			$resp['datos']['id']=$emp->getId();
			$resp['datos']['cedula']=$emp->getCedula();
			$resp['datos']['email']=$emp->getEmail();
			$resp['datos']['tipo']=utf8_encode($emp->getTipo());
			$resp['datos']['apellido']=utf8_encode($this->adecuarTexto($emp->getApellido()));
			$resp['datos']['nombre']=utf8_encode($this->adecuarTexto($emp->getNombre()));
		}
		$resp['errorMsj']= $errorMsj;
		$resp['success']= true;
		return ($resp);

	}

	public function guardar($cedula,$nombre,$apellido,$correo,$categoria,$idDecanato){
		$success=false;
		$enviarCorreo=false;
		$id=0;
		$emp = $this->findFirst("cedula='$cedula'");
		if ($emp){
			$emp->setNombre($nombre);
			$emp->setApellido($apellido);
			$emp->setEmail($correo);
			$emp->setTipo($categoria);
			$emp->setDecanato_id($idDecanato);
			$emp->setEstatus('A');
			$id= $emp->getId();
			$success = $emp->update();
		}else{
			$this->setCedula($cedula);
			$this->setNombre($nombre);
			$this->setApellido($apellido);
			$this->setTipo($categoria);
			$this->setEmail($correo);
			$this->setDecanato_id($idDecanato);
			$this->setEstatus('I');
			$enviarCorreo = true;
			$success= $this->save();
			$emp = $this->findFirst("cedula='$cedula'");
			$id= $emp->getId();
		}
		return array("success"=>$success,
					"correo"=> $enviarCorreo,
					"id"=>$id);

	}
	public function activarEmpleado($id) {
		$flag=false;
		$emp= $this->findFirst("id='$id'");
		if ($emp){
			$emp->setEstatus('A');
			$flag=$emp->update();
		}
		return $flag;
	}

	public function actualizar($id,$nombre,$apellido,$correo){
		$success=false;
		$emp = $this->findFirst("id='$id'");
		if ($emp){
			$emp->setNombre($nombre);
			$emp->setApellido($apellido);
			$emp->setEmail($correo);
			$success = $emp->update();
		}
		return $success;
	}


	public function eliminar($id){
		$success=false;
		$emp = $this->findFirst("id='$id'");
		if ($emp){
			$emp->setEstatus('E');
			$success = $emp->update();
		}
		return $success;
	}


	public function buscarbyId($id){
		$resp=array();
		$emp = $this->findFirst("id='$id'");
		if ($emp){
			$resp['cedula']=$emp->getCedula();
			$resp['email']=$emp->getEmail();
			$resp['tipo']=utf8_encode($emp->getTipo());
			$resp['apellido']=utf8_encode($this->adecuarTexto($emp->getApellido()));
			$resp['nombre']=utf8_encode($this->adecuarTexto($emp->getNombre()));
		}
		return ($resp);
	}
	
	
	public function existeCoordinador(){
		$success=false;
		$emp = $this->findFirst("estatus='A' AND tipo='C'");
		if ($emp){
			$success = true;
		}
		return $success;
		
	} 
}

?>