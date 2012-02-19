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
 * @package		Dispatcher
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @version 	$Id: Dispatcher.php 97 2009-09-30 19:28:13Z gutierrezandresfelipe $
 */

/**
 * Dispatcher
 *
 * Clase para que administra las peticiones del Servidor de Aplicaciones
 *
 * @category	Kumbia
 * @package		Dispatcher
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @access		public
 * @abstract
 */
abstract class Dispatcher {

	/**
	 * Contiene referencias a los controladores instanciados
	 *
	 * @var array
	 * @staticvar
	 */
	static private $_controllerReferences = array();

	/**
	 * Estadisticas de ejecución de controladores
	 *
	 * @var array
	 */
	static private $_controllerStatistic = array();

	/**
	 * Indica si ya se han inicializado los componentes
	 *
	 * @var boolean
	 * @staticvar
	 */
	static private $_initializedComponents = false;

	/**
	 * Indica el estado de ejecucion de la aplicacion
	 *
	 * @var integer
	 * @staticvar
	 */
	static private $_requestStatus = self::STATUS_UNINITIALIZED;

	/**
	 * Valor devuelto por el metodo accion ejecutado
	 *
	 * @var string
	 * @staticvar
	 */
	static private $_valueReturned = null;

	/**
	 * Objeto del controlador en ejecucion
	 *
	 * @var mixed
	 * @staticvar
	 */
	static private $_controller;

	/**
	 * Directorio de controladores
	 *
	 * @var string
	 * @staticvar
	 */
	static private $_controllersDir;

	/**
	 * Lista de clases que no deben ser serializadas por el Dispatcher
	 *
	 * @var array
	 */
	static private $_notSerializableClasses = array('ActiveRecord', 'ActiveRecordResulset');

	/**
	 * Codigo de error cuando no encuentra la accion
	 */
	const NOT_FOUND_ACTION = 100;
	const NOT_FOUND_CONTROLLER = 101;
	const NOT_FOUND_FILE_CONTROLLER = 102;
	const NOT_FOUND_INIT_ACTION = 103;

	/**
	 * Otros codigos de excepciones
	 */
	const INVALID_METHOD_CALLBACK = 104;
	const INVALID_ARGUMENT_NUMBER = 105;

	/**
	 * Estados de Ejecucion de la Peticion
	 */
	const STATUS_UNINITIALIZED = 199;
	const STATUS_DISPATCHING = 200;
	const STATUS_RUNNING_BEFORE_FILTERS = 201;
	const STATUS_RUNNING_AFTER_FILTERS = 202;
	const STATUS_RENDER_PRESENTATION = 203;
	const STATUS_RUNNING_BEFORE_STORE_PERSISTENCE = 204;
	const STATUS_RUNNING_AFTER_STORE_PERSISTENCE = 205;
	const STATUS_RUNNING_CONTROLLER_ACTION = 206;

	/**
	 * Ejecuta la accion init en ApplicationController
	 *
	 * @access public
	 * @return boolean
	 * @static
	 */
	static public function initBase(){

		/**
		 * Inicializa los componentes del Framework
		 */
		self::$_requestStatus = self::STATUS_RUNNING_CONTROLLER_ACTION;
		self::initComponents();

		$applicationController = new ApplicationController();
		if(method_exists($applicationController, 'init')){
			$applicationController->init();
		} else {
			if(self::executeNotFound($applicationController)==false){
				//No se encontro el método init en la clase ControllerBase
				$message = CoreLocale::getErrorMessage(-103);
				self::throwException($message, self::NOT_FOUND_INIT_ACTION);
			} else {
				self::$_controller = $applicationController;
			}
		}
	}

	/**
	 * Ejecuta accion notFound
	 *
	 * @access 	private
	 * @param 	Controller $applicationController
	 * @static
	 */
	static private function executeNotFound($applicationController=''){
		if(!$applicationController){
			$applicationController = new ApplicationController();
		}
		PluginManager::notifyFromController('beforeNotFoundAction', $applicationController);
		if(method_exists($applicationController, 'notFoundAction')){
			call_user_func_array(array($applicationController, 'notFoundAction'), Router::getAllParameters());
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Establece el directorio de los controladores
	 *
	 * @access public
	 * @param string $directory
	 * @static
	 */
	static public function setControllerDir($directory){
		self::$_controllersDir = $directory;
	}

	/**
	 * Establece el controlador interno de Dispatcher
	 *
	 * @access public
	 * @param Object $controller
	 * @static
	 */
	static public function setController($controller){
		self::$_controller = $controller;
	}

	/**
	 * Ejecuta el filtro before presente en el controlador
	 *
	 * @access public
	 * @param mixed $appController
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 * @static
	 */
	static private function _runBeforeFilters($appController, $controller, $action, $params){

		/**
		 * El metodo beforeFilter es llamado antes de ejecutar una accion en un
		 * controlador, puede servir para realizar ciertas validaciones
		 */
		self::$_requestStatus = self::STATUS_RUNNING_BEFORE_FILTERS;
		if(method_exists($appController, 'beforeFilter')){
			if(call_user_func_array(array(self::$_controller, 'beforeFilter'), $params)===false){
				return false;
			}
		} else {
			if(isset(self::$_controller->beforeFilter)){
				if(call_user_func_array(array(self::$_controller, self::$_controller->beforeFilter), $params)===false){
					return false;
				}
			}
		}
	}

	/**
	 * Corre los filtros after en el controlador actual
	 *
	 * @param string $appController
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 * @static
	 */
	static private function _runAfterFilters($appController, $controller, $action, $params){
		/**
		 * El metodo afterFilter es llamado despues de ejecutar una accion en un
		 * controlador, puede servir para realizar ciertas validaciones
		 */
		self::$_requestStatus = self::STATUS_RUNNING_BEFORE_FILTERS;
		if(method_exists($appController, 'afterFilter')){
			call_user_func_array(array(self::$_controller, 'afterFilter'), $params);
		} else {
			if(isset(self::$_controller->afterFilter)){
				call_user_func_array(array(self::$_controller, self::$_controller->afterFilter), $params);
			}
		}
	}

	/**
	 * Incluye los componentes para ejecutar la petición
	 *
	 * @access public
	 * @static
	 */
	static public function initComponents(){
		if(self::$_initializedComponents==false){
			self::$_initializedComponents = true;
		} else {
			return;
		}
	}

	/**
	 * Agrega una clase que no debe ser serializada
	 *
	 * @access 	public
	 * @param 	string $className
	 * @static
	 */
	static public function addNotSerializableClass($className){
		self::$_notSerializableClasses[] = $className;
	}

	/**
	 * Realiza el dispatch de una ruta
	 *
	 * @access 	public
	 * @param	string $module
	 * @param 	string $controller
	 * @param 	string $action
	 * @param 	array $parameters
	 * @param 	array $allParameters
	 * @return 	boolean
	 * @static
	 */
	static public function executeRoute($module, $controller, $action, $parameters, $allParameters){

		// Aplicacion activa
		$activeApp = Router::getApplication();

		if($module!=''){
			$controllersDir = self::$_controllersDir.'/'.$module;
		} else {
			$controllersDir = self::$_controllersDir;
		}
		$notFoundExecuted = false;
		$appController = $controller.'Controller';
		if(class_exists($appController, false)==false){
			if(Core::fileExists($controllersDir.'/'.$controller.'_controller.php')){
				require $controllersDir.'/'.$controller.'_controller.php';
			} else {
				$applicationController = new ApplicationController();
				if(self::executeNotFound($applicationController)==false){
					//No se encontro el controlador
					$message = CoreLocale::getErrorMessage(-102, $controller);
					self::throwException($message, self::NOT_FOUND_FILE_CONTROLLER);
				} else {
					self::$_controller = $applicationController;
					$notFoundExecuted = true;
				}
			}
		}

		// Inicia la sesion de acuerdo al adaptador instalado
		Session::startSession();

		// Incializa el nombre de la instancia
		Core::setInstanceName();

		if(class_exists($controller.'Controller', false)){

			//Inicializa los componentes del Framework
			self::initComponents();

			// Dispatcher mantiene referencias los controladores instanciados
			$instanceName = Core::getInstanceName();
			if(!isset(self::$_controllerReferences[$appController])){
				if(!isset($_SESSION['KCON'][$instanceName][$activeApp][$module][$appController])){
					self::$_controller = new $appController();
				} else {
					// Obtiene el objeto persistente
					$persistedData = $_SESSION['KCON'][$instanceName][$activeApp][$module][$appController];
					if($persistedData['status']=='C'){
						$persistedData['data'] = gzuncompress($persistedData['data']);
					}
					self::$_controller = unserialize($persistedData['data']);
				}
				self::$_controllerReferences[$appController] = self::$_controller;
				// Envia a la persistencia por si se genera una excepcion no controlada
				if(self::$_controller->getPersistance()==true){
					$_SESSION['KCON'][$instanceName][$activeApp][$module][$appController] = array(
						'data' => serialize(self::$_controller),
						'time' => Core::getProximityTime(),
						'status' => 'N'
						);
				}
			} else {
				self::$_controller = self::$_controllerReferences[$appController];
			}

			self::$_controller->setResponse('');
			self::$_controller->setControllerName($controller);
			self::$_controller->setActionName($action);

			if(isset($parameters[0])){
				self::$_controller->setId($parameters[0]);
			} else {
				self::$_controller->setId('');
			}
			self::$_controller->setAllParameters($allParameters);
			self::$_controller->setParameters($parameters);

			try {

				// Se ejecutan los filtros before
				if(self::_runBeforeFilters($appController, $controller, $action, $parameters)===false){
					return self::$_controller;
				}

				//Se ejecuta el metodo con el nombre de la accion en la clase mas el sufijo Action
				$actionMethod = $action.'Action';
				self::$_requestStatus = self::STATUS_DISPATCHING;
				if(method_exists(self::$_controller, $actionMethod)==false){
					if(method_exists(self::$_controller, 'notFoundAction')){
						call_user_func_array(array(self::$_controller, 'notFoundAction'), Router::getAllParameters());
						return self::$_controller;
					} else {
						//No se encontró la acción
						$message = CoreLocale::getErrorMessage(-100, $action, $controller, $action);
						self::throwException($message, Dispatcher::NOT_FOUND_ACTION);
					}
				}

				self::$_requestStatus = self::STATUS_RUNNING_CONTROLLER_ACTION;
				#if[compile-time]
				$method = new ReflectionMethod($appController, $actionMethod);
				if($method->isPublic()==false){
					$message = CoreLocale::getErrorMessage(-104, $action);
					self::throwException($message, self::INVALID_METHOD_CALLBACK);
				}
				$methodParameters = $method->getParameters();
				$paramNumber = 0;
				foreach($methodParameters as $methodParameter){
					if($methodParameter->isOptional()==false&&!isset($parameters[$paramNumber])){
						//Numero inválido de argumentos
						$message = CoreLocale::getErrorMessage(-105, $methodParameter->getName(), $action);
						self::throwException($message, self::INVALID_ARGUMENT_NUMBER);
					}
					++$paramNumber;
				}
				#endif
				self::$_valueReturned = call_user_func_array(array(self::$_controller, $actionMethod), $parameters);

				//Corre los filtros after
				self::_runAfterFilters($appController, $controller, $action, $parameters);
				self::$_requestStatus = self::STATUS_RENDER_PRESENTATION;

			}
			catch(Exception $e){

				// Notifica la excepcion a los Plugins
				$cancelThrowException = PluginManager::notifyFromApplication('onControllerException', $e);

				if(method_exists(self::$_controller, 'onException')){
					self::$_controller->onException($e);
				} else {
					if($cancelThrowException==false){
						throw $e;
					}
				}
			}

			// Se clona el controlador y se serializan las propiedades que no sean instancias de modelos
			if(self::$_controller->getPersistance()==true){
				$controller = clone self::$_controller;
				try {
					self::$_requestStatus = self::STATUS_RUNNING_BEFORE_STORE_PERSISTENCE;
					if(method_exists($controller, 'beforeStorePersistence')){
						$controller->beforeStorePersistence();
					}
					foreach($controller as $property => $value){
						if(is_object($value)){
							foreach(self::$_notSerializableClasses as $className){
								if(is_subclass_of($value, $className)){
									unset($controller->{$property});
								}
							}
						}
					}
					if(isset($_SESSION['KCON'][$instanceName][$activeApp][$module][$appController])){
						$_SESSION['KCON'][$instanceName][$activeApp][$module][$appController] = array(
							'data' => serialize($controller),
							'time' => Core::getProximityTime(),
							'status' => 'N'
							);
					}
					self::$_requestStatus = self::STATUS_RUNNING_AFTER_STORE_PERSISTENCE;
				}
				catch(PDOException $e){
					throw new CoreException($e->getMessage(), $e->getCode());
				}
			}
			return self::$_controller;
		} else {
			if($notFoundExecuted==false){
				//No se encontró el controlador
				$message = CoreLocale::getErrorMessage(-101, $appController);
				self::throwException($message, self::NOT_FOUND_CONTROLLER);
			} else {
				return $applicationController;
			}
		}
	}

	/**
	 * Obtener el controlador en ejecucion
	 *
	 * @access public
	 * @return mixed
	 * @static
	 */
	public static function getController(){
		return self::$_controller;
	}

	/**
	 * Devuelve el valor devuelto por el metodo ejecutado en la ultima accion
	 *
	 * @access 	public
	 * @return	mixed
	 * @static
	 */
	public static function getValueReturned(){
		return self::$_valueReturned;
	}

	/**
	 * Devuelve el estado de ejecucion de la peticion
	 *
	 * @access public
	 * @static
	 */
	public static function getDispatchStatus(){
		return self::$_requestStatus;
	}

	/**
	 * Indica si el estado de ejecucion es la logica de Controlador
	 *
	 * @access public
	 * @return boolean
	 * @static
	 */
	public static function isRunningController(){
		return self::$_requestStatus == Dispatcher::STATUS_RUNNING_CONTROLLER_ACTION;
	}

	/**
	 * Indica si el estado de ejecucion de la aplicacion esta a nivel de usuario
	 *
	 * @access public
	 * @return boolean
	 * @static
	 */
	public static function isRunningUserLevel(){
		return self::$_requestStatus != self::STATUS_DISPATCHING;
	}

	/**
	 * Lanza una excepción de tipo DispatcherException
	 *
	 * @access public
	 * @throws DispatcherException
	 * @static
	 */
	public static function throwException($message, $code){
		throw new DispatcherException($message, $code);
	}

}
