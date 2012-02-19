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
 * @package		CommonEvent
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @version 	$Id: CommonEventManager.php 5 2009-04-24 01:48:48Z gutierrezandresfelipe $
 */

/**
 * CommonEventManager
 *
 * Permite agregar dinámicamente eventos que se ejecuten en la framework
 *
 * @category	Kumbia
 * @package		CommonEvent
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @abstract
 */
abstract class CommonEventManager {

	/**
	 * Eventos del administrador
	 *
	 * @var array
	 */
	private static $_events = array();

	/**
	 * Agrega un evento al administrador de eventos
	 *
	 * @param Event $event
	 */
	public static function attachEvent(CommonEvent $event){
		if(!isset(self::$_events[$event->getEventName()])){
			self::$_events[$event->getEventName()] = array();
		}
		self::$_events[$event->getEventName()][] = $event;
	}

	/**
	 * Notifica un evento por su nombre
	 *
	 * @param string $eventName
	 */
	public static function notifyEvent($eventName){
		if(isset(self::$_events[$eventName])){
			foreach(self::$_events[$eventName] as $event){
				$event->execute();
			}
		}
	}

}
