<?php
require_once('utilities/constantes.php');
/**
 * Todas las controladores heredan de esta clase en un nivel superior
 * por lo tanto los metodos aqui definidos estan disponibles para
 * cualquier controlador.
 *
 * @category Kumbia
 * @package Controller
 **/
class ControllerBase {

	/**
	 * Redirije el trafico al ingresar al site principal de la web
	 */
	public function init(){
		Router::routeTo("controller: noticia");
	}

	/**
	 * Se ejecuta antes de acceder a cualquier recurso (controlador), valida el nivel de acceso 
	 * del usuario y lo redirige a la pagina solicitada si posee el nivel suficiente o a una pagina de error
	 * @throws ApplicationControllerException
	 * @return boolean
	 */
	public function beforeFilter(){
			
		if (Auth::isValid()){
			$auth=Auth::getActiveIdentity();
			$role = $auth['categoriaUsuario_id'];
		}else{
			$role = CAT_USUARIO_VISITANTE;
		}

		$acl = new Acl('Model', 'className: AccessList');
		$resourceName = $this->getControllerName();
		$operationName = $this->getActionName();

		if($acl->isAllowed($role, $resourceName, $operationName)==false){
			throw new ApplicationControllerException("No tiene permiso
	                    para usar esta aplicación");
				
			return false;
		}
	}

	/**
	 * Redirige segun el tipo de exception originada
	 * @param  $e
	 * @throws ApplicationControllerException
	 */
	public function onException($e){
		if($e instanceof DispatcherException){
			switch ($e->getCode()) {
				case Dispatcher::NOT_FOUND_ACTION:
					Router::routeToURI('/error/index/noAction');
					break;
				case Dispatcher::NOT_FOUND_CONTROLLER:
					Router::routeToURI('/error/index/noController');
					break;
				case Dispatcher::NOT_FOUND_FILE_CONTROLLER:
					Router::routeToURI('/error/index/noFileController');
					break;
				case Dispatcher::NOT_FOUND_INIT_ACTION:
					Router::routeToURI('/error/index/noInit');
					break;
				case Dispacher::INVALID_METHOD_CALLBACK:
					Router::routeToURI('/error/index/error');
					break;
				case Dispatcher::INVALID_ACTION_VALUE_PARAMETER:
					Router::routeToURI('/error/index/error');
					break;
				default:
					Router::routeToURI('/error/index/error');
					break;
			}
		} else {
			if($e instanceof ApplicationControllerException){
				Router::routeToURI('/error/index/noPermiso');
			}else{
				throw $e;
			}
		}

	}
	
	public function mostrarMensaje($titulo,$contendio){
		$msj = "<script type='text/javascript'> 
					Ext.example.msg('$titulo', '$contendio');
			 </script>";
		$this->renderText($msj);

	} 
	public function obtenerParametroRequest($index) {
		if ($index =='start'){
			return $this->getParametro('start', 'Numerico',PAGINABLE_START );
		}elseif ($index=='limit'){
			return  $this->getParametro('limit', 'Numerico',PAGINABLE_LIMIT);	
		}
	}
	
	/**
	 * Obtiene el valor del request, en caso que no cumpla con las condiciones se retorna default
	 * @param string $index
	 * @param string $type
	 * @param any $default
	 * @param int $maxLegth
	 * @return value of request or  default in negative case  
	 */
	public function getParametro($index,$type,$default,$maxLegth=0) {
		$param=null;
		if (!(isset($_REQUEST[$index])) AND (empty($_REQUEST[$index]))){ 
			$param=$default; 
		}else {
			 $param= $_REQUEST[$index];
			 $param=$this->sanitizar($param,$maxLegth); 
			 switch (strtoupper($type)) {
			 	case 'NUMERICO':
			 		$param= is_numeric($param)?$param:$default;
			 		break;
			 	case 'STRING':
			 		$param= is_string($param)?$param:$default;
			 		break;
			 	case 'BOOLEANO':
			 		$param= is_bool($param)?$param:$default;
			 		break;
			 }
		}
 		return $param;
		
		
	}
	
	/**
	 * Convierte las tags de html a su equivalente entidad, ademas de escapar  el string
	 * @param string $value
	 * @param int $maxLength
	 * @return string
	 */
	private function sanitizar($value,$maxLength=0) {
		$aux= htmlentities($value,ENT_COMPAT,"UTF-8");
		$aux= mysql_real_escape_string($aux);
		if ($maxLength!=0){
			$aux=substr($aux, 0, $maxLength);
		}
		return $aux;
	}

}

