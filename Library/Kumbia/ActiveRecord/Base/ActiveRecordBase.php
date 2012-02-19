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
 * @subpackage	ActiveRecordBase
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright	Copyright (c) 2007-2007 Roger Jose Padilla Camacho (rogerjose81 at gmail.com)
 * @copyright	Copyright (c) 2007-2008 Emilio Rafael Silveira Tovar (emilio.rst at gmail.com)
 * @copyright	Copyright (c) 2007-2008 Deivinson Tejeda Brito (deivinsontejeda at gmail.com)
 * @license		New BSD License
 * @version 	$Id: ActiveRecordBase.php 104 2009-10-09 02:52:26Z gutierrezandresfelipe $
 */

/**
 * @see ActiveRecordResultInterface
 */
require 'Library/Kumbia/ActiveRecord/Interface.php';

/**
 * ActiveRecordBase
 *
 * Este componente es el encargado de realizar el mapeo objeto-relacional y
 * de encargarse de los modelos en la arquitectura MVC de las aplicaciones.
 * El concepto de ORM se refiere a una técnica de mapear las relaciones de
 * una base de datos a objetos nativos del lenguaje utilizado
 * (PHP en este caso), de tal forma que se pueda interactuar con ellos
 * en forma más natural.
 *
 * Los objetivos de este componente van más allá de mapear tablas y
 * convertirlas en clases (incluyendo tipos de datos, constraints,
 * lógica de dominio, etc.) ó de convertir registros en objetos.
 *
 * La idea es reducir el mantenimiento de la interacción con las bases
 * de datos en gran medida mediante varias capas de abstracción, esto
 * incluye reducir el uso de SQL ó lidiar con conexiones y sintaxis
 * programacional de bajo nivel.
 *
 * @category	Kumbia
 * @package		ActiveRecord
 * @subpackage	ActiveRecordBase
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright	Copyright (C) 2007-2007 Roger Jose Padilla Camacho(rogerjose81 at gmail.com)
 * @copyright	Copyright (C) 2007-2008 Emilio Rafael Silveira Tovar (emilio.rst@gmail.com)
 * @copyright	Copyright (c) 2007-2008 Deivinson Tejeda Brito (deivinsontejeda at gmail.com)
 * @license		New BSD License
 * @access		public
 * @abstract
 */
abstract class ActiveRecordBase extends Object implements ActiveRecordResultInterface {

	/**
	 * Resource de conexion a la base de datos
	 *
	 * @var DbBase
	 */
	protected $_db = '';

	/**
	 * Schema donde esta la tabla
	 *
	 * @var string
	 */
	protected $_schema = '';

	/**
	 * Tabla utilizada para realizar el mapeo
	 *
	 * @var string
	 */
	protected $_source = '';

	/**
	 * Numero de resultados generados en la ultima consulta
	 *
	 * @var integer
	 */
	protected $_count = 0;

	/**
	 * Indica si la clase corresponde a un mapeo de una vista
	 * en la base de datos
	 *
	 * @var boolean
	 */
	protected $isView = false;

	/**
	 * Indica si el modelo esta en modo debug
	 *
	 * @var boolean
	 */
	private $_debug = false;

	/**
	 * Indica si se logearan los mensajes generados por la clase
	 *
	 * @var mixed
	 */
	private $_logger = false;

	/**
	 * Variable para crear una condicion basada en los
	 * valores del where
	 *
	 * @var string
	 */
	private $_wherePk = '';

	/**
	 * Puntero del Objeto en la transaccion
	 *
	 * @var int
	 */
	private $_dependencyPointer;

	/**
	 * Indica si ya se han obtenido los metadatos del Modelo
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_dumped = false;

	/**
	 * Indica si hay bloqueo sobre los warnings cuando una propiedad
	 * del modelo no esta definida
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_dumpLock = false;

	/**
	 * Lista de mensajes de error
	 *
	 * @var array
	 * @access protected
	 */
	protected $_errorMessages = '';

	/**
	 * Indica la ultima operación realizada en el modelo
	 *
	 * @var int
	 */
	protected $_operationMade;

	/**
	 * Indica si la entidad ya existe y/o obliga a comprobarlo
	 *
	 * @var bool
	 */
	protected $_forceExists = false;

	/**
	 * Indica si se debe hacer dynamic update
	 *
	 * @var boolean
	 */
	private static $_dynamicUpdate = false;

	/**
	 * Indica si se debe hacer dynamic insert
	 *
	 * @var boolean
	 */
	private static $_dynamicInsert = false;

	/**
	 * Indica si se deben deshabilitar los eventos
	 *
	 * @var boolean
	 */
	private static $_disableEvents = false;

	/**
	 * Indica que la ultima operación fue una inserción
	 *
	 */
	const OP_CREATE = 1;

	/**
	 * Indica que la ultima operación fue una actualización
	 *
	 */
	const OP_UPDATE = 2;

	/**
	 * Indica que la ultima operación fue una eliminación
	 *
	 */
	const OP_DELETE = 3;

	/**
	 * Constructor del Modelo
	 *
	 * @access public
	 */
	public function __construct(){
		if(Facility::getFacility()==Facility::USER_LEVEL){
			if($this->_source==''){
				$this->_source = EntityManager::getSourceName(get_class($this));
			}
			if(method_exists($this, 'initialize')){
				$this->initialize();
			}
			$numberArguments = func_num_args();
			if($numberArguments>0){
				$params = func_get_args();
				if(!isset($params[0])||!is_array($params[0])){
					$params = Utils::getParams($params, $numberArguments);
				}
				$this->dumpResultSelf($params);
			}
		} else {
			if(method_exists($this, 'initialize')){
				$this->initialize();
			}
		}
	}

	/**
	 * Obtiene el nombre de la relacion en el RDBM a partir del nombre de la clase
	 *
	 * @access private
	 */
	private function _findModelName(){
		if($this->_source==''){
			$this->_source = Utils::uncamelize(get_class($this));
		}
		if($this->_source==''){
			$this->_source = get_class($this);
		}
	}

	/**
	 * Establece publicamente el $source de la tabla
	 *
	 * @param string $source
	 * @access public
	 */
	public function setSource($source){
		$this->_source = $source;
	}

	/**
	 * Devuelve el source actual
	 *
	 * @access public
	 * @return string
	 */
	public function getSource(){
		return $this->_source;
	}

	/**
	 * Establece el Schema de la tabla
	 *
	 * @param string $schema
	 */
	public function setSchema($schema){
		CoreType::assertString($schema);
		if($schema!=$this->_schema){
			$this->_dumped = false;
		}
		$this->_schema = $schema;
	}

	/**
	 * Devuelve el schema donde está tabla
	 *
	 * @param string $schema
	 * @return string
	 */
	public function getSchema(){
		return $this->_schema;
	}

	/**
	 * Establece la conexion con la que trabajará el modelo
	 *
	 * @access public
	 * @param string $mode
	 */
	public function setConnection($db){
		$this->_db = $db;
	}

	/**
	 * Devuelve el conteo del ultimo find ejecutado en el modelo
	 *
	 * @access public
	 * @return integer
	 */
	public function getCount(){
		return $this->_count;
	}

	/**
	 * Pregunta si el ActiveRecord ya ha consultado la informacion de metadatos
	 * de la base de datos o del registro persistente
	 *
	 * @access public
	 * @return boolean
	 */
	public function isDumped(){
		return $this->_dumped;
	}

	/**
	 * Se conecta a la base de datos y descarga los meta-datos si es necesario
	 *
	 * @param boolean $newConnection
	 * @access protected
	 */
	protected function _connect($newConnection=false){
		if($newConnection||!is_object($this->_db)){
			$this->_db = DbBase::rawConnect($newConnection);
		}
		if($this->_debug==true){
			$this->_db->setDebug($this->_debug);
		}
		if($this->_logger!=false){
			$this->_db->setLogger($this->_logger);
		}
		$this->dump();
	}

	/**
	 * Cargar los metadatos de la tabla
	 *
	 * @access public
	 */
	public function dumpModel(){
		$this->_connect();
	}

	/**
	 * Verifica si la tabla definida en $this->_source existe
	 * en la base de datos y la vuelca en dumpInfo
	 *
	 * @access protected
	 * @return boolean
	 * @throws ActiveRecordException
	 */
	protected function dump(){
		if($this->_dumped===true){
			return false;
		}
		if($this->_source==''){
			$this->_findModelName();
			if($this->_source==''){
				return false;
			}
		}
		$table = $this->_source;
		$schema = $this->_schema;
		if(!ActiveRecordMetaData::existsMetaData($table, $schema)) {
			$this->_dumped = true;
			if($this->_db->tableExists($table, $schema)){
				$this->_dumpInfo($table, $schema);
			} else {
				if($schema!=''){
					throw new ActiveRecordException('No existe la entidad "'.$schema."'.'".$table.'" en el gestor relacional: '.get_class($this));
				} else {
					throw new ActiveRecordException('No existe la entidad "'.$table.'" en el gestor relacional: '.get_class($this));
				}
				return false;
			}
		} else {
			if($this->isDumped()==false){
				$this->_dumped = true;
				$this->_dumpInfo($table, $schema);
			}
		}
		$this->_dumpLock = true;
		foreach(ActiveRecordMetaData::getAttributes($table, $schema) as $field){
			if(!isset($this->$field)){
				$this->$field = '';
			}
		}
		$this->_dumpLock = false;
		return true;
	}

	/**
	 * Establece el bloqueo de excepciones
	 *
	 * @param boolean $dumplock
	 */
	protected function _setDumpLock($dumplock){
		$this->_dumpLock = $dumplock;
	}

	/**
	 * Obtiene el estado del dumpLock
	 *
	 * @return boolean
	 */
	protected function _getDumpLock(){
		return $this->_dumpLock;
	}

	/**
	 * Vuelca la información de la tabla $table en la base de datos
	 * para crear los atributos y meta-data del ActiveRecord
	 *
	 * @access protected
	 * @param string $tablename
	 * @param string $schemaName
	 * @return boolean
	 */
	protected function _dumpInfo($tableName, $schemaName=''){
		$this->_dumpLock = true;
		if(!ActiveRecordMetaData::existsMetaData($tableName, $schemaName)){
			$metaData = $this->_db->describeTable($tableName, $schemaName);
			ActiveRecordMetaData::dumpMetaData($tableName, $schemaName, $metaData);
		}
		$fields = ActiveRecordMetaData::getAttributes($tableName, $schemaName);
		if(count($fields)==0){
			if($schemaName){
				$tablename = '"'.$tableName.'"."'.$schemaName.'"';
			} else {
				$tableName = '"'.$tableName.'"';
			}
			throw new ActiveRecordException('No se pudo obtener los meta-datos de la entidad '.$tableName);
		}
		foreach($fields as $field){
			if(!isset($this->$field)){
				$this->$field = '';
			}
		}
		$this->_dumpLock = false;
		return true;
	}

	/**
	 * Inicializa los valores
	 *
	 * @access public
	 */
	public function clear(){
		$this->_connect();
		$fields = $this->_getAttributes();
		foreach($fields as $field){
			$this->$field = null;
		}
	}

	/**
	 * Elimina la información de cache del objeto y hace que sea cargada en la proxima operación
	 *
	 * @access public
	 */
	public function resetMetaData(){
		$this->_dumped = false;
		if($this->isDumped()==false){
			$this->dump();
		}
	}

	/**
	 * Permite especificar si esta en modo debug o no
	 *
	 * @access public
	 * @param boolean $debug
	 */
	public function setDebug($debug){
		CoreType::assertBool($debug);
		$this->_debug = $debug;
		if($debug==true){
			$this->_connect();
			$this->_db->setDebug($this->_debug);
		}
	}

	/**
	 * Permite especificar el logger del Modelo
	 *
	 * @access public
	 * @param boolean $logger
	 */
	public function setLogger($logger){
		$this->_logger = $logger;
	}

	/**
	 * Establece el administrador de Transaciones del Modelo
	 *
	 * @access public
	 * @param ActiveRecordTransaction $transaction
	 * @throws ActiveRecordException
	 */
	public function setTransaction(ActiveRecordTransaction $transaction){
		if($transaction->getConnection()->isUnderTransaction()==false){
			throw new ActiveRecordException('La transacción no se ha iniciado');
		}
		if($transaction->isManaged()==true){
			$this->_dependencyPointer = $transaction->attachDependency($this->_dependencyPointer, $this);
		}
		$this->_db = $transaction->getConnection();
	}

	/**
	 * Cambia la conexión transaccional por la conexión predeterminada
	 *
	 * @access public
	 */
	public function detachTransaction(){
		$this->_db = Db::rawConnect();
	}

	/**
	 * Devuelve el objeto interno de conexión a la base de datos
	 *
	 * @access public
	 * @return DbBase
	 */
	public function getConnection(){
		if(!$this->_db){
			$this->_connect();
		}
		return $this->_db;
	}

	/**
	 * Find all records in this table using a SQL Statement
	 *
	 * @access public
	 * @param string $sqlQuery
	 * @return ActiveRecordResultset
	 */
	public function findAllBySql($sqlQuery){
		$this->_connect();
		$resultSet = $this->_db->query($sqlQuery);
		if($this->_db->numRows($resultSet)>0){
			return new ActiveRecordResultset($this, $resultSet, $sqlQuery);
		} else {
			return new ActiveRecordResultset($this, false, $sqlQuery);
		}
	}

	/**
	 * Find a record in this table using a SQL Statement
	 *
	 * @access public
	 * @param string $sqlQuery
	 * @return ActiveRecord Cursor
	 */
	public function findBySql($sqlQuery){
		$this->_connect();
		$this->_db->setFetchMode(DbBase::DB_ASSOC);
		$row = $this->_db->fetchOne($sqlQuery);
		if($row!==false){
			$this->dumpResultSelf($row);
			return $this->dumpResult($row);
		} else {
			return false;
		}
	}

	/**
	 * Execute a SQL Query Statement directly
	 *
	 * @access public
	 * @param string $sqlQuery
	 * @return DbResource
	 */
	public function sql($sqlQuery){
		$this->_connect();
		return $this->_db->query($sqlQuery);
	}

	/**
	 * Return Fist Record
	 *
	 * @access public
	 * @param mixed $params
	 * @param boolean $debug
	 * @return ActiveRecord
	 */
	public function findFirst($params=''){
		$this->_connect();
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		$select = 'SELECT ';
		if(isset($params['columns'])){
			$this->clear();
			$select.= $params['columns'];
		} else {
			$select.= join(', ', $this->_getAttributes());
		}
		if($this->_schema!=''){
			$select.= ' FROM '.$this->_schema.'.'.$this->_source;
		} else {
			$select.= ' FROM '.$this->_source;
		}
		if(!isset($params['limit'])){
			$params['limit'] = 1;
		}
		$select.= $this->convertParamsToSql($params);
		$resp = false;
		try {
			$this->_db->setFetchmode(DbBase::DB_ASSOC);
			$result = $this->_db->fetchOne($select);
			if($result){
				$this->dumpResultSelf($result);
				$resp = $this->dumpResult($result);
			}
			$this->_db->setFetchmode(DbBase::DB_BOTH);
		}
		catch(Exception $e){
			$this->exceptions($e);
		}
		return $resp;
	}

	/**
	 * Crea una sentencia SQL
	 *
	 * @access private
	 * @param array $params
	 * @return string
	 */
	private function _createSQLSelect($params){
		$select = 'SELECT ';
		if(isset($params['columns'])){
			$this->clear();
			$select.= $params['columns'];
		} else {
			$select.= join(', ', $this->_getAttributes());
		}
		if($this->_schema){
			$select.= ' FROM '.$this->_schema.'.'.$this->_source;
		} else {
			$select.= ' FROM '.$this->_source;
		}
		$return = 'n';
		$primaryKeys = $this->_getPrimaryKeyAttributes();
		if(isset($params['conditions'])&&$params['conditions']){
			$select.= ' WHERE '.$params['conditions'].' ';
		} else {
			if(!isset($primaryKeys[0])){
				if($this->isView==true){
					$primaryKeys[0] = 'id';
				}
			}
			if(isset($params[0])){
				if(is_numeric($params[0])){
					if(isset($primaryKeys[0])){
						$params['conditions'] = $primaryKeys[0].' = '.$this->_db->addQuotes($params[0]);
						$return = '1';
					} else {
						throw new ActiveRecordException('No se ha definido una llave primaria para este objeto');
					}
				} else {
					if($params[0]==''){
						$params['conditions'] = $primaryKeys[0]." = ''";
					} else {
						$params['conditions'] = $params[0];
					}
					$return = 'n';
				}
			}
			if(isset($params['conditions'])){
				$select.= ' WHERE '.$params['conditions'];
			}
		}
		if(isset($params['order'])&&$params['order']) {
			$select.= ' ORDER BY '.$params['order'];
		}
		if(isset($params['limit'])&&$params['limit']) {
			$select = $this->_limit($select, $params['limit']);
		}
		if(isset($params['for_update'])&&$params['for_update']==true){
			$select = $this->_db->forUpdate($select);
		}
		if(isset($params['shared_lock'])&&$params['shared_lock']==true){
			$select = $this->_db->sharedLock($select);
		}
		return array('return' => $return, 'sql' => $select);
	}

	/**
	 * Crea un Resultset creado por _createSQLSelect
	 *
	 * @access private
	 * @param string $select
	 * @return boolean|ActiveRecordResulset
	 */
	private function _createResultset($select, $resultResource){
		if($select['return']=='1'){
			if($this->_db->numRows($resultResource)==0){
				$this->_count = 0;
				return false;
			} else {
				$this->_db->setFetchMode(DbBase::DB_ASSOC);
				$uniqueRow = $this->_db->fetchArray($resultResource);
				$this->_db->setFetchMode(DbBase::DB_BOTH);
				$this->dumpResultSelf($uniqueRow);
				$this->_count = 1;
				return $this->dumpResult($uniqueRow);
			}
		} else {
			$this->_count = $this->_db->numRows($resultResource);
			if($this->_count>0){
				return new ActiveRecordResultset($this, $resultResource, $select['sql']);
			} else {
				return new ActiveRecordResultset($this, false, $select['sql']);
			}
		}
	}

	/**
	 * Find data on Relational Map table
	 *
	 * @access	public
	 * @param 	string $params
	 * @return 	ActiveRecordResulset
	 */
	public function find($params=''){
		$this->_connect();
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		$select = $this->_createSQLSelect($params);
		$resultResource = $this->_db->query($select['sql']);
		return $this->_createResultset($select, $resultResource);
	}

	/**
	 * Find data on Relational Map table and locks Resultset
	 *
	 * @access public
	 * @param string $params
	 * @return ActiveRecordResulset
	 * @throws ActiveRecordException
	 */
	public function findForUpdate($params=''){
		$this->_connect();
		if($this->_db->isUnderTransaction()==false){
			throw new ActiveRecordException('No se puede hacer el findForUpdate mientras no este bajo una transacción');
		}
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		$params['for_update'] = true;
		$select = $this->_createSQLSelect($params);
		$resultResource = $this->_db->query($select['sql']);
		return $this->_createResultset($select, $resultResource);
	}

	/**
	 * Find data on Relational Map table and locks Resultset using SharedLock
	 *
	 * @access public
	 * @param string $params
	 * @return ActiveRecordResulset
	 * @throws ActiveRecordException
	 */
	public function findWithSharedLock($params=''){
		$this->_connect();
		if($this->_db->isUnderTransaction()==false){
			throw new ActiveRecordException('No se puede hacer el findWithSharedLock mientras no este bajo una transacción');
		}
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		$params['shared_lock'] = true;
		$select = $this->_createSQLSelect($params);
		$resultResource = $this->_db->query($select['sql']);
		return $this->_createResultset($select, $resultResource);
	}

	/**
	 * Arma una consulta SQL con el parametro $params, aso:
	 * 	$params = Utils::getParams(func_get_args());
	 * 	$select = "SELECT * FROM Clientes";
	 *	$select.= $this->convertParamsToSql($params);
	 *
	 * @access public
	 * @param string $params
	 * @return string
	 */
	public function convertParamsToSql($params = ''){
		$select = '';
		if(is_array($params)){
			if(isset($params['conditions'])&&$params['conditions']){
				$select.= ' WHERE '.$params["conditions"].' ';
			} else {
				$primaryKeys = $this->_getPrimaryKeyAttributes();
				if(!isset($primaryKeys[0]) && (isset($this->id) || $this->isView)){
					$primaryKeys[0] = 'id';
				}
				if(isset($params[0])){
					if(is_numeric($params[0])){
						$params['conditions'] = $primaryKeys[0].' = '.$this->_db->addQuotes($params[0]);
					} else {
						if($params[0]==''){
							$params['conditions'] = $primaryKeys[0].' = \'\'';
						} else {
							$params['conditions'] = $params[0];
						}
					}
				}
				if(isset($params['conditions'])){
					$select.= ' WHERE '.$params['conditions'];
				}
			}
			if(isset($params['order'])&&$params['order']) {
				$select.=' ORDER BY '.$params['order'];
			} else {
				$select.=' ORDER BY 1';
			}
			if(isset($params['limit'])&&$params['limit']) {
				$select = $this->_limit($select, $params['limit']);
			}
			if(isset($params['for_update'])){
				if($params['for_update']==true){
					$select = $this->_db->forUpdate($select);
				}
			}
			if(isset($params['shared_lock'])){
				if($params['shared_lock']==true){
					$select = $this->_db->sharedLock($select);
				}
			}
		} else {
			if(strlen($params)>0){
				if(is_numeric($params)){
					$select.= 'WHERE '.$primaryKeys[0].' = \''.$params.'\'';
				} else {
					$select.= 'WHERE '.$params;
				}
			}
		}
		return $select;
	}

	/**
	 * Devuelve una clausula LIMIT adecuada al RDBMS empleado
	 *
	 * @access private
	 * @param string $sqlStatement
	 * @param $number
	 * @return string
	 */
	private function _limit($sqlStatement, $number = 1){
		return $this->_db->limit($sqlStatement, $number);
	}

	/**
	 * Obtiene ó crea una instancia dadas unas condiciones
	 *
	 * @param string $entityName
	 * @param array $conditions
	 * @param array $findOptions
	 * @static
	 * @return ActiveRecord
	 */
	static public function getInstance($entityName, array $conditions, array $findOptions=array()){
		$criteria = array();
		foreach($conditions as $field => $value){
			if(is_integer($value)||is_double($value)){
				$criteria[] = $field.' = '.$value;
			} else {
				$criteria[] = $field.' = '.$value;
			}
		}
		$queryConditions = join(' AND ', $criteria);
		$entity = EntityManager::getEntityInstance($entityName);
		$arguments = array($queryConditions) + $findOptions;
		$exists = call_user_func_array(array($entity, 'findFirst'), $arguments);
		if($exists==false){
			foreach($conditions as $field => $value){
				$entity->writeAttribute($field, $value);
			}
		}
		return $entity;
	}

	/**
	 * Realiza un SELECT distinct de una columna del Modelo
	 *
	 * @access public
	 * @param string $params
	 * @return array
	 */
	public function distinct($params=''){
		$this->_connect();
		if($this->_schema){
			$table = $this->_schema.'.'.$this->_source;
		} else {
			$table = $this->_source;
		}
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params['column'])){
			$params['column'] = $params['0'];
		} else {
			if(!$params['column']) {
				$params['column'] = $params['0'];
			}
		}
		$select = 'SELECT DISTINCT '.$params['column'].' FROM '.$table;
		if(isset($params['conditions'])&&$params['conditions']) {
			$select.=' WHERE '.$params["conditions"];
		}
		if(isset($params['order'])&&$params['order']) {
			$select.=' ORDER BY '.$params["order"].' ';
		} else {
			$select.=' ORDER BY 1 ';
		}
		if(isset($params['limit'])&&$params['limit']) {
			$select = $this->_limit($select, $params['limit']);
		}
		$results = array();
		$this->_db->setFetchMode(DbBase::DB_NUM);
		foreach($this->_db->fetchAll($select) as $result){
			$results[] = $result[0];
		}
		$this->_db->setFetchMode(DbBase::DB_ASSOC);
		return $results;
	}

	/**
	 * Realiza un SELECT que ejecuta funciones del RBDM
	 *
	 * @access public
	 * @param string $sql
	 * @return array
	 * @static
	 */
	static public function singleSelect($sql){
		$db = db::rawConnect();
		if(substr(ltrim($sql), 0, 7)!='SELECT') {
			$sql = 'SELECT '.$sql;
		}
		$db->setFetchMode(DbBase::DB_NUM);
		$num = $db->fetchOne($sql);
		$db->setFetchMode(DbBase::DB_ASSOC);
		return $num[0];
	}

	/**
	 * Devuelve el resultado del agrupamiento
	 *
	 * @param array $params
	 * @param string $selectStatement
	 * @param string $alias
	 * @return mixed
	 */
	private function _getGroupResult(array $params, $selectStatement, $alias){
		if(isset($params['group'])){
			$resultResource = $this->_db->query($selectStatement);
			$count = $this->_db->numRows($resultResource);
			if($count>0){
				$rowObject = new ActiveRecordRow();
				$rowObject->setConnection($this->_db);
				return new ActiveRecordResultset($rowObject, $resultResource, $selectStatement);
			} else {
				return new ActiveRecordResultset(new stdClass(), false, $selectStatement);
			}
		} else {
			$num = $this->_db->fetchOne($selectStatement);
			return $num[$alias];
		}
	}

	/**
	 * Realiza un conteo de filas
	 *
	 * @access public
	 * @param string $params
	 * @return integer
	 */
	public function count($params=''){
		$this->_connect();
		if($this->_schema){
			$table = $this->_schema.'.'.$this->_source;
		} else {
			$table = $this->_source;
		}
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(isset($params['distinct'])&&$params['distinct']) {
			$select = 'SELECT COUNT(DISTINCT '.$params['distinct'].') AS rowcount FROM '.$table.' ';
		} else {
			if(isset($params['group'])&&$params['group']){
				$select = 'SELECT '.$params['group'].',COUNT(*) AS rowcount FROM '.$table.' ';
			} else {
				$select = 'SELECT COUNT(*) AS rowcount FROM '.$table.' ';
			}
		}
		if(isset($params['conditions'])&&$params['conditions']) {
			$select.=' WHERE '.$params['conditions'].' ';
		} else {
			if(isset($params[0])){
				if(is_numeric($params[0])){
					$primaryKeys = $this->_getPrimaryKeyAttributes();
					if($this->isView&&(!isset($primaryKeys[0])||!$primaryKeys[0])){
						$primaryKeys[0] = 'id';
					}
					$select.= ' WHERE '.$primaryKeys[0].' = \''.$params[0].'\'';
				} else {
					$select.= ' WHERE '.$params[0];
				}
			}
		}
		if(isset($params['group'])){
			$select.=' GROUP BY '.$params['group'].' ';
		}
		if(isset($params['having'])){
			$select.=' HAVING '.$params['having'].' ';
		}
		if(isset($params['order'])&&$params['order']) {
			$select.=' ORDER BY '.$params['order'].' ';
		}
		if(isset($params['limit'])&&$params['limit']) {
			$select = $this->_limit($select, $params['limit']);
		}
		return $this->_getGroupResult($params, $select, 'rowcount');
	}

	/**
	 * Realiza un promedio sobre el campo $params
	 *
	 * @param string $params
	 * @return array
	 */
	public function average($params=''){
		$this->_connect();
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(isset($params['column'])) {
			if(!$params['column']){
				$params['column'] = $params[0];
			}
		} else {
			$params['column'] = $params[0];
		}
		if($this->_schema){
			$table = $this->_schema.'.'.$this->_source;
		} else {
			$table = $this->_source;
		}
		if(isset($params['group'])&&$params['group']){
			$select = "SELECT {$params['group']},AVG({$params['column']}) AS average FROM $table " ;
		} else {
			$select = "SELECT AVG({$params['column']}) AS average FROM $table " ;
		}
		if(isset($params['conditions'])&&$params['conditions']){
			$select.= ' WHERE '.$params['conditions'].' ';
		}
		if(isset($params['group'])){
			$select.=' GROUP BY '.$params['group'].' ';
		}
		if(isset($params['having'])){
			$select.=' HAVING '.$params["having"].' ';
		}
		if(isset($params['order'])&&$params['order']){
			$select.=' ORDER BY '.$params['order'].' ';
		} else {
			$select.=' ORDER BY 1 ';
		}
		if(isset($params['limit'])&&$params['limit']){
			$select = $this->_limit($select, $params['limit']);
		}
		return $this->_getGroupResult($params, $select, 'average');
	}

	/**
	 * Realiza una sumatoria
	 *
	 * @access public
	 * @param string $params
	 * @return double
	 */
	public function sum($params=''){
		$this->_connect();
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(isset($params['column'])) {
			if(!$params['column']){
				$params['column'] = $params[0];
			}
		} else {
			if(!isset($params[0])){
				throw new ActiveRecordException('No ha definido la columna a sumar');
			} else {
				$params['column'] = $params[0];
			}
		}
		if($this->_schema){
			$table = $this->_schema.'.'.$this->_source;
		} else {
			$table = $this->_source;
		}
		if(isset($params['group'])&&$params['group']){
			$select = "SELECT {$params['group']},SUM({$params['column']}) AS sumatory FROM $table " ;
		} else {
			$select = "SELECT SUM({$params['column']}) AS sumatory FROM $table " ;
		}
		if(isset($params['conditions'])&&$params['conditions']){
			$select.= ' WHERE '.$params['conditions'].' ';
		}
		if(isset($params['group'])){
			$select.=' GROUP BY '.$params['group'].' ';
		}
		if(isset($params['having'])){
			$select.=' HAVING '.$params["having"].' ';
		}
		if(isset($params['order'])&&$params['order']){
			$select.=' ORDER BY '.$params['order'].' ';
		} else {
			$select.=' ORDER BY 1 ';
		}
		if(isset($params['limit'])&&$params['limit']){
			$select = $this->_limit($select, $params['limit']);
		}
		return $this->_getGroupResult($params, $select, 'sumatory');
	}

	/**
	 * Busca el valor máximo para el campo $params
	 *
	 * @access public
	 * @param string $params
	 * @return mixed
	 */
	public function maximum($params=''){
		$this->_connect();
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(isset($params['column'])) {
			if(!$params['column']){
				$params['column'] = $params[0];
			}
		} else {
			$params['column'] = $params[0];
		}
		if($this->_schema){
			$table = $this->_schema.'.'.$this->_source;
		} else {
			$table = $this->_source;
		}
		if(isset($params['group'])&&$params['group']){
			$select = "SELECT {$params['group']},MAX({$params['column']}) AS maximum FROM $table " ;
		} else {
			$select = "SELECT MAX({$params['column']}) AS maximum FROM $table " ;
		}
		if(isset($params['conditions'])&&$params['conditions']){
			$select.= ' WHERE '.$params['conditions'].' ';
		}
		if(isset($params['group'])){
			$select.=' GROUP BY '.$params['group'].' ';
		}
		if(isset($params['having'])){
			$select.=' HAVING '.$params["having"].' ';
		}
		if(isset($params['order'])&&$params['order']){
			$select.=' ORDER BY '.$params['order'].' ';
		} else {
			$select.=' ORDER BY 1 ';
		}
		if(isset($params['limit'])&&$params['limit']){
			$select = $this->_limit($select, $params['limit']);
		}
		return $this->_getGroupResult($params, $select, 'maximum');
	}

	/**
	 * Busca el valor minimo para el campo $params
	 *
	 * @access public
	 * @param string $params
	 * @return mixed
	 */
	public function minimum($params=''){
		$this->_connect();
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(isset($params['column'])) {
			if(!$params['column']){
				$params['column'] = $params[0];
			}
		} else {
			$params['column'] = $params[0];
		}
		if($this->_schema){
			$table = $this->_schema.'.'.$this->_source;
		} else {
			$table = $this->_source;
		}
		if(isset($params['group'])&&$params['group']){
			$select = 'SELECT '.$params['group'].',MIN('.$params['column'].') AS minimum FROM '.$table.' ' ;
		} else {
			$select = 'SELECT MIN('.$params['column'].') AS minimum FROM '.$table.' ' ;
		}
		if(isset($params['conditions'])&&$params['conditions']){
			$select.= ' WHERE '.$params['conditions'].' ';
		}
		if(isset($params['group'])){
			$select.=' GROUP BY '.$params['group'].' ';
		}
		if(isset($params['having'])){
			$select.=' HAVING '.$params["having"].' ';
		}
		if(isset($params['order'])&&$params['order']){
			$select.=' ORDER BY '.$params['order'].' ';
		} else {
			$select.=' ORDER BY 1 ';
		}
		if(isset($params['limit'])&&$params['limit']){
			$select = $this->_limit($select, $params['limit']);
		}
		return $this->_getGroupResult($params, $select, 'minimum');
	}

	/**
	 * Realiza un conteo directo mediante $sql
	 *
	 * @param string $sqlQuery
	 * @return mixed
	 */
	public function countBySql($sqlQuery){
		CoreType::assertString($sqlQuery);
		$this->_connect();
		$this->_db->setFetchMode(DbBase::DB_NUM);
		$num = $this->_db->fetchOne($sqlQuery);
		return (int) $num[0];
	}

	/**
	 * Iguala los valores de un resultado de la base de datos
	 * en un nuevo objeto con sus correspondientes
	 * atributos de la clase
	 *
	 * @param array $result
	 * @return ActiveRecord
	 */
	public function dumpResult(array $result){
		$this->_connect();
		$object = clone $this;
		$object->_forceExists = true;
		/**
		 * Consulta si la clase es padre de otra y crea el tipo de dato correcto
		 */
		/*if(isset($result['type'])){
			if(in_array($result['type'], $this->_parentOf)){
			if(class_exists($result['type'])){
			$obj = new $result['type'];
			unset($result['type']);
			}
			}
			}*/
		$this->_dumpLock = true;
		if(is_array($result)==true){
			foreach($result as $key => $value){
				$object->$key = $value;
			}
		}
		$this->_dumpLock = false;
		return $object;
	}

	/**
	 * Iguala los valores de un resultado de la base de datos
	 * con sus correspondientes atributos de la clase
	 *
	 * @access public
	 * @param array $result
	 */
	public function dumpResultSelf(array $result){
		$this->_connect();
		$this->_dumpLock = true;
		if(is_array($result)==true){
			foreach($result as $key => $value){
				$this->$key = $value;
			}
		}
		$this->_dumpLock = false;
	}

	/**
	 * Obtiene los mensajes de error generados en el proceso de validacion
	 *
	 * @return array
	 */
	public function getMessages(){
		return $this->_errorMessages;
	}

	/**
	 * Agrega un mensaje a la lista de errores de validación
	 *
	 * @param string $field
	 * @param string $message
	 * @throws ActiveRecordException
	 */
	public function appendMessage($message){
		if(is_object($message)&&get_class($message)!='ActiveRecordMessage'){
			throw new ActiveRecordException("Formato de Mensaje invalido '".get_class($message)."'");
		}
		$this->_errorMessages[] = $message;
	}

	/**
	 * Establece el valor del DynamicUpdate
	 *
	 * @param boolean $dynamicUpdate
	 */
	protected function setDynamicUpdate($dynamicUpdate){
		CoreType::assertBool($dynamicUpdate);
		self::$_dynamicUpdate = $dynamicUpdate;
	}

	/**
	 * Establece el valor del DynamicInsert
	 *
	 * @param boolean $dynamicInsert
	 */
	protected function setDynamicInsert($dynamicInsert){
		CoreType::assertBool($dynamicInsert);
		self::$_dynamicInsert = $dynamicInsert;
	}

	/**
	 * Devuelve los atributos del modelo (campos) (internal)
	 *
	 * @access protected
	 * @return array
	 */
	protected function _getAttributes(){
		return ActiveRecordMetaData::getAttributes($this->_source, $this->_schema);
	}

	/**
	 * Devuelve los atributos del modelo (campos)
	 *
	 * @access public
	 * @return array
	 */
	public function getAttributes(){
		$this->_connect();
		return $this->_getAttributes();
	}

	/**
	 * Devuelve los campos que son llave primaria (interno)
	 *
	 * @access protected
	 * @return array
	 */
	protected function _getPrimaryKeyAttributes(){
		return ActiveRecordMetaData::getPrimaryKeys($this->_source,  $this->_schema);
	}

	/**
	 * Devuelve los campos que son llave primaria
	 *
	 * @access public
	 * @return array
	 */
	public function getPrimaryKeyAttributes(){
		$this->_connect();
		return $this->_getPrimaryKeyAttributes();
	}

	/**
	 * Devuelve los campos que no son llave primaria (internal)
	 *
	 * @access protected
	 * @return array
	 */
	protected function _getNonPrimaryKeyAttributes(){
		return ActiveRecordMetaData::getNonPrimaryKeys($this->_source,  $this->_schema);
	}

	/**
	 * Devuelve los campos que no son llave primaria
	 *
	 * @access public
	 * @return array
	 */
	public function getNonPrimaryKeyAttributes(){
		$this->_connect();
		return $this->_getNonPrimaryKeyAttributes();
	}

	/**
	 * Devuelve los campos que son no nulos (internal)
	 *
	 * @access protected
	 * @return array
	 */
	protected function _getNotNullAttributes(){
		return ActiveRecordMetaData::getNotNull($this->_source, $this->_schema);
	}

	/**
	 * Devuelve los campos que son no nulos
	 *
	 * @access public
	 * @return array
	 */
	public function getNotNullAttributes(){
		$this->_connect();
		return $this->_getNotNullAttributes();
	}

	/**
	 * Devuelve los campos fecha que asignan la fecha del sistema automaticamente al insertar (internal)
	 *
	 * @access protected
	 * @return array
	 */
	protected function _getDatesAtAttributes(){
		return ActiveRecordMetaData::getDatesAt($this->_source, $this->_schema);
	}

	/**
	 * Obtiene los tipos de datos de los atributos
	 *
	 * @access public
	 * @return array
	 */
	public function getDataTypes(){
		$this->_connect();
		return $this->_getDataTypes();
	}

	/**
	 * Obtiene los tipos de datos de los atributos (internal)
	 *
	 * @access protected
	 * @return array
	 */
	protected function _getDataTypes(){
		return ActiveRecordMetaData::getDataTypes($this->_source, $this->_schema);
	}

	/**
	 * Devuelve los nombres de los atributos del modelo
	 *
	 * @access public
	 * @return array
	 */
	public function getAttributesNames(){
		$this->_connect();
		return ActiveRecordMetaData::getAttributes($this->_source, $this->_schema);
	}

	/**
	 * Devuelve los campos fecha que asignan la fecha del sistema automaticamente al insertar
	 *
	 * @access public
	 * @return array
	 */
	public function getDatesAtAttributes(){
		$this->_connect();
		return $this->_getDatesAtAttributes();
	}

	/**
	 * Devuelve los campos fecha que asignan la fecha del sistema automaticamente al modificar (internal)
	 *
	 * @access protected
	 * @return array
	 */
	protected function _getDatesInAttributes(){
		return ActiveRecordMetaData::getDatesIn($this->_source, $this->_schema);
	}

	/**
	 * Devuelve los campos fecha que asignan la fecha del sistema automaticamente al modificar
	 *
	 * @access public
	 * @return array
	 */
	public function getDatesInAttributes(){
		$this->_connect();
		return $this->_getDatesInAttributes();
	}

	/**
	 * Lee un atributo de la entidad por su nombre
	 *
	 * @access public
	 * @param string $attribute
	 * @return mixed
	 */
	public function readAttribute($attribute){
		CoreType::assertString($attribute);
		$this->_connect();
		return $this->$attribute;
	}

	/**
	 * Escribe el valor de un atributo de la entidad por su nombre
	 *
	 * @access public
	 * @param string $attribute
	 * @param mixed $value
	 */
	public function writeAttribute($attribute, $value){
		CoreType::assertString($attribute);
		$this->_connect();
		$this->$attribute = $value;
	}

	/**
	 * Indica si el modelo tiene el campo indicado
	 *
	 * @param string $field
	 * @return boolean
	 */
	public function hasField($field){
		CoreType::assertString($field);
		$fields = $this->_getAttributes();
		return in_array($field, $fields);
	}

	/**
	 * Indica si el modelo tiene el campo indicado
	 *
	 * @param string $field
	 * @return boolean
	 */
	public function isAttribute($field){
		return $this->hasField($field);
	}

	/**
	 * Creates a new row in map table
	 *
	 * @access	public
	 * @param	mixed $values
	 * @return	boolean
	 * @throws	ActiveRecordException
	 */
	public function create($values=''){
		$this->_connect();
		$primaryKeys = $this->_getPrimaryKeyAttributes();
		if(is_array($values)){
			$fields = $this->getAttributes();
			if(isset($values[0])&&is_array($values[0])){
				foreach($values as $value){
					foreach($fields as $field){
						$this->$field = '';
					}
					foreach($value as $k => $r){
						if(isset($this->$k)){
							$this->$k = $r;
						} else {
							throw new ActiveRecordException("No existe el Atributo '$k' en la entidad '{$this->_source}' al ejecutar la inserción");
						}
					}
					if($primaryKeys[0]=='id'){
						$this->id = null;
					}
					return $this->save();
				}
			} else {
				foreach($fields as $f){
					$this->$f = '';
				}
				foreach($values as $k => $r){
					if(isset($this->$k)){
						$this->$k = $r;
					} else {
						throw new ActiveRecordException("No existe el Atributo '$k' en la entidad '{$this->_source}' al ejecutar la insercion");
					}
				}
				if($primaryKeys[0]=='id'){
					$this->id = null;
				}
				return $this->save();
			}
		} else {
			if($values!==''){
				throw new ActiveRecordException("Parámetro incompatible en acción 'create'. No se pudo crear ningun registro");
			} else {
				//Detectar campo autonumerico
				$this->_forceExists = true;
				if($primaryKeys[0]=='id'){
					$this->id = null;
				}
				return $this->save();
			}
		}
		return true;
	}

	/**
	 * Consulta si un determinado registro existe o no en la entidad de la base de datos
	 *
	 * @access	private
	 * @param	string $wherePk
	 * @return	bool
	 */
	private function _exists($wherePk=''){
		if($this->_forceExists==false){
			if($this->_schema){
				$table = $this->_schema.'.'.$this->_source;
			} else {
				$table = $this->_source;
			}
			if($wherePk==''){
				$wherePk = array();
				$primaryKeys = $this->_getPrimaryKeyAttributes();
				if(count($primaryKeys)>0){
					foreach($primaryKeys as $key){
						if($this->$key!==null&&$this->$key!==''){
							$wherePk[] = ' '.$key.' = \''.$this->$key.'\'';
						}
					}
					if(count($wherePk)){
						$this->_wherePk = join(' AND ', $wherePk);
					} else {
						return 0;
					}
					$query = 'SELECT COUNT(*) AS rowcount FROM '.$table.' WHERE '.$this->_wherePk;
				} else {
					return 0;
				}
			} else {
				if(is_numeric($wherePk)){
					$query = 'SELECT COUNT(*) AS rowcount FROM '.$table.' WHERE id = \''.$wherePk.'\'';
				} else {
					$query = 'SELECT COUNT(*) AS rowcount FROM '.$table.' WHERE '.$wherePk;
				}
			}
			$num = $this->_db->fetchOne($query);
			return (bool) $num['rowcount'];
		} else {
			$wherePk = array();
			$primaryKeys = $this->_getPrimaryKeyAttributes();
			if(count($primaryKeys)>0){
				foreach($primaryKeys as $key){
					if($this->$key!==null&&$this->$key!==''){
						$wherePk[] = ' '.$key.' = \''.$this->$key.'\'';
					}
				}
				if(count($wherePk)){
					$this->_wherePk = join(' AND ', $wherePk);
					return true;
				} else {
					return 0;
				}
			} else {
				return 0;
			}
		}

	}

	/**
	 * Consulta si un determinado registro existe o no en la entidad de la base de datos
	 *
	 * @access	public
	 * @param	string $wherePk
	 * @return	bool
	 */
	public function exists($wherePk=''){
		$this->_connect();
		return $this->_exists($wherePk);
	}

	/**
	 * Saves Information on the ActiveRecord Properties
	 *
	 * @return boolean
	 */
	public function save(){

		$this->_connect();
		$exists = $this->_exists();

		if($exists==false){
			$this->_operationMade = self::OP_CREATE;
		} else {
			$this->_operationMade = self::OP_UPDATE;
		}

		// Run Validation Callbacks Before
		$this->_errorMessages = array();
		if(self::$_disableEvents==false){
			if($this->_callEvent('beforeValidation')===false){
				return false;
			}
			if(!$exists){
				if($this->_callEvent('beforeValidationOnCreate')===false){
					return false;
				}
			} else {
				if($this->_callEvent('beforeValidationOnUpdate')===false){
					return false;
				}
			}
		}

		//Generadores
		$className = get_class($this);
		$generator = null;
		if(EntityManager::hasGenerator($className)){
			$generator = EntityManager::getEntityGenerator($className);
			$generator->setIdentifier($this);
		}

		//LLaves foráneas virtuales
		if(EntityManager::hasForeignKeys($className)){
			$foreignKeys = EntityManager::getForeignKeys($className);
			$error = false;
			foreach($foreignKeys as $indexKey => $keyDescription){
				$entity = EntityManager::getEntityInstance($indexKey, false);
				$field = $keyDescription['fi'];
				$conditions = $keyDescription['rf'].' = \''.$this->$field.'\'';
				if(isset($keyDescription['op']['conditions'])){
					$conditions.= ' AND '.$keyDescription['op']['conditions'];
				}
				$rowcount = $entity->count($conditions);
				if($rowcount==0){
					if(isset($keyDescription['op']['message'])){
						$userMessage = $keyDescription['op']['message'];
					} else {
						$userMessage = "El valor de '{$keyDescription['fi']}' no existe en la tabla referencia";
					}
					$message = new ActiveRecordMessage($userMessage, $keyDescription['fi'], 'ConstraintViolation');
					$this->appendMessage($message);
					$error = true;
					break;
				}
			}
			if($error==true){
				$this->_callEvent('onValidationFails');
				return false;
			}
		}

		$notNull = $this->_getNotNullAttributes();
		$at = $this->_getDatesAtAttributes();
		$in = $this->_getDatesInAttributes();
		if(is_array($notNull)){
			$error = false;
			$numFields = count($notNull);
			for($i=0;$i<$numFields;++$i){
				$field = $notNull[$i];
				if($this->$field===null||$this->$field===''){
					if(!$exists&&$field=='id'){
						continue;
					}
					if(!$exists){
						if(isset($at[$field])){
							continue;
						}
					} else {
						if(isset($in[$field])){
							continue;
						}
					}
					$field = str_replace('_id', '', $field);
					$message = new ActiveRecordMessage("El campo $field no puede ser nulo ''", $field, 'PresenceOf');
					$this->appendMessage($message);
					$error = true;
				}
			}
			if($error==true){
				$this->_callEvent('onValidationFails');
				return false;
			}
		}

		// Run Validation
		if($this->_callEvent('validation')===false){
			$this->_callEvent('onValidationFails');
			return false;
		}

		if(self::$_disableEvents==false){
			// Run Validation Callbacks After
			if(!$exists){
				if($this->_callEvent('afterValidationOnCreate')===false){
					return false;
				}
			} else {
				if($this->_callEvent('afterValidationOnUpdate')===false){
					return false;
				}
			}
			if($this->_callEvent('afterValidation')===false){
				return false;
			}

			// Run Before Callbacks
			if($this->_callEvent('beforeSave')===false){
				return false;
			}
			if($exists){
				if($this->_callEvent('beforeUpdate')===false){
					return false;
				}
			} else {
				if($this->_callEvent('beforeCreate')===false){
					return false;
				}
			}
		}

		if($this->_schema){
			$table = $this->_schema.'.'.$this->_source;
		} else {
			$table = $this->_source;
		}

		$dataType = $this->_getDataTypes();
		$primaryKeys = $this->_getPrimaryKeyAttributes();
		if($exists){
			if(self::$_dynamicUpdate==false){
				$fields = array();
				$values = array();
				$nonPrimary = $this->_getNonPrimaryKeyAttributes();
				foreach($nonPrimary as $np){
					if(isset($in[$field])){
						$this->$np = Date::now();
					}
					$fields[] = $np;
					if(is_object($this->$np)&&($this->$np instanceof DbRawValue)){
						$values[] = $this->$np->getValue();
					} else {
						if($this->$np===''||$this->$np===null){
							$values[] = 'NULL';
						} else {
							if($this->isANumericType($np)==false){
								if($dataType[$np]=='date'){
									$values[] = $this->_db->getDateUsingFormat($this->$np);
								} else {
									$values[] = '\''.addslashes($this->$np).'\'';
								}
							} else {
								$values[] = '\''.addslashes($this->$np).'\'';
							}
						}
					}
				}
			} else {
				$conditions = array();
				foreach($primaryKeys as $field){
					$conditions[] = $field.' = \''.$this->field.'\'';
				}
				$pkCondition = join(' AND ', $conditions);
				$existRecord = clone $this;
				$record = $existRecord->findFirst($pkCondition);
				$fields = array();
				$values = array();
				$nonPrimary = $this->_getNonPrimaryKeyAttributes();
				foreach($nonPrimary as $np){
					if(isset($in[$np])){
						$this->$np = $this->_db->getCurrentDate();
					}
					if(is_object($this->$np)){
						if($this->$np instanceof DbRawValue){
							$value = $this->$np->getValue();
							if($record->$np!=$value){
								$fields[] = $np;
								$values[] = $values;
							}
						} else {
							throw new ActiveRecordException('El objeto instancia de "'.get_class($this->$field).'" en el campo "'.$field.'" es muy complejo, debe realizarle un "cast" a un tipo de dato escalar antes de almacenarlo');
						}
					} else {
						if($this->$np===''||$this->$np===null){
							if($record->$np!==''&&$record->$np!==null){
								$fields[] = $np;
								$values[] = 'NULL';
							}
						} else {
							if($this->isANumericType($np)==false){
								if($dataType[$np]=='date'){
									$value = $this->_db->getDateUsingFormat($this->$np);
									if($record->$np!=$value){
										$fields[] = $np;
										$values[] = $value;
									}
								} else {
									if($record->$np!=$this->$np){
										$fields[] = $np;
										$values[] = "'".addslashes($this->$np)."'";
									}
								}
							}
						}
					}
				}
			}
			$success = $this->_db->update($table, $fields, $values, $this->_wherePk);
		} else {
			$fields = array();
			$values = array();
			$attributes = $this->getAttributes();
			foreach($attributes as $field){
				if($field!='id'){
					if(isset($at[$field])){
						if($this->$field==null||$this->$field===""){
							$this->$field = $this->_db->getCurrentDate();
						}
					}
					if(isset($in[$field])){
						$this->$field = new DbRawValue('NULL');
					}
					$fields[] = $field;
					if(is_object($this->$field)){
						if($this->$field instanceof DbRawValue){
							$values[] = $this->$field->getValue();
						} else {
							throw new ActiveRecordException('El objeto instancia de "'.get_class($this->$field).'" en el campo "'.$field.'" es muy complejo, debe realizarle un "cast" a un tipo de dato escalar antes de almacenarlo');
						}
					} else {
						if($this->isANumericType($field)==true||$this->$field=='NULL'){
							if($this->$field===''||$this->$field===null){
								$values[] = 'NULL';
							} else {
								$values[] = addslashes($this->$field);
							}
						} else {
							if($dataType[$field]=='date'){
								if($this->$field===null||$this->$field===''){
									$values[] = 'NULL';
								} else {
									$values[] = $this->_db->getDateUsingFormat(addslashes($this->$field));
								}
							} else {
								if($this->$field===null||$this->$field===''){
									$values[] = 'NULL';
								} else {
									if(get_magic_quotes_runtime()==true){
										$values[] = "'".$this->$field."'";
									} else {
										$values[] = "'".addslashes($this->$field)."'";
									}
								}
							}
						}
					}
				}
			}
			$sequenceName = '';
			if($generator===null){
				if(count($primaryKeys)==1){
					// Hay que buscar la columna identidad aqui!
					if(!isset($this->id)||!$this->id){
						if(method_exists($this, 'sequenceName')){
							$sequenceName = $this->sequenceName();
						}
						$identityValue = $this->_db->getRequiredSequence($this->_source, $primaryKeys[0], $sequenceName);
						if($identityValue!==false){
							$fields[] = 'id';
							$values[] = $identityValue;
						}
					} else {
						if(isset($this->id)){
							$fields[] = 'id';
							$values[] = $this->id;
						}
					}
				}
			}
			$success = $this->_db->insert($table, $values, $fields);
		}
		if($this->_db->isUnderTransaction()==false){
			if($this->_db->getHaveAutoCommit()==true){
				$this->_db->commit();
			}
		}
		if($success){
			if($exists==true){
				$this->_callEvent('afterUpdate');
			} else {
				if($generator===null){
					if(count($primaryKeys)==1){
						if($this->isANumericType($primaryKeys[0])){
							$lastId = $this->_db->lastInsertId($table, $primaryKeys[0], $sequenceName);
							$this->{$primaryKeys[0]} = $lastId;
							$this->findFirst($lastId);
						}
					}
				} else {
					//Actualiza el consecutivo para algunos generadores
					$generator->updateConsecutive($this);
				}
				$this->_callEvent('afterCreate');
			}
			$this->_callEvent('afterSave');
			return $success;
		} else {
			return false;
		}
	}

	/**
	 * Devuelve el codigo de la ultima operación realizada
	 *
	 * @return boolean
	 */
	public function getOperationMade(){
		return $this->_operationMade;
	}

	/**
	 * Indica si la ultima operación realizada fue una actualizacion
	 *
	 * @return boolean
	 */
	public function operationWasInsert(){
		return $this->_operationMade == self::OP_CREATE ? true : false;
	}

	/**
	 * Indica si la ultima operación realizada fue una actualizacion
	 *
	 * @return boolean
	 */
	public function operationWasUpdate(){
		return $this->_operationMade == self::OP_UPDATE ? true : false;
	}

	/**
	 * Permite establecer el tipo de generador de valores únicos a usar
	 *
	 * @param string $adapter
	 * @param string $column
	 * @param array $options
	 */
	public function setIdGenerator($adapter, $column, $options=array()){
		EntityManager::setEntityGenerator(get_class($this), $adapter, $column, $options);
	}

	/**
	 * Find All data in the Relational Table
	 *
	 * @access public
	 * @param string $field
	 * @param string $value
	 * @return ActiveRecordResultset
	 */
	public function findAllBy($field, $value){
		CoreType::assertString($field);
		return $this->find(array('conditions' => $field." = '$value'"));
	}

	/**
	 * Updates Data in the Relational Table
	 *
	 * @param mixed $values
	 * @return boolean
	 * @throws ActiveRecordException
	 */
	public function update($values=''){
		$this->_connect();
		$numberArguments = func_num_args();
		$values = Utils::getParams(func_get_args(), $numberArguments);
		if(is_array($values)){
			foreach($values as $key => $value){
				if(isset($this->$key)){
					$this->$key = $value;
				} else {
					throw new ActiveRecordException("No existe el Atributo '$key' en la entidad '{$this->_source}' al ejecutar la insercion");
				}
			}
			if($this->_exists()==true){
				return $this->save();
			} else {
				$this->appendMessage('', 'No se puede actualizar porque el registro no existe');
				return false;
			}
		} else {
			if($this->_exists()==true){
				return $this->save();
			} else {
				$this->appendMessage('', 'No se puede actualizar porque el registro no existe');
				return false;
			}
		}
	}

	/**
	 * Deletes data from Relational Map Table
	 *
	 * @access public
	 * @param mixed $params
	 * @return boolean
	 */
	public function delete($params=''){
		$this->_connect();
		if($this->_schema){
			$table = $this->_schema.'.'.$this->_source;
		} else {
			$table = $this->_source;
		}
		$conditions = '';
		if(is_array($params)){
			$numberArguments = func_num_args();
			$params = Utils::getParams(func_get_args(), $numberArguments);
			if(isset($params['conditions'])){
				$conditions = $params['conditions'];
			}
		} else {
			$primaryKeys = $this->_getPrimaryKeyAttributes();
			if(is_numeric($params)){
				$conditions = $primaryKeys[0]." = '".$params."'";
			} else{
				if($params){
					$conditions = $params;
				} else {
					$primaryKey = $this->{$primaryKeys[0]};
					$conditions = $primaryKeys[0]." = '".$primaryKey."'";
				}
			}
		}
		if(method_exists($this, 'beforeDelete')){
			if($this->id){
				$this->find($this->id);
			}
			if($this->beforeDelete()===false){
				return false;
			}
		} else {
			if(isset($this->beforeDelete)){
				if($this->id){
					$this->find($this->id);
				}
				$method = $this->beforeDelete;
				if($this->$method()===false){
					return false;
				}
			}
		}
		$val = $this->_db->delete($table, $conditions);
		$this->_operationMade = self::OP_DELETE;
		if($val){
			$this->_callEvent('afterDelete');
		}
		return $val;
	}

	/**
	 * Actualiza todos los atributos de la entidad
	 * $Clientes->updateAll("estado='A', fecha='2005-02-02'", "id>100");
	 * $Clientes->updateAll("estado='A', fecha='2005-02-02'", "id>100", "limit: 10");
	 *
	 * @access public
	 * @param string $values
	 * @return boolean
	 */
	public function updateAll($values){
		$this->_connect();
		$params = array();
		if($this->_schema){
			$table = $this->_schema.'.'.$this->_source;
		} else {
			$table = $this->_source;
		}
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params['conditions'])||!$params['conditions']){
			if(isset($params[1])){
				$params['conditions'] = $params[1];
			} else {
				$params['conditions'] = '';
			}
		}
		if($params['conditions']){
			$params['conditions'] = ' WHERE '.$params['conditions'];
		}
		if(!isset($params[0])){
			throw new ActiveRecordException('Debe indicar los valores a actualizar');
		}
		$sql = 'UPDATE '.$table.' SET '.$params[0].' '.$params['conditions'];
		if(isset($params['limit'])&&$params['limit']){
			$sql = $this->_limit($sql, $params["limit"]);
		}
		return $this->_db->query($sql);
	}

	/**
	 * Delete All data from Relational Map Table
	 *
	 * @access public
	 * @param string $conditions
	 * @return boolean
	 */
	public function deleteAll($conditions=''){
		CoreType::assertString($conditions);
		$this->_connect();
		if($this->_schema){
			$table = $this->_schema.'.'.$this->_source;
		} else {
			$table = $this->_source;
		}
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(isset($params['limit'])){
			$conditions = $this->_limit($params[0], $params['limit']);
		}
		if(isset($params[0])){
			return $this->_db->delete($table, $params[0]);
		} else {
			return $this->_db->delete($table);
		}
	}

	/**
	 * *********************************************************************************
	 * Metodos de Debug
	 * *********************************************************************************
	 */

	/**
	 * Imprime una version humana de los valores de los campos
	 * del modelo en una sola linea
	 *
	 * @access public
	 * @return string
	 */
	public function inspect(){
		$inspect = array();
		$fields = $this->_getAttributes();
		foreach($fields as $field){
			if(!is_array($field)){
				if(is_object($this->$field)){
					if(method_exists($this->$field, '__toString')){
						$inspect[] = $field.'='.$this->$field;
					} else {
						$inspect[] = $field.'=<'.get_class($this->$field).'>';
					}
				} else {
					$inspect[] = $field.'='.$this->$field;
				}
			}
		}
		return join(', ', $inspect);
	}

	/**
	 * Ejecuta el evento del modelo
	 *
	 * @param string $eventName
	 * @return boolean
	 */
	private function _callEvent($eventName){
		if(self::$_disableEvents==false){
			if(method_exists($this, $eventName)){
				if($this->{$eventName}()===false){
					return false;
				}
			} else {
				if(isset($this->{$eventName})){
					$method = $this->{$eventName};
					if($this->$method()===false){
						return false;
					}
				}
			}
		}
		return true;
	}

	/**
	 * *********************************************************************************
	 * Metodos de Validacion
	 * *********************************************************************************
	 */

	/**
	 * Ejecuta un validador sobre un campo de la entidad
	 *
	 * @param 	string $className
	 * @param 	string $field
	 * @param 	array $options
	 */
	private function _executeValidator($className, $field, $options){
		if(is_array($field)==false){
			$validator = new $className($this, $field, $this->$field, $options);
		} else {
			$values = array();
			foreach($field as $singleField){
				$values[] = $this->$singleField;
			}
			$validator = new $className($this, $field, $values, $options);
		}
		$validator->checkOptions();
		if($validator->validate()===false){
			foreach($validator->getMessages() as $message){
				$this->_errorMessages[] = $message;
			}
		}
	}

	/**
	 * Instancia los validadores y los ejecuta
	 *
	 * @access	public
	 * @param	string $validatorClass
	 * @param	array $options
	 * @throws	ActiveRecordException
	 */
	protected function validate($validatorClass, $options){
		if(!interface_exists('ActiveRecordValidatorInterface')){
			require 'Library/Kumbia/ActiveRecord/Validator/Interface.php';
		}
		if(!class_exists('ActiveRecordValidator', false)){
			require 'Library/Kumbia/ActiveRecord/Validator/ActiveRecordValidator.php';
		}
		$className = $validatorClass.'Validator';
		if(!class_exists($className, false)){
			if(Core::fileExists('Library/Kumbia/ActiveRecord/Validators/'.$className.'.php')){
				require 'Library/Kumbia/ActiveRecord/Validators/'.$className.'.php';
			} else {
				$application = Router::getApplication();
				if(Core::fileExists('apps/'.$application.'/validators/'.$className.'.php')){
					require 'apps/'.$application.'/validators/'.$className.'.php';
				}
			}
		}
		if(class_exists($className)==false){
			throw new ActiveRecordException("No se encontró el validador de entidades '$className'");
		}
		if(!in_array('ActiveRecordValidatorInterface', class_implements($className))){
			throw new ActiveRecordException("La clase validador '$className' debe implementar la interface ActiveRecordValidatorInteface");
		}
		if(is_array($options)){
			if(!isset($options['field'])){
				throw new ActiveRecordException("No ha indicado el campo a validar para '$className'");
			} else {
				$field = $options['field'];
			}
		} else {
			if($options==''){
				throw new ActiveRecordException("No ha indicado el campo a validar para '$className'");
			} else {
				$field = $options;
			}
		}
		if(!is_array($field)){
			#if(!isset($this->$field)){
			#	throw new ActiveRecordException("No se puede validar el campo '$field' por que no esta presente en la entidad");
			#}
			$this->_executeValidator($className, $field, $options);
		} else {
			foreach($field as $singleField){
				if(!isset($this->$singleField)){
					throw new ActiveRecordException("No se puede validar el campo '$singleField' por que no esta presente en la entidad");
				}
			}
			$this->_executeValidator($className, $field, $options);
		}
	}

	/**
	 * Permite saber si el proceso de validacion ha generado mensajes
	 *
	 * @return boolean
	 */
	public function validationHasFailed(){
		if(count($this->_errorMessages)>0){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Verifica si un campo es de tipo de dato numerico o no
	 *
	 * @param string $field
	 * @return boolean
	 */
	public function isANumericType($field){
		$dataType = $this->getDataTypes();
		if(
		strpos($dataType[$field], 'int')!==false||
		strpos($dataType[$field], 'decimal')!==false||
		strpos($dataType[$field], 'number')!==false
		){
			return true;
		} else {
			return false;
		}
	}

	/*******************************************************************************************
	 * Metodos para generacion de relaciones
	 *******************************************************************************************/

	/**
	 * Crea una relacion 1-1 entre dos modelos
	 *
	 * @access protected
	 * @param string $relation
	 */
	protected function hasOne($fields='', $referenceTable='', $referencedFields=''){
		EntityManager::addHasOne(get_class($this), $fields, $referenceTable, $referencedFields);
	}

	/**
	 * Crea una relacion 1-1 inversa entre dos entidades
	 *
	 * @param mixed $fields
	 * @param string $referenceTable
	 * @param string $referencedFields
	 * @param string $relationName
	 */
	protected function belongsTo($fields='', $referenceTable='', $referencedFields='', $relationName=''){
		EntityManager::addBelongsTo(get_class($this), $fields, $referenceTable, $referencedFields, $relationName);
	}

	/**
	 * Crea una relacion 1-n entre dos entidades
	 *
	 * @param mixed $fields
	 * @param string $referenceTable
	 * @param string $referencedFields
	 */
	protected function hasMany($fields='', $referenceTable='', $referencedFields=''){
		EntityManager::addHasMany(get_class($this), $fields, $referenceTable, $referencedFields);
	}

	/**
	 * Crea una relacion n-m entre dos modelos
	 *
	 * @param string $relation
	 */
	protected function hasAndBelongsToMany($relation){
		/*$relations =  func_get_args();
		 foreach($relations as $relation){
			if(!in_array($relation, $this->_hasAndBelongsToMany)){
			$this->_hasAndBelongsToMany[] = $relation;
			}
			}*/
	}

	/**
	 * Agrega una llave primaria
	 *
	 * @param	mixed $fields
	 * @param	string $referencedTable
	 * @param	mixed $referencedFields
	 * @param	array $options
	 */
	protected function addForeignKey($fields, $referencedTable='', $referencedFields='', $options=array()){
		EntityManager::addForeignKey(get_class($this), $fields, $referencedTable, $referencedFields, $options);
	}

	/**
	 * Establece que un campo no debe ser persistido
	 *
	 * @param	string $attribute
	 */
	public function setTrasient($attribute){
		EntityManager::addTrasientAttribute(get_class($this), $attribute);
	}

	/**
	 * Forza a que la entidad existe y evita su comprobación
	 *
	 * @param bool $forceExists
	 */
	public function setForceExists($forceExists){
		$this->_forceExists = $forceExists;
	}

	/**
	 * Herencia Simple
	 */

	/**
	 * Especifica que la clase es padre de otra
	 *
	 * @param string $parent
	 */
	public function parentOf($parent){
		/*$parents = func_get_args();
		 foreach($parents as $parent){
			if(!in_array($parent, $this->_parentOf)){
			$this->_parentOf[] = $parent;
			}
			}*/
	}

	/**
	 * Reescribiendo este metodo se puede controlar las excepciones generadas en los modelos
	 *
	 * @param Exception $e
	 * @throws Exception
	 */
	public function exceptions($e){
		throw $e;
	}

	/**
	 * Implementacion de __toString Standard
	 *
	 * @return string
	 */
	public function __toString(){
		return '<'.get_class().' Object>';
	}

	/**
	 * Indica si los eventos de validacion estan activos o no
	 *
	 * @param boolean $disableEvents
	 */
	public static function disableEvents($disableEvents){
		self::$_disableEvents = $disableEvents;
	}

	/**
	 * Valida que los valores que sean leidos del objeto ActiveRecord esten definidos
	 * previamente o sean atributos de la entidad
	 *
	 * @access public
	 * @param string $property
	 * @throws ActiveRecordException
	 */
	public function __get($property){
		$this->_connect();
		if($this->_dumpLock==false){
			if(!isset($this->$property)){
				throw new ActiveRecordException("Propiedad indefinida '$property' leida de el modelo '$this->_source'");
			} else {
				try {
					$reflectorProperty = new ReflectionProperty(get_class($this), $property);
					if($reflectorProperty->isPublic()==false){
						throw new ActiveRecordException("Propiedad protegida ó privada '$property' leida de el modelo '$this->_source' ");
					}
				}
				catch(Exception $e){
					if($e instanceof ActiveRecordException){
						throw $e;
					}
				}
			}
		}
		return null;
	}

	/**
	 * Valida que los valores que sean asignados al objeto ActiveRecord esten definidos
	 * o sean atributos de la entidad
	 *
	 * @param string $property
	 * @param mixed $value
	 * @throws ActiveRecordException
	 */
	public function __set($property, $value){
		$this->_connect();
		if($this->_dumpLock==false){
			if(isset($this->$property)==false){
				throw new ActiveRecordException("La propiedad '$property' no existe en la entidad '".get_class($this)."'");
			}
		}
		$this->$property = $value;
	}

	/**
	 * Valida los llamados a los metodos del modelo cuando se llame un metodo que no exista
	 *
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 * @throws ActiveRecordException
	 */
	public function __call($method, $args = array()){
		$this->_connect();
		if(substr($method, 0, 6)=='findBy'){
			$field = Utils::uncamelize(Utils::lcfirst(substr($method, 6)));
			if (isset($args[0])) {
				$arg = array("conditions: $field = {$this->_db->addQuotes($args[0]) }");
				unset($args[0]);
			} else {
				$arg = array();
			}
			return call_user_func_array(array($this, 'findFirst'), array_merge($arg, $args));
		}
		if(substr($method, 0, 7)=='countBy'){
			$field = Utils::uncamelize(Utils::lcfirst(substr($method, 7)));
			if (isset($args[0])) {
				$arg = array("$field = {$this->_db->addQuotes($args[0]) }");
				unset($args[0]);
			} else {
				$arg = array();
			}
			return call_user_func_array(array($this, 'count'), array_merge($arg, $args));
		}
		if(substr($method, 0, 9)=='findAllBy'){
			$field = Utils::uncamelize(Utils::lcfirst(substr($method, 9)));
			if (isset($args[0])) {
				$arg = array("$field = {$this->_db->addQuotes($args[0]) }");
				unset($args[0]);
			} else {
				$arg = array();
			}
			return call_user_func_array(array($this, 'find'), array_merge($arg, $args));
		}

		$entityName = get_class($this);
		if(substr($method, 0, 3)=='get'){
			$requestedRelation = preg_replace('/^get/', '', $method);
			$requestedRelation = ucfirst($requestedRelation);
			if(EntityManager::existsBelongsTo($entityName, $requestedRelation)==true){
				$arguments = array($entityName, $requestedRelation, $this);
				return call_user_func_array(array('EntityManager', 'getBelongsToRecords'), array_merge($arguments, $args));
			}
			if(EntityManager::existsHasMany($entityName, $requestedRelation)==true){
				$arguments = array($entityName, $requestedRelation, $this);
				return call_user_func_array(array('EntityManager', 'getHasManyRecords'), array_merge($arguments, $args));
			}
			if(EntityManager::existsHasOne($entityName, $requestedRelation)==true){
				return EntityManager::getHasOneRecords($entityName, $requestedRelation, $this);
			}
		}
		throw new ActiveRecordException("No se encontró el método '$method' en el modelo '".get_class($this)."'");

		/*
		 if(array_key_exists($mmodel, $this->_hasAndBelongsToMany)) {
			$hasRelation = true;
			$relation = $this->_hasAndBelongsToMany[$mmodel];
			$models = EntityManager::getModels();
			if($models[$relation->model]) {
			if($this->{$this->primary_key[0]}) {
			$source = $this->_source;
			$relation_model = $models[$relation->model];
			$relation_model->dumpModel();
			$relation_source = $relation_model->getSource();
			/**
			* Cargo atraves de que tabla se efectuara la relacion
			*
			if (!isset($relation->through)) {
			if ($source > $relation_source) {
			$relation->through = "{$this->_source}_{$relation_source}";
			} else {
			$relation->through = "{$relation_source}_{$this->_source}";
			}
			}
			$sql = "SELECT $relation_source.* FROM $relation_source, {$relation->through}, $source
			WHERE {$relation->through}.{$relation->key} = {$this->_db->addQuotes($this->{$this->primary_key[0]}) }
			AND {$relation->through}.{$relation->fk} = $relation_source.{$relation_model->primary_key[0]}
			AND {$relation->through}.{$relation->key} = $source.{$this->primary_key[0]}
			ORDER BY $relation_source.{$relation_model->primary_key[0]}";
			return $models[$relation->model]->findAllBySql($sql);
			} else {
			return array();
			}
			}
			}*/
	}

	/**
	 * Sleep de ActiveRecordBase
	 *
	 * @access public
	 * @return array
	 */
	public function __sleep(){
		return array('_schema', '_source', '_dependencyPointer', '_dumped');
	}
	
	
	/**
	 * Convierte todas las entidades html a sus simbolos originales y elimina los slashes que contenga el texto
	 * @param string $texto
	 * @return string
	 */
	public function adecuarTexto($texto){
		return html_entity_decode(stripslashes($texto));
	}

}
