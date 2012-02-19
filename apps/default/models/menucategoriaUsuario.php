<?php
class MenuCategoriaUsuario extends ActiveRecord {
	protected $id;
	protected $menu_id;
	protected $categoriaUsuario_id;

	protected function initialize(){
		$this->hasOne('menu_id','menu','id');
		$this->hasOne('categoriaUsuario_id','categoriaUsuario','id');
	}
	public function getId() { 
		return $this->id;
	 }
	public function getMenu_id() { 
		return $this->menu_id; 
	}
	public function getCategoriaUsuario_id() {
		return $this->categoriaUsuario_id; 
	}
	public function setId($x) { 
		$this->id = $x; 
	}
	public function setMenu_id($x) { 
		$this->menu_id = $x; 
	}
	public function setCategoriaUsuario_id($x) { 
		$this->categoriaUsuario_id = $x; 
	}
	public function getMenus($categoriaUsuario_id) {
		$aux = array();
		$menus = array();
		$j=0;
		$idAnterior=0;
		$sql   = " SELECT cm.id as idCategoria, cm.nombre as categoria , m.nombre as menu , ruta , target";
		$sql  .= " FROM menucategoriaUsuario mu,menu m,categoriaMenu  cm ";
		$sql  .= " WHERE mu.menu_id=m.id AND cm.id=m.categoriaMenu_id AND m.estatus='A' ";
		$sql  .= " AND mu.categoriausuario_id='".$categoriaUsuario_id."' ";
		$sql  .= " ORDER BY cm.id, m.nombre";
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			if($j==0){
				$aux[][0]=utf8_encode($row['categoria']);
				$j++;
				$idAnterior=$row['idCategoria'];
			}else{
				if ($idAnterior!=$row['idCategoria']){
					$aux[($j-1)][]=$menus;
					$menus = array();
					$aux[][0]=utf8_encode($row['categoria']);
					$j++;
					$idAnterior=$row['idCategoria'];
				}
			}
			$menus['nombre'][]=utf8_encode($row['menu']);
			$menus['ruta'][]=$row['ruta'];
			$menus['target'][]=$row['target'];
		}
		if($j!=0)//Si no entra al while, no hay menues
			$aux[($j-1)][]=$menus;
	
		return $aux;
	}
}
?>