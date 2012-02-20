<?php
	class ModalidadPasantia extends ActiveRecord{
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
		public function getModalidadesPasantia(){
			$arrModalidades = array();
			$i = 0;
			$modalidades = $this->find("order: descripcion");
			foreach ($modalidades as $modalidad){
				$arrModalidades[$i]['id'] = $modalidad->id;
				$arrModalidades[$i]['descripcion'] = utf8_encode($modalidad->descripcion);
				$i++;
			}
			return $arrModalidades;
		}
//-----------------------------------------------------------------------------------------			
	}
?>