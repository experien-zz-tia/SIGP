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
 * @package		Db
 * @subpackage	Profiler
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @version 	$Id: DbProfiler.php 5 2009-04-24 01:48:48Z gutierrezandresfelipe $
 */

/**
 * DbProfiler
 *
 * Los objetos del componente Db permiten generar Profiles de la ejecución
 * de sentencias SQL que se envian al gestor relacional. La información
 * generada incluye los tiempos en milisegundos que duró la ejecución
 * de cada sentencia y así poder identificar cuellos de botella en la aplicación.
 *
 * @category	Kumbia
 * @package		Db
 * @subpackage	Profiler
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 */
class DbProfiler implements DbProfileInterface {

	/**
	 * Todos los DbProfileItems del Profile Activo
	 *
	 * @var array
	 */
	private $_allProfiles = array();

	/**
	 * DbProfileItem activo
	 *
	 * @var DbProfileItem
	 */
	private $_activeProfile;

	/**
	 * Tiempo total que ha durado los Profiles
	 *
	 * @var float
	 */
	private $_totalSeconds = 0;

	/**
	 * Constructor de la clase DbProfiler
	 *
	 * @access public
	 */
	public function __construct(){
		if(!class_exists("DbProfilerItem")){
			require "Library/Db/DbProfiler/DbProfilerItem.php";
		}
	}

	/**
	 * Realiza el Profile de una sentencia SQL
	 *
	 * @access public
	 * @param string $sqlStatement
	 */
	public function startProfile($sqlStatement){
		$this->_activeProfile = new DbProfiler();
		$this->_activeProfile->setSqlStatement($sqlStatement);
		$this->_activeProfile->setInitialTime(microtime(true));
	}

	/**
	 * Cierra el profile activo
	 *
	 * @access public
	 */
	public function stopProfile(){
		$finalTime = microtime(true);
		$this->_activeProfile->setFinalTime($finalTime);
		$this->_totalSeconds+= ($finalTime-$this->_activeProfile->getInitialTime());
		$this->_allProfiles[] = $this->_activeProfile;
	}

	/**
	 * Devuelve el numero total de sentencias SQL procesadas
	 *
	 * @access public
	 * @return integer
	 */
	public function getNumberTotalStatements(){
		return count($this->_allProfiles);
	}

	/**
	 * Develve el tiempo total que han durado los profiles
	 *
	 * @access public
	 * @return float
	 */
	public function getTotalElapsedSeconds(){
		return $this->_totalSeconds;
	}

	/**
	 * Devuelve los profiles procesados
	 *
	 * @access public
	 * @return array
	 */
	public function getProfiles(){
		return $this->_allProfiles;
	}

	/**
	 * Resetea el Profiler borrando
	 *
	 * @access public
	 */
	public function reset(){
		unset($this->_allProfiles);
		$this->_allProfiles = array();
	}

	/**
	 * Devuelve la ultima sentencia que se le hizo profile
	 *
	 * @access public
	 * @return DbProfileItem
	 */
	public function getLastProfile(){
		return $this->_activeProfile;
	}

}
