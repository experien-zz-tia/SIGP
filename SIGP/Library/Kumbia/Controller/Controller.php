<?php

/**
 * Kumbia Enterprise Framework
 *
 * LICENSE
 *
 * This source file is subject to the New BSD License that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@loudertechnology.com so we can send you a copy immediately.
 *
 * @category	Kumbia
 * @package		Controller
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @version 	$Id: Controller.php 82 2009-09-13 21:06:31Z gutierrezandresfelipe $
 */

/**
 * Controller
 *
 * Componente de Controladores
 *
 * @category	Kumbia
 * @package		Controller
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 */
class Controller extends ControllerBase {

	/**
	 * Nombre del controlador actual
	 *
	 * @var string
	 */
	private $_controllerName = '';

	/**
	 * Nombre de la accion actual
	 *
	 * @var string
	 */
	private $_actionName = '';

	/**
	 * Nombre del primer parametro despues de action
	 * en la URL
	 *
	 * @var string
	 */
	private $_id;

	/**
	 * Parametros enviados por una clean URL
	 *
	 * @var array
	 */
	private $_parameters = array();


	/**
	 * Todos los Parametros enviados por una clean URL
	 *
	 * @var array
	 */
	private $_allParameters = array();

	/**
	 * Numero de minutos que ser&aacute; cacheada la vista actual
	 *
	 * @var integer
	 */
	private $_cacheView = 0;

	/**
	 * Numero de minutos que ser&aacute; cacheada el layout actual
	 *
	 * @var integer
	 */
	private $_cacheLayout = 0;

	/**
	 * Numero de minutos que ser&aacute; cacheado el template actual
	 *
	 * @var integer
	 */
	private $_cacheTemplate = 0;

	/**
	 * Template del Controlador que se insertan antes del layout del controlador
	 *
	 * @var string
	 */
	private $_templateBefore = '';

	/**
	 * Template del Controlador que se insertan despues del layout del controlador
	 *
	 * @var string
	 */
	private $_templateAfter = '';

	/**
	 * Indica si el controlador soporta persistencia
	 *
	 * @var boolean
	 */
	private $_persistance = false;

	/**
	 * Tipo de Respuesta que sera generado
	 *
	 * @access private
	 * @var string
	 */
	private $_response = '';

	/**
	 * Indica si el controlador es persistente o no
	 *
	 * @access public
	 * @staticvar
	 * @var boolean
	 */
	static public $force = false;

	/**
	 * Logger implicito del controlador
	 *
	 * @access private
	 * @var string
	 */
	private $_logger;

	/**
	 * Permite asignar attributos sin generar una excepcion
	 *
	 * @var boolean
	 */
	private $_settingLock = false;

	/**
	 * Constructor de la clase
	 *
	 * @access public
	 */
	public function __construct(){
		if(method_exists($this, 'initialize')){
			$this->initialize();
		}
	}

	/**
	 * Cache la vista correspondiente a la accion durante $minutes
	 *
	 * @access protected
	 * @param int $minutes
	 */
	protected function cacheView($minutes){
		$this->_cacheView = $minutes;
	}

	/**
	 * Obtiene el valor en minutos para el cache de la
	 * vista actual
	 *
	 * @access public
	 * @return string
	 */
	public function getViewCache(){
		return $this->_cacheView;
	}

	/**
	 * Cache la vista en views/layouts/
	 * correspondiente al controlador durante $minutes
	 *
	 * @access protected
	 * @param integer $minutes
	 */
	protected function cacheLayout($minutes){
		$this->_cacheLayout = $minutes;
	}

	/**
	 * Obtiene el valor en minutos para el cache del
	 * layout actual
	 *
	 * @access public
	 * @return string
	 */
	public function getLayoutCache(){
		return $this->_cacheLayout;
	}

	/**
	 * Hace el enrutamiento desde un controlador a otro, o desde
	 * una accion a otra.
	 *
	 * Ej:
	 * <code>
	 * return $this->routeTo("controller: clientes", "action: consultar", "id: 1");
	 * </code>
	 *
	 * @access protected
	 */
	protected function routeTo(){
		$args = func_get_args();
		return call_user_func_array(array('Router', 'routeTo'), $args);
	}

	/**
	 * Hace el enrutamiento desde un controlador a otro, o desde
	 * una accion a otra.
	 *
	 * Ej:
	 * <code>
	 * return $this->routeToURI("clientes/buscar/21");
	 * </code>
	 *
	 * @access protected
	 */
	protected function routeToURI(){
		$args = func_get_args();
		return call_user_func_array(array('Router', 'routeToURI'), $args);
	}

	/**
	 * Obtiene un valor del arreglo $_POST
	 *
	 * @access protected
	 * @param string $paramName
	 * @return mixed
	 */
	protected function getPostParam($paramName){
		$funcGetArgs = func_get_args();
		return call_user_func_array(array($this->getRequestInstance(), 'getParamPost'), $funcGetArgs);
	}

	/**
	 * Obtiene un valor del arreglo $_GET
	 *
	 * @access protected
	 * @param string $paramName
	 * @return mixed
	 */
	protected function getQueryParam($paramName){
		$funcGetArgs = func_get_args();
		return call_user_func_array(array($this->getRequestInstance(), 'getParamQuery'), $funcGetArgs);
	}

	/**
	 * Obtiene un valor del arreglo $_REQUEST
	 *
	 * @access protected
	 * @param string $paramName
	 * @return mixed
	 */
	protected function getRequestParam($paramName){
		$funcGetArgs = func_get_args();
		return call_user_func_array(array($this->getRequestInstance(), 'getParamRequest'), $funcGetArgs);
	}

	/**
	 * Obtiene un valor del arreglo superglobal $_SERVER
	 *
	 * @access protected
	 * @param string $paramName
	 * @return mixed
	 */
	protected function getServer($paramName){
		$funcGetArgs = func_get_args();
		return call_user_func_array(array($this->getRequestInstance(), 'getParamServer'), $funcGetArgs);
	}

	/**
	 * Obtiene un valor del arreglo superglobal $_ENV
	 *
	 * @access protected
	 * @param string $paramName
	 * @return mixed
	 */
	protected function getEnvironment($paramName){
		$funcGetArgs = func_get_args();
		return call_user_func_array(array($this->getRequestInstance(), 'getParamEnv'), $funcGetArgs);
	}

	/**
	 * Filtra un valor
	 *
	 * @access protected
	 * @param string $paramValue
	 * @return mixed
	 */
	protected function filter($paramValue){
		/**
		 * Si hay mas de un argumento, toma los demas como filtros
		 */
		if(func_num_args()>1){
			$args = func_get_args();
			$args[0] = $paramValue;
			$filter = new Filter();
			return call_user_func_array(array($filter, 'applyFilter'), $args);
		} else {
			throw new ApplicationControllerException('Debe indicar al menos un filtro a aplicar');
		}
		return $paramValue;
	}

	/**
	 * Establece el valor de un parametro enviado por $_REQUEST;
	 *
	 * @access protected
	 * @param mixed $index
	 * @param mixed $value
	 */
	protected function setRequestParam($index, $value){
		$funcGetArgs = func_get_args();
		return call_user_func_array(array($this->getRequestInstance(), 'setParamRequest'), $funcGetArgs);
	}

	/**
	 * Establece el valor de un parametro enviado por $_POST;
	 *
	 * @access protected
	 * @param mixed $index
	 * @param mixed $value
	 */
	protected function setPostParam($index, $value){
		$funcGetArgs = func_get_args();
		return call_user_func_array(array($this->getRequestInstance(), 'setParamPost'), $funcGetArgs);
	}

	/**
	 * Establece el valor de un parametro enviado por $_GET;
	 *
	 * @access protected
	 * @param mixed $index
	 * @param mixed $value
	 */
	protected function setQueryParam($index, $value){
		$funcGetArgs = func_get_args();
		return call_user_func_array(array($this->getRequestInstance(), 'setParamQuery'), $funcGetArgs);
	}

	/**
	 * Establece el valor de un parametro enviado por $_COOKIE;
	 *
	 * @access protected
	 * @param mixed $index
	 * @param mixed $value
	 */
	protected function setCookie($index, $value){
		$funcGetArgs = func_get_args();
		return call_user_func_array(array($this->getRequestInstance(), 'setParamCookie'), $funcGetArgs);
	}

	/**
	 * Sube un archivo al directorio img/upload si esta en $_FILES
	 *
	 * @access protected
	 * @param string $name
	 * @return string
	 */
	protected function uploadImage($name){
		if(isset($_FILES[$name])){
			move_uploaded_file($_FILES[$name]['tmp_name'], htmlspecialchars('public/img/upload/'.$_FILES[$name]['name']));
			return urlencode(htmlspecialchars('upload/'.$_FILES[$name]['name']));
		} else {
			return urlencode($this->request($name));
		}
	}

	/**
	 * Sube un archivo al directorio $dir si esta en $_FILES
	 *
	 * @access public
	 * @param string $name
	 * @param string $dir
	 * @return string
	 */
	protected function uploadFile($name, $dir){
		if(!isset($_FILES[$name])){
			return false;
		}
		if($_FILES[$name]){
			return move_uploaded_file($_FILES[$name]['tmp_name'], htmlspecialchars("$dir/{$_FILES[$name]['name']}"));
		} else {
			return false;
		}
	}

	/**
	 * Indica si un controlador va a ser persistente, en este
	 * caso los valores internos son automaticamente almacenados
	 * en sesion y disponibles cada vez que se ejecute una accion
	 * en el controlador
	 *
	 * @access 	public
	 * @param 	boolean $value
	 */
	protected function setPersistance($value){
		$this->_persistance = $value;
	}

	/**
	 * Indica si el controlador es persistente o no
	 *
	 * @access	public
	 * @return	boolean
	 */
	public function getPersistance(){
		return $this->_persistance;
	}

	/**
	 * Redirecciona la ejecucion a otro controlador en un
	 * tiempo de ejecucion determinado
	 *
	 * @access protected
	 * @param string $controller
	 * @param integer $seconds
	 */
	protected function redirect($controller, $seconds=0.5){
		$config = CoreConfig::readEnviroment();
		$instancePath = Core::getInstancePath();
		if(headers_sent()==true){
			$seconds*=1000;
			echo "<script type='text/javascript'>window.setTimeout(\"window.location='", $instancePath, $controller, "'\", $seconds);</script>\n";
		} else {
			$application = Router::getActiveApplication();
			View::setRenderLevel(View::LEVEL_NO_RENDER);
			if($application==""){
				$this->getResponseInstance()->setHeader('Location: '.$instancePath.$controller, true);
			} else {
				$this->getResponseInstance()->setHeader('Location: '.$instancePath.$application.'/'.$controller, true);
			}
		}
	}

	/**
	 * Indica el tipo de Respuesta dada por el controlador (este metodo está obsoleto)
	 *
	 * @access 		public
	 * @param		string $type
	 * @deprecated
	 */
	public function setResponse($type){
		$response = ControllerResponse::getInstance();
		switch($type){
			case 'ajax':
			case 'view':
				View::setRenderLevel(View::LEVEL_ACTION_VIEW);
				$response->setResponseType(ControllerResponse::RESPONSE_NORMAL);
				$response->setResponseAdapter('');
				break;
			case 'xml':
				View::setRenderLevel(View::LEVEL_NO_RENDER);
				$response->setResponseType(ControllerResponse::RESPONSE_OTHER);
				$response->setResponseAdapter('xml');
				break;
			case 'json':
				$response->setResponseType(ControllerResponse::RESPONSE_OTHER);
				View::setRenderLevel(View::LEVEL_NO_RENDER);
				$response->setResponseAdapter('json');
				break;
			case 'rss':
				View::setRenderLevel(View::LEVEL_NO_RENDER);
				$response->setResponseType(ControllerResponse::RESPONSE_OTHER);
				$response->setResponseAdapter('rss');
				break;
		}
	}

	/**
	 * Reescribir este metodo permite controlar las excepciones generadas en un controlador
	 *
	 * @access protected
	 * @param Exception $exception
	 */
	protected function exceptions($exception){
		throw $exception;
	}

	/**
	 * Crea un log sino existe y guarda un mensaje
	 *
	 * @access protected
	 * @param string $msg
	 * @param integer $type
	 */
	protected function log($msg, $type=Logger::DEBUG){
		if(is_array($msg)){
			$msg = print_r($msg, true);
		}
		if(!$this->_logger){
			$this->_logger = new Logger($this->controllerName.'.txt');
		}
		$this->_logger->log($msg, $type);
	}

	/**
	 * Devuelve una salida en JavaScript
	 *
	 * @access protected
	 * @param string $js
	 */
	protected function renderJavascript($js){
		$this->renderText("<script type='text/javascript'>$js</script>");
	}

	/**
	 * Convierte una variable a notacion JSON
	 *
	 * @access protected
	 * @param mixed $data
	 * @return string
	 */
	protected function jsonEncode($data){
		return json_encode($data);
	}

	/**
	 * Genera una salida JSON estableciendo el tipo de salida adecuada
	 *
	 * @access protected
	 * @param mixed $data
	 */
	protected function outputJSONResponse($data){
		$this->setResponse('json');
		echo json_encode($data);
	}

	/**
	 * Devuelve el nombre del controlador actual
	 *
	 * @access public
	 * @return string
	 */
	public function getControllerName(){
		return $this->_controllerName;
	}

	/**
	 * Establece el nombre del controlador actual
	 *
	 * @access public
	 * @param string $controllerName
	 */
	public function setControllerName($controllerName){
		$this->_controllerName = $controllerName;
	}

	/**
	 * Devuelve el nombre de la accion actual
	 *
	 * @access public
	 * @return string
	 */
	public function getActionName(){
		return $this->_actionName;
	}

	/**
	 * Establece el nombre de la accion actual
	 *
	 * @access public
	 * @param string $actionName
	 */
	public function setActionName($actionName){
		$this->_actionName = $actionName;
	}

	/**
	 * Establece el valor del parametro id del controlador
	 *
	 * @access public
	 * @param string $id
	 */
	public function setId($id){
		$this->_id = $id;
	}

	/**
	 * Devuelve el valor del parametro id del controlador
	 *
	 * @access public
	 */
	public function getId(){
		return $this->_id;
	}

	/**
	 * Establece el valor de los parametros adicionales en el controlador
	 *
	 * @access public
	 * @param array $parameters
	 */
	public function setParameters($parameters){
		$this->_parameters = $parameters;
	}

	/**
	 * Establece el valor de todos los parametros adicionales en el controlador
	 *
	 * @access public
	 * @param array $allParameters
	 */
	public function setAllParameters($allParameters){
		$this->_allParameters = $allParameters;
	}

	/**
	 * Devuelve un callback que administrara la forma en que se presente
	 * la vista del controlador
	 *
	 * @access public
	 */
	public function getViewHandler(){
		return array('View', 'handleViewRender');
	}

	/**
	 * Devuelve un callback que administrará la forma en que se presente
	 * la vista del controlador
	 *
	 * @access 	public
	 * @return 	callback
	 */
	public function getViewExceptionHandler(){
		return array('View', 'handleViewExceptions');
	}

	/**
	 * Establece el/los template(s) que se insertan antes del layout del controlador
	 *
	 * @access public
	 * @param string|array $template
	 */
	public final function setTemplateBefore($template){
		$this->_templateBefore = $template;
	}

	/**
	 * Limpia los templates que se insertarán en la petición antes del layout del controlador
	 *
	 * @access public
	 */
	public final function cleanTemplateBefore(){
		$this->_templateBefore = '';
	}

	/**
	 * Establece el/los template(s) que se insertan despues del layout del controlador
	 *
	 * @access 	public
	 * @param 	string|array $template
	 */
	public final function setTemplateAfter($template){
		$this->_templateAfter = $template;
	}

	/**
	 * Limpia los templates que se insertarán en la petición despues del layout del controlador
	 *
	 * @access public
	 */
	public final function cleanTemplateAfter(){
		$this->_templateAfter = '';
	}

	/**
	 * Devuelve el/los nombre(s) del Template Before Actual
	 *
	 * @access public
	 * @return string|array
	 */
	public final function getTemplateBefore(){
		return $this->_templateBefore;
	}

	/**
	 * Devuelve el/los nombre(s) del Template After Actual
	 *
	 * @access 	public
	 * @return 	string|array
	 */
	public final function getTemplateAfter(){
		return $this->_templateAfter;
	}

	/**
	 * Devuelve la instancia del Objeto Request
	 *
	 * @access 	public
	 * @return 	ControllerRequest
	 */
	public function getRequestInstance(){
		return ControllerRequest::getInstance();
	}

	/**
	 * Devuelve la instancia del Objeto Response
	 *
	 * @access 	public
	 * @return 	ControllerResponse
	 */
	public function getResponseInstance(){
		return ControllerResponse::getInstance();
	}

	/**
	 * Establece una variable de la vista directamente
	 *
	 * @access 	public
	 * @param 	string $index
	 * @param 	string $value
	 */
	public function setParamToView($index, $value){
		View::setViewParam($index, $value);
	}

	/**
	 * Al deserializar asigna 0 a los tiempos del cache
	 *
	 * @access public
	 */
	public function __wakeup(){
		$this->_logger = false;
		$this->_cacheView = 0;
		$this->_cacheLayout = 0;
		if(method_exists($this, 'initialize')){
			$this->initialize();
		}
	}

	/**
	 * Establece el control de acceso a las propiedades del controlador
	 *
	 * @access 	public
	 * @param 	boolean $lock
	 */
	public function setSettingLock($lock){
		$this->_settingLock = $lock;
	}

	/**
	 * La definición de este metodo indica si se debe exportar las variables publicas
	 *
	 * @access 	public
	 * @return 	boolean
	 */
	public function isExportable(){
		return false;
	}

	/**
	 * Obliga a que todas las propiedades del controlador esten definidas
	 * previamente
	 *
	 * @access public
	 * @param string $property
	 * @param string $value
	 */
	public function __set($property, $value){
		if($this->_settingLock==false){
			if(EntityManager::isModel($property)==false){
				throw new ApplicationControllerException('Asignando propiedad indefinida "'.$property.'" al controlador');
			}
		} else {
			$this->$property = $value;
		}
	}

	/**
	 * Obliga a que todas las propiedades del controlador esten definidas
	 * previamente
	 *
	 * @access public
	 * @param string $property
	 */
	public function __get($property){
		if(EntityManager::isModel($property)==false){
			throw new ApplicationControllerException('Leyendo propiedad indefinida "'.$property.'" del controlador');
		} else {
			$entity = EntityManager::getEntityInstance($property);
			$this->_settingLock = true;
			$this->$property = $entity;
			$this->_settingLock = false;
			return $this->$property;
		}
	}

	/**
	 * Carga los modelos como propiedades
	 *
	 */
	public function loadModel(){
		foreach(func_get_args() as $model){
			$entity = EntityManager::getEntityInstance($model);
			$this->_settingLock = true;
			$this->$model = $entity;
			$this->_settingLock = false;
		}
	}

	/**
	 * Obtiene una instancia de un servicio web del contenedor ó mediante Naming Directory
	 *
	 * @param 	string $serviceName
	 * @return  WebServiceClient
	 */
	public function getService($serviceName){
		$service = Resolver::lookUp($serviceName);
		return $service;
	}

	/**
	 * Valida que los campos requeridos enten presentes
	 *
	 * @access	protected
	 * @param	string $fields
	 * @param	string $base
	 * @param	string $getMode
	 * @return	boolean
	 */
	protected function validateRequired($fields, $base='', $getMode=''){
		return Validation::validateRequired($fields, $base, $getMode);
	}

	/**
	 * Limpia la lista de Mensajes
	 *
	 * @access protected
	 */
	protected function cleanValidationMessages(){
		Validation::cleanValidationMessages();
	}

	/**
	 * Agrega un mensaje a la lista de mensajes
	 *
	 * @access 	protected
	 * @param 	string $fieldName
	 * @param 	string $message
	 */
	protected function addValidationMessage($message, $fieldName=''){
		Validation::addValidationMessage($message, $fieldName);
	}

	/**
	 * Devuelve los mensajes de validación generados
	 *
	 * @access 	protected
	 * @return 	array
	 */
	public function getValidationMessages(){
		return Validation::getMessages();
	}

}
