<?php

/**
 * Kumbia Enteprise Framework
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
 * @package 	Router
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @license 	New BSD License
 * @version 	$Id: Resolver.php 88 2009-09-19 19:10:13Z gutierrezandresfelipe $
 */

/**
 * Resolver
 *
 * Este componente permite resolver los servicios web en el contenedor
 * de servicios ó mediante un naming directory service
 *
 * @category 	Kumbia
 * @package 	Resolver
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @license  	New BSD License
 * @abstract
 */
abstract class Resolver {

	/**
	 * Servicios resueltos
	 *
	 * @var array
	 */
	static private $_resolvedServices = array();

	/**
	 * Almacena el context-id de un servicio
	 *
	 * @access 	protected
	 * @static
	 */
	protected static function _setContextId($serviceName){
		$activeApp = Router::getActiveApplication();
		$instanceName = Core::getInstanceName();
		if(!isset($_SESSION['KRS'][$instanceName][$activeApp][$serviceName])){
			if(!isset($_SESSION['KRS'])){
				$_SESSION['KRS'] = array();
			}
			if(!isset($_SESSION['KRS'][$instanceName])){
				$_SESSION['KRS'][$instanceName] = array();
			}
			if(!isset($_SESSION['KRS'][$instanceName][$activeApp])){
				$_SESSION['KRS'][$instanceName][$activeApp] = array();
			}
			if(!isset($_SESSION['KRS'][$instanceName][$activeApp][$serviceName])){
				$contextId = md5(uniqid());
				$_SESSION['KRS'][$instanceName][$activeApp][$serviceName] = $contextId;
			}
		} else {
			$contextId = $_SESSION['KRS'][$instanceName][$activeApp][$serviceName];
		}
		#self::$_resolvedServices[$serviceName]->__setCookie('PHPSESSID', $contextId);
	}

	/**
	 * Localiza la ubicación de un servicio web
	 *
	 * @access 	public
	 * @param 	string $serviceName
	 * @return 	WebServiceClient
	 * @static
	 */
	public static function lookUp($serviceName){
		if(!isset(self::$_resolvedServices[$serviceName])){
			$instancePath = Core::getInstancePath();
			$activeApp = Router::getApplication();
			$servicePath = str_replace('.', '/', $serviceName);
			$serviceURL = 'http://'.$_SERVER['HTTP_HOST'].$instancePath.$activeApp.'/'.$servicePath;
			self::$_resolvedServices[$serviceName] = new WebServiceClient(array(
				'actor' => 'http://app-services/'.$serviceName,
				'location' => $serviceURL,
				'compression' => 0
			));
			self::_setContextId($serviceName);
		}
		return self::$_resolvedServices[$serviceName];
	}

}