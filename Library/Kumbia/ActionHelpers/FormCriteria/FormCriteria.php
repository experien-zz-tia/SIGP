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
 * @package		ActionHelpers
 * @subpackage	FormCriteria
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2008-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @version 	$Id: FormCriteria.php 103 2009-10-09 01:30:42Z gutierrezandresfelipe $
 */

/**
 * FormCriteria
 *
 * Permite crear criterios para consultas SQL apartir de la entrada de usuario
 *
 * @category	Kumbia
 * @package		ActionHelpers
 * @subpackage	FormCriteria
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2008-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @access		public
 * @abstract
 */
class FormCriteria {

	/**
	 * Condiciones temporales de la consulta
	 *
	 * @var array
	 */
	private $_conditions = array();

	/**
	 * Constructor de FormCritera
	 *
	 * @todo Falta por rango de campos
	 * @param array $provider
	 * @param array $criteria
	 */
	public function __construct($provider, $criteria){
		$conditions = array();
		$magicQuotes = get_magic_quotes_gpc();
		$mcriteria = array();
		foreach($criteria as $key => $descriptor){
			if(strpos($key, ':')!==false){
				$fields = explode(':', $key);
				foreach($fields as $field){
					$mcriteria[$field] = $descriptor;
					$mcriteria[$field]['subcondition'] = true;
					$mcriteria[$field]['joinOperator'] = 'AND';
				}
				$mcriteria[$fields[0]]['operator'] = '>=';
				$mcriteria[$fields[1]]['operator'] = '<=';
			} else {
				if(isset($descriptor['fieldName'])){
					if(strpos($descriptor['fieldName'], ':')!==false){

					}
				} else {
					$mcriteria[$key] = $descriptor;
				}
			}
		}
		foreach($mcriteria as $key => $descriptor){
			if(isset($provider[$key])){
				if(isset($descriptor['fieldName'])){
					$fieldName = $descriptor['fieldName'];
				} else {
					$fieldName = $key;
				}
				if(isset($descriptor['type'])){
					switch($descriptor['type']){
						case 'integer':
							if($provider[$key]!==null&&$provider[$key]!==''){
								$filter = new Filter();
								$value = $filter->applyFilter($provider[$key], 'int');
							} else {
								$value = null;
							}
							break;
						case 'double':
						case 'float':
							if($provider[$key]!==null&&$provider[$key]!==''){
								$filter = new Filter();
								$value = $filter->applyFilter($provider[$key], 'double');
							} else {
								$value = null;
							}
							break;
						case 'date':
							$filter = new Filter();
							$value = $filter->applyFilter($provider[$key], 'date');
							break;
						case 'string':
							$value = $provider[$key];
							if($magicQuotes==false){
								$value = addslashes($value);
							}
							if(!isset($descriptor['operator'])){
								$descriptor['operator'] = 'LIKE';
								$value = preg_replace('/[ ]+/', '%', $value);
								$value = "'%".$value."%'";
							} else {
								$value = "'".$value."'";
							}
							break;
						default:
							$value = $provider[$key];
					}
					if(isset($descriptor['missOnNull'])&&$descriptor['missOnNull']==false){
						$this->addCondition($descriptor, $fieldName, $value);
					} else {
						if(!isset($descriptor['nullValue'])){
							if($provider[$key]!==''&&$provider[$key]!==null){
								$this->addCondition($descriptor, $fieldName, $value);
							}
						} else {
							if($provider[$key]!=$descriptor['nullValue']){
								$this->addCondition($descriptor, $fieldName, $value);
							}
						}
					}
				} else {
					$value = $provider[$key];
					if($magicQuotes==false){
						$value = addslashes($value);
					}
					$this->addCondition($descriptor, $fieldName, $value);
				}
			}
		}
	}

	/**
	 * Agrega una condicion
	 *
	 * @param array $descriptor
	 * @param string $fieldName
	 * @param mixed $value
	 */
	private function addCondition($descriptor, $fieldName, $value){
		if(!isset($descriptor['operator'])){
			$op = '=';
		} else {
			$op = $descriptor['operator'];
		}
		if(isset($descriptor['subcondition'])&&$descriptor['subcondition']){
			if(isset($descriptor['joinOperator'])){
				$this->_conditions[$descriptor['joinOperator']][] = $fieldName.' '.$op.' '.$value;
			} else {
				$this->_conditions['AND'][] = $fieldName.' '.$op.' '.$value;
			}
		} else {
			$this->_conditions[0][] = $fieldName.' '.$op.' '.$value;
		}
	}

	/**
	 * Obtiene las condiciones
	 *
	 * @param string $joinOperator
	 * @return string
	 */
	public function getConditions($joinOperator='OR'){
		if(isset($this->_conditions['AND'])){
			$andConditions = '('.join(' AND ', $this->_conditions['AND']).')';
			$this->_conditions[0][] = $andConditions;
		}
		return join(' '.$joinOperator.' ', $this->_conditions[0]);
	}

	/**
	 * Une 2 ó más objetos FormCriteria
	 *
	 * @param string $operator
	 * @param array $criteriaArray
	 * @return string
	 */
	public static function join($operator, $criteriaArray){
		return join(' '.$operator.' ', $criteriaArray);
	}

}
