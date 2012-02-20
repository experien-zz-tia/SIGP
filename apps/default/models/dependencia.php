<?php
	class Dependencia extends ActiveRecord{
		protected $id;
		protected $descripcion;
		protected $estatus;
//-----------------------------------------------------------------------------------------		
		public function getId(){ return $this->id; }
		public function setId($valor){ $this->id = $valor; }
		
		public function getDescripcion(){ return $this->descripcion; }
		public function setDescripcion($valor){ $this->descripcion = $valor; }
		
		public function getEstatus(){ return $this->estatus; }
		public function setEstatus($valor){ $this->estatus = $valor; }
//-----------------------------------------------------------------------------------------
		public function getDependencias(){
			$arrDependencias = array();
			$i = 0;
			$Dependencias = $this->find("order: descripcion");
			foreach ($Dependencias as $dependencia){
				$arrDependencias[$i]['id'] = $dependencia->id;
				$arrDependencias[$i]['descripcion'] = utf8_encode($dependencia->descripcion);
				$i++;
			}
			return $arrDependencias;
		}
//-----------------------------------------------------------------------------------------
	}
?>
