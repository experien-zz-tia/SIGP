<?php
require_once('utilities/constantes.php');
class NoticiaController extends ApplicationController {
	protected   $auth;
	protected function initialize(){
		$this->setTemplateAfter("registro");
		$this->auth=Auth::getActiveIdentity();
	}
	
	public function indexAction(){
		$noticia = new Noticia();
		$pagina= $this->getRequestParam('pagina');
		if (!$pagina || (is_numeric($pagina)==false)){
			$pagina = 1;
			}
		$totalNoticias = $noticia->getTotalNoticias();
		$totalPaginas= ceil($totalNoticias/MAX_NOTICIAS_POR_PAGINA);
		$start = MAX_NOTICIAS_POR_PAGINA*($pagina-1);
		$noticias = $noticia->getNoticias($start, MAX_NOTICIAS_POR_PAGINA);
		$noticias = $this->vistaPreliminarNoticia($noticias);
		$this->setParamToView('noticias', $noticias);
		$this->setParamToView('pagina', $pagina);
		$this->setParamToView('totalPaginas', $totalPaginas);
	
	}
	
	public function verAction(){
		$detalleNoticia = array();
		$id= $this->getRequestParam('id');
		if ($id and (is_numeric($id))){
			$noticia = new Noticia();
			$detalleNoticia = $noticia->getNoticia($id);
		}
		$this->setParamToView('noticia', $detalleNoticia);
	}
	
	/**
	 * Recorta el texto del contenido de la noticia
	 * @param array $noticias
	 * @return string
	 */
	protected function vistaPreliminarNoticia($noticias) {
		for ($i = 0; $i < count($noticias); $i++) {
			$noticias[$i]['contenido']=substr(strip_tags($noticias[$i]['contenido']),0,MAX_CARACTERES_PRELIMINAR_NOTICIA);
		}
		return $noticias;
	}
	
	/**
	 * Concatena el nomreb y apellido del usuario creador de la noticia para convertirlo en uno solo.
	 * @param array $noticias
	 * @return string
	 */
	protected function completarAutor($noticias) {
		for ($i = 0; $i < count($noticias); $i++) {
			$noticias[$i]['autor']=$noticias[$i]['nombre'].', '.$noticias[$i]['apellido'];
			unset($noticias[$i]['nombre']);
			unset($noticias[$i]['apellido']);
		}
		return $noticias;
	}
	
	public function gestionarAction(){
		$this->setTemplateAfter("menu");
	
	}
	
	/**
	 * Obtiene las noticias por partes ( basado en parametros start y limit ).
	 * 
	 */
	public function getNoticiasAction(){
		$start = ($this->getRequestParam('start')) ? $this->getRequestParam('start') : PAGINABLE_START;
		$limit = ($this->getRequestParam('limit')) ? $this->getRequestParam('limit') : PAGINABLE_LIMIT;
		$this->setResponse('ajax');
		$noticias = new Noticia();
		$resultado = array(); 
		$resultado['total']=$noticias->getTotalNoticias();
		$detalleNoticias= $noticias->getNoticias($start, $limit);
		$detalleNoticias=$this->vistaPreliminarNoticia($detalleNoticias);
		$detalleNoticias=$this->completarAutor($detalleNoticias);
		$resultado['resultado']=$detalleNoticias;
		$this->renderText(json_encode($resultado));
	}
	
	/**
	 * Obtiene el detalle de la noticia asociada al id pasado como parametro
	 * 
	 */
	public function getNoticiaAction(){
		$this->setResponse('ajax');
		$resultado = array(); 
		$success=false;
		$id= $this->getRequestParam('pNoticiaId');
		if ($id and (is_numeric($id))){
			$noticia = new Noticia();
			$resultado= $noticia->getNoticia($id);
			$success= (count($resultado)==0)?false:true;
		}
		$this->renderText(json_encode(array("success"=>$success,
											"resultado"=>$resultado)));
	}
	
	/**
	 * Crea  una noticia. 
	 */
	public function registrarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= '';
		$this->setResponse('ajax');
		$catUsuario=$this->auth['categoriaUsuario_id'];
		if ($catUsuario==CAT_USUARIO_ADMINISTRADOR or $catUsuario==CAT_USUARIO_ANALISTA or $catUsuario==CAT_USUARIO_COORDINADOR){
			$idUsuario=$this->auth['idUsuario'];
			$noticia = new Noticia();
			$titulo=utf8_decode($this->getRequestParam('txtTitulo'));
			$contenido=utf8_decode($this->getRequestParam('txtContenido'));
			$resp['success'] = $noticia->guardarNoticia($idUsuario,$titulo,$contenido);
				if (!$resp['success']){
					$resp['errorMsj']='No se ha podido registrar la noticia.';
				}
		}else{
			$resp['errorMsj']= 'Ud. no posee la permisologia para realizar esta operaci&oacute;n.';
		}
		$resp['errorMsj']=utf8_encode($resp['errorMsj']);
		$this->renderText(json_encode($resp));
	}
	/**
	 * Actualiza la noticia asociada al id. 
	 */
	public function actualizarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= 'Par&aacute;metros incorrectos.';
		$this->setResponse('ajax');
		$idNoticia= $this->getRequestParam('pNoticiaId');
		if ($idNoticia and is_numeric($idNoticia)){
			$catUsuario=$this->auth['categoriaUsuario_id'];
			$titulo=utf8_decode($this->getRequestParam('txtTitulo'));
			$contenido=utf8_decode($this->getRequestParam('txtContenido'));
			switch ($catUsuario) {
				case CAT_USUARIO_ADMINISTRADOR:
				// Sin break, al tener el mismo nivel para esta operacion, entra en el sgte case
				;
				case CAT_USUARIO_COORDINADOR:
					$resp['success']=$this->actualizarNoticia($idNoticia, $titulo, $contenido);
					if (!$resp['success']){
						$resp['errorMsj']= 'No se ha actualizado la noticia.';
					}else{
						$resp['errorMsj']='';
					}
					break;
				case CAT_USUARIO_ANALISTA:
					$idUsuario=$this->auth['idUsuario'];
					if ($this->verificarPermiso($idNoticia, $idUsuario)){
						$resp['success']=$this->actualizarNoticia($idNoticia, $titulo, $contenido);
						if (!$resp['success']){
							$resp['errorMsj']= 'No se ha actualizado la noticia.';
						}else{
							$resp['errorMsj']='';
						}
					}else{
						$resp['errorMsj']= 'Ud. s&oacute;lo puede actualizar noticias propias.';
					}
				
					break;
				default:	
					$resp['errorMsj']= 'Ud. no posee la permisologia para realizar esta operaci&oacute;n.';
					break;
			}
		}
		$resp['errorMsj']=utf8_encode($resp['errorMsj']);
		$this->renderText(json_encode($resp));
	}
	
	
	/**
	 * Actualiza la informacion asociada a una noticia dada
	 * @param int $id
	 * @param string $titulo
	 * @param string $contenido
	 */
	protected function actualizarNoticia($id,$titulo,$contenido) {
		$noticia = new Noticia();
		return $noticia->actualizarNoticia($id, $titulo, $contenido);
	}
	
	/**
	 * Elimina la noticia asociada al Id
	 * @param int $id
	 */
	protected function eliminarNoticia($id) {
		$noticia = new Noticia();
		return $noticia->eliminarNoticia($id);
	}
	
	/**
	 * Verifica si el usuario es el autor de la noticia, para permitir operar sobre ella. 
	 * @param int $idNoticia
	 * @param int $idUsuario
	 */
	protected function verificarPermiso($idNoticia,$idUsuario) {
		$noticia = new Noticia();
		$flag=false;
		$detalle=$noticia->getNoticia($idNoticia);
		if($detalle){
			$flag= ($detalle['empleado_id']==$idUsuario)?true:false;
		}
		return $flag;
		
	}
	
	/**
	 * Elimina la noticia asociada al id. 
	 */
	public function eliminarAction(){
		$resp=array();
		$resp['success']= false;
		$resp['errorMsj']= 'Par&aacute;metros incorrectos.';
		$this->setResponse('ajax');
		$idNoticia= $this->getRequestParam('pNoticiaId');
		if ($idNoticia and is_numeric($idNoticia)){
			$catUsuario=$this->auth['categoriaUsuario_id'];
			switch ($catUsuario) {
				case CAT_USUARIO_ADMINISTRADOR:
				// Sin break, al tener el mismo nivel para esta operacion, entra en el sgte case
				;
				case CAT_USUARIO_COORDINADOR:
					$resp['success']=$this->eliminarNoticia($idNoticia);
					if (!$resp['success']){
						$resp['errorMsj']= 'No se ha eliminado la noticia.';
					}else{
						$resp['errorMsj']='';
					}
					break;
				case CAT_USUARIO_ANALISTA:
					$idUsuario=$this->auth['idUsuario'];
					if ($this->verificarPermiso($idNoticia, $idUsuario)){
						$resp['success']=$this->eliminarNoticia($idNoticia);
						if (!$resp['success']){
							$resp['errorMsj']= 'No se ha eliminado la noticia.';
						}else{
							$resp['errorMsj']='';
						}
					}else{
						$resp['errorMsj']= 'Ud. s&oacute;lo puede eliminar noticias propias.';
					}
				
					break;
				default:	
					$resp['errorMsj']= 'Ud. no posee la permisologia para realizar esta operaci&oacute;n.';
					break;
			}
		}
		$resp['errorMsj']=utf8_encode($resp['errorMsj']);
		$this->renderText(json_encode($resp));
	}

}