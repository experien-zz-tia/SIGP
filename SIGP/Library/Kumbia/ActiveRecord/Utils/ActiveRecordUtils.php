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
 * @package		ActiveRecord
 * @subpackage	ActiveRecordUtils
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2008-2008 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @version 	$Id: ActiveRecordUtils.php 5 2009-04-24 01:48:48Z gutierrezandresfelipe $
 */

/**
 * ActiveRecordUtils
 *
 * Implementa métodos de seguridad y validación usados internamente
 * por ActiveRecordBase
 *
 * @category	Kumbia
 * @package		ActiveRecord
 * @subpackage	ActiveRecordUtils
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2008-2008 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 */
abstract class ActiveRecordUtils extends Object {

	/**
	 * Elimina caracteres que podrian ayudar a ejecutar
	 * un ataque de Inyeccion SQL
	 *
	 * @access public
	 * @param string $sqlItem
	 * @static
	 */
	public static function sqlItemSanizite($sqlItem){
		preg_match('/^[a-zA-Z0-9_]+$/', $sqlItem, $regs);
		return $regs[0];
	}

	/**
	 * Elimina caracteres que podrian ayudar a ejecutar
	 * un ataque de Inyeccion SQL
	 *
	 * @access public
	 * @param string $sqlItem
	 * @static
	 */
	public static function sqlSanizite($sqlItem){
		return $sqlItem;
	}

}
