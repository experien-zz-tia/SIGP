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
 * @package		Controller
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @version 	$Id: ControllerUploadFile.php 5 2009-04-24 01:48:48Z gutierrezandresfelipe $
 */

/**
 * ControllerUploadFile
 *
 * Esta clase encapusula toda la información de la respuesta HTTP
 * del controlador
 *
 * @category	Kumbia
 * @package		Controller
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @access		public
 */
class ControllerUploadFile extends Object {

	/**
	 * Nombre del archivo
	 *
	 * @var string
	 */
	private $_name;

	/**
	 * Tamaño del archivo en bytes
	 *
	 * @var int
	 */
	private $_size;

	/**
	 * Tipo de archivo subido
	 *
	 * @var
	 */
	private $_type;

	/**
	 * Nombre temporal del archivo
	 *
	 * @var string
	 */
	private $_temp;

	/**
	 * Constructor de ControllerUploadFile
	 *
	 * @param array $file
	 */
	public function __construct($file){
		if(isset($file['name'])){
			$this->_name = $file['name'];
		}
		if(isset($file['size'])){
			$this->_size = $file['size'];
		}
		if(isset($file['type'])){
			$this->_type = $file['type'];
		}
		if(isset($file['tmp_name'])){
			$this->_temp = $file['tmp_name'];
		}
	}

	/**
	 * Devuelve el nombre del archivo
	 *
	 * @return string
	 */
	public function getFileName(){
		return $this->_name;
	}

	/**
	 * Obtiene el tamaño del archivo en bytes
	 *
	 * @return int
	 */
	public function getFileSize(){
		return $this->_size;
	}

	/**
	 * Obtiene el tipo de archivo
	 *
	 * @return string
	 */
	public function getFileType(){
		return $this->_type;
	}

	/**
	 * Obtiene el nombre del temporal
	 *
	 * @return string
	 */
	public function getTempName(){
		return $this->_temp;
	}

	/**
	 * Mover el archivo a una ubicación
	 *
	 * @param string $path
	 */
	public function moveFileTo($path){
		return move_uploaded_file($this->_temp, $path);
	}

}
