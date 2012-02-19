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
 * @subpackage	ActiveRecordJoin
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2008-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @version 	$Id: ActiveRecordJoin.php 82 2009-09-13 21:06:31Z gutierrezandresfelipe $
 */

/**
 * ActiveRecordJoin
 *
 * El subcomponente ActiveRecordJoin permite aprovechar las relaciones
 * establecidas en el modelo de datos para generar consultas simples ó
 * con agrupamientos en más de 2 entidades relacionadas ó no relacionadas,
 * proponiendo una forma adicional de utilizar el Object-Relational-Mapping (ORM).
 *
 * @category	Kumbia
 * @package		ActiveRecord
 * @subpackage	ActiveRecordJoin
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2008-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 */
class ActiveRecordJoin extends Object {

	/**
	 * Conexion al motor con el que se hara la consulta
	 *
	 * @access private
	 * @var dbBase
	 */
	private $_db;

	/**
	 * Consulta generada apartir de los parametros
	 *
	 * @access private
	 * @var string
	 */
	private $_sqlQuery;

	/**
	 * Constructor de la clase
	 *
	 * @access public
	 */
	public function __construct($params){
		if(!isset($params['entities'])||count($params['entities'])==0){
			throw new ActiveRecordException("Debe indicar las entidades con las que se har&aacute; la consulta");
		}
		$entitiesSources = array();
		$groupFields = array();
		$requestedFields = array();
		foreach($params['entities'] as $entityName){
			$entityInstance = EntityManager::getEntityInstance($entityName);
			$entitiesSources[$entityName] = $entityInstance->getSource();
		}
		if(!isset($params['fields'])){
			if(isset($params['groupFields'])){
				foreach($params['groupFields'] as $alias => $field){
					if(preg_match('/\{\#([a-zA-Z0-9\_]+)\}/', $field, $regs)){
						if(!isset($entitiesSources[$regs[1]])){
							throw new ActiveRecordException("La entidad '{$regs[1]}' en los campos solicitados no se encontró en la lista de entidades a agrupar");
						} else {
							$sqlField = str_replace($regs[0], $entitiesSources[$regs[1]], $field);
							if(!is_numeric($alias)){
								$requestedFields[] = $sqlField." AS $alias";
							} else {
								$requestedFields[] = $sqlField;
							}
							if(strpos($sqlField, " ")==false){
								$groupFields[] = $sqlField;
							} else {
								$groupFields[] = substr($sqlField, 0, strpos($sqlField, " "));
							}
						}
					} else {
						$groupFields[] = $field;
					}
				}
			}
			$groupFunctions = array(
				'sumatory' => 'SUM',
				'count' => 'COUNT',
				'minimum' => 'MIN',
				'maximum' => 'MAX',
				'average' => 'AVG',
			);
			foreach($groupFunctions as $key => $function){
				if(isset($params[$key])){
					foreach($params[$key] as $alias => $field){
						$existsEntity = false;
						$replacedField = $field;
						while(preg_match('/\{\#([a-zA-Z0-9\_]+)\}/', $replacedField, $regs)){
							if(!isset($entitiesSources[$regs[1]])){
								throw new ActiveRecordException("La entidad '{$regs[1]}' en los campos solicitados no se encontró en la lista de entidades con acumulado de sumatoria");
							} else {
								$replacedField = str_replace($regs[0], $entitiesSources[$regs[1]], $replacedField);
								if(is_numeric($alias)){
									if(strpos($replacedField, '.')==false){
										$alias = $replacedField;
									} else {
										$alias = substr($replacedField, strpos($replacedField, '.')+1);
									}
								}
							}
							$existsEntity = true;
						}
						if($existsEntity==false){
							if($alias==""||is_numeric($alias)){
								$requestedFields[] = "$function($field)";
							} else {
								$requestedFields[] = "$function($field) AS $alias";
							}
						} else {
							$requestedFields[] = "$function($replacedField) AS $alias";
						}
					}
				}
			}
		} else {
			$requestedFields = array();
			if(is_array($params['fields'])){
				foreach($params['fields'] as $alias => $field){
					if(preg_match('/\{\#([a-zA-Z0-9]+)\}/', $field, $regs)){
						if(!in_array($regs[1], $params['entities'])){
							throw new ActiveRecordException("La entidad '{$regs[1]}' en los campos solicitados no se encontró en la lista de entidades");
						} else {
							if(is_numeric($alias)){
								$requestedFields[] = str_replace($regs[0], $entitiesSources[$regs[1]], $field);
							} else {
								$alias = (string) $alias;
								$requestedFields[] = str_replace($regs[0], $entitiesSources[$regs[1]], $field).' AS '.$alias;
							}
						}
					} else {
						if(is_numeric($alias)){
							$requestedFields[] = $field;
						} else {
							$alias = (string) $alias;
							$requestedFields[] = $field.' AS '.$alias;
						}
					}
				}
			}
		}
		$join = array();
		if(!isset($params['noRelations'])||$params['noRelations']==false){
			foreach($params['entities'] as $entityName){
				$relations = EntityManager::getRelationsOf($entityName);
				if(count($relations)>0){
					if(isset($relations['belongsTo'])){
						foreach($params['entities'] as $relationEntity){
							if($relationEntity!=$entityName){
								if(isset($relations['belongsTo'][$relationEntity])){
									$belongsTo = $relations['belongsTo'][$relationEntity];
									$source = $entitiesSources[$entityName];
									if(!is_array($belongsTo['rf'])){
										$join[] = "{$belongsTo['rt']}.{$belongsTo['rf']} = $source.{$belongsTo['fi']}";
									} else {
										$i = 0;
										foreach($belongsTo['rf'] as $rf){
											$join[] = "{$belongsTo['rt']}.{$rf} = $source.{$belongsTo['fi'][$i]}";
											++$i;
										}
									}
								}
							}
						}
					}
					if(isset($relations['hasMany'])){
						foreach($params['entities'] as $relationEntity){
							if($relationEntity!=$entityName){
								if(isset($relations['hasMany'][$relationEntity])){
									$hasMany = $relations['hasMany'][$relationEntity];
									$source = $entitiesSources[$entityName];
									if(!is_array($hasMany['rf'])){
										$join[] = "$source.{$hasMany['rf']} = {$hasMany['rt']}.{$hasMany['fi']}";
									} else {
										$i = 0;
										foreach($hasMany['rf'] as $rf){
											$join[] = "$source.{$rf} = {$hasMany['rt']}.{$hasMany['fi'][$i]}";
											++$i;
										}
									}
								}
							}
						}
					}
				}
			}
			if(count($join)==0){
				if(isset($params['noRelations'])){
					if($params['noRelations']==false){
						throw new ActiveRecordException("No se pudo encontrar las relaciones entre las entidades");
					}
				} else {
					throw new ActiveRecordException("No se pudo encontrar las relaciones entre las entidades");
				}
			} else {
				$join = array_unique($join);
				if(isset($params['conditions'])){
					if($params['conditions']!=""){
						foreach($params['entities'] as $entityName){
							$params['conditions'] = str_replace('{#'.$entityName.'}', $entitiesSources[$entityName], $params['conditions']);
						}
						$join[] = $params['conditions'];
					}
				}
			}
		} else {
			if(isset($params['conditions'])){
				if($params['conditions']!=''){
					foreach($params['entities'] as $entityName){
						$params['conditions'] = str_replace('{#'.$entityName.'}', $entitiesSources[$entityName], $params['conditions']);
					}
					$join[] = $params['conditions'];
				}
			}
		}
		if(isset($params['order'])){
			if(!is_array($params['order'])){
				foreach($params['entities'] as $entityName){
					$params['order'] = str_replace('{#'.$entityName.'}', $entitiesSources[$entityName], $params['order']);
				}
				$order = $params['order'];
			} else {
				foreach($params['order'] as $key => $valueOrder){
					if(preg_match('/\{\#([a-zA-Z0-9]+)\}/', $valueOrder, $regs)){
						if(in_array($regs[1], $params['entities'])){
							$params['order'][$key] = str_replace("{#$regs[1]}", $entitiesSources[$regs[1]], $valueOrder);
						} else {
							throw new DbSQLGrammarException("No se encuentra la entidad '{$regs[1]}' en la lista de ordenamiento");
						}
					}
				}
				$order = join(',', $params['order']);
			}
		} else {
			$order = '1';
		}
		$this->_db = DbBase::rawConnect();
		if(count($requestedFields)>0){
			$fields = join(', ', $requestedFields);
		} else {
			$fields = '*';
		}
		$this->_sqlQuery = 'SELECT '.$fields.' FROM '.join(', ', $entitiesSources).' WHERE '.join(' AND ', $join);
		if(count($groupFields)){
			$this->_sqlQuery.= ' GROUP BY '.join(', ', $groupFields);
		}
		if(isset($params['having'])){
			$this->_sqlQuery.=' HAVING '.$params['having'];
		}
		$this->_sqlQuery.=' ORDER BY '.$order;
	}

	/**
	 * Devuelve los resultados del join
	 *
	 * @access public
	 */
	public function getResultSet(){
		$resultResource = $this->_db->query($this->_sqlQuery);
		$count = $this->_db->numRows($resultResource);
		if($count>0){
			$rowObject = new ActiveRecordRow();
			$rowObject->setConnection($this->_db);
			return new ActiveRecordResultset($rowObject, $resultResource, $this->_sqlQuery);
		} else {
			return new ActiveRecordResultset(new stdClass(), false, $this->_sqlQuery);
		}
	}

	/**
	 * Devuelve el SQL interno generado
	 *
	 * @access public
	 * @return string
	 */
	public function getSQLQuery(){
		return $this->_sqlQuery;
	}

	/**
	 * Obtiene un resumen de la sumatoria de los valores de una columna
	 *
	 * @access public
	 * @param string $columnName
	 */
	public function getSummaryBy($columnName){
		$resulSet = $this->getResultSet();
		$summary = array();
		foreach($resultSet as $result){
			#$summary->
		}
	}

}
