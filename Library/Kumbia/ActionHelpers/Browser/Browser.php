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
 * @package 	ActionHelpers
 * @subpackage 	Browser
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2008-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license 	New BSD License
 * @version 	$Id: Browser.php 94 2009-09-24 00:44:18Z gutierrezandresfelipe $
 */

/**
 * Browser
 *
 * Este ActionHelper permite obtener información del explorador del
 * cliente desde el cuál se está accediendo a la aplicación.
 *
 * @category	Kumbia
 * @package		Browser
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2008-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @access		public
 * @abstract
 */
abstract class Browser {

	/**
	 * Indica si el Browser utilizado es Mozilla Firefox
	 *
	 * @access 	public
	 * @return 	boolean
	 * @static
	 */
	public static function isFirefox(){
		return strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')!==false ? true : false;
	}

	/**
	 * Indica si el Browser utilizado es Camino
	 *
	 * @access 	public
	 * @return 	boolean
	 * @static
	 */
	public static function isCamino(){
		return strpos($_SERVER['HTTP_USER_AGENT'], 'Camino')!==false ? true : false;
	}

	/**
	 * Indica si el Browser utilizado es Safari
	 *
	 * @access 	public
	 * @return 	boolean
	 * @static
	 */
	public static function isSafari(){
		return strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')!==false ? true : false;
	}

	/**
	 * Indica si el Browser utilizado es Opera
	 *
	 * @access 	public
	 * @return 	boolean
	 * @static
	 */
	public static function isOpera(){
		return strpos($_SERVER['HTTP_USER_AGENT'], 'Opera')!==false ? true : false;
	}

	/**
	 * Indica si el Browser utilizado es Microsoft Internet Explorer
	 *
	 * @access public
	 * @return boolean
	 * @static
	 */
	public static function isInternetExplorer(){
		return strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')!==false ? true : false;
	}

	/**
	 * Indica si el motor de renderizado del Browser utilizado es Internet Explorer Mobile
	 *
	 * @access public
	 * @return boolean
	 * @static
	 */
	public static function isIEMobile(){
		return strpos($_SERVER['HTTP_USER_AGENT'], 'IEMobile')!==false ? true : false;
	}

	/**
	 * Indica si el motor de renderizado del Browser utilizado es Opera Mobile
	 *
	 * @access public
	 * @return boolean
	 * @static
	 */
	public static function isOperaMobile(){
		return strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi')!==false ? true : false;
	}

	/**
	 * Indica si el Browser utilizado es Mobile Safari
	 *
	 * @access public
	 * @return boolean
	 * @static
	 */
	public static function isMobileSafari(){
		return strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile Safari')!==false ? true : false;
	}

	/**
	 * Indica si es un explorador movil
	 *
	 * @access	public
	 * @return	boolean
	 * @static
	 */
	public static function isMobile(){
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile')!==false){
			return true;
		} else {
			return (self::isIEMobile()||self::isMobileSafari()||self::isOperaMobile());
		}
	}

	/**
	 * Devuelve la versión del explorador utilizado
	 *
	 * @access 	public
	 * @return 	string
	 * @static
	 */
	public static function getVersion(){
		$userAgent = self::getUserAgent();
		if(self::isFirefox()==true){
			if(preg_match('/Firefox\/([0-9\.]+)$/', $userAgent, $matches)){
				return $matches[1];
			}
		}
		if(self::isSafari()==true||self::isMobileSafari()==true){
			if(preg_match('/Version\/([0-9\.]+) /', $userAgent, $matches)){
				return $matches[1];
			}
		}
		if(self::isCamino()==true){
			if(preg_match('/Camino\/([0-9\.]+) /', $userAgent, $matches)){
				return $matches[1];
			}
		}
		if(self::isOpera()==true){
			if(preg_match('/Opera\/([0-9\.]+) /', $userAgent, $matches)){
				return $matches[1];
			}
		}
		if(self::isIEMobile()==true){
			if(preg_match('/IEMobile ([0-9\.]+)/', $userAgent, $matches)){
				return $matches[1];
			}
		}
		return $userAgent;
	}

	/**
	 * Indica si el motor de renderizado del Browser utilizado es Gecko
	 *
	 * @access 	public
	 * @return 	boolean
	 * @static
	 */
	public static function isGecko(){
		return strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko')!==false ? true : false;
	}

	/**
	 * Indica si el motor de renderizado del Browser utilizado es WebKit
	 *
	 * @access 	public
	 * @return 	boolean
	 * @static
	 */
	public static function isWebKit(){
		return strpos($_SERVER['HTTP_USER_AGENT'], 'WebKit')!==false ? true : false;
	}

	/**
	 * Indica si se está usando Google Chrome Framew
	 *
	 * @access	public
	 * @return	boolean
	 * @static
	 */
	public static function isChromeFrame(){
		return strpos($_SERVER['HTTP_USER_AGENT'], 'chromeframe')!==false ? true : false;
	}

	/**
	 * Devuelve el User/Agent del cliente
	 *
	 * @access 	public
	 * @return 	string
	 * @static
	 */
	public static function getUserAgent(){
		return $_SERVER['HTTP_USER_AGENT'];
	}

	/**
	 * Devuelve el Accept Encoding del explorador
	 *
	 * @access 	public
	 * @return 	string
	 * @static
	 */
	public static function getAcceptEncoding(){
		return $_SERVER['HTTP_ACCEPT_ENCODING'];
	}

	/**
	 * Devuelve el idioma del explorador
	 *
	 * @access 	public
	 * @return 	string
	 * @static
	 */
	public static function getAcceptLanguage(){
		return $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	}

	/**
	 * Indica si el explorador acepta salida comprimida
	 *
	 * @access 	public
	 * @return 	boolean
	 * @static
	 */
	public static function acceptCompressedOutput(){
		return (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')!==false || strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate')!==false);
	}

	/**
	 * Devuelve el nombre del explorador actual
	 *
	 * @return string
	 * @static
	 */
	public static function getBrowserAbrev(){
		$explorers = array(
			'firefox3' => 'Firefox/3',
			'firefox' => 'Firefox',
			'webkit' => 'WebKit',
			'msie' => 'MSIE'
			);
			foreach($explorers as $key => $explorer){
				if(strpos($_SERVER['HTTP_USER_AGENT'], $explorer)){
					return $key;
				}
			}
	}

	/**
	 * Indica si el SO del cliente es Mac OS X
	 *
	 * @return boolean
	 * @static
	 */
	public static function isMacOSX(){
		return stripos($_SERVER['HTTP_USER_AGENT'], 'Mac OS X')!==false ? true : false;
	}

	/**
	 * Indica si el SO del cliente es Windows
	 *
	 * @return boolean
	 * @static
	 */
	public static function isWindows(){
		return stripos($_SERVER['HTTP_USER_AGENT'], 'Win')!==false ? true : false;
	}

	/**
	 * Indica si el SO del cliente es Linux
	 *
	 * @return boolean
	 * @static
	 */
	public static function isLinux(){
		return stripos($_SERVER['HTTP_USER_AGENT'], 'Linux')!==false ? true : false;
	}

}
