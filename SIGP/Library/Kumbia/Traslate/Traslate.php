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
 * @package 	Traslate
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license 	New BSD License
 * @version 	$Id: Traslate.php 97 2009-09-30 19:28:13Z gutierrezandresfelipe $
 */

/**
 * Traslate
 *
 * El componente Traslate permite la creación de aplicaciones multi-idioma usando
 * diferentes adaptadores para obtener las listas de traducción.
 *
 * @category Kumbia
 * @package Traslate
 * @copyright Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license New BSD License
 */
class Traslate extends Object {

	/**
	 * Objeto Adaptador
	 *
	 * @var mixed
	 */
	private $_adapter;

	/**
	 * Contructor de la clase Traslate
	 *
	 * @param string $adapter
	 * @param mixed $data
	 */
	public function __construct($adapter, $data){
		$adapterClass = $adapter.'Traslate';
		if(interface_exists('TraslateInterface')==false){
			require 'Library/Kumbia/Traslate/Interface.php';
		}
		if(class_exists($adapterClass, false)==false){
			$file = "Library/Kumbia/Traslate/Adapters/$adapter.php";
			if(Core::fileExists($file)==true){
				require $file;
			} else {
				throw new TraslateException("No existe el adaptador '$adapter'");
			}
		}
		$this->_adapter = new $adapterClass($data);
	}

	/**
	 * Traduce una cadena usando el adaptador interno
	 *
	 * @param string $traslate
	 * @return string
	 */
	public function _($traslate){
		return $this->_adapter->query($traslate);
	}

}
