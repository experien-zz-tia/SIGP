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
 * @category 	Kumbia
 * @package 	View
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license 	New BSD License
 * @version 	$Id: View.php 97 2009-09-30 19:28:13Z gutierrezandresfelipe $
 */

/**
 * View
 *
 * El componente View se encarga de administrar la forma estándar en la que se
 * genera la presentación al usuario final en su explorador. La presentación
 * estándar en una aplicación en Kumbia Enterprise se basa en varios patrones
 * de diseño que permiten reducir la codificación y hacer más mantenible
 * esta parte del desarrollo.
 *
 * El primer patrón utilizado es Template View el cuál habla de utilizar
 * tags personalizados ó marcas embebidas en el contenido dinámico proporcionando
 * flexibilidad y poder para crear interfaces web. El segundo patrón es el
 * Two State View el cual permite definir múltiples interfaces de acuerdo
 * al dispositivo ó cliente desde el cuál se este se accediendo a la aplicación.
 *
 * Este tipo de implementación favorece principalmente aplicaciones
 * que accedan desde un browser ó un telefono celular en donde es
 * necesario personalizar detalles para cada tipo de interfaz.
 *
 * La arquitectura MVC presenta el concepto de vista la cuál actúa como
 * puente entre el usuario final y la lógica de dominio en los controladores.
 *
 * @category 	Kumbia
 * @package 	View
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license 	New BSD License
 * @access	public
 * @abstract
 */
abstract class View {

	/**
	 * Nivel de presentación: Hasta la vista principal
	 *
	 */
	const LEVEL_MAIN_VIEW = 6;

	/**
	 * Nivel de presentación: Hasta los template after
	 *
	 */
	const LEVEL_AFTER_TEMPLATE = 5;

	/**
	 * Nivel de presentación: Hasta el layout del controlador
	 *
	 */
	const LEVEL_LAYOUT = 3;

	/**
	 * Nivel de presentación: Hasta los template before
	 *
	 */
	const LEVEL_BEFORE_TEMPLATE = 2;

	/**
	 * Nivel de presentación: Hasta la vista de la accion
	 *
	 */
	const LEVEL_ACTION_VIEW = 1;

	/**
	 * Nivel de presentación: No utilizar ninguna vista
	 *
	 */
	const LEVEL_NO_RENDER = 0;

	/**
	 * Cachea la salida al navegador
	 *
	 * @var string
	 */
	static private $_content = "";

	/**
	 * Variables de la Vista
	 *
	 * @var array
	 */
	static private $_data = array();

	/**
	 * Nivel de profundidad de la visualización
	 *
	 * @var integer
	 */
	static private $_renderLevel = 6;

	/**
	 * Proxy a componente de Terceros
	 *
	 * @var string
	 */
	static private $_proxyProvider;

	/**
	 * Opciones del proxy
	 *
	 * @var array
	 */
	static private $_proxyOptions;

	/**
	 * Componente usado como PluginManager
	 *
	 * @var array
	 */
	static private $_pluginManager = 'PluginManager';

	/**
	 * Envia la salida en buffer al navegador
	 *
	 * @access 	public
	 * @param 	boolean $returnContent
	 * @return 	string
	 * @static
	 */
	public static function getContent($returnContent=false){
		if($returnContent==false){
			print self::$_content;
		} else {
			return self::$_content;
		}
		return "";
	}

	/**
	 * Establece el componente que será usado como PluginManager
	 *
	 * @access 	public
	 * @param 	array $pluginManager
	 * @static
	 */
	public static function setPluginManager($pluginManager){
		self::$_pluginManager = $pluginManager;
	}

	/**
	 * Inicializa la salida
	 *
	 * @access 	private
	 * @param 	$controllerName
	 * @param 	$actionName
	 * @static
	 */
	static private function _startResponse($controllerName, $actionName){
		$controllerResponse = ControllerResponse::getInstance();
		//Establece una salida normal
		$controllerResponse->setHeader('X-Application-State: OK', true);
		//Establece la ubicacion actual
		$location = $controllerName.'/'.$actionName;
		$controllerResponse->setHeader('X-Application-Location: '.$location, true);
		call_user_func_array(array(self::$_pluginManager, 'notifyFromView'), array('beforeRender', $controllerResponse));
	}

	/**
	 * Carga el adaptador de View
	 *
	 * @access	private
	 * @static
	 */
	static private function _loadAdapter(){
		$controllerResponse = ControllerResponse::getInstance();
		$adapter = ucfirst($controllerResponse->getResponseAdapter());
		$adapterClassName = $adapter.'ViewResponse';
		if(!class_exists($adapterClassName, false)){
			if(!interface_exists('ViewResponseInterface')){
				require 'Library/Kumbia/View/Interface.php';
			}
			$path = 'Library/Kumbia/View/Adapters/'.$adapter.'.php';
			if(Core::fileExists($path)==true){
				require $path;
			}
		}
		return new $adapterClassName();
	}

	/**
	 * Visualiza un valor con el adaptador de presentación
	 *
	 * @param string $value
	 */
	static private function _handleResponseAdapter($value){
		$controllerResponse = ControllerResponse::getInstance();
		if($controllerResponse->getResponseAdapter()){
			$responseHandler = self::_loadAdapter();
			$responseHandler->render($controllerResponse, $value);
		}
	}

	/**
	 * Envia la excepcion al adaptador de presentación
	 *
	 * @param Exception $e
	 */
	static private function _handleExceptionAdapter($e){
		$controllerResponse = ControllerResponse::getInstance();
		if($controllerResponse->getResponseAdapter()){
			$responseHandler = self::_loadAdapter();
			$responseHandler->renderException($controllerResponse, $e);
		}
	}

	/**
	 * Toma el objeto controlador y ejecuta la presentaci&oacute;n correspondiente a este
	 *
	 * @access 	public
	 * @param 	Controller $controller
	 * @static
	 */
	static public function handleViewRender($controller){
		$controllerResponse = ControllerResponse::getInstance();
		$_valueReturned = Dispatcher::getValueReturned();
		if($controllerResponse->getResponseType()!=ControllerResponse::RESPONSE_NORMAL){
			self::_handleResponseAdapter($_valueReturned);
			call_user_func_array(array(self::$_pluginManager, 'notifyFromView'), array('afterRender', $controllerResponse));
			return;
		}
		$controllerName = $controller->getControllerName();
		$actionName = $controller->getActionName();
		self::_startResponse($controllerName, $actionName);
		if(!empty($controllerName)){
			foreach(EntityManager::getEntities() as $_entityName => $_entity){
				$$_entityName = $_entity;
			}
			if($controller->isExportable()==true){
				foreach($controller as $_var => $_value) {
					$$_var = $_value;
				}
			}
			foreach(self::$_data as $_key => $_value){
				$$_key = $_value;
			}
			if(!isset($id)){
				$id = $controller->getId();
			}

			/**
			 * View busca un los templates correspondientes al nombre de la accion y el layout
			 * del controlador. Si el controlador tiene un atributo $template tambien va a
			 * cargar la vista ubicada en layouts con el valor de esta
			 *
			 * en views/$controller/$action
			 * en views/layouts/$controller
			 * en views/layouts/$template
			 *
			 * Los archivos con extension .phtml son archivos template de kumbia que
			 * tienen codigo html y php y son el estandar
			 *
			 */
			self::$_content = ob_get_contents();

			/**
			 * Verifica si existe cache para el layout, vista &oacute; template
			 * sino, crea un directorio en cache
			 */
			if($controllerName!=""){

				$activeApp = Router::getActiveApplication();
				$_viewsDir = Core::getActiveViewsDir();

				/**
				 * Crear los directorios de cache si es necesario
				 */
				if($controller->getViewCache()||$controller->getLayoutCache()){
					$viewCacheDir = 'cache/'.session_id().'/';
					if(!Core::fileExists('cache/'.session_id().'/')){
						mkdir($viewCacheDir);
					}

					$viewCacheDir.=$activeApp.'_'.$controllerName;
					if(!Core::fileExists($viewCacheDir)){
						mkdir($viewCacheDir);
					}
				}

				/**
				 * Insertar la vista si es necesario
				 */
				if(self::$_renderLevel>=self::LEVEL_ACTION_VIEW){
					if(Core::fileExists($_viewsDir.'/'.$controllerName.'/'.$actionName.'.phtml')){
						ob_clean();
						/**
						 * Aqui verifica si existe un valor en minutos para el cache
						 */
						if($controller->getViewCache()>0){
							/**
							 * Busca el archivo en el directorio de cache que se crea
							 * a partir del valor $_SESSION['SID'] para que sea único
							 * para cada sesi&oacute;n
							 */
							if(Core::fileExists($viewCacheDir.'/'.$actionName)==false){
								include $_viewsDir.'/'.$controllerName.'/'.$actionName.'.phtml';
								file_put_contents($viewCacheDir."/$actionName", ob_get_contents());
							} else {
								$time_cache = $controller->get_view_cache();
								if((time()-$time_cache*60)<filemtime("$viewCacheDir/$actionName")){
									include "$viewCacheDir/$actionName";
								} else {
									include "$_viewsDir/$controllerName/$actionName.phtml";
									file_put_contents($viewCacheDir."/$actionName", ob_get_contents());
								}
							}
						} else {
							include $_viewsDir.'/'.$controllerName.'/'.$actionName.'.phtml';
						}
						self::$_content = ob_get_contents();
					}
				}

				/**
				 * Incluir el/los Template(s) before
				 */
				if(self::$_renderLevel>=self::LEVEL_BEFORE_TEMPLATE){
					$_template = $controller->getTemplateBefore();
					if($_template!=""){
						if(is_array($_template)==false){
							/**
							 * Aqui verifica si existe un valor en minutos para el cache
							 */
							if(Core::fileExists($_viewsDir.'/layouts/'.$controller->getTemplateBefore().'.phtml')){
								ob_clean();
								if($controller->getLayoutCache()){
									/**
									 * Busca el archivo en el directorio de cache que se crea
									 * a partir del valor session_id() para que sea único
									 * para cada sesion
									 */
									if(!Core::fileExists($viewCacheDir.'/layout')){
										include "$_viewsDir/layouts/".$controller->getTemplateBefore().".phtml";
										file_put_contents($viewCacheDir."/layout", ob_get_contents());
									} else {
										$time_cache = $controller->getLayoutCache();
										if((time()-$time_cache*60)<filemtime($viewCacheDir."/layout")){
											include $viewCacheDir."/layout";
										} else {
											include "$_viewsDir/layouts/".$controller->getTemplateBefore().".phtml";
											file_put_contents($viewCacheDir."/layout", ob_get_contents());
										}
									}
								} else {
									include $_viewsDir.'/layouts/'.$controller->getTemplateBefore().'.phtml';
								}
								self::$_content = ob_get_contents();
							} else {
								throw new ViewException("No existe el template '$_template' en views/layouts");
							}
						} else {
							foreach(array_reverse($_template) as $_singleTemplate){
								/**
								 * Aqui verifica si existe un valor en minutos para el cache
								 */
								if(Core::fileExists("$_viewsDir/layouts/$_singleTemplate.phtml")){
									ob_clean();
									if($controller->getLayoutCache()){
										/**
										 * Busca el archivo en el directorio de cache que se crea
										 * a partir del valor session_id() para que sea único
										 * para cada sesion
										 */
										if(!Core::fileExists($viewCacheDir."/layout")){
											include "$_viewsDir/layouts/$_singleTemplate.phtml";
											file_put_contents($viewCacheDir."/layout", ob_get_contents());
										} else {
											$time_cache = $controller->getLayoutCache();
											if((time()-$time_cache*60)<filemtime($viewCacheDir."/layout")){
												include $viewCacheDir."/layout";
											} else {
												include "$_viewsDir/layouts/$_singleTemplate.phtml";
												file_put_contents($viewCacheDir."/layout", ob_get_contents());
											}
										}
									} else {
										include "$_viewsDir/layouts/$_singleTemplate.phtml";
									}
									self::$_content = ob_get_contents();
								} else {
									throw new ViewException("No existe el template '$_singleTemplate' en views/layouts");
								}
							}
						}
					}
				}

				/**
				 * Incluir Layout
				 */
				if(self::$_renderLevel>=self::LEVEL_LAYOUT){
					if(Core::fileExists($_viewsDir.'/layouts/'.$controllerName.'.phtml')){
						ob_clean();
						if($controller->getLayoutCache()){
							/**
							 * Busca el archivo en el directorio de cache que se crea
							 * a partir del valor session_id() para que sea único
							 * para cada sesion
							 */
							if(!Core::fileExists($viewCacheDir.'/layout')){
								include $_viewsDir.'/layouts/'.$controllerName.'.phtml';
								file_put_contents($viewCacheDir.'/layout', ob_get_contents());
							} else {
								$time_cache = $controller->getLayoutCache();
								if((time()-$time_cache*60)<filemtime($viewCacheDir.'/layout')){
									include $viewCacheDir.'/layout';
								} else {
									include $_viewsDir.'/layouts/'.$controllerName.'.phtml';
									file_put_contents($viewCacheDir.'/layout', ob_get_contents());
								}
							}
						} else {
							include $_viewsDir.'/layouts/'.$controllerName.'.phtml';
						}
						self::$_content = ob_get_contents();
					}
				}
			}

			/**
			 * Incluir el/los Template(s) After
			 */
			if(self::$_renderLevel>=self::LEVEL_AFTER_TEMPLATE){
				$_template = $controller->getTemplateAfter();
				if($_template!=""){
					if(is_array($_template)==false){
						/**
						 * Aqui verifica si existe un valor en minutos para el cache
						 */
						if(Core::fileExists($_viewsDir.'/layouts/'.$controller->getTemplateAfter().'.phtml')){
							ob_clean();
							if($controller->getLayoutCache()){
								/**
								 * Busca el archivo en el directorio de cache que se crea
								 * a partir del valor session_id() para que sea único
								 * para cada sesion
								 */
								if(!Core::fileExists($viewCacheDir.'/layout')){
									include $_viewsDir.'/layouts/'.$controller->getTemplateAfter().".phtml";
									file_put_contents($viewCacheDir."/layout", ob_get_contents());
								} else {
									$time_cache = $controller->getLayoutCache();
									if((time()-$time_cache*60)<filemtime($viewCacheDir."/layout")){
										include $viewCacheDir."/layout";
									} else {
										include "$_viewsDir/layouts/".$controller->getTemplateAfter().".phtml";
										file_put_contents($viewCacheDir."/layout", ob_get_contents());
									}
								}
							} else {
								include $_viewsDir.'/layouts/'.$controller->getTemplateAfter().'.phtml';
							}
							self::$_content = ob_get_contents();
						} else {
							throw new ViewException("No existe el template '$_template' en views/layouts");
						}
					} else {
						foreach(array_reverse($_template) as $_singleTemplate){
							/**
							 * Aqui verifica si existe un valor en minutos para el cache
							 */
							if(Core::fileExists($_viewsDir.'/layouts/'.$_singleTemplate.'.phtml')){
								ob_clean();
								if($controller->getLayoutCache()){
									/**
									 * Busca el archivo en el directorio de cache que se crea
									 * a partir del valor session_id() para que sea único
									 * para cada sesion
									 */
									if(!Core::fileExists($viewCacheDir.'/layout')){
										include $_viewsDir.'/layouts/'.$_singleTemplate.'.phtml';
										file_put_contents($viewCacheDir.'/layout', ob_get_contents());
									} else {
										$time_cache = $controller->getLayoutCache();
										if((time()-$time_cache*60)<filemtime($viewCacheDir.'/layout')){
											include $viewCacheDir.'/layout';
										} else {
											include $_viewsDir.'/layouts/'.$_singleTemplate.'.phtml';
											file_put_contents($viewCacheDir.'/layout', ob_get_contents());
										}
									}
								} else {
									include $_viewsDir.'/layouts/'.$_singleTemplate.'.phtml';
								}
								self::$_content = ob_get_contents();
							} else {
								throw new ViewException("No existe el template '$_singleTemplate' en views/layouts");
							}
						}
					}
				}
			}

			/**
			 * Incluir Vista Principal
			 */
			if(self::$_renderLevel>=self::LEVEL_MAIN_VIEW){
				if(Core::fileExists($_viewsDir.'/index.phtml')){
					ob_clean();
					include $_viewsDir.'/index.phtml';
					self::$_content = ob_get_contents();
				}
				$controller = null;
				if(Core::isTestingMode()==true){
					ob_clean();
				}
			} else {
				ob_end_flush();
			}
		}
		call_user_func_array(array(self::$_pluginManager, 'notifyFromView'), array('afterRender', $controllerResponse));
	}

	/**
	 * Administra la presentación cuando se genera una excepción en la presentación
	 *
	 * @access 	public
	 * @param 	Exception $e
	 * @param 	Controller $controller
	 * @static
	 */
	static public function handleViewExceptions($e, $controller){
		if(Core::isTestingMode()==false){
			if(!$controller){
				$controller = new Controller();
			}
			$controllerResponse = ControllerResponse::getInstance();
			$controllerRequest = ControllerRequest::getInstance();
			//Se está solicitando contenido estático
			if($controllerRequest->isRequestingStaticContent()==true){
				$controllerResponse->setHeader('X-Application-State: Exception', true);
				$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
				if(get_class($e)=='DisptacherException'){
					return;
				}
			}
			//Se genera un encabezado HTTP de problema
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			// Si el encabezado solicita la salida en de la excepcion en XML se realiza asi
			if(isset($_SERVER['HTTP_X_ACCEPT_CONTENT'])&&$_SERVER['HTTP_X_ACCEPT_CONTENT']=='text/xml'){
				//Genera una salida XML valida
				$controllerResponse->setHeader('Content-Type: text/xml', true);
				$controllerResponse->setHeader('Pragma: no-cache', true);
				$controllerResponse->setHeader('Expires: 0', true);
				ob_end_clean();
				print $e->showMessageAsXML();
			} else {
				// Si no es una Accion AJAX incluye index.phtml y muestra
				// el contenido de las excepciones dentro de este.
				if($controllerResponse->getResponseAdapter()!='json'){
					Tag::removeStylesheets();
					ob_clean();
					$e->showMessage();
					self::$_content = ob_get_contents();
					ob_end_clean();
					View::xhtmlTemplate('white');
				} else {
					self::_handleExceptionAdapter($e);
				}
			}
		} else {
			throw $e;
		}
	}

	/**
	 * Permite visualizar una vista parcial
	 *
	 * @access	public
	 * @param	string $_partialView
	 * @param	string $_partialValue
	 * @static
	 */
	public static function renderPartial($_partialView, $_partialValue=''){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params['controller'])){
			$controllerName = Router::getController();
		} else {
			$controllerName = $params['controller'];
		}
		$_viewsDir = Core::getActiveViewsDir();
		$partialPath = $_viewsDir.'/'.$controllerName.'/_'.$_partialView.'.phtml';
		if(Core::fileExists($partialPath)==false){
			$partialPath = $_viewsDir.'/partials/_'.$_partialView.'.phtml';
			if(Core::fileExists($partialPath)==false){
				throw new ViewException('No se puede encontrar la vista parcial: "'.$_partialView.'"', 0);
			}
		}
		foreach(EntityManager::getEntities() as $_entityName => $_entity){
			$$_entityName = $_entity;
		}
		foreach(self::$_data as $_key => $_value){
			$$_key = $_value;
		}
		$controller = Dispatcher::getController();
		if($controller->isExportable()==true){
			foreach($controller as $_var => $_value) {
				$$_var = $_value;
			}
			$id = $controller->getId();
		}
		$$_partialView = $_partialValue;
		include $partialPath;
	}

	/**
	 * Permite renderizar una vista del controlador actual
	 *
	 * @access 	public
	 * @param	string $_view
	 * @static
	 */
	static public function renderView($_view){
		$_viewsDir = Core::getActiveViewsDir();
		if(Core::fileExists($_viewsDir.'/'.$_view.'.phtml')){
			$_controller = Dispatcher::getController();
			if($_controller->isExportable()){
				foreach($_controller as $_var => $_value) {
					$$_var = $_value;
				}
				foreach(self::$_data as $_key => $_value){
					$$_key = $_value;
				}
				$id = $_controller->getId();
			}
			include $_viewsDir.'/'.$_view.'.phtml';
		} else {
			throw new ViewException("La vista '$_view' no existe o no se puede cargar", 0);
		}
	}

	/**
	 * Devuelve los mensajes de validacion generados en el controlador
	 *
	 * @access	public
	 * @return	array
	 * @static
	 */
	public static function getValidationMessages(){
		$controller = Dispatcher::getController();
		return $controller->getValidationMessages();
	}

	/**
	 * Permite definir el contenido de salida
	 *
	 * @access 	public
	 * @param 	string $content
	 * @static
	 */
	public static function setContent($content){
		self::$_content = $content;
	}

	/**
	 * Establece una variable de vista
	 *
	 * @access 	public
	 * @param	string $index
	 * @param 	string $value
	 */
	public static function setViewParam($index, $value){
		self::$_data[$index] = $value;
	}

	/**
	 * Devuelve las variables de vistas
	 *
	 * @access	public
	 * @return	array
	 * @static
	 */
	public static function getViewParams(){
		return self::$_data;
	}

	/**
	 * Establece el nivel de profundidad de la visualización
	 *
	 * @access 	public
	 * @param 	int $level
	 * @static
	 */
	public static function setRenderLevel($level){
		self::$_renderLevel = $level;
	}

	/**
	 * Establece el proxy provider para las vistas
	 *
	 * @access 	public
	 * @param 	string $proxy
	 * @param 	array $options
	 * @static
	 */
	public static function setProxyProvider($proxy, $options){
		self::$_proxyProvider = $proxy;
		self::$_proxyOptions = $options;
	}

	/**
	 * Reenvia las peticiones de vistas a otros componentes de terceros
	 *
	 * @access public
	 * @static
	 */
	public static function proxyHandler(){
		//Cargar el ProxyProvider
		$path = 'Library/Kumbia/View/Proxy/'.self::$_proxyProvider.'.php';
		if(Core::fileExists($path)){
			require $path;
			$proxyClass = self::$_proxyProvider.'ProxyView';
			$proxyAdapter = new $proxyClass(self::$_proxyOptions);

			$controller = Dispatcher::getController();
			$controllerName = $controller->getControllerName();
			$actionName = $controller->getActionName();
			self::_startResponse($controllerName, $actionName);

			//Exportar datos
			foreach(EntityManager::getEntities() as $_entityName => $_entity){
				$proxyAdapter->setData($_entityName, $_entity);
			}
			if($controller->isExportable()==true){
				foreach($controller as $_var => $_value){
					$proxyAdapter->setData($_var, $_value);
				}
			}
			foreach(self::$_data as $_key => $_value){
				$proxyAdapter->setData($_key, $_value);
			}
			$proxyAdapter->setData('id', $controller->getId());

			//Salida del controlador
			self::$_content = ob_get_contents();

			if($controllerName!=""){
				$activeApp = Router::getActiveApplication();
				$_viewsDir = Core::getActiveViewsDir();
				// Insertar la vista si es necesario
				if(self::$_renderLevel>=self::LEVEL_ACTION_VIEW){
					$path = $_viewsDir.'/'.$controllerName.'/';
					if(Core::fileExists($path.$actionName.'.phtml')){
						ob_clean();
						echo $proxyAdapter->renderView($path, $actionName);
						self::$_content = ob_get_contents();
					}
				}

				//Incluir el/los Template(s) before
				if(self::$_renderLevel>=self::LEVEL_BEFORE_TEMPLATE){
					$_template = $controller->getTemplateBefore();
					if($_template!=""){
						if(is_array($_template)==false){
							/**
							 * Aqui verifica si existe un valor en minutos para el cache
							 */
							$path = $_viewsDir.'/layouts/';
							if(Core::fileExists($path.$controller->getTemplateBefore().'.phtml')){
								ob_clean();
								echo $proxyAdapter->renderView($path.$controller->getTemplateBefore());
								self::$_content = ob_get_contents();
							} else {
								throw new ViewException("No existe el template '$_template' en views/layouts");
							}
						} else {
							foreach(array_reverse($_template) as $_singleTemplate){
								// Aqui verifica si existe un valor en minutos para el cache
								$path = $_viewsDir.'/layouts/';
								if(Core::fileExists($path.$_singleTemplate.'.phtml')){
									ob_clean();
									echo $proxyAdapter->renderView($path, $_singleTemplate);
									self::$_content = ob_get_contents();
								} else {
									throw new ViewException("No existe el template '$_singleTemplate' en views/layouts");
								}
							}
						}
					}
				}

				// Incluir Layout
				if(self::$_renderLevel>=self::LEVEL_LAYOUT){
					$path = $_viewsDir.'/layouts/';
					if(Core::fileExists($path.$controllerName.'.phtml')){
						ob_clean();
						echo $proxyAdapter->renderView($path, $controllerName);
						self::$_content = ob_get_contents();
					}
				}
			}

			// Incluir el/los Template(s) After
			if(self::$_renderLevel>=self::LEVEL_AFTER_TEMPLATE){
				$_template = $controller->getTemplateAfter();
				if($_template!=""){
					if(is_array($_template)==false){
						// Aqui verifica si existe un valor en minutos para el cache
						$path = $_viewsDir.'/layouts/';
						if(Core::fileExists($path.$controller->getTemplateAfter().'.phtml')){
							ob_clean();
							echo $proxyAdapter->renderView($path, $controller->getTemplateAfter());
							self::$_content = ob_get_contents();
						} else {
							throw new ViewException("No existe el template '$_template' en views/layouts");
						}
					} else {
						foreach(array_reverse($_template) as $_singleTemplate){
							// Aqui verifica si existe un valor en minutos para el cache
							$path = $_viewsDir.'/layouts/';
							if(Core::fileExists($path.$_singleTemplate.'.phtml')){
								ob_clean();
								echo $proxyAdapter->renderView($path, $_singleTemplate);
								self::$_content = ob_get_contents();
							} else {
								throw new ViewException("No existe el template '$_singleTemplate' en views/layouts");
							}
						}
					}
				}
			}


			/**
			 * Incluir Vista Principal
			 */
			if(self::$_renderLevel>=self::LEVEL_MAIN_VIEW){
				if(Core::fileExists($_viewsDir.'/index.phtml')){
					ob_clean();
					include $_viewsDir.'/index.phtml';
					self::$_content = ob_get_contents();
				}
				$controller = null;
				if(Core::isTestingMode()==true){
					ob_clean();
				}
			} else {
				ob_end_flush();
			}

			$controllerResponse = ControllerResponse::getInstance();
			call_user_func_array(array(self::$_pluginManager, 'notifyFromView'), array('afterRender', $controllerResponse));
		} else {
			throw new ViewException('No existe el proxy a "'.self::$_proxyProvider.'"');
		}
	}

	/**
	 * Consulta si una vista de accion existe
	 *
	 * @param 	string $name
	 * @param 	string $controllerName
	 * @return	boolean
	 * @static
	 */
	public static function existsActionView($name, $controllerName=''){
		if($controllerName==''){
			$controllerName = Router::getController();
		}
		$_viewsDir = Core::getActiveViewsDir();
		$path = $_viewsDir.'/'.$controllerName.'/'.$name.'.phtml';
		return Core::fileExists($path);
	}

	/**
	 * Inserta un documento XHTML antes de una salida en buffer
	 *
	 * @access public
	 * @param string $template
	 * @static
	 */
	public static function xhtmlTemplate($template='template'){
		Tag::stylesheetLink("style");
		print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  '.Tag::getDocumentTitle().'
  <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />'."\n";
		Core::stylesheetLinkTags();
		print '</head>
 <body class="'.$template.'">';
		print View::getContent();
		print '
 </body>
</html>';
	}

}
