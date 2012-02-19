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
 * @package 	Security
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license 	New BSD License
 * @version 	$Id: Security.php 82 2009-09-13 21:06:31Z gutierrezandresfelipe $
 */

/**
 * Security
 *
 * Clase que contiene metodos utiles para manejar seguridad
 *
 * @category 	Kumbia
 * @package 	Security
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license 	New BSD License
 * @access 		public
 */
abstract class Security extends Object {

	/**
	 * Objeto ACL que administra el acceso a la aplicacion
	 *
	 * @var Acl
	 */
	static private $_securityAccessList;

	/**
	 * Nombre del rol activo
	 *
	 * @var string
	 */
	static private $_roleName;

	/**
	 * Nombre del Rol por defecto
	 *
	 * @var string
	 */
	static private $_defaultRoleName = 'Public';

	/**
	 * Inicializa la lista de Acceso que controla la seguridad de una aplicacion
	 *
	 * @access public
	 * @static
	 */
	static public function initAccessManager(){
		$config = CoreConfig::readAppConfig();
		if(isset($config->application->securityAccessList)){
			$instanceName = Core::getInstanceName();
			self::$_securityAccessList = Acl::getAclFromDescriptor($config->application->securityAccessList);
			$activeApp = Router::getActiveApplication();
			if(isset($_SESSION['KSEC'][$instanceName][$activeApp]['roleName'])){
				self::$_roleName = $_SESSION['KSEC'][$instanceName][$activeApp]['roleName'];
			}
		}
	}

	/**
	 * Devuelve el nombre del rol activo en la sesion
	 *
	 * @return string
	 */
	static public function getActiveRole(){
		if(self::$_roleName!==null){
			return self::$_roleName;
		} else {
			$activeApp = Router::getActiveApplication();
			$instanceName = Core::getInstanceName();
			if(isset($_SESSION['KSEC'][$instanceName][$activeApp]['roleName'])){
				self::$_roleName = $_SESSION['KSEC'][$instanceName][$activeApp]['roleName'];
				return self::$_roleName = $_SESSION['KSEC'][$instanceName][$activeApp]['roleName'];
			} else {
				return 'Public';
			}
		}
	}

	/**
	 * Establece el nombre del rol por defecto
	 *
	 * @param string $roleName
	 */
	static public function setDefaultRole($roleName){
		self::$_defaultRoleName = $roleName;
	}

	/**
	 * Devuelve el nombre del rol por defecto
	 *
	 * @return string
	 * @static
	 */
	static public function getDefaultRole(){
		return self::$_defaultRoleName;
	}

	/**
	 * Establece el ó los roles del usuario activo en la aplicacion
	 *
	 * @param mixed $roleName
	 * @static
	 */
	static public function setActiveRole($roleName){
		self::$_roleName = $roleName;
		$instanceName = Core::getInstanceName();
		$_SESSION['KSEC'][$instanceName][$activeApp]['roleName'] = $roleName;
	}

	/**
	 * Valida que se tenga acceso al recurso solicitado
	 *
	 * @param mixed $resource
	 * @static
	 */
	static public function checkResourceAccess($resource){
		if(self::$_securityAccessList!==null){
			if(self::$_securityAccessList->isAllowed(self::getActiveRole(), Router::getController(), Router::getAction())==false){
				Router::routeTo(array('action' => 'unauthorizedAccess'));
			}
		}
	}

	/**
	 * Genera un INPUT tipo hidden con una llave unica utilizada
	 * para comprobaciones de validez en transacciones AJAX
	 *
	 * @param boolean $kumbia
	 * @return string
	 */
	static public function generateRSAKey($kumbia){
		$h = date("G")>12 ? 1 : 0;
		$time = uniqid().mktime($h, 0, 0, date("m"), date("d"), date("Y"));
		$key = sha1($time);
		$_SESSION['rsa_key'] = $key;
		$xCode = "<input type='hidden' id='rsa32_key' value='$key' />\r\n";
		if($kumbia){
			formsPrint($xCode);
		} else {
			return $xCode;
		}
		return "";
	}

	/**
	 * Crea un INPUT tipo hidden con una llave unica utilizada
	 * para comprobaciones de validez en transacciones AJAX
	 *
	 * @param boolean $kumbia
	 * @return string
	 */
	static public function createSecureRSAKey($kumbia=true){
		$config = CoreConfig::getInstanceConfig();
		if($config->kumbia->secure_ajax){
			if($_SESSION['rsa_key']){
				if((time()%8)==0){
					return generateRSAKey($kumbia);
				} else {
					if($kumbia){
						formsPrint("<input type='hidden' id='rsa32_key' value=\"{$_SESSION['rsa_key']}\"/> \r\n");
					} else {
						echo "<input type='hidden' id='rsa32_key' value=\"{$_SESSION['rsa_key']}\"/> \r\n";
					}
				}
			} else {
				return generateRSAKey($kumbia);
			}
		}
		return null;
	}

}
