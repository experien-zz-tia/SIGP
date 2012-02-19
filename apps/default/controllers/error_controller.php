<?php
class ErrorController extends ApplicationController{
	public  $contenido;
	
	/**
	 * Action por defecto para el manejo de mensajes de error.
	 * @param string $msj
	 */
	public function indexAction($msj){
		$msj = (string)$msj;
		switch ($msj) {
			case 'noAction':
				$this->contenido='La acci&oacute;n  no ha sido encontrada.';
				break;
			case 'noFileController':
				$this->contenido='El controlador no ha sido encontrado.';
				break;
			case 'noController':
				$this->contenido='El controlador no ha sido encontrado.';
				break;
			case 'noPermiso':
				$this->contenido='Ud. no tiene permisos para  acceder al recurso solicitado.';
				break;
			case 'parametrosNoValidos':
				$this->contenido='Los par&aacute;metros  no son v&aacute;lidos .';
				break;
			default:
				$this->contenido='Error en el despachador.';
				break;
		}
	}

}

?>