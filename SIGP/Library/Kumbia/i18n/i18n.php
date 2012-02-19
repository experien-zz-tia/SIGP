<?php

/**
 * Kumbia Enterprise Framework
 *
 * LICENSE
 *
 * This source file is subject to the New BSD License that is bundled
 * with this package in the file docs/LICENSE.txt.

 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@loudertechnology.com so we can send you a copy immediately.
 *
 * @category	Kumbia
 * @package		i18n
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2008 Andres Felipe Gutierrez (gutierrezandresfelipe@gmail.com)
 * @license 	New BSD License
 * @version 	$Id: i18n.php 112 2009-10-31 13:12:43Z gutierrezandresfelipe $
 */

/**
 * i18n
 *
 * Implenta funciones de internacionalización
 *
 * @category 	Kumbia
 * @package 	i18n
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2008 Andres Felipe Gutierrez (gutierrezandresfelipe@gmail.com)
 * @license 	New BSD License
 * @abstract
 */
abstract class i18n {

	/**
	 * Indica si es posible utilizar unicode
	 *
	 * @var boolean
	 */
	private static $_unicodeEnabled = null;

	/**
	 * Indica si la extension Multi-Byteesta disponible
	 *
	 * @var boolean
	 */
	private static $_multiByteEnabled = null;

	/**
	 * Charset por defecto del Componente
	 *
	 * @var string
	 */
	private static $_defaultCharset = 'UTF-8';

	/**
	 * Permite determinar si es posible realizar operaciones de cadenas en Unicode
	 *
	 * @access public
	 * @static
	 */
	static public function isUnicodeEnabled(){
		if(self::$_unicodeEnabled!==null){
			return self::$_unicodeEnabled;
		} else {
			if(extension_loaded('mbstring')){
				mb_internal_encoding(self::$_defaultCharset);
				mb_regex_encoding(self::$_defaultCharset);
				self::$_multiByteEnabled = true;
			} else {
				self::$_multiByteEnabled = false;
			}
			self::$_unicodeEnabled = (@preg_match('/\pL/u', 'a')) ? true : false;
		}
	}

	/**
	 * Cambia una cadena de caracteres a minúsculas
	 *
	 * @access	public
	 * @param 	string $str
	 * @return 	string
	 * @static
	 */
	static public function strtolower($str){
		if(self::$_multiByteEnabled==false){
			return strtolower($str);
		} else {
			return mb_strtolower($str, self::$_defaultCharset);
		}
	}

	/**
	 * Cambia una cadena de caracteres a minúsculas
	 *
	 * @access 	public
	 * @param 	string $str
	 * @return 	string
	 * @static
	 */
	static public function strtoupper($str){
		if(self::$_multiByteEnabled==false){
			return strtoupper($str);
		} else {
			return mb_strtoupper($str, self::$_defaultCharset);
		}
	}

	/**
	 * Obtiene una parte de un String
	 *
	 * @param string $str
	 * @param int $start
	 * @param int $length
	 * @return string
	 */
	static public function substr($str, $start, $length=null){
		if(self::$_multiByteEnabled==false){
			return substr($str, $start, $length);
		} else {
			if($length===null){
				$length = mb_strlen($str);
			}
			return mb_substr($str, $start, $length, self::$_defaultCharset);
		}
	}

	/**
	 * Reemplaza en una cadena de caracteres mediante una expresion regular
	 *
	 * @access 	public
	 * @param 	string $pattern
	 * @param 	string $replacement
	 * @param 	array $regs
	 * @static
	 */
	static public function eregReplace($pattern, $replacement, &$regs){
		if(self::$_multiByteEnabled==false){
			return preg_replace('/'.$pattern.'/', $replacement, $regs);
		} else {
			return mb_ereg_replace($pattern, $replacement, $regs);
		}
	}

}
