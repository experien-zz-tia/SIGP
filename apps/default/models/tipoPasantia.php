<?php
	class TipoPasantia extends ActiveRecord{
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
		public function getTiposPasantia(){
			$arrTipos = array();
			$i = 0;
			$tipos = $this->find("order: descripcion");
			foreach ($tipos as $tipo){
				$arrTipos[$i]['id'] = $tipo->id;
				$arrTipos[$i]['descripcion'] = utf8_encode($tipo->descripcion);
				$i++;
			}
			return $arrTipos;
		}
//-----------------------------------------------------------------------------------------
	}
?>
