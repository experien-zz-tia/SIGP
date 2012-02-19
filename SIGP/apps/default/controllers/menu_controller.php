<?php
class MenuController extends ApplicationController {
	public $menus;
	public function indexAction(){
		$this->crearMenu();
	}
	protected function crearMenu() {
		$auth=Auth::getActiveIdentity();
		$categoriaUsuario=$auth['categoriaUsuario_id']; 
		$menu = new MenuCategoriaUsuario();
		$this->menus =$menu->getMenus($categoriaUsuario);
	}
}
?>