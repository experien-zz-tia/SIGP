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
 * @subpackage	Adapters
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @version 	$Id: Oracle.php 82 2009-09-13 21:06:31Z gutierrezandresfelipe $
 */

/**
 * Oracle Database Support
 *
 * Estas funciones le permiten acceder a servidores de bases de datos Oracle.
 * Puede encontrar mas información sobre Oracle en http://www.oracle.com/.
 * La documentación de Oracle puede encontrarse en http://www.oracle.com/technology/documentation/index.html.
 *
 * @category	Kumbia
 * @package		Db
 * @subpackage	Adapters
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @link		http://www.php.net/manual/es/ref.oci8.php
 */
class DbOracle extends DbBase implements DbBaseInterface  {

	/**
	 * Ultimo mensaje de error
	 *
	 * @var array
	 */
	private $_errorMessage = array();

	/**
	 * Tipo de Dato Integer
	 *
	 */
	const TYPE_INTEGER = 'INTEGER';

	/**
	 * Tipo de Dato Date
	 *
	 */
	const TYPE_DATE = 'DATE';

	/**
	 * Tipo de Dato Varchar
	 *
	 */
	const TYPE_VARCHAR = 'VARCHAR2';

	/**
	 * Tipo de Dato Decimal
	 *
	 */
	const TYPE_DECIMAL = 'DECIMAL';

	/**
	 * Tipo de Dato Datetime
	 *
	 */
	const TYPE_DATETIME = 'DATETIME';

	/**
	 * Tipo de Dato Char
	 *
	 */
	const TYPE_CHAR = 'CHAR';

	/**
	 * Constructor de la Clase
	 *
	 * @access public
	 * @param stdClass $descriptor
	 */
	public function __construct($descriptor=''){
		if($descriptor==''){
			$descriptor = $this->_descriptor;
		}
		$this->connect($descriptor);
	}

	/**
	 * Hace una conexion a la base de datos de Oracle
	 *
	 * @param string $dbhost
	 * @param string $dbuser
	 * @param string $dbpass
	 * @param string $dbname
	 * @param string $dbport
	 * @param string $dbdsn
	 * @return resource
	 */
	public function connect($descriptor=''){
		if($descriptor==''){
			$descriptor = $this->_descriptor;
		}
		$host = isset($descriptor->host) ? $descriptor->host : '';
		$username = isset($descriptor->username) ? $descriptor->username : '';
		$password = isset($descriptor->password) ? $descriptor->password : '';
		$charset = isset($descriptor->charset) ? $descriptor->charset : 'AL32UTF8';
		$instance = isset($descriptor->instance) ? $descriptor->instance : '';
		if(isset($descriptor->port)){
			$dbstring = '//'.$host.':'.$descriptor->port.'/'.$instance;
		} else {
			$dbstring = '//'.$host.'/'.$instance;
		}
		if($this->_idConnection = @oci_new_connect($username, $password, $dbstring, $charset)){
			$sort = isset($descriptor->sort) ? $descriptor->sort : 'binary_ci';
			$comp = isset($descriptor->comp) ? $descriptor->comp : 'linguistic';
			$language = isset($descriptor->language) ? $descriptor->language : 'spanish';
			$territory = isset($descriptor->territory) ? $descriptor->territory : 'spain';
			$date_format = isset($descriptor->date_format) ? $descriptor->date_format : 'YYYY-MM-DD HH24:MI:SS';
			$this->query("ALTER SESSION SET nls_date_format='$date_format' nls_territory=$territory nls_language=$language nls_sort=$sort nls_comp=$comp");
			parent::__construct($descriptor);
		}
		if(!$this->_idConnection){
			throw new DbException($this->error($php_errormsg), $this->noError(), false);
		} else {
			register_shutdown_function(array($this, 'close'));
			return true;
		}
	}

	/**
	 * Efectua operaciones SQL sobre la base de datos
	 *
	 * @param string $sqlQuery
	 * @return resource|false
	 */
	public function query($sqlQuery){
		$this->debug($sqlQuery);
		$this->log($sqlQuery, Logger::DEBUG);
		if(!$this->_idConnection){
			$this->connect();
			if(!$this->_idConnection){
				return false;
			}
		}
		$this->_lastQuery = $sqlQuery;
		$resultQuery = @oci_parse($this->_idConnection, $sqlQuery);
		if($resultQuery){
			$this->_lastResultQuery = $resultQuery;
		} else {
			$this->_lastResultQuery = false;
			$errorCode = $this->noError($resultQuery);
			$errorMessage = $this->error($php_errormsg);
			$errorMessage = "\"$errorMessage\" al ejecutar \"$sqlQuery\"  en la conexión ".$this->_idConnection;
			switch($errorCode){
				case 1756:
					throw new DbSQLGrammarException($errorMessage, $errorCode, true, $this);
					break;
				default:
					throw new DbException($errorMessage, $errorCode, true, $this);
			}
			return false;
		}
		if($this->_autoCommit==true){
			$commit = OCI_COMMIT_ON_SUCCESS;
		} else {
			$commit = OCI_DEFAULT;
		}
		if(!@oci_execute($resultQuery, $commit)){
			$this->_lastResultQuery = false;
			$errorCode = $this->noError($resultQuery);
			$errorMessage = $this->error($php_errormsg);
			$errorMessage = "\"$errorMessage\" al ejecutar \"$sqlQuery\"  en la conexión ".$this->_idConnection;
			switch($errorCode){
				case 6550:
				case 907:
					throw new DbSQLGrammarException($errorMessage, $errorCode, true, $this);
					break;
				case 1839:
					throw new DbInvalidFormatException($errorMessage, $errorCode, true, $this);
					break;
				case 2291:
				case 2292:
					throw new DbConstraintViolationException($errorMessage, $errorCode, true, $this);
				case 1:
					throw new DbLockAdquisitionException($errorMessage, $errorCode, true, $this);
				default:
					throw new DbException($errorMessage, $errorCode, true, $this);
			}
			return false;
		}
		return $resultQuery;
	}

	/**
	 * Cierra la Conexion al Motor de Base de datos
	 *
	 * @return boolean
	 */
	public function close(){
		if($this->_idConnection) {
			return oci_close($this->_idConnection);
		}
	}

	/**
	 * Devuelve fila por fila el contenido de un select
	 *
	 * @access public
	 * @param resource $resultQuery
	 * @param integer $opt
	 * @return array
	 */
	public function fetchArray($resultQuery=''){
		if(!$this->_idConnection){
			return false;
		}
		if(!$resultQuery){
			$resultQuery = $this->_lastResultQuery;
			if(!$resultQuery){
				return false;
			}
		}
		$result = oci_fetch_array($resultQuery, $this->_fetchMode+OCI_RETURN_NULLS);
		if(is_array($result)){
			return array_change_key_case($result, CASE_LOWER);
		} else {
			return false;
		}
		return false;
	}

	/**
	 * Devuelve el numero de filas de la ultima sentencia SELECT ejecutada
	 *
	 * @param resource $resultQuery
	 * @return intger|boolean
	 */
	public function numRows($resultQuery=''){
		if(!$this->_idConnection){
			return false;
		}
		$sql = $this->_lastQuery;
		$fromPosition = stripos($sql, 'FROM');
		if($fromPosition===false){
			return 0;
		} else {
			$sqlQuery = 'SELECT COUNT(*) '.substr($sql, $fromPosition);
			$resultQuery = @oci_parse($this->_idConnection, $sqlQuery);
			if($this->_autoCommit==true){
				$commit = OCI_COMMIT_ON_SUCCESS;
			} else {
				$commit = OCI_DEFAULT;
			}
			if(@oci_execute($resultQuery, $commit)){
				$count = oci_fetch_array($resultQuery, OCI_NUM);
				return $count[0];
			} else {
				return false;
			}
		}
	}

	/**
	 * Devuelve el nombre de un campo en el resultado de un select
	 *
	 * @param integer $number
	 * @param resource $resultQuery
	 * @return string
	 */
	public function fieldName($number, $resultQuery=''){
		if(!$this->_idConnection){
			return false;
		}
		if(!$resultQuery){
			$resultQuery = $this->_lastResultQuery;
			if(!$resultQuery){
				throw new DbException($this->error('Resource invalido para db::field_name'), $this->noError());
				return false;
			}
		}

		if(($fieldName = oci_field_name($resultQuery, $number+1))!==false){
			return strtolower($fieldName);
		} else {
			throw new DbException($this->error(), $this->noError());
			return false;
		}
		return false;
	}

	/**
	 * Se Mueve al resultado indicado por $number en un select
	 *
	 * @param integer $number
	 * @param resource $resultQuery
	 * @return boolean
	 */
	public function dataSeek($number, $resultQuery=''){
		if(!$resultQuery){
			$resultQuery = $this->_lastResultQuery;
			if(!$resultQuery){
				throw new DbException($this->error('Resource invalido para db::dataSeek'), $this->noError());
				return false;
			}
		}
		if($this->_autoCommit){
			$commit = OCI_COMMIT_ON_SUCCESS;
		} else {
			$commit = OCI_DEFAULT;
		}
		if(!@oci_execute($resultQuery, $commit)){
			$errorMessage = $php_errormsg." al ejecutar <i>'{$this->_lastQuery}'</i>";
			throw new DbException($this->error($errorMessage), $this->noError());
			return false;
		}
		if($number){
			for($i=0;$i<=$number-1;++$i){
				if(!oci_fetch_row($resultQuery)){
					return false;
				}
			}
		} else {
			return true;
		}
		return true;
	}

	/**
	 * Nómero de Filas afectadas en un insert, update ó delete
	 *
	 * @param resource $resultQuery
	 * @return integer
	 */
	public function affectedRows($resultQuery=''){
		if(!$this->_idConnection){
			return false;
		}
		if(!$resultQuery){
			$resultQuery = $this->_lastResultQuery;
			if(!$resultQuery){
				return false;
			}
		}
		if(($numberRows = oci_num_rows($resultQuery))!==false){
			return $numberRows;
		} else {
			throw new DbException($this->error('Resource invalido para db::affectedRows'), $this->noError());
			return false;
		}
		return false;
	}

	/**
	 * Devuelve el error de Oracle
	 *
	 * @param string $err
	 * @return string
	 */
	public function error($err='', $resultQuery=''){
		if($resultQuery==''){
			if(!$this->_idConnection){
				$this->_errorMessage = oci_error();
				if(is_array($this->_errorMessage)){
					if($this->_errorMessage['message']==""){
						$this->_errorMessage['message'].=" > $err ";
					}
					return $this->_errorMessage['message'];
				} else {
					$this->_errorMessage['message'] = "[Error Desconocido en Oracle] $php_errormsg ";
					return $this->_errorMessage['message'];
				}
			}
			$this->_errorMessage = oci_error($this->_idConnection);
			if(is_array($this->_errorMessage)){
				$this->_errorMessage['message'].=" > $err ";
			} else {
				$this->_errorMessage['message'] = $err;
			}
			return $this->_errorMessage['message'];
		} else {
			$this->_errorMessage = oci_error($resultQuery);
			return $this->_errorMessage['message'];
		}
	}

	/**
	 * Devuelve el no error de Oracle
	 *
	 * @return integer
	 */
	public function noError($resultQuery=null){
		if($resultQuery==null){
			if(!$this->_idConnection){
				$this->_errorMessage = oci_error() ? oci_error() : 0;
				if(is_array($this->_errorMessage)){
					return $this->_errorMessage['code'];
				} else {
					if(isset($this->_errorMessage['code'])){
						return $this->_errorMessage['code'];
					}
				}
			}
			$this->_errorMessage = oci_error($this->_idConnection);
			return $this->_errorMessage['code'];
		} else {
			$this->_errorMessage = oci_error($resultQuery);
			if(is_array($this->_errorMessage)){
				return $this->_errorMessage['code'];
			} else {
				if(isset($this->_errorMessage['code'])){
					return $this->_errorMessage['code'];
				}
				return 0;
			}
		}
	}

	/**
	 * Inicia una transacción
	 *
	 * @return boolean
	 */
	public function begin(){
		$this->_autoCommit = false;
		$this->setUnderTransaction(true);
		return true;
	}

	/**
	 * Cancela una transacción si es posible
	 *
	 * @access public
	 * @return boolean
	 */
	public function rollback(){
		if($this->isUnderTransaction()==true){
			$this->setUnderTransaction(false);
			$this->_autoCommit = true;
			return oci_rollback($this->_idConnection);
		} else {
			if($this->_autoCommit==false){
				throw new DbException("No hay una transacción activa en la conexión al gestor relacional", 0, true, $this);
			}
		}
	}

	/**
	 * Hace commit sobre una transacción si es posible
	 *
	 * @access public
	 * @return boolean
	 */
	public function commit(){
		if($this->isUnderTransaction()==true){
			$this->setUnderTransaction(false);
			$this->_autoCommit = true;
			return oci_commit($this->_idConnection);
		} else {
			if($this->_autoCommit==false){
				throw new DbException("No hay una transacción activa en la conexión al gestor relacional", 0, true, $this);
			}
		}
	}

	/**
	 * Devuelve un LIMIT valido para un SELECT del RBDM
	 *
	 * @param string $sql
	 * @param integer $number
	 * @return string
	 */
	public function limit($sql, $number){
		if(!is_numeric($number)||$number<0){
			return $sql;
		}
		if(preg_match('/ORDER[\t\n\r ]+BY/i', $sql)){
			if(stripos($sql, 'WHERE')){
				return preg_replace('/ORDER[\t\n\r ]+BY/i', "AND ROWNUM <= $number ORDER BY", $sql);
			} else {
				return preg_replace('/ORDER[\t\n\r ]+BY/i', "WHERE ROWNUM <= $number ORDER BY", $sql);
			}
		} else {
			if(stripos($sql, 'WHERE')){
				return $sql.' AND ROWNUM <= '.$number;
			} else {
				return $sql.' WHERE ROWNUM <= '.$number;
			}
		}
	}

	/**
	 * Devuelve un FOR UPDATE valido para un SELECT del RBDM
	 *
	 * @param string $sql
	 * @return string
	 */
	public function forUpdate($sql){
		return $sql.' FOR UPDATE';
	}

	/**
	 * Devuelve un SHARED LOCK valido para un SELECT del RBDM
	 *
	 * @param string $sql
	 * @return string
	 */
	public function sharedLock($sql){
		return $sql;
	}

	/**
	 * Borra una tabla de la base de datos
	 *
	 * @param string $table
	 * @param boolean $ifExists
	 * @return boolean
	 */
	public function dropTable($table, $ifExists=true){
		if($ifExists==true){
			if($this->tableExists($table)){
				return $this->query("DROP TABLE $table");
			} else {
				return true;
			}
		} else {
			return $this->query("DROP TABLE $table");
		}
	}

	/**
	 * Crea una tabla utilizando SQL nativo del RDBM
	 *
	 * TODO:
	 * - Falta que el parametro index funcione. Este debe listar indices compuestos multipes y unicos
	 * - Agregar el tipo de tabla que debe usarse (Oracle)
	 * - Soporte para campos autonumericos
	 * - Soporte para llaves foraneas
	 *
	 * @access public
	 * @param string $table
	 * @param array $definition
	 * @param array $index
	 * @return boolean
	 */
	public function createTable($table, $definition, $index=array(), $tableOptions=array()){
		$create_sql = "CREATE TABLE $table (";
		if(!is_array($definition)){
			new DbException("Definición invalida para crear la tabla '$table'");
			return false;
		}
		$create_lines = array();
		$index = array();
		$unique_index = array();
		$primary = array();
		$not_null = "";
		$size = "";
		foreach($definition as $field => $field_def){
			if(isset($field_def['not_null'])){
				$not_null = $field_def['not_null'] ? 'NOT NULL' : '';
			} else {
				$not_null = "";
			}
			if(isset($field_def['size'])){
				$size = $field_def['size'] ? '('.$field_def['size'].')' : '';
			} else {
				$size = "";
			}
			if(isset($field_def['index'])){
				if($field_def['index']){
					$index[] = "INDEX($field)";
				}
			}
			if(isset($field_def['unique_index'])){
				if($field_def['unique_index']){
					$index[] = "UNIQUE($field)";
				}
			}
			if(isset($field_def['primary'])){
				if($field_def['primary']){
					$primary[] = "$field";
				}
			}
			if(isset($field_def['auto'])){
				if($field_def['auto']){
					$this->query("CREATE SEQUENCE {$table}_{$field}_seq START WITH 1");
				}
			}
			if(isset($field_def['extra'])){
				$extra = $field_def['extra'];
			} else {
				$extra = "";
			}
			$create_lines[] = "$field ".$field_def['type'].$size.' '.$not_null.' '.$extra;
		}
		$create_sql.= join(',', $create_lines);
		$last_lines = array();
		if(count($primary)){
			$last_lines[] = 'PRIMARY KEY('.join(",", $primary).')';
		}
		if(count($index)){
			$last_lines[] = join(',', $index);
		}
		if(count($unique_index)){
			$last_lines[] = join(',', $unique_index);
		}
		if(count($last_lines)){
			$create_sql.= ','.join(',', $last_lines).')';
		}
		return $this->query($create_sql);

	}

	/**
	 * Listado de Tablas
	 *
	 * @param string $table
	 * @return boolean
	 */
	public function listTables($schema=''){
		if($schema==''){
			$query = "SELECT TABLE_NAME FROM ALL_TABLES WHERE OWNER = '".strtoupper($this->getUsername())."'";
		} else {
			$query = "SELECT TABLE_NAME FROM ALL_TABLES WHERE OWNER = '".strtoupper($schema)."'";
		}
		$fetchMode = $this->_fetchMode;
		$this->_fetchMode = self::DB_NUM;
		$tables = $this->fetchAll($query);
		$allTables = array();
		foreach($tables as $table){
			$allTables[] = $table[0];
		}
		$this->_fetchMode = $fetchMode;
		return $allTables;

	}

	/**
	 * Indica si el RBDM requiere de secuencias y devuelve el nombre por convencion
	 *
	 * @param string $tableName
	 * @param array $primaryKey
	 */
	public function getRequiredSequence($tableName='', $identityColumn='', $sequenceName=''){
		if($sequenceName==""){
			return "\"".strtoupper($tableName)."_".strtoupper($identityColumn)."_SEQ\".NEXTVAL";
		} else {
			return "\"".strtoupper($sequenceName)."\".NEXTVAL";
		}
	}

	/**
	 * Devuelve el ultimo id autonumerico generado en la BD
	 *
	 * @param string $table
	 * @param array $identityColumn
	 * @return integer
	 */
	public function lastInsertId($table='', $identityColumn='', $sequenceName=''){
		if(!$this->_idConnection){
			return false;
		}
		/**
		 * Oracle No soporta columnas identidad
		 */
		if($table&&$identityColumn){
			if($sequenceName==""){
				$sequenceName = "\"".strtoupper($table)."_".$identityColumn."_SEQ\"";
			}
			$value = $this->fetchOne("SELECT \"".strtoupper($sequenceName)."\".CURRVAL FROM dual");
			return $value[0];
		}
		return false;
	}

	/**
	 * Verifica si una tabla existe o no
	 *
	 * @access public
	 * @param string $table
	 * @param string $schema
	 * @return boolean
	 */
	public function tableExists($table, $schema=''){
		if($schema!=""){
			$sql = "SELECT COUNT(*) FROM ALL_TABLES WHERE TABLE_NAME = UPPER('$table') AND OWNER = UPPER('$schema')";
		} else {
			$sql = "SELECT COUNT(*) FROM ALL_TABLES WHERE TABLE_NAME = UPPER('$table') AND OWNER = UPPER('".$this->getUsername()."')";
		}
		$fetchMode = $this->_fetchMode;
		$this->_fetchMode = OCI_NUM;
		$num = $this->fetchOne($sql);
		$this->_fetchMode = $fetchMode;
		return $num[0];
	}

	/**
	 * Listar los campos de una tabla
	 *
	 * @access public
	 * @param string $table
	 * @param string $schema
	 * @return array
	 */
	public function describeTable($table, $schema=''){
		if($schema==""){
			$schema = $this->getUsername();
		}
		$sql = "SELECT LOWER(ALL_TAB_COLUMNS.COLUMN_NAME) AS FIELD,
		LOWER(ALL_TAB_COLUMNS.DATA_TYPE) AS TYPE,
		ALL_TAB_COLUMNS.NULLABLE AS ISNULL,
		ALL_TAB_COLUMNS.DATA_SCALE,
		ALL_TAB_COLUMNS.DATA_PRECISION,
		ALL_CONSTRAINTS.CONSTRAINT_TYPE AS KEY,
		ALL_CONS_COLUMNS.POSITION
		FROM ALL_TAB_COLUMNS
		LEFT JOIN (ALL_CONS_COLUMNS JOIN ALL_CONSTRAINTS
		ON (ALL_CONS_COLUMNS.CONSTRAINT_NAME = ALL_CONSTRAINTS.CONSTRAINT_NAME AND
		ALL_CONS_COLUMNS.TABLE_NAME = ALL_CONSTRAINTS.TABLE_NAME AND
		ALL_CONSTRAINTS.CONSTRAINT_TYPE = 'P'))
		ON ALL_TAB_COLUMNS.TABLE_NAME = ALL_CONS_COLUMNS.TABLE_NAME AND
		ALL_TAB_COLUMNS.COLUMN_NAME = ALL_CONS_COLUMNS.COLUMN_NAME
		JOIN ALL_TABLES ON (ALL_TABLES.TABLE_NAME = ALL_TAB_COLUMNS.TABLE_NAME
		AND ALL_TABLES.OWNER = ALL_TAB_COLUMNS.OWNER)
		WHERE
		UPPER(ALL_TAB_COLUMNS.TABLE_NAME) = UPPER('$table') AND
		UPPER(ALL_TAB_COLUMNS.OWNER) = UPPER('$schema')
		ORDER BY COLUMN_ID";
		$fetchMode = $this->_fetchMode;
		$this->_fetchMode = OCI_ASSOC;
		$describe = $this->fetchAll($sql);
		$this->_fetchMode = $fetchMode;
		$finalDescribe = array();
		$fields = array();
		foreach($describe as $key => $value){
			if(!in_array($value['field'], $fields)){
				if($value['data_precision']!=''){
					if($value['data_scale']==0){
						$type = $value['type'].'('.$value['data_precision'].')';
					} else {
						$type = $value['type'].'('.$value['data_precision'].','.$value['data_scale'].')';
					}
				} else {
					$type = $value['type'];
				}
				$finalDescribe[] = array(
					'Field' => $value['field'],
					'Type' => $type,
					'Null' => $value['isnull'] == 'Y' ? 'YES' : 'NO',
					'Key' => $value['key'] == 'P' ? 'PRI' : ''
					);
					$fields[] = $value['field'];
			}
		}
		return $finalDescribe;
	}

	/**
	 * Devuelve una fecha formateada de acuerdo al RBDM
	 *
	 * @param string $date
	 * @param string $format
	 * @return string
	 */
	public function getDateUsingFormat($date, $format='YYYY-MM-DD HH:MI:SS'){
		if(strlen($date)<=10){
			$format = "YYYY-MM-DD";
		}
		return "TO_DATE('$date', '$format')";
	}

	/**
	 * Devuelve la fecha actual segun el motor
	 *
	 * @return string
	 */
	public function getCurrentDate(){
		return new DbRawValue('sysdate');
	}

	/**
	 * Permite establecer el nivel de isolacion de la conexion
	 *
	 * @param int $isolationLevel
	 */
	public function setIsolationLevel($isolationLevel){
		return true;
	}

	/**
	 * Establece el modo en se que deben devolver los registros
	 *
	 * @param int $fetchMode
	 */
	public function setFetchMode($fetchMode){
		if($fetchMode==self::DB_ASSOC){
			$this->_fetchMode = OCI_ASSOC;
			return;
		}
		if($fetchMode==self::DB_BOTH){
			$this->_fetchMode = OCI_BOTH;
			return;
		}
		if($fetchMode==self::DB_NUM){
			$this->_fetchMode = OCI_NUM;
			return;
		}
	}

	/**
	 * Devuelve la extension o extensiones de PHP requeridas para
	 * usar el adaptador
	 *
	 * @return string|array
	 */
	public static function getPHPExtensionRequired(){
		return 'oci8';
	}

}
