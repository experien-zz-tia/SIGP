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
 * @package		EntityManager
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2008-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @version 	$Id: EntityManager.php 103 2009-10-09 01:30:42Z gutierrezandresfelipe $
 */

/**
 * EntityManager
 *
 * El componente EntityManager es usado internamente por el framework y
 * principalmente por ActiveRecord y Controller para administrar las
 * entidades, sus relaciones de asociación, relaciones de integridad y
 * generadores de tal forma que el acceso a ellas sea consistente y uniforme.
 *
 * @category	Kumbia
 * @package		EntityManager
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2008-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @abstract
 */
abstract class EntityManager {

	/**
	 * Indica si todos los modelos son inicializados en cada peticion
	 *
	 * @var boolean
	 */
	private static $_autoInitialize = false;

	/**
	 * Directorio de modelos de la Aplicacion
	 *
	 * @var string
	 */
	private static $_modelsDir = false;

	/**
	 * Modelos Administrados
	 *
	 * @var array
	 */
	private static $_entities = array();

	/**
	 * Tablas de los Modelos Administrados
	 *
	 * @var array
	 */
	private static $_sources = array();

	/**
	 * Modelos Temporales Administrados
	 *
	 * @var array
	 */
	private static $_temporaryEntities = array();

	/**
	 * Tipos de datos de los campos del modelo
	 *
	 * @var array
	 */
	private static $_dataType = array();

	/**
	 * Relaciones a las cuales tiene una cardinalidad *-1
	 *
	 * @var array
	 */
	private static $_hasOne = array();

	/**
	 * Relaciones a las cuales tiene una cardinalidad 1-1
	 *
	 * @staticvar
	 * @var array
	 */
	private static $_hasMany = array();

	/**
	 * Relaciones a las cueles tiene una cardinalidad *-1
	 *
	 * @staticvar
	 * @var array
	 */
	private static $_belongsTo = array();

	/**
	 * Relaciones a las cuales tiene una cardinalidad n-n (muchos a muchos)
	 *
	 * @staticvar
	 * @var array
	 */
	private static $_hasAndBelongsToMany = array();

	/**
	 * Clases de las cuales es padre la clase actual
	 *
	 * @staticvar
	 * @var array
	 */
	private static $_parentOf = array();

	/**
	 * Campos que no deben ser persistidos
	 *
	 * @var array
	 * @staticvar
	 */
	static private $_trasient = array();

	/**
	 * Generadores de las entidades
	 *
	 * @var array
	 */
	static private $_generators = array();

	/**
	 * Registro de la existencia de entidades temporal por conexion
	 *
	 * @var array
	 * @staticvar
	 */
	static private $_connectionManager = array();

	/**
	 * Llaves foraneas virtuales en las entidades
	 *
	 * @var array
	 */
	static private $_foreignKeys = array();

	/**
	 * Devuelve los modelos administrados por el EntityManager
	 *
	 * @access public
	 * @return array
	 * @static
	 */
	public static function getEntities(){
		return self::$_entities;
	}

	/**
	 * Establece si los modelos se autoinicializaron al iniciar la peticion
	 *
	 * @access 	public
	 * @param 	boolean $autoInitialize
	 * @static
	 */
	public static function setAutoInitialize($autoInitialize){
		self::$_autoInitialize = $autoInitialize;
	}

	/**
	 * Devuevle si los modelos se autoinicializaron al iniciar la peticion
	 *
	 * @access 	public
	 * @return 	boolean
	 * @static
	 */
	public static function getAutoInitialize(){
		return self::$_autoInitialize;
	}

	/**
	 * Establece el directorio de modelos
	 *
	 * @param string $modelsDir
	 */
	public static function setModelsDirectory($modelsDir){
		self::$_modelsDir = $modelsDir;
	}

	/**
	 * Devuelve la instancia de un modelo
	 *
	 * @param 	string $entityName
	 * @param 	boolean $newInstance
	 * @return 	ActiveRecordBase
	 * @throws  EntityManagerException
	 */
	public static function getEntityInstance($entityName, $newInstance=true){
		if(self::$_autoInitialize==true){
			if(is_object(self::$_entities[$entityName])){
				if($newInstance==true){
					return clone self::$_entities[$entityName];
				} else {
					return self::$_entities[$entityName];
				}
			} else {
				throw new EntityManagerException("No existe la entidad '$entityName'");
			}
		} else {
			if(isset(self::$_entities[$entityName])){
				if($newInstance==true){
					$instance = clone self::$_entities[$entityName];
					return $instance;
				} else {
					return self::$_entities[$entityName];
				}
			} else {
				$model = Utils::uncamelize($entityName);
				if(Core::fileExists(self::$_modelsDir."/$model.php")){
					require self::$_modelsDir."/$model.php";
					self::_initializeModel($entityName, $model);
					if($newInstance==true){
						return clone self::$_entities[$entityName];
					} else {
						return self::$_entities[$entityName];
					}
				} else {
					throw new EntityManagerException("No existe la entidad '$entityName' ($model)");
				}
			}
		}
	}

	/**
	 * Inicializa un modelo
	 *
	 * @param string $entityName
	 * @param string $model
	 */
	private static function _initializeModel($entityName, $model){
		if(class_exists($entityName, false)==false){
			throw new EntityManagerException("No se encontró la clase \"$entityName\", es necesario definir una clase en el modelo '$model' llamado '$entityName' para que esto funcione correctamente.");
		} else {
			self::$_entities[$entityName] = new $entityName();
			if(!is_subclass_of(self::$_entities[$entityName], 'ActiveRecordBase')){
				throw new EntityManagerException("Error inicializando modelo '$entityName', el modelo '$model' debe heredar ActiveRecord");
			}
			$sourceName = self::$_entities[$entityName]->getSource();
			if($sourceName==""){
				self::$_entities[$entityName]->setSource($model);
				self::$_sources[$entityName] = $model;
			} else {
				self::$_sources[$entityName] = $sourceName;
			}
		}
	}

	/**
	 * Inicializa el modelo ActiveRecord base de la aplicacion
	 *
	 * @access public
	 * @param string $modelsDir
	 * @static
	 */
	public static function initModelBase($modelsDir){
		/**
		 * Inicializa los Modelos. modelBase es el modelo base
		 */
		if(class_exists('ActiveRecord', false)==false){
			if(Core::fileExists("$modelsDir/base/modelBase.php")){
				require "$modelsDir/base/modelBase.php";
			} else {
				throw new EntityManagerException("No existe el archivo de modelo Base ($modelsDir/base/modelBase.php)");
			}
		}
	}

	/**
	 * Inicializa los modelos en el directorio models de forma recursiva
	 *
	 * @access 	public
	 * @param 	string $modelsDir
	 * @static
	 */
	public static function initModels($modelsDir){
		foreach(scandir($modelsDir) as $model){
			if(!in_array($model, array('.', '..', 'base'))){
				if(is_dir($modelsDir."/".$model)){
					self::initModels($modelsDir."/".$model);
				}
			}
			if(preg_match('/([a-zA-Z_0-9]+)\.php$/', $model, $matches)==true){
				require "$modelsDir/$model";
				$objectModel = Utils::camelize($matches[1]);
				self::_initializeModel($objectModel, $matches[1]);
			}
		}
	}

	/**
	 * Verifica si $entityName es una entidad en la aplicacion
	 *
	 * @access 	public
	 * @param 	string $entityName
	 * @return 	boolean
	 * @static
	 */
	static public function isEntity($entityName){
		return self::isModel($entityName);
	}

	/**
	 * Verifica si $model es un modelo de la Aplicacion
	 *
	 * @access 	public
	 * @param 	string $modelName
	 * @return 	boolean
	 * @static
	 */
	static public function isModel($modelName){
		if($modelName==''){
			return false;
		}
		if(self::$_autoInitialize==true){
			return isset(self::$_entities[self::getEntityName($modelName)]);
		} else {
			if(isset(self::$_entities[self::getEntityName($modelName)])==true){
				return true;
			} else {
				$model = Utils::uncamelize($modelName);
				if(Core::fileExists(self::$_modelsDir.'/'.$model.'.php')){
					require self::$_modelsDir.'/'.$model.'.php';
					self::_initializeModel($modelName, $model);
					return true;
				} else {
					return false;
				}
			}
		}
	}

	/**
	 * Devuelve el nombre de modelo de la entidad $model
	 *
	 * @access 	public
	 * @param 	string $model
	 * @return 	string
	 * @static
	 */
	static public function getEntityName($model){
		if($model==''){
			return false;
		}
		return Utils::camelize($model);
	}

	/**
	 * Indica si hay una relacion tipo belongsTo en la entidad solicitada
	 *
	 * @access 	public
	 * @param 	string $entityName
	 * @param 	string $relationRequested
	 * @return 	boolean
	 * @static
	 */
	static public function existsBelongsTo($entityName, $relationRequested){
		return isset(self::$_belongsTo[$entityName][$relationRequested]) ? true : false;
	}

	/**
	 * Indica si hay una relacion tipo hasMany en la entidad solicitada
	 *
	 * @access 	public
	 * @param 	string $entityName
	 * @param 	string $relationRequested
	 * @return 	boolean
	 * @static
	 */
	static public function existsHasMany($entityName, $relationRequested){
		return isset(self::$_hasMany[$entityName][$relationRequested]) ? true : false;
	}

	/**
	 * Indica si hay una relacion tipo hasOne en la entidad solicitada
	 *
	 * @access 	public
	 * @param 	string $entityName
	 * @param 	string $relationRequested
	 * @return 	boolean
	 * @static
	 */
	static public function existsHasOne($entityName, $relationRequested){
		return isset(self::$_hasOne[$entityName][$relationRequested]) ? true : false;
	}

	/**
	 * Devuelve los registros de la relacion 1-1 ó n-1
	 *
	 * @access 	public
	 * @param 	string $entityName
	 * @param 	string $relationRequested
	 * @param 	ActiveRecord $record
	 * @return 	boolean
	 * @static
	 */
	static public function getBelongsToRecords($entityName, $relationRequested, $record){
		$relation = self::$_belongsTo[$entityName][$relationRequested];
		if(!is_array($relation['rf'])){
			$value = $record->readAttribute($relation['fi']);
			$condition = "{$relation['rf']} = '$value'";
		} else {
			$i = 0;
			$conditions = array();
			foreach($relation['rf'] as $referencedField){
				$value = $record->readAttribute($relation['fi'][$i]);
				$conditions[] = "{$relation['rf'][$i]} = '$value'";
				++$i;
			}
			$condition = join(" AND ", $conditions);
		}
		$arguments = func_get_args();
		$arguments = array_merge(array($condition), array_slice($arguments, 3));
		$referenceTable = ucfirst(Utils::camelize($relation['rt']));
		if(self::$_autoInitialize==true){
			if(isset(self::$_entities[$referenceTable])){
				$returnedRecord = call_user_func_array(array(self::$_entities[$referenceTable], "findFirst"), $arguments);
				return $returnedRecord;
			} else {
				throw new EntityManagerException("No existe la entidad '$referenceTable' para realizar la relación n-1");
			}
		} else {
			$entity = self::getEntityInstance($referenceTable);
			$returnedRecord = call_user_func_array(array($entity, "findFirst"), $arguments);
			return $returnedRecord;
		}
	}

	/**
	 * Devuelve los registros de la relacion 1-1
	 *
	 * @access 	public
	 * @param 	string $entityName
	 * @param 	string $relationRequested
	 * @param 	ActiveRecord $record
	 * @return 	boolean
	 * @static
	 */
	static public function getHasOneRecords($entityName, $relationRequested, $record){
		$relation = self::$_hasOne[$entityName][$relationRequested];
		if(!is_array($relation['rf'])){
			$value = $record->readAttribute($relation['fi']);
			$condition = "{$relation['rf']} = '$value'";
		} else {
			$i = 0;
			$conditions = array();
			foreach($relation['rf'] as $referencedField){
				$value = $record->readAttribute($relation['fi'][$i]);
				$conditions[] = "{$relation['rf'][$i]} = '$value'";
				++$i;
			}
			$condition = join(" AND ", $conditions);
		}
		$referenceTable = ucfirst(Utils::camelize($relation['rt']));
		if(self::$_autoInitialize==true){
			if(isset(self::$_entities[$referenceTable])){
				$returnedRecord = self::$_entities[$referenceTable]->findFirst($condition);
				return $returnedRecord;
			} else {
				throw new EntityManagerException("No existe la entidad '$referenceTable' para realizar la relación 1-1");
			}
		} else {
			$entity = self::getEntityInstance($referenceTable);
			$returnedRecord = $entity->findFirst($condition);
			return $returnedRecord;
		}
	}

	/**
	 * Devuelve los registros de la relacion n-1
	 *
	 * @access 	public
	 * @param 	string $entityName
	 * @param 	string $relationRequested
	 * @param 	ActiveRecord $record
	 * @return 	boolean
	 * @static
	 */
	static public function getHasManyRecords($entityName, $relationRequested, $record){
		$relation = self::$_hasMany[$entityName][$relationRequested];
		if(!is_array($relation['fi'])){
			$value = $record->readAttribute($relation['rf']);
			$condition = "{$relation['fi']} = '$value'";
		} else {
			$i = 0;
			$conditions = array();
			foreach($relation['fi'] as $referencedField){
				$value = $record->readAttribute($relation['rf'][$i]);
				$conditions[] = "{$relation['fi'][$i]} = '$value'";
				++$i;
			}
			$condition = join(' AND ', $conditions);
		}
		$numberArgs = func_num_args();
		if($numberArgs>3){
			$allParams = func_get_args();
			$findParams = array();
			$conditionsKey = false;
			for($i=3;$i<$numberArgs;++$i){
				$param = Utils::getParam($allParams[$i]);
				if($param['key']=='0'||$param['key']=='conditions'){
					$allParams[$i] = $condition.' AND '.$param['value'];
					$conditionsKey = true;
				}
				$findParams[] = $allParams[$i];
			}
			if($conditionsKey==false){
				$findParams[] = 'conditions: $condition';
			}
		} else {
			$findParams = array($condition);
		}
		$referenceTable = ucfirst(Utils::camelize($relation['rt']));
		if(self::$_autoInitialize==true){
			if(isset(self::$_entities[$referenceTable])){
				return call_user_func_array(array(self::$_entities[$referenceTable], "find"), $findParams);
			} else {
				throw new EntityManagerException("No existe la entidad '$referenceTable' para realizar la relación n-1");
			}
		} else {
			$referencedEntity = self::getEntityInstance($referenceTable);
			return call_user_func_array(array($referencedEntity, 'find'), $findParams);
		}
	}

	/**
	 * Agrega una nueva relacion n-1
	 *
	 * @access 	public
	 * @param 	string $entityName
	 * @param 	string $fields
	 * @param 	string $referenceTable
	 * @param 	string $referencedFields
	 * @param 	string $relationName
	 * @static
	 */
	public static function addBelongsTo($entityName, $fields='', $referenceTable='', $referencedFields='', $relationName=''){
		if(!isset(self::$_belongsTo[$entityName])){
			self::$_belongsTo[$entityName] = array();
		}
		if($relationName==''){
			if(!$referenceTable){
				if(is_array($fields)){
					$indexKey = join('', sort(array_map('ucfirst', $fields)));
				} else {
					$indexKey = Utils::camelize($fields);
				}
			} else {
				$indexKey = Utils::camelize($referenceTable);
			}
		} else {
			$indexKey = $relationName;
		}
		if(!isset(self::$_belongsTo[$entityName][$indexKey])){
			if(is_array($fields)){
				if(count($fields)>0&&$referenceTable==''){
					throw new EntityManagerException('Debe indicar la tabla referenciada en la relación belongsTo');
				}
			} else {
				if($referenceTable==''){
					$referenceTable = $fields;
					$fields = $fields.'_id';
					$referencedFields = 'id';
				}
			}
			if($referencedFields==''){
				$referencedFields = $fields;
			}
			if(is_array($referencedFields)){
				if(count($fields)!=count($referencedFields)){
					throw new EntityManagerException('El número de campos referenciados no es el mismo');
				}
			}
			self::$_belongsTo[$entityName][$indexKey] = array(
				'fi' => $fields,
				'rt' => $referenceTable,
				'rf' => $referencedFields
			);
		} else {
			return;
		}
	}

	/**
	 * Agrega una nueva relacion n-1
	 *
	 * @access 	public
	 * @param 	mixed $fields
	 * @param 	string $entityName
	 * @param 	string $referenceTable
	 * @param 	mixed $referencedFields
	 * @static
	 */
	public static function addHasMany($entityName, $fields='', $referenceTable='', $referencedFields=''){
		if(!isset(self::$_hasMany[$entityName])){
			self::$_hasMany[$entityName] = array();
		}
		if($referenceTable==''){
			if(is_array($fields)){
				$indexKey = join('', sort(array_map('ucfirst', $fields)));
			} else {
				$indexKey = ucfirst(Utils::camelize($fields));
			}
		} else {
			$indexKey = ucfirst(Utils::camelize($referenceTable));
		}
		if(!isset(self::$_hasMany[$entityName][$indexKey])){
			if(is_array($fields)){
				if(count($fields)>0&&$referenceTable==''){
					throw new EntityManagerException('Debe indicar la tabla referenciada en la relación hasMany');
				}
			} else {
				if($referenceTable==''){
					$referenceTable = $fields;
					$referencedFields = 'id';
					$fields = Utils::uncamelize(Utils::lcfirst($entityName)).'_id';
				}
			}
			if($referencedFields==''){
				$referencedFields = $fields;
			}
			if(is_array($referencedFields)){
				if(count($fields)!=count($referencedFields)){
					throw new EntityManagerException('El número de campos referenciados no es el mismo');
				}
			}
			self::$_hasMany[$entityName][$indexKey] = array(
				'fi' => $fields,
				'rt' => $referenceTable,
				'rf' => $referencedFields
			);
		} else {
			return;
		}
	}

	/**
	 * Agrega una nueva relacion 1-1
	 *
	 * @access 	public
	 * @param 	mixed $fields
	 * @param 	string $entityName
	 * @param 	string $referenceTable
	 * @param 	mixed $referencedFields
	 * @static
	 */
	public static function addHasOne($entityName, $fields='', $referenceTable='', $referencedFields=''){
		if(!isset(self::$_hasOne[$entityName])){
			self::$_hasOne[$entityName] = array();
		}
		if($referenceTable==''){
			if(is_array($fields)){
				$indexKey = join('', sort(array_map('ucfirst', $fields)));
			} else {
				$indexKey = ucfirst(Utils::camelize($fields));
			}
		} else {
			$indexKey = ucfirst(Utils::camelize($referenceTable));
		}
		if(!isset(self::$_hasOne[$entityName][$indexKey])){
			if(is_array($fields)){
				if(count($fields)>0&&$referenceTable==''){
					throw new EntityManagerException('Debe indicar la tabla referenciada en la relación hasOne');
				}
			} else {
				if($referenceTable==''){
					$referenceTable = $fields;
					$fields = 'id';
					$referencedFields = Utils::uncamelize(Utils::lcfirst($entityName)).'_id';
				}
			}
			if($referencedFields==''){
				$referencedFields = $fields;
			}
			if(is_array($referencedFields)){
				if(count($fields)!=count($referencedFields)){
					throw new EntityManagerException('El número de campos referenciados no es el mismo');
				}
			}
			self::$_hasOne[$entityName][$indexKey] = array(
				'fi' => $fields,
				'rt' => $referenceTable,
				'rf' => $referencedFields
			);
		} else {
			return;
		}
	}

	/**
	 * Establece campos tipo Trasient de las entidades
	 *
	 * @access 	public
	 * @param 	string $entityName
	 * @param 	string $attribute
	 * @static
	 */
	public static function addTrasientAttribute($entityName, $attribute){
		if(!isset(self::$_trasient[$entityName])){
			self::$_trasient[$entityName] = array();
		}
		if(!in_array($attribute, self::$_trasient[$entityName])){
			self::$_trasient[$entityName][] = $attribute;
		}
	}

	/**
	 * Devuelve las relaciones de una entidad
	 *
	 * @access	public
	 * @param 	string $entityName
	 * @return 	array
	 * @static
	 */
	public static function getRelationsOf($entityName){
		if(!isset(self::$_entities[$entityName])){
			throw new EntityManagerException("No existe la entidad '$entityName'");
		}
		$relations = array();
		if(isset(self::$_belongsTo[$entityName])){
			$relations['belongsTo'] = self::$_belongsTo[$entityName];
		}
		if(isset(self::$_hasMany[$entityName])){
			$relations['hasMany'] = self::$_hasMany[$entityName];
		}
		if(isset(self::$_hasOne[$entityName])){
			$relations['hasOne'] = self::$_hasOne[$entityName];
		}
		return $relations;
	}

	/**
	 * Indica si un modelo temporal existe
	 *
	 * @access 	public
	 * @param 	string $entityName
	 * @return 	boolean
	 * @static
	 */
	public static function existsTemporaryEntity($entityName){
		return in_array($entityName, self::$_temporaryEntities);
	}

	/**
	 * Agrega unua entidad temporal al adminitrador de entidades
	 *
	 * @access public
	 * @param string $entityName
	 * @static
	 */
	public static function addTemporaryEntity($entityName){
		if(in_array($entityName, self::$_temporaryEntities)==false){
			self::$_temporaryEntities[] = $entityName;
		}
	}

	/**
	 * Elimina una entidad temporal del administrador
	 *
	 * @access 	public
	 * @param 	string $entityName
	 * @static
	 */
	public static function destroyTemporaryEntity($entityName){
		if(isset(self::$_temporaryEntities[$entityName])==false){
			$i = 0;
			foreach(self::$_temporaryEntities as $temporaryEntity){
				if($temporaryEntity==$entityName){
					$entity = EntityManager::getEntityInstance($entityName);
					if(isset(self::$_connectionManager[$entityName])){
						foreach(self::$_connectionManager[$entityName] as $connection){
							$entity->destroy($connection);
						}
					}
					unset(self::$_temporaryEntities[$i]);
				}
				++$i;
			}
		} else {
			throw new EntityManagerException("No existe la entidad temporal '$entityName'");
		}
	}

	/**
	 * Administra las entidades temporales en cada conexion
	 *
	 * @access 	public
	 * @param 	DbBase $connection
	 * @param 	string $entityName
	 * @return	boolean
	 * @static
	 */
	public static function isCreatedTemporaryEntity($connection, $entityName){
		$connectionId = (string) $connection->getConnectionId();
		if(!isset(self::$_connectionManager[$entityName])){
			self::$_connectionManager[$entityName] = array();
		}
		if(!isset(self::$_connectionManager[$entityName][$connectionId])){
			self::$_connectionManager[$entityName][$connectionId] = $connection->getConnectionId();
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Devuelve el source de una entidad
	 *
	 * @param string $entityName
	 * @return string
	 * @static
	 */
	public static function getSourceName($entityName){
		if(isset(self::$_sources[$entityName])){
			return self::$_sources[$entityName];
		} else {
			return null;
		}
	}

	/**
	 * Agrega una entidad al administrador por su nombre de clase
	 *
	 * @access 	public
	 * @param 	string $entityClass
	 * @static
	 */
	public static function addEntityByClass($entityClass){
		$sourceName = Utils::uncamelize($entityClass);
		self::_initializeModel($entityClass, $sourceName);
	}

	/**
	 * Establece el generador y sus parametros a usar en una determinada entidad
	 *
	 * @access 	public
	 * @param 	string $entityName
	 * @param 	string $adapter
	 * @param 	string $column
	 * @param 	array $options
	 * @static
	 */
	public static function setEntityGenerator($entityName, $adapter, $column, $options){
		if(self::hasGenerator($entityName)==false){
			self::$_generators[$entityName] = array(
				'adapter' => $adapter,
				'column' => $column,
				'options' => $options,
				'generator' => null,
			);
		}
	}

	/**
	 * Indica si se ha establecido un generador especial para una entidad
	 *
	 * @access public
	 * @param  string $entityName
	 * @return boolean
	 * @static
	 */
	public static function hasGenerator($entityName){
		if(isset(self::$_generators[$entityName])){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Obtiene el generador de una entidad
	 *
	 * @param string $entityName
	 * @return ActiveRecordGenerator
	 * @static
	 */
	public static function getEntityGenerator($entityName){
		if(isset(self::$_generators[$entityName])){
			if(self::$_generators[$entityName]['generator']===null){
				$generator = new ActiveRecordGenerator(
				self::$_generators[$entityName]['adapter'],
				self::$_generators[$entityName]['column'],
				self::$_generators[$entityName]['options']
				);
				self::$_generators[$entityName]['generator'] = $generator;
			} else {
				$generator = self::$_generators[$entityName]['generator'];
			}
			return $generator;
		} else {
			return false;
		}
	}

	/**
	 * Devuelve todos los generadores creados
	 *
	 * @return array
	 * @static
	 */
	public static function getAllCreatedGenerators(){
		$generators = array();
		foreach(self::$_generators as $generator){
			if(is_object($generator['generator'])){
				$generators[] = $generator['generator'];
			}
		}
		return $generators;
	}

	/**
	 * Agrega una llave foranea al administrador
	 *
	 * @access 	public
	 * @param 	string $entityName
	 * @param 	array $fields
	 * @param 	string $referenceTable
	 * @param 	array $referencedFields
	 * @param 	array $options
	 * @static
	 */
	public static function addForeignKey($entityName, $fields='', $referenceTable='', $referencedFields='', $options=array()){
		if(!isset(self::$_foreignKeys[$entityName])){
			self::$_foreignKeys[$entityName] = array();
		}
		if($referenceTable==''){
			if(is_array($fields)){
				$indexKey = join('', sort(array_map('ucfirst', $fields)));
			} else {
				$indexKey = ucfirst(Utils::camelize($fields));
			}
		} else {
			$indexKey = ucfirst(Utils::camelize($referenceTable));
		}
		if(!isset(self::$_foreignKeys[$entityName][$indexKey])){
			if(is_array($fields)){
				if(count($fields)>0&&$referenceTable==''){
					throw new EntityManagerException('Debe indicar la tabla referenciada en la llave foránea virtual');
				}
			} else {
				if($referenceTable==''){
					$referenceTable = $fields;
					$fields = 'id';
					$referencedFields = Utils::uncamelize(Utils::lcfirst($entityName)).'_id';
				}
			}
			if($referencedFields==''){
				$referencedFields = $fields;
			}
			if(is_array($referencedFields)){
				if(count($fields)!=count($referencedFields)){
					throw new EntityManagerException('El número de campos referenciados no es el mismo');
				}
			}
			self::$_foreignKeys[$entityName][$indexKey] = array(
				'fi' => $fields,
				'rt' => $referenceTable,
				'rf' => $referencedFields,
				'op' => $options
			);
		} else {
			return;
		}
	}

	/**
	 * Devuelve las llaves foraneas de una entidad
	 *
	 * @param 	string $entityName
	 * @param 	string $indexKey
	 * @return 	array
	 * @static
	 */
	public static function getForeignKeys($entityName){
		if(isset(self::$_foreignKeys[$entityName])){
			return self::$_foreignKeys[$entityName];
		} else {
			return false;
		}
	}

	/**
	 * Indica si la entidad tiene llaves foraneas
	 *
	 * @param string $entityName
	 * @return boolean
	 * @static
	 */
	public static function hasForeignKeys($entityName){
		return isset(self::$_foreignKeys[$entityName]);
	}

}
