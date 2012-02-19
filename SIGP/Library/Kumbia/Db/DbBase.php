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
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright	Copyright (C) 2006-2007 Giancarlo Corzo Vigil (www.antartec.com)
 * @license 	New BSD License
 * @version 	$Id: DbBase.php 25 2009-04-27 21:15:39Z gutierrezandresfelipe $
 */

/**
 * @see DbBaseInterface
 */
require 'Library/Kumbia/Db/Interface.php';

/**
 * DbBase
 *
 * Clase principal que deben heredar todas las clases driver de Kumbia
 * contiene metodos de debug, consulta y propiedades generales
 *
 * @category	Kumbia
 * @package		Db
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright	Copyright (C) 2006-2007 Giancarlo Corzo Vigil (www.antartec.com)
 * @license		New BSD License
 * @access		public
 */
class DbBase extends Object {

	/**
	 * Parametros de conexion al gestor relacional
	 *
	 * @var stdClass
	 */
	protected $_descriptor;

	/**
	 * Nombre del adaptador utilizado
	 *
	 * @var int
	 */
	protected $_fetchMode;

	/**
	 * Indica si esta en modo debug o no
	 *
	 * @var boolean
	 */
	private $_debug = false;

	/**
	 * Indica si se debe trazar todo el SQL ejecutado
	 *
	 * @var boolean
	 */
	private $_trace = false;

	/**
	 * Lista del SQL Trazado
	 *
	 * @var array
	 */
	private $_tracedSQL = array();

	/**
	 * Indica si debe loggear todo el SQL enviado mediante el objeto o no (tambi&eacute;n permite establecer el nombre del log)
	 *
	 * @var mixed
	 */
	private $_logger = false;

	/**
	 * Referencia al Objeto DbProfiler
	 *
	 * @var DbProfiler
	 */
	private $_profiler = null;

	/**
	 * Indica si la conexión a la base de datos se encuentra en una transacción
	 *
	 * @var boolean
	 */
	private $_underTransaction = false;

	/**
	 * Singleton de la conexión a la base de datos
	 *
	 * @var resource
	 */
	static private $_rawConnection = null;

	/**
	 * Indica si la conexión es de solo lectura
	 *
	 * @var boolean
	 */
	private $_isReadOnly = false;

	/**
	 * Indica si el gestor esta en modo autocommit
	 *
	 * @var boolean
	 */
	protected $_autoCommit = true;

	/**
	 * Resource de la conexion de bajo nivel
	 *
	 * @var resource
	 */
	protected $_idConnection;

	/**
	 * Ultimo Recurso de una Query
	 *
	 * @var resource
	 */
	protected $_lastResultQuery;

	/**
	 * Ultima sentencia SQL enviada a Oracle
	 *
	 * @var string
	 */
	protected $_lastQuery;

	/**
	 * Ultimo error generado por Oracle
	 *
	 * @var string
	 */
	protected $_lastError;

	/**
	 * Resultado de Array Asociativo
	 *
	 */
	const DB_ASSOC = 1;

	/**
	 * Resultado de Array Asociativo y Numerico
	 *
	 */
	const DB_BOTH = 2;

	/**
	 * Resultado de Array Numerico
	 *
	 */
	const DB_NUM = 3;

	/**
	 * Constructor padre de los adaptadores de RBDMs
	 *
	 * @param stdClass $descriptor
	 */
	protected function __construct($descriptor){
		if(isset($descriptor->tracing)){
			if($descriptor->tracing==true){
				$this->setTracing(true);
			}
		}
		if(isset($descriptor->logging)){
			if($descriptor->logging){
				$this->setLogger(true);
			}
		}
		if(isset($descriptor->profiling)){
			if($descriptor->profiling){
				$this->setProfiling(true);
			}
		}
		$this->_descriptor = $descriptor;
		PluginManager::notifyFrom('Db', 'onCreateConnection', $this);
	}

	/**
	 * Devuelve los campos de una tabla
	 *
	 * @access public
	 * @param string $tableName
	 * @return array
	 */
	public function getFieldsFromTable($tableName){
		$description = $this->describeTable($tableName);
		$fields = array();
		foreach($description as $field){
			$fields[] = $field['Field'];
		}
		return $fields;
	}

	/**
	 * Ejecuta las tareas de Profile, Timeout, Traza, Debug y Logueo de SQL en la conexión
	 *
	 * @access protected
	 * @param string $sqlStatement
	 */
	protected function beforeQuery($sqlStatement){
		$this->debug($sqlStatement);
		$this->log($sqlStatement, Logger::DEBUG);
		$this->trace($sqlStatement);
	}

	/**
	 * Ejecuta las tareas de Profile en la conexion
	 *
	 * @access protected
	 * @param string $sqlStatement
	 */
	protected function afterQuery($sqlStatement){

	}

	/**
	 * Ejecuta tareas antes de cerrar la conexion
	 *
	 * @access protected
	 */
	protected function close(){
		PluginManager::notifyFrom('Db', 'onCloseConnection', $this);
	}

	/**
	 * Hace un select de una forma mas corta, listo para usar en un foreach
	 *
	 * @access public
	 * @param string $table
	 * @param string $where
	 * @param string $fields
	 * @param string $orderBy
	 * @return array
	 */
	public function find($table, $where="", $fields="*", $orderBy="1"){
		ActiveRecord::sqlItemSanizite($table);
		ActiveRecord::sqlSanizite($fields);
		ActiveRecord::sqlSanizite($orderBy);
		if($where!=''){
			$where = 'WHERE '.$where;
		}
		$q = $this->query("SELECT $fields FROM $table WHERE $where ORDER BY $orderBy");
		$results = array();
		while($row = $this->fetchArray($q)){
			$results[] = $row;
		}
		return $results;
	}

	/**
	 * Realiza un query SQL y devuelve un array con los array resultados en forma
	 * indexada por numeros y asociativamente
	 *
	 * @access public
	 * @param string $sqlQuery
	 * @param integer $type
	 * @return array
	 */
	public function inQuery($sqlQuery){
		$resultQuery = $this->query($sqlQuery);
		$results = array();
		if($resultQuery!=false){
			while($row = $this->fetchArray($resultQuery)){
				$results[] = $row;
			}
		}
		return $results;
	}

	/**
	 * Realiza un query SQL y devuelve un array con los array resultados en forma
	 * indexada por numeros y asociativamente (Alias para inQuery)
	 *
	 * @param string $sqlQuery
	 * @param integer $type
	 * @return array
	 */
	public function fetchAll($sqlQuery){
		return $this->inQuery($sqlQuery);
	}

	/**
	 * Realiza un query SQL y devuelve un array con los array resultados en forma
	 * indexada asociativamente
	 *
	 * @param string $sqlQuery
	 * @param integer $type
	 * @return array
	 */
	public function inQueryAssoc($sqlQuery){
		$q = $this->query($sqlQuery);
		$results = array();
		if($q){
			$this->setFetchMode(self::DB_ASSOC);
			while($row = $this->fetchArray($q)){
				$results[] = $row;
			}
		}
		return $results;
	}

	/**
	 * Realiza un query SQL y devuelve un array con los array resultados en forma
	 * numerica
	 *
	 * @param string $sqlQuery
	 * @param integer $type
	 * @return array
	 */
	public function inQueryNum($sqlQuery){
		$resultQuery = $this->query($sqlQuery);
		$results = array();
		if($resultQuery){
			$this->setFetchMode(self::DB_NUM);
			while($row = $this->fetchArray($q)){
				$results[] = $row;
			}
		}
		return $results;
	}

	/**
	 * Devuelve un array del resultado de un select de un solo registro
	 *
	 * @access public
	 * @param string $sqlQuery
	 * @param integer $fetchType
	 * @return array
	 */
	public function fetchOne($sqlQuery){
		$resultQuery = $this->query($sqlQuery);
		if($resultQuery){
			if($this->numRows($resultQuery)>1){
				Flash::warning("Una sentencia SQL: \"$sqlQuery\" retornó m&aacute;s de una fila cuando se esperaba una sola");
			}
			return $this->fetchArray($resultQuery);
		} else {
			return array();
		}
	}

	/**
	 * Realiza una inserción
	 *
	 * @access public
	 * @param string $table
	 * @param array $values
	 * @param array $fields
	 * @param boolean $automaticQuotes
	 * @return boolean
	 */
	public function insert($table, $values, $fields=null, $automaticQuotes=false){
		$insertSQL = '';
		if($this->isReadOnly()==true){
			throw new DbException("No se puede efectuar la operación. La transacción es de solo lectura");
		}
		if(is_array($values)==true){
			if(count($values)==0){
				throw new DbException("Imposible realizar inserción en $table sin datos");
			} else {
				if($automaticQuotes==true){
					foreach($values as $key => $value){
						if(is_object($value)&&($value instanceof DbRawValue)){
							$values[$key] = addslashes($value->getValue());
						} else {
							$values[$key] = "'".addslashes($value)."'";
						}
					}
				}
			}
			if(is_array($fields)==true){
				$insertSQL = 'INSERT INTO '.$table.' ('.join(', ', $fields).') VALUES ('.join(', ', $values).')';
			} else {
				$insertSQL = 'INSERT INTO '.$table.' VALUES ('.join(', ', $values).')';
			}
			return $this->query($insertSQL);
		} else{
			throw new DbException('El segundo parámetro para insert no es un Array', 0, true, $this);
		}
	}

	/**
	 * Actualiza registros en una tabla
	 *
	 * @access public
	 * @param string $table
	 * @param array $fields
	 * @param array $values
	 * @param string $whereCondition
	 * @param boolean $automaticQuotes
	 * @return boolean
	 */
	public function update($table, $fields, $values, $whereCondition=null, $automaticQuotes=false){
		if($this->isReadOnly()==true){
			throw new DbException("No se puede efectuar la operación. La transacción es de solo lectura", 0, true, $this);
		}
		$updateSql = 'UPDATE '.$table.' SET ';
		if(count($fields)!=count($values)){
			throw new DbException('Los n&uacute;mero de valores a actualizar no es el mismo de los campos', 0, true, $this);
		}
		$i = 0;
		$updateValues = array();
		foreach($fields as $field){
			if($automaticQuotes==true){
				if(is_object($values[$i])&&($values[$i] instanceof DbRawValue)){
					$values[$i] = addslashes($values[$i]->getValue());
				} else {
					$values[$i] = "'".addslashes($values[$i])."'";
				}
			}
			$updateValues[] = $field.' = '.$values[$i];
			$i++;
		}
		$updateSql.= join(', ', $updateValues);
		if($whereCondition!=null){
			$updateSql.= ' WHERE '.$whereCondition;
		}
		return $this->query($updateSql);
	}

	/**
	 * Borra registros de una tabla
	 *
	 * @access public
	 * @param string $table
	 * @param string $whereCondition
	 * @return boolean
	 */
	public function delete($table, $whereCondition=''){
		if($this->isReadOnly()==true){
			throw new DbException("No se puede efectuar la operación. La transacción es de solo lectura", 0, true, $this);
		}
		if(trim($whereCondition)!=""){
			return $this->query('DELETE FROM '.$table.' WHERE '.$whereCondition);
		} else {
			return $this->query('DELETE FROM '.$table);
		}
	}

	/**
	 * Inicia una transacción si es posible
	 *
	 * @access public
	 * @return boolean
	 */
	public function begin(){
		$this->_autoCommit = false;
		$this->_underTransaction = true;
		return $this->query('BEGIN');
	}

	/**
	 * Cancela una transacción si es posible
	 *
	 * @access public
	 * @return boolean
	 */
	public function rollback(){
		if($this->_underTransaction==true){
			$this->_underTransaction = false;
			$this->_autoCommit = true;
			return $this->query('ROLLBACK');
		} else {
			throw new DbException("No hay una transacción activa en la conexión al gestor relacional", 0, true, $this);
		}
	}

	/**
	 * Hace commit sobre una transacción si es posible
	 *
	 * @access public
	 * @return boolean
	 */
	public function commit(){
		if($this->_underTransaction==true){
			$this->_underTransaction = false;
			$this->_autoCommit = true;
			return $this->query('COMMIT');
		} else {
			throw new DbException("No hay una transacción activa en la conexión al gestor relacional", 0, true, $this);
		}
	}

	/**
	 * Agrega comillas o simples segun soporte el RBDM
	 *
	 * @access public
	 * @param string $value
	 * @return string
	 * @static
	 */
	static public function addQuotes($value){
		return "'".addslashes($value)."'";
	}

	/**
	 * Loggea las operaciones sobre la base de datos si estan habilitadas
	 *
	 * @access protected
	 * @param string $sqlStatement
	 * @param string $type
	 */
	protected function log($sqlStatement, $type){
		if($this->_logger){
			if(is_bool($this->_logger)&&$this->_logger==true){
				$this->_logger = new Logger('File', 'db'.date('Ymd').'.txt');
			} else {
				if(is_object($this->_logger)){
					$this->_logger = $this->_logger;
				} else {
					if(is_string($this->_logger)){
						$this->_logger = new Logger('File', $this->_logger);
					} else {
						return false;
					}
				}
			}
			$this->_logger->log($sqlStatement, $type);
		}
	}

	/**
	 * Almacena una traza interna de todo el SQL en una conexión
	 *
	 * @access protected
	 * @param string $sqlStatement
	 */
	protected function trace($sqlStatement){
		if($this->_trace==true){
			$this->_tracedSQL[] = $sqlStatement;
		}
	}

	/**
	 * Devuelve el vector del SQL trazado
	 *
	 * @access public
	 * @return array
	 */
	public function getTracedSQL(){
		return $this->_tracedSQL;
	}

	/**
	 * Muestra Mensajes de Debug en Pantalla si esta habilitado
	 *
	 * @access protected
	 * @param string $sqlStatement
	 */
	protected function debug($sqlStatement){
		if($this->_debug==true){
			Flash::notice($this->getConnectionId(true).': '.$sqlStatement);
		}
	}

	/**
	 * Realiza una conexión directa al motor de base de datos
	 * usando el driver de Kumbia
	 *
	 * $newConnection = Si es verdadero devuelve un objeto
	 * db nuevo y no el del singleton
	 *
	 * @access public
	 * @param boolean $renovate
	 * @param boolean $newConnection
	 * @return DbBase
	 * @static
	 */
	public static function rawConnect($newConnection=false, $renovate=false){
		$config = CoreConfig::readEnviroment();
		if($newConnection==true){
			if(isset($config->database)==false){
				throw new DbException('No se ha definido los par&aacute;metros de conexión al gestor relacional en enviroment.ini', 0, true, $this);
			}
			$connection = new db($config->database);
			if($renovate==true){
				self::$_rawConnection = $connection;
			}
		} else {
			if(isset($config->database)==false){
				throw new DbException('No se ha definido los par&aacute;metros de conexión al gestor relacional en enviroment.ini');
			}
			if(self::$_rawConnection==null){
				self::$_rawConnection = new db($config->database);
			}
			$connection = self::$_rawConnection;
		}
		return $connection;
	}

	/**
	 * Permite especificar si esta en modo debug o no
	 *
	 * @param boolean $debug
	 */
	public function setDebug($debug){
		$this->_debug = $debug;
	}

	/**
	 * Permite especificar el logger del Adaptador
	 *
	 * @param boolean $logger
	 */
	public function setLogger($logger){
		$this->_logger = $logger;
	}

	/**
	 * Establece si va a realizar Profile en la conexion
	 *
	 * @param DbProfiler|boolean $profiler
	 */
	public function setProfiling($profiler){
		if(is_object($profiler)){
			$this->_profiler = $profiler;
		} else {
			if($profiler){
				$this->_profiler = new DbProfiler();
			}
		}
	}

	/**
	 * Establece si se debe trazar el SQL enviado en la conexión activa
	 *
	 * @param boolean $trace
	 */
	public function setTracing($trace){
		$this->_trace = $trace;
	}

	/**
	 * Indica si la conexion se le esta haciendo traza
	 *
	 * @param boolean $trace
	 */
	public function isTracing(){
		return $this->_trace;
	}

	/**
	 * Indica si la conexión se encuentra en una transacción
	 *
	 * @access public
	 * @return boolean
	 */
	public function isUnderTransaction(){
		return $this->_underTransaction;
	}

	/**
	 * Permite establecer si se encuentra bajo transaccion
	 *
	 * @param boolean $underTransaction
	 */
	protected function setUnderTransaction($underTransaction){
		$this->_underTransaction = $underTransaction;
	}

	/**
	 * Indica si el Gestor tiene Autocommit habilitado
	 *
	 * @access public
	 * @return boolean
	 */
	public function getHaveAutoCommit(){
		return $this->_autoCommit;
	}

	/**
	 * Permite establecer si la conexión es de solo lectura
	 *
	 * @access public
	 * @param boolean $readOnly
	 */
	public function setReadOnly($readOnly){
		$this->_isReadOnly = $readOnly;
	}

	/**
	 * Indica si la conexión es de solo lectura
	 *
	 * @access public
	 * @return boolean
	 */
	public function isReadOnly(){
		return $this->_isReadOnly;
	}

	/**
	 * Indica si la conexión esta bajo debug
	 *
	 * @access public
	 * @return boolean
	 */
	public function isDebugged(){
		return $this->_debug;
	}

	/**
	 * Ejecuta una sentencia SQL en el gestor relacional
	 *
	 * @param string $sqlStatement
	 * @return boolean
	 */
	public function query($sqlStatement){
		return false;
	}

	/**
	 * Devuelve el nombre de la base de datos
	 *
	 * @return string
	 */
	public function getDatabaseName(){
		if(isset($this->_descriptor->name)){
			return $this->_descriptor->name;
		} else {
			return '';
		}
	}

	/**
	 * Devuelve el nombre del usuario de la base de datos ó propietario del schema
	 *
	 * @return string
	 */
	public function getUsername(){
		if(isset($this->_descriptor->username)){
			return $this->_descriptor->username;
		} else {
			return '';
		}
	}

	/**
	 * Devuelve el nombre del host o direccion IP del servidor del RBDM
	 *
	 * @return string
	 */
	public function getHostName(){
		if(isset($this->_descriptor->host)){
			return $this->_descriptor->host;
		} else {
			return '';
		}
	}

	/**
	 * Devuelve el id de Conexion generado por el driver
	 *
	 * @param boolean $asString
	 * @return resource
	 */
	public function getConnectionId($asString=false){
		return $this->_idConnection;
	}

	/**
	 * Devuelve el ultimo cursor generado por el driver
	 *
	 * @return resource
	 */
	public function getLastResultQuery(){
		return $this->_lastResultQuery;
	}

	/**
	 * Establece el timeout de la conexion
	 *
	 * @param int $timeout
	 */
	public function setTimeout($timeout){

	}

}
