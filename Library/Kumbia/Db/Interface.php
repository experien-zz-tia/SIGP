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
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2008 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright	Copyright (C) 2006-2007 Giancarlo Corzo Vigil (www.antartec.com)
 * @license		New BSD License
 * @version 	$Id: Interface.php 5 2009-04-24 01:48:48Z gutierrezandresfelipe $
 */

/**
 * DbBaseInterface
 *
 * Esta interface expone los metodos que se deben implementar en un adaptador
 * de conexion a un RBDM
 *
 * @category	Kumbia
 * @package		Db
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2007-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright	Copyright (C) 2006-2007 Giancarlo Corzo Vigil (www.antartec.com)
 * @license		New BSD License
 * @access		public
 */
interface DbBaseInterface {

	public function __construct($descriptor='');
	public function connect($descriptor='');
	public function query($sqlStatement);
	public function fetchArray($resultQuery='');
	public function close();
	public function numRows($resultQuery='');
	public function fieldName($position, $resultQuery='');
	public function dataSeek($position, $resultQuery='');
	public function affectedRows($resultQuery='');
	public function error($errorInfo='', $resultQuery='');
	public function noError($resultQuery='');
	public function inQuery($sqlStatement);
	public function inQueryAssoc($sqlStatement);
	public function inQueryNum($sqlStatement);
	public function fetchOne($sqlStatement);
	public function fetchAll($sqlStatement);
	public function insert($tableName, $values, $fields='', $automaticQuotes=true);
	public function update($tableName, $fields, $values, $whereCondition=null, $automaticQuotes=true);
	public function delete($tableName, $whereCondition='');
	public function limit($sqlStatement, $number);
	public function forUpdate($sqlStatement);
	public function sharedLock($sqlStatement);
	public function begin();
	public function rollback();
	public function commit();
	public function listTables($schemaName='');
	public function describeTable($tableName, $schema='');
	public function getRequiredSequence($tableName='', $identityColumn='', $sequenceName='');
	public function lastInsertId($tableName='', $identityColumn='', $sequenceName='');
	public function createTable($tableName, $definition, $index=array(), $tableOptions=array());
	public function dropTable($tableName, $ifExists=false);
	public function tableExists($tableName, $schema='');
	public function getDateUsingFormat($date, $format='YYYY-MM-DD');
	public function getHaveAutoCommit();
	public function setIsolationLevel($isolationLevel);
	public function getCurrentDate();
	public function getLastResultQuery();
	public function getConnectionId($asString=false);
	public function getDatabaseName();
	public function getUsername();
	public function getHostName();
	public function setFetchMode($fetchMode);

}
