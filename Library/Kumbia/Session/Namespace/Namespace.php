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
 * @package 	Session
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright 	Copyright (C) 2007-2007 Emilio Rafael Silveira Tovar (emilio.rst at gmail.com)
 * @license 	New BSD License
 * @version 	$Id: Namespace.php 5 2009-04-24 01:48:48Z gutierrezandresfelipe $
 */

/**
 * SessionNamespace
 *
 * Modelo orientado a objetos para el acceso a datos en Sesiones a través de espacios con nombres
 *
 * @category 	Kumbia
 * @package 	Session
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2008 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright 	Copyright (C) 2007-2007 Emilio Rafael Silveira Tovar (emilio.rst@gmail.com)
 * @license 	New BSD License
 * @access 		public
 * @abstract
 */
abstract class SessionNamespace extends Object {

	/**
	 * Prefijo usado para no confundir Namespaces con datos de sesion
	 *
	 * @var string
	 */
	static private $_preffix = 'ns_';

	/**
	 * A&ntilde;ade un namespace
	 *
	 * @param string $namespace nombre del espacio
	 * @param string $property nombre de la propiedad
	 * @param mixed $value valor
	 * @return stdClass
	 */
	static public function add($namespace, $property='', $value=''){
		$namespace = (string) $namespace;
		if(!Session::isSetData(self::$_preffix.$namespace)){
			Session::set(self::$_preffix.$namespace, new NamespaceContainer());
		}
		$obj_namespace = Session::getData(self::$_preffix.$namespace);
		if($property){
			$setProperty = "set".ucfirst($property);
			$obj_namespace->$setProperty($value);
		}
		return $obj_namespace;
	}

	/**
	 * Bloquea el namespace y lo hace de solo lectura
	 *
	 * @param string $namespace
	 */
	static public function lock($namespace){
		if(!Session::isSetData(self::$_preffix.$namespace)){
			Session::set(self::$_preffix.$namespace, new StdClass());
		}
		$obj_namespace = Session::getData(self::$_preffix.$namespace);
		$obj_namespace->setReadOnly(true);
	}

	/**
	 * Bloquea el namespace y lo hace de solo lectura
	 *
	 * @param string $namespace
	 */
	static public function unlock($namespace){
		if(!Session::isSetData(self::$_preffix.$namespace)){
			Session::set(self::$_preffix.$namespace, new StdClass());
		}
		$obj_namespace = Session::getData(self::$_preffix.$namespace);
		$obj_namespace->setReadOnly(false);
	}

	/**
	 * Obtiene los atributos de un namespace
	 *
	 * @param string $namespace
	 * @return object
	 */
	static public function get($namespace){
		return SessionNamespace::exists($namespace) ? Session::getData(self::$_preffix.$namespace) : null;
	}

	/**
	 * Verifica si existe el namespace
	 *
	 * @param string $namespace
	 * @return mixed
	 */
	static public function exists($namespace){
		return Session::issetData(self::$_preffix.$namespace);
	}

	/**
	 * Reinicia el namespace
	 *
	 * @param string $namespace
	 */
	static public function reset($namespace){
		Session::setData(self::$_preffix.$namespace, new NamespaceContainer());
	}

	/**
	 * Elimina un Namespace
	 *
	 * @param string $namespace
	 */
	static public function drop($namespace){
		Session::unsetData(self::$_preffix.$namespace);
	}
}

