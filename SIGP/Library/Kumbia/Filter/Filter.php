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
 * @package		Filter
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2007-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright	Copyright (c) 2007-2007 Emilio Rafael Silveira Tovar(emilio.rst at gmail.com)
 * @copyright	Copyright (c) 2007-2007 Deivinson Tejeda Brito (deivinsontejeda at gmail.com)
 * @license		New BSD License
 * @version 	$Id: Filter.php 97 2009-09-30 19:28:13Z gutierrezandresfelipe $
 */

/**
 * Filter
 *
 * Implementación de Filtros para Kumbia
 *
 * @category	Kumbia
 * @package		Filter
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2007-2008 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright	Copyright (c) 2007-2007 Emilio Rafael Silveira Tovar(emilio.rst at gmail.com)
 * @copyright	Copyright (c) 2007-2007 Deivinson Tejeda Brito (deivinsontejeda at gmail.com)
 * @license		New BSD License
 */
class Filter extends Object {

	/**
	 * Filtros a que se aplicaran atraves del metodo "apply"
	 *
	 * @var array
	 */
	private $_filters = array();

	/**
	 * Temporal para los filtros que se aplicaran atraves de "applyFilter"
	 *
	 * @var array
	 */
	private $_bufferFilters;

	/**
	 * Obtiene los parametros por nombre de los Filtros
	 *
	 * @param array $params
	 * @return array
	 */
	private function _getParams($params){
		$data = array();
		$i = 0;
		foreach($params as $p){
			if(is_string($p) && preg_match('/([a-z_0-9]+\.?[a-z_0-9]+[:][ ]).+/', $p, $regs)){
				$p = str_replace($regs[1], '', $p);
				$n = str_replace(": ", "", $regs[1]);
				$data[$n] = $p;
			} else {
				$data[$i] = $p;
			}
			++$i;
		}
		return $data;
	}

	/**
	 * Constructor de la clase Filter
	 *
	 */
	public function __construct(){
		$this->_bufferFilters = array();
		$this->_filters = array();
		$params = func_get_args();
		call_user_func_array(array($this, 'addFilter'), $params);
	}

	/**
	 * Agrega un filtro a la cola de filtros
	 *
	 * @return boolean
	 */
	public function addFilter(){
		$params = func_get_args();
		foreach($params as $param){
			if(is_object($param)&&method_exists($param, 'execute')){
				$this->_bufferFilters[] = $param;
			} else {
				$className = $param.'Filter';
				if(class_exists($className, false)==false){
					self::load($className);
				}
				$filter = new $className();
				$this->_bufferFilters[] = $filter;
			}
		}
		return true;
	}

	/**
	 * Aplica un filtro
	 *
	 * @param Filter $s
	 */
	public function apply(&$s){
		if(is_array($s)){
			foreach($s as $key => $value){
				if(is_array($value) || is_object($value)){
					$this->apply($s[$key]);
				} else {
					foreach($this->_filters as $f) {
						$s[$key] = $f->execute($value);
					}
				}
			}
		} else{
			if(is_object($s)){
				foreach(get_object_vars($s) as $attr => $value){
					if(is_array($value)||is_object($value)){
						$this->apply($s->$attr);
					} else {
						foreach($this->_filters as $f) {
							$s->$attr = $f->execute($value);
						}
					}
				}
			} else {
				foreach($this->_filters as $f) {
					$s = $f->execute($s);
				}
			}
		}
	}

	/**
	 * Aplica un filtro
	 *
	 * @param array $s
	 * @return mixed
	 */
	public function applyFilter($s){

		//para cargar los filtros
		if(func_num_args()>1){
			$params = $this->_getParams(func_get_args());

			//cargo los atributos para los filtros en un array
			$attributes = array();
			foreach($params as $attr => $value) {
				if(!is_numeric($attr)) {
					$attributes[$attr] = $value;
				}
			}

			//agrego los filtros (recordar que $params[0] es el parametro a filtrar)
			for($i=1; isset($params[$i]); ++$i) {
				if(is_object($params[$i])) {
					foreach($attributes as $attr => $value) {
						if(preg_match('/([a-z_0-9]+)\.([a-z_0-9]+)/', $attr, $regs)) {
							$filter = ucfirst(Utils::camelize($regs[1])).'Filter';
							if($params[$i] instanceof $filter) {
								$params[$i]->$regs[2] = $value;
							}
						} else {
							$params[$i]->$attr = $value;
						}
					}
					array_push($this->_bufferFilters, $params[$i]);
				} else {
					$filter = ucfirst(Utils::camelize($params[$i])).'Filter';
					if(!class_exists($filter, false)){
						self::load($params[$i]);
					}
					if(class_exists($filter, false)) {
						$obj = new $filter();
						foreach($attributes as $attr => $value) {
							if(preg_match('/([a-z_0-9]+)\.([a-z_0-9]+)/', $attr, $regs)) {
								$filter = ucfirst(Utils::camelize($regs[1])).'Filter';
								if($obj instanceof $filter) {
									$obj->$regs[2] = $value;
								}
							} else {
								$obj->$attr = $value;
							}
						}
						array_push($this->_bufferFilters, $obj);
					} else {
						throw new FilterException("No existe el filtro '$filter'");
					}
				}
			}
		}

		//aplico los filtros
		if(is_array($s)){
			foreach($s as $key => $value){
				if(is_array($value)||is_object($value)){
					$this->applyFilter($s[$key]);
				} else {
					foreach($this->_bufferFilters as $f) {
						$s[$key] = $f->execute($value);
					}
				}
			}
		} else {
			if(is_object($s)) {
				foreach(get_object_vars($s) as $attr => $value){
					if(is_array($value)||is_object($value)){
						$this->applyFilter($s->$attr);
					} else {
						foreach($this->bufferFilters as $f){
							$s->$attr = $f->execute($value);
						}
					}
				}
			} else {
				foreach($this->_bufferFilters as $f){
					$s = $f->execute($s);
				}
			}
		}
		return $s;
	}

	/**
	 * Elimina el buffer de filtros
	 *
	 * @access public
	 */
	public function clearBufferFilters(){
		$this->_bufferFilters = array();
	}

	/**
	 * Carga la clase de un filtro para su posterior aplicacion
	 *
	 * @access public
	 * @param string $filterName
	 * @static
	 */
	public static function load($filterName){
		$filters = func_get_args();
		if(interface_exists('FilterInterface')==false){
			/**
			 * @see FilterInterface
			 */
			require 'Library/Kumbia/Filter/Interface.php';
		}
		foreach($filters as $filterName){
			if(class_exists($filterName.'Filter', false)==false){
				$fileName = ucfirst($filterName);
				if(Core::fileExists('Library/Kumbia/Filter/BaseFilters/'.$fileName.'.php')==true){
					require 'Library/Kumbia/Filter/BaseFilters/'.$fileName.'.php';
				} else {
					$activeApp = Router::getApplication();
					if($activeApp!=""){
						$config = CoreConfig::readAppConfig();
						if(isset($config->application->filtersDir)){
							$filtersDir = 'apps/'.$config->application->filtersDir;
						} else {
							$filtersDir = 'apps/'.$activeApp.'/filters';
						}
						$path = $filtersDir.'/'.$fileName.'.php';
						if(Core::fileExists($path)){
							require $path;
						} else {
							throw new FilterException("No existe el filtro '$fileName'");
						}
					}
				}
			}
		}
	}

	/**
	 * Valida que un determinado valor se encuentre en un rango
	 *
	 * @param string $value
	 * @param array $range
	 */
	public static function inRange($value, $range){
		if(in_array($value, $range)){
			return $value;
		} else {
			return null;
		}
	}

}
