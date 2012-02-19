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
 * @subpackage	ActiveRecordMetaData
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (C) 2008-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright 	Copyright (C) 2007-2008 Emilio Rafael Silveira Tovar (emilio.rst@gmail.com)
 * @license		New BSD License
 * @version 	$Id: ActiveRecordMetaData.php 103 2009-10-09 01:30:42Z gutierrezandresfelipe $
 */

/**
 * ActiveRecordMetaData
 *
 * Gran parte de la ciencia en la implementación de ActiveRecord esta
 * relacionada con la administración de los metadatos de las tablas
 * mapeadas.
 *
 * El almacenamiento de sus características es punto fundamental para
 * la utilización de los métodos que consultan, borran, modifican,
 * almacenan, etc.
 *
 * El subcomponente ActiveRecordMetadata implementa el patrón
 * Metadata Mapping el cual permite crear un data map por schema sobre
 * la información de las tablas y así reducir el consumo de memoria
 * por objeto ActiveRecord y consolidar una base de datos in-memory
 * de las características de cada entidad utilizada en la aplicación.
 *
 * @category	Kumbia
 * @package		ActiveRecord
 * @subpackage	ActiveRecordMetaData
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (C) 2008-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @access		public
 */
abstract class ActiveRecordMetaData {

	/**
	 * Persistance Models Meta-data
	 *
	 * @var array
	 * @staticvar
	 */
	static private $_models;

	/**
	 * Meta-Data de los nombres de los campos de los modelos
	 *
	 * @var array
	 * @staticvar
	 */
	static private $_modelsAttributes = array();

	/**
	 * Meta-Data de los nombres de los campos que son llave primaria de los modelos
	 *
	 * @var array
	 * @staticvar
	 */
	static private $_modelsPrimaryKeys = array();

	/**
	 * Meta-Data de los nombres de los campos que no son llave primaria de los modelos
	 *
	 * @var array
	 * @staticvar
	 */
	static private $_modelsNonPrimaryKeys = array();

	/**
	 * Meta-Data de los nombres de los campos que no pueden ser nulos de los modelos
	 *
	 * @var array
	 * @staticvar
	 */
	static private $_modelsNotNull = array();

	/**
	 * Meta-Data de los nombres de los campos que no pueden ser nulos de los modelos
	 *
	 * @var array
	 * @staticvar
	 */
	static private $_modelsDataType = array();

	/**
	 * Meta-Data de los nombres de los campos que no pueden ser nulos de los modelos
	 *
	 * @var array
	 * @staticvar
	 */
	static private $_modelsDatesAt = array();

	/**
	 * Meta-Data de los nombres de los campos que no pueden ser nulos de los modelos
	 *
	 * @var array
	 * @staticvar
	 */
	static private $_modelsDatesIn = array();

	/**
	 * Escribe en la ruta de sesión asignada al modelo
	 *
	 * @access private
	 * @param string $schema
	 * @param string $table
	 * @param string $index
	 * @param mixed $value
	 * @static
	 */
	private static function _sessionWrite($table, $schema, $index, $value){
		$activeApp = Router::getActiveApplication();
		$instanceName = Core::getInstanceName();
		if(!isset($_SESSION['KMD'])){
			$_SESSION['KMD'] = array();
		}
		if(!isset($_SESSION['KMD'][$instanceName])){
			$_SESSION['KMD'][$instanceName] = array();
		}
		if(!isset($_SESSION['KMD'][$instanceName][$activeApp])){
			$_SESSION['KMD'][$instanceName][$activeApp] = array();
		}
		if(!isset($_SESSION['KMD'][$instanceName][$activeApp][$schema])){
			$_SESSION['KMD'][$instanceName][$activeApp][$schema] = array();
		}
		if(!isset($_SESSION['KMD'][$instanceName][$activeApp][$schema][$table])){
			$_SESSION['KMD'][$instanceName][$activeApp][$schema][$table] = array();
		}
		$_SESSION['KMD'][$instanceName][$activeApp][$schema][$table][$index] = $value;
	}

	/**
	 * Indica si se ha definido un valor de meta-datos de la tabla
	 *
	 * @access private
	 * @param string $table
	 * @param string $schema
	 * @param string $index
	 * @return mixed
	 * @static
	 */
	private static function _sessionIsSet($table, $schema, $index){
		$activeApp = Router::getActiveApplication();
		$instanceName = Core::getInstanceName();
		return isset($_SESSION['KMD'][$instanceName][$activeApp][$schema][$table][$index]);
	}

	/**
	 * Lee un valor de meta-datos de la tabla
	 *
	 * @access private
	 * @param string $table
	 * @param string $index
	 * @return mixed
	 * @static
	 */
	private static function _sessionRead($table, $schema, $index){
		$activeApp = Router::getActiveApplication();
		$instanceName = Core::getInstanceName();
		return $_SESSION['KMD'][$instanceName][$activeApp][$schema][$table][$index];
	}

	/**
	 * Crea un registro de meta datos para la tabla especificada
	 *
	 * @param string $table
	 * @param string $schema
	 * @param array $metaData
	 */
	static public function existsMetaData($table, $schema){
		if(isset(self::$_models[$schema][$table])){
			return true;
		} else {
			if(self::_sessionIsSet($table, $schema, 'dp')){
				self::$_modelsAttributes[$schema][$table] = self::_sessionRead($table, $schema, 'at');
				self::$_modelsPrimaryKeys[$schema][$table] = self::_sessionRead($table, $schema, 'pk');
				self::$_modelsNonPrimaryKeys[$schema][$table] = self::_sessionRead($table, $schema, 'npk');
				self::$_modelsNotNull[$schema][$table] = self::_sessionRead($table, $schema, 'nn');
				self::$_modelsDataType[$schema][$table] = self::_sessionRead($table, $schema, 'dt');
				self::$_modelsDatesAt[$schema][$table] = self::_sessionRead($table, $schema, 'da');
				self::$_modelsDatesIn[$schema][$table] = self::_sessionRead($table, $schema, 'di');
				self::_sessionWrite($table, $schema, 'dp', Core::getProximityTime());
				if(!isset($_models[$schema])){
					self::$_models[$schema] = array();
				}
				self::$_models[$schema][$table] = true;
				return true;
			}
		}
		return false;
	}

	/**
	 * Crea los meta-datos del modelo indicado
	 *
	 * @param string $table
	 * @param string $schema
	 * @static
	 */
	static public function createMetaData($table, $schema){
		self::_sessionWrite($table, $schema, 'dp', Core::getProximityTime());
		if(!isset($_models[$schema])){
			self::$_models[$schema] = array();
		}
		self::$_models[$schema][$table] = true;
	}

	/**
	 * Permite definir los atributos de un modelo en forma de memoria compartida
	 *
	 * @param string $tableName
	 * @param string $schemaName
	 * @param array $attributes
	 * @static
	 */
	static public function setAttributes($tableName, $schemaName, $attributes){
		self::_sessionWrite($tableName, $schemaName, 'at', $attributes);
		if(!isset(self::$_modelsAttributes[$schemaName])){
			self::$_modelsAttributes[$schemaName] = array();
		}
		self::$_modelsAttributes[$schemaName][$tableName] = $attributes;
	}

	/**
	 * Obtiene los atributos de un modelo en forma de memoria compartida
	 *
	 * @param string $tableName
	 * @param string $schemaName
	 * @return array
	 * @static
	 */
	static public function getAttributes($tableName, $schemaName){
		if(!isset(self::$_modelsAttributes[$schemaName][$tableName])){
			if(self::_sessionIsSet($tableName, $schemaName, 'at')){
				self::$_modelsAttributes[$schemaName][$tableName] = self::_sessionRead($tableName, $schemaName, 'at');
				return self::$_modelsAttributes[$schemaName][$tableName];
			} else {
				return array();
			}
		} else {
			return self::$_modelsAttributes[$schemaName][$tableName];
		}
	}

	/**
	 * Permite definir los atributos que son llave primaria de un modelo en forma de memoria compartida
	 *
	 * @param string $tableName
	 * @param string $schemaName
	 * @param array $primaryKey
	 * @return array
	 * @static
	 */
	static public function setPrimaryKeys($tableName, $schemaName, $primaryKey){
		self::_sessionWrite($tableName, $schemaName, 'pk', $primaryKey);
		if(!isset(self::$_modelsPrimaryKeys[$schemaName])){
			self::$_modelsPrimaryKeys[$schemaName] = array();
		}
		self::$_modelsPrimaryKeys[$schemaName][$tableName] = $primaryKey;
	}

	/**
	 * Obtiene los atributos de un modelo que son llave primaria en forma de memoria compartida
	 *
	 * @param string $tableName
	 * @param string $schemaName
	 * @return array
	 */
	static public function getPrimaryKeys($tableName, $schemaName){
		if(!isset(self::$_modelsPrimaryKeys[$schemaName])){
			return array();
		}
		return self::$_modelsPrimaryKeys[$schemaName][$tableName];
	}

	/**
	 * Permite definir los atributos que no son llave primaria de un modelo en forma de memoria compartida
	 *
	 * @param string $tableName
	 * @param string $schemaName
	 * @param array $nonPrimaryKey
	 */
	static public function setNonPrimaryKeys($tableName, $schemaName, $nonPrimaryKey){
		self::_sessionWrite($tableName, $schemaName, 'npk', $nonPrimaryKey);
		if(!isset(self::$_modelsNonPrimaryKeys[$schemaName])){
			self::$_modelsNonPrimaryKeys[$schemaName] = array();
		}
		self::$_modelsNonPrimaryKeys[$schemaName][$tableName] = $nonPrimaryKey;
	}

	/**
	 * Obtiene los atributos de un modelo que no son llave primaria en forma de memoria compartida
	 *
	 * @param string $tableName
	 * @param string $schemaName
	 * @return array
	 */
	static public function getNonPrimaryKeys($tableName, $schemaName){
		if(!isset(self::$_modelsNonPrimaryKeys[$schemaName][$tableName])){
			if(self::_sessionIsSet($tableName, $schemaName, 'npk')){
				self::$_modelsNonPrimaryKeys[$schemaName][$tableName] = self::_sessionRead($tableName, $schemaName, 'npk');
				return self::$_modelsNonPrimaryKeys[$schemaName][$tableName];
			} else {
				return array();
			}
		} else {
			return self::$_modelsNonPrimaryKeys[$schemaName][$tableName];
		}
	}

	/**
	 * Permite definir los atributos que no permiten nulos de un modelo en forma de memoria compartida
	 *
	 * @param string $tableName
	 * @param string $schemaName
	 * @param array $NotNull
	 */
	static public function setNotNull($tableName, $schemaName, $NotNull){
		self::_sessionWrite($tableName, $schemaName, 'nn', $NotNull);
		if(!isset(self::$_modelsNotNull[$schemaName])){
			self::$_modelsNotNull[$schemaName] = array();
		}
		self::$_modelsNotNull[$tableName] = $NotNull;
	}

	/**
	 * Obtiene los atributos de un modelo que no permiten nulos en forma de memoria compartida
	 *
	 * @param string $tableName
	 * @param string $schemaName
	 * @return array
	 */
	static public function getNotNull($tableName, $schemaName){
		if(!isset(self::$_modelsNotNull[$schemaName][$tableName])){
			if(self::_sessionIsSet($tableName, $schemaName, 'nn')){
				self::$_modelsNotNull[$schemaName][$tableName] = self::_sessionRead($tableName, $schemaName, 'nn');
				return self::$_modelsNotNull[$schemaName][$tableName];
			} else {
				return array();
			}
		} else {
			return self::$_modelsNotNull[$schemaName][$tableName];
		}
	}

	/**
	 * Permite definir los tipos de datos de atributos de un modelo en forma de memoria compartida
	 *
	 * @param string $tableName
	 * @param string $schemaName
	 * @param array $DataType
	 * @static
	 */
	static public function setDataType($tableName, $schemaName, $DataType){
		self::_sessionWrite($tableName, $schemaName, 'dt', $DataType);
		self::$_modelsDataType[$schemaName][$tableName] = $DataType;
	}

	/**
	 * Obtiene los tipos de datos de atributos en forma de memoria compartida
	 *
	 * @param string $tableName
	 * @param string $schemaName
	 * @return array
	 */
	static public function getDataTypes($tableName, $schemaName){
		if(isset(self::$_modelsDataType[$schemaName][$tableName])){
			return self::$_modelsDataType[$schemaName][$tableName];
		} else {
			return array();
		}
	}

	/**
	 * Permite definir los tipos de datos de atributos de un modelo en forma de memoria compartida
	 *
	 * @param string $tableName
	 * @param string $schemaName
	 * @param array $DatesAt
	 * @static
	 */
	static public function setDatesAt($tableName, $schemaName, $DatesAt){
		self::_sessionWrite($tableName, $schemaName, 'da', $DatesAt);
		self::$_modelsDatesAt[$schemaName][$tableName] = $DatesAt;
	}

	/**
	 * Obtiene los tipos de datos de atributos en forma de memoria compartida
	 *
	 * @param string $tableName
	 * @param string $schemaName
	 * @return array
	 * @static
	 */
	static public function getDatesAt($tableName, $schemaName){
		if(isset(self::$_modelsDatesAt[$schemaName][$tableName])){
			return self::$_modelsDatesAt[$schemaName][$tableName];
		} else {
			return array();
		}
	}

	/**
	 * Permite definir los campos con fecha de atributo _at
	 *
	 * @param string $tableName
	 * @param string $schemaName
	 * @param array $DatesIn
	 * @static
	 */
	static public function setDatesIn($tableName, $schemaName, $DatesIn){
		self::_sessionWrite($tableName, $schemaName, 'di', $DatesIn);
		self::$_modelsDatesIn[$schemaName][$tableName] = $DatesIn;
	}

	/**
	 * Obtiene los campos con fecha de atributo _in
	 *
	 * @param string $tableName
	 * @param string $schemaName
	 * @return array
	 * @static
	 */
	static public function getDatesIn($tableName, $schemaName){
		if(isset(self::$_modelsDatesIn[$schemaName][$tableName])){
			return self::$_modelsDatesIn[$schemaName][$tableName];
		} else {
			return array();
		}
	}

	/**
	 * Trae los metadatos de la base de tatos
	 *
	 * @param string $table
	 * @param string $schema
	 * @param array $metaData
	 * @static
	 */
	static public function dumpMetaData($table, $schema, $metaData){
		$fields = array();
		$primaryKey = array();
		$nonPrimary = array();
		$notNull = array();
		$_dataType = array();
		$_at = array();
		$_in = array();
		foreach($metaData as $field){
			$fields[] = $field['Field'];
			if($field['Key']=='PRI'){
				$primaryKey[] = $field['Field'];
			} else {
				$nonPrimary[] = $field['Field'];
			}
			if($field['Null']=='NO'){
				$notNull[] = $field['Field'];
			}
			if($field['Type']){
				$_dataType[$field['Field']] = strtolower($field['Type']);
			}
			if(preg_match('/_at$/', $field['Field'])){
				$_at[$field['Field']] = 1;
			} else {
				if(preg_match('/_in$/', $field['Field'])){
					$_in[$field['Field']] = 1;
				}
			}
		}
		if(count($fields)==0){
			if($schema){
				$table = $schema.'\'.\''.$table;
			}
			throw new ActiveRecordMetaDataException("Meta-datos invalidos para '$table'");
		}
		ActiveRecordMetaData::createMetaData($table, $schema);
		ActiveRecordMetaData::setAttributes($table, $schema, $fields);
		ActiveRecordMetaData::setPrimaryKeys($table, $schema,  $primaryKey);
		ActiveRecordMetaData::setNonPrimaryKeys($table, $schema, $nonPrimary);
		ActiveRecordMetaData::setNotNull($table, $schema, $notNull);
		ActiveRecordMetaData::setDataType($table, $schema, $_dataType);
		ActiveRecordMetaData::setDatesAt($table, $schema, $_at);
		ActiveRecordMetaData::setDatesIn($table, $schema, $_in);
	}

}
