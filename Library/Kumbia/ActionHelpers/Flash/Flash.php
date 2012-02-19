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
 * @package		ActionHelpers
 * @subpackage 	Flash
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @version 	$Id: Flash.php 82 2009-09-13 21:06:31Z gutierrezandresfelipe $
 */

/**
 * Flash
 *
 * Es la clase standard para enviar mensajes contextuales
 *
 * @category	Kumbia
 * @package		ActionHelpers
 * @subpackage	Flash
 * @abstract
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @access		public
 */
abstract class Flash  {

	/**
	 * Indica si se debe forzar a incluir el estilo
	 *
	 * @var string
	 */
	private static $_includeStyle = false;

	/**
	 * Archivo CSS con estilos
	 *
	 * @var string
	 */
	private static $_cssStyle = 'style';

	/**
	 * Mensajes de error
	 *
	 */
	const ERROR = 0;

	/**
	 * Mensaje de información
	 *
	 */
	const NOTICE = 1;

	/**
	 * Mensaje de Exito
	 *
	 */
	const SUCCESS = 3;

	/**
	 * Mensaje de advertencia
	 *
	 */
	const WARNING = 4;

	/**
	 * Visualiza un mensaje de acuerdo a las clases CSS establecidas
	 *
	 * @access private
	 * @param string $message
	 * @param array $classes
	 * @static
	 */
	private static function _showMessage($message, array $classes=array()){
		if(isset($_SERVER['SERVER_SOFTWARE'])){
			if(self::$_includeStyle==true){
				Tag::stylesheetLink(self::$_cssStyle, true);
			}
			if(is_array($message)){
				$clases = join(' ', $classes);
				foreach($message as $msg){
					echo '<div class="'.$clases.'">', $msg, '</div>', "\n";
				}
			} else {
				echo '<div class="'.join(' ', $classes).'">', $message, '</div>', "\n";
			}
		} else {
			echo strip_tags($message), "\n";
		}
	}

	/**
	 * Visualiza un mensaje de error
	 *
	 * @access public
	 * @param string $message
	 * @param array $classes
	 * @static
	 */
	static public function error($message, array $cssClasses=array('kumbiaDisplay', 'errorMessage')){
		self::_showMessage($message, $cssClasses);
	}

	/**
	 * Visualiza informacion en pantalla
	 *
	 * @access public
	 * @param string $message
	 * @param array $classes
	 * @static
	 */
	static public function notice($message, array $cssClasses=array('kumbiaDisplay', 'noticeMessage')){
		self::_showMessage($message, $cssClasses);
	}

	/**
	 * Visualiza un mensaje de exito en pantalla
	 *
	 * @access public
	 * @param string $message
	 * @param array $classes
	 * @static
	 */
	static public function success($message, array $cssClasses=array('kumbiaDisplay', 'successMessage')){
		self::_showMessage($message, $cssClasses);
	}

	/**
	 * Visualiza un mensaje de advertencia en pantalla
	 *
	 * @access public
	 * @param string $message
	 * @param array $classes
	 * @static
	 */
	static public function warning($message, array $cssClasses=array('kumbiaDisplay', 'warningMessage')){
		self::_showMessage($message, $cssClasses);
	}

	/**
	 * Visualiza un mensaje por su codigo
	 *
	 * @access public
	 * @param string $message
	 * @param int $code
	 * @param array $classes
	 * @static
	 */
	static public function message($message, $code, array $cssClasses=null){
		switch($code){
			case self::ERROR:
				if($cssClasses){
					self::error($message, $cssClasses);
				} else {
					self::error($message);
				}
				break;
			case self::SUCCESS:
				if($cssClasses){
					self::success($message, $cssClasses);
				} else {
					self::success($message);
				}
				break;
			case self::NOTICE:
				if($cssClasses){
					self::notice($message, $cssClasses);
				} else {
					self::notice($message);
				}
				break;
			case self::WARNING:
				if($cssClasses){
					self::warning($message, $cssClasses);
				} else {
					self::warning($message);
				}
				break;
			default:
				self::notice($message);
				break;
		}
	}

	/**
	 * Establece si se debe cargar un archivo CSS antes de mostrar el mensaje
	 *
	 * @param string $style
	 */
	public static function loadStylesheet($style){
		if($style===true){
			self::$_includeStyle = true;
		} else {
			if(is_string($style)){
				self::$_includeStyle = true;
				self::$_cssStyle = $style;
			}
		}
	}

	/**
	 * Agrega un mensaje al buffer
	 *
	 * @param string $message
	 * @param int $type
	 */
	public static function addMessage($message, $type){
		$messages = Session::get('FLASH_MESSAGES');
		if(!is_array($messages)){
			$messages = array();
		}
		$messages[] = array(
			'message' => $message,
			'type' => $type,
		);
		Session::set('FLASH_MESSAGES', $messages);
	}

	/**
	 * Devuelve los mensajes del buffer
	 *
	 * @return array
	 */
	public static function getMessages(){
		$messages = Session::get('FLASH_MESSAGES');
		if(!is_array($messages)){
			$messages = array();
		} else {
			Session::unsetData('FLASH_MESSAGES');
		}
		return $messages;
	}

}
