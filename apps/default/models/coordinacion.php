<?php
class Coordinacion extends ActiveRecord{

	protected $id;
	protected $descripcion;
	protected $ubicacion;
	protected $telefono;
	protected $email;
	protected $decanato_id;
	protected $empleado_id;
	protected $estatus;

	protected function initialize(){
		$this->belongsTo("empleado_id","empleado","id");

	}
	public function getId() {
		return $this->id;
	}
	public function getDescripcion() {
		return $this->descripcion;
	}
	public function getUbicacion() {
		return $this->ubicacion;
	}
	public function getTelefono() {
		return $this->telefono;
	}
	public function getEmail() {
		return $this->email;
	}
	public function getDecanato_id() {
		return $this->decanato_id;
	}
	public function getEmpleado_id() {
		return $this->empleado_id;
	}
	public function getEstatus() {
		return $this->estatus;
	}
	public function setId($x) {
		$this->id = $x;
	}
	public function setDescripcion($x) {
		$this->descripcion = $x;
	}
	public function setUbicacion($x) {
		$this->ubicacion = $x;
	}
	public function setTelefono($x) {
		$this->telefono = $x;
	}
	public function setEmail($x) {
		$this->email = $x;
	}
	public function setDecanato_id($x) {
		$this->decanato_id = $x;
	}
	public function setEmpleado_id($x) {
		$this->empleado_id = $x;
	}
	public function setEstatus($x) {
		$this->estatus = $x; }

		//-----------------------------------------------------------------------------------------
		public function getDatosCoordinador($idDecanato) {
			$aux= array();
			$coordinacion=$this->findFirst("decanato_id='$idDecanato'");
			if ($coordinacion){
				$coordinador= $coordinacion->getEmpleado();
				if ($coordinador){
					$aux['id']=$coordinador->getId();
					$aux['nombre']=utf8_decode($coordinador->getNombre());
					$aux['apellido']=utf8_decode($coordinador->getApellido());
					$aux['cedula']=$coordinador->getCedula();
				}
			}
			return $aux;
		}
		//-----------------------------------------------------------------------------------------
		public function asignarCoordinador($decanatoId,$idEmpleado) {
			$flag = false;
			$coord = $this->findFirst("decanato_id='$decanatoId'");
			if ($coord){
				$coord->setEmpleado_id($idEmpleado);
				$flag=$coord->update();
			}
			return $flag;
		}
		//-----------------------------------------------------------------------------------------
		public function getCoordinaciones(){
			$auxcoordinaciones = array();
			$i = 0;
			$coordinaciones = $this->find("estatus='A'","order: descripcion");
			foreach($coordinaciones as $coordinacion){
				$auxcoordinaciones[$i]['id'] = $coordinacion->getId();
				$auxcoordinaciones[$i]['descripcion'] = utf8_encode($coordinacion->getDescripcion());
				$auxcoordinaciones[$i]['ubicacion'] = utf8_encode($coordinacion->getUbicacion());
				$auxcoordinaciones[$i]['decanato_id'] = $coordinacion->getDecanato_id();
				$decanato = new Decanato();
				$dec = $decanato->findFirst("id = ".$coordinacion->getDecanato_id());
				if ($dec != null){
					$auxcoordinaciones[$i]['decanato'] = utf8_encode($dec->getNombre());
				}
				$auxcoordinaciones[$i]['empleado_id'] = $coordinacion->getEmpleado_id();
				$empleado = new Empleado();
				$emp = $empleado->findFirst("id = ".$coordinacion->getEmpleado_id());
				if ($emp){
					$auxcoordinaciones[$i]['empleado'] = utf8_encode($emp->getNombre().' '.$emp->getApellido());
				}

				$auxcoordinaciones[$i]['telefono'] = $coordinacion->getTelefono();
				$auxcoordinaciones[$i]['email'] = $coordinacion->getEmail();
				//$auxcoordinaciones[$i]['estatus'] = $coordinacion->getEstatus();

				$i++;
			}

			return $auxcoordinaciones;
		}
		//-----------------------------------------------------------------------------------------
		public function buscar($id){
			$auxCoordinacion = array();
			$i = 0;
			$coordinacion = $this->find("id = ".$id." AND estatus = 'A'");
			$auxCoordinacion['success'] = false;
			$resp['datos'] = array();
			foreach($coordinacion as $coord){
				$auxCoordinacion['datos']['id'] = $id;
				$auxCoordinacion['datos']['nombre'] = utf8_encode($coord->getDescripcion());
				$auxCoordinacion['datos']['direccion'] = utf8_encode($coord->getUbicacion());
				$auxCoordinacion['datos']['decanato_id'] = $coord->getDecanato_id();
				$auxCoordinacion['datos']['empleado_id'] = $coord->getEmpleado_id();
				$auxCoordinacion['datos']['telefono'] = $coord->getTelefono();
				$auxCoordinacion['datos']['email'] = $coord->getEmail();
				$auxCoordinacion['datos']['estatus'] = $coord->getEstatus();
				$auxCoordinacion['success'] = true;

				$i++;
			}

			return $auxCoordinacion;
		}

		//-----------------------------------------------------------------------------------------
		public function actualizarCoordinacion($vId, $vDescripcion, $vDireccion, $vDecanato, $vEmpleado, $vTelefono, $vEmail){
			$success = false;
			$coordinacion = $this->findFirst("id = ".$vId);
			if($coordinacion != null){
				$coordinacion->setDescripcion($vDescripcion);
				$coordinacion->setUbicacion($vDireccion);
				$coordinacion->setDecanato_id($vDecanato);
				$coordinacion->setEmpleado_id($vEmpleado);
				$coordinacion->setTelefono($vTelefono);
				$coordinacion->setEmail($vEmail);
				$success = $coordinacion->update();
			}
			return $success;
		}
		//-----------------------------------------------------------------------------------------
		public function registrar($vDescripcion, $vDireccion, $vDecanato, $vEmpleado, $vTelefono, $vEmail) {
			$success = false;
			$this->setDescripcion($vDescripcion);
			$this->setUbicacion($vDireccion);
			$this->setDecanato_id($vDecanato);
			$this->setEmpleado_id($vEmpleado);
			$this->setTelefono($vTelefono);
			$this->setEmail($vEmail);
			$this->setEstatus('A');
			$success = $this->save();

			return $success;
		}
		//-----------------------------------------------------------------------------------------
		public function eliminar($vId) {
			$success = false;
			$coordinacion = $this->findFirst("id = ".$vId." AND estatus = 'A'");
			if ($coordinacion != null){
				$coordinacion->setEstatus('E');
				$success = $coordinacion->update();
			}
			return $success;
		}
		//-----------------------------------------------------------------------------------------
}

?>