<?php

class DOMDocument {

	private $_version;

	public function __construct($version=1.0, $encoding='UTF-8'){
		$this->_version =  $version;
		$this->_encoding = $encoding;
	}

	public function saveXML(){

	}
}

/**
 * SeekableIterator
 *
 * Permite cambiar de una posicion a otra estableciendo la posicion
 *
 * @package SPL
 * @copyright Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright PHP Group
 */
interface SeekableIterator {

}

/**
 * SplFileInfo
 *
 * The SplFileInfo class offers a high-level object oriented interface to information for an individual file.
 *
 * @package SPL
 * @copyright Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright PHP Group
 */
class SplFileInfo {

	/**
	 * Path to the file.
	 *
	 * @var string
	 */
	protected $_fileName;

	/**
	 * Construct a new SplFileInfo object
	 *
	 * @param string $fileName
	 */
	public function __construct($fileName){
		$this->_fileName = $fileName;
	}

	/**
	 * Gets the inode change time of the file
	 *
	 * @return int
	 */
	public function getATime(){
		return fileatime($this->_fileName);
	}

	/**
	 * Gets the base name of the file
	 *
	 * @param string $suffix
	 */
	public function getBaseName($suffix){
		return basename($this->_fileName, $suffix);
	}

	/**
	 * Gets the inode change time
	 *
	 * @return string
	 */
	public function getCTime(){
		return filectime($this->_fileName);
	}

	/**
	 * Obtiene el nombre del archivo
	 *
	 * @return string
	 */
	public function getFileName(){
		return dirname($this->_fileName);
	}

	/**
	 * Gets the file group
	 *
	 * @return int
	 */
	public function getGroup(){
		return filegroup($this->_fileName);
	}

	/**
	 * Gets the inode for the file
	 *
	 * @return int
	 */
	public function getInode(){
		return fileinode($this->_fileName);
	}

	/**
	 * Gets the target of a link
	 *
	 * @return int
	 */
	public function getLinkTarget(){
		return readlink($this->_fileName);
	}

	/**
	 * Gets the last modified time
	 *
	 * @return int
	 */
	public function getMTime(){
		return filemtime($this->_fileName);
	}

	/**
	 * Gets the owner of the file
	 *
	 * @return string
	 */
	public function getOwner(){
		return fileowner($this->_fileName);
	}

	/**
	 * Gets the path without filename
	 *
	 * @return string
	 */
	public function getPath(){
		return dirname($this->_fileName);
	}

	/**
	 * Gets an SplFileInfo object for the path
	 *
	 * @return SplFileInfo
	 */
	public function getPathInfo(){
		return new SplFileInfo($this->getPath());
	}

	/**
	 * Gets the path to the file
	 *
	 * @return string
	 */
	public function getPathName(){
		$pathinfo = pathinfo($this->_fileName, PATHINFO_DIRNAME);
		return $pathinfo['dirname'];
	}

	/**
	 * Devuelve los permisos del archivo
	 *
	 * @return int
	 */
	public function getPerms(){
		return fileperms($this->_fileName);
	}

	/**
	 * Gets absolute path to file
	 *
	 * @return string
	 */
	public function getRealPath(){
		return realpath($this->_fileName);
	}

	/**
	 * Gets file size
	 *
	 * @return int
	 */
	public function getSize(){
		return filesize($this->_fileName);
	}

	/**
	 * Gets file type
	 *
	 * @return string
	 */
	public function getType(){
		return filetype($this->_fileName);
	}

	/**
	 * Tells if the file is a directory
	 *
	 * @return bool
	 */
	public function isDir(){
		return is_dir($this->_fileName);
	}

	/**
	 * Tells if the file is executable
	 *
	 * @return bool
	 */
	public function isExecutable(){
		return is_executable($this->_fileName);
	}

	/**
	 * Tells if the object references a regular file
	 *
	 * @return bool
	 */
	public function isFile(){
		return is_file($this->_fileName);
	}

	/**
	 * Tells if the file is a link
	 *
	 * @return bool
	 */
	public function isLink(){
		return is_link($this->_fileName);
	}

	/**
	 * Tells if file is readable
	 *
	 * @return bool
	 */
	public function isReadable(){
		return is_readable($this->_fileName);
	}

	/**
	 * Tells if the entry is writable
	 *
	 * @return bool
	 */
	public function isWritable(){
		return is_writable($this->_fileName);
	}

	/**
	 * Gets an SplFileObject object for the file
	 *
	 * @return SplFileObject
	 */
	public function openFile(){

	}

	/**
	 * Sets the class name used with SplFileInfo::openFile()
	 *
	 */
	public function setFileClass(){

	}

	/**
	 * Sets the class used with getFileInfo and getPathInfo
	 *
	 */
	public function setInfoClass(){

	}

	/**
	 * Returns the path to the file as a string
	 *
	 * @return string
	 */
	public function __toString(){
		return $this->_fileName;
	}

}

/**
 * DirectoryIterator
 *
 * The DirectoryIterator class provides a simple interface for viewing the contents of filesystem directories.
 * Due to some strange bug on com.ibm.p8 we need to redefine all the SplFileInfo methdos
 *
 * @package SPL
 * @copyright Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright PHP Group
 */
class DirectoryIterator implements Iterator {

	/**
	 * The path of the directory to traverse.
	 *
	 * @var string
	 */
	private $_path;

	/**
	 * Directory Handler
	 *
	 * @var resource
	 */
	private $_directoryHandler;

	/**
	 * Entty Position
	 *
	 * @var int
	 */
	private $_pointer = 0;

	/**
	 * Path to the file.
	 *
	 * @var string
	 */
	private $_fileName;

	/**
	 * Constructor de DirectoryIterator
	 *
	 * @param string $path
	 */
	public function __construct($path){
		$this->_position = 0;
		$this->_path = $path;
		$this->_fileName = $path;
		$this->_directoryHandler = opendir($path);
	}

	/**
	 * Gets the inode change time of the file
	 *
	 * @return int
	 */
	public function getATime(){
		return fileatime($this->_fileName);
	}

	/**
	 * Gets the base name of the file
	 *
	 * @param string $suffix
	 */
	public function getBaseName($suffix){
		return basename($this->_fileName, $suffix);
	}

	/**
	 * Gets the inode change time
	 *
	 * @return string
	 */
	public function getCTime(){
		return filectime($this->_fileName);
	}

	/**
	 * Obtiene el nombre del archivo
	 *
	 * @return string
	 */
	public function getFileName(){
		return dirname($this->_fileName);
	}

	/**
	 * Gets the file group
	 *
	 * @return int
	 */
	public function getGroup(){
		return filegroup($this->_fileName);
	}

	/**
	 * Gets the inode for the file
	 *
	 * @return int
	 */
	public function getInode(){
		return fileinode($this->_fileName);
	}

	/**
	 * Gets the target of a link
	 *
	 * @return int
	 */
	public function getLinkTarget(){
		return readlink($this->_fileName);
	}

	/**
	 * Gets the last modified time
	 *
	 * @return int
	 */
	public function getMTime(){
		return filemtime($this->_fileName);
	}

	/**
	 * Gets the owner of the file
	 *
	 * @return string
	 */
	public function getOwner(){
		return fileowner($this->_fileName);
	}

	/**
	 * Gets the path without filename
	 *
	 * @return string
	 */
	public function getPath(){
		return dirname($this->_fileName);
	}

	/**
	 * Gets an SplFileInfo object for the path
	 *
	 * @return SplFileInfo
	 */
	public function getPathInfo(){
		return new SplFileInfo($this->getPath());
	}

	/**
	 * Gets the path to the file
	 *
	 * @return string
	 */
	public function getPathName(){
		$pathinfo = pathinfo($this->_fileName, PATHINFO_DIRNAME);
		return $pathinfo['dirname'];
	}

	/**
	 * Devuelve los permisos del archivo
	 *
	 * @return int
	 */
	public function getPerms(){
		return fileperms($this->_fileName);
	}

	/**
	 * Gets absolute path to file
	 *
	 * @return string
	 */
	public function getRealPath(){
		return realpath($this->_fileName);
	}

	/**
	 * Gets file size
	 *
	 * @return int
	 */
	public function getSize(){
		return filesize($this->_fileName);
	}

	/**
	 * Gets file type
	 *
	 * @return string
	 */
	public function getType(){
		return filetype($this->_fileName);
	}

	/**
	 * Tells if the file is a directory
	 *
	 * @return bool
	 */
	public function isDir(){
		return is_dir($this->_fileName);
	}

	/**
	 * Tells if the file is executable
	 *
	 * @return bool
	 */
	public function isExecutable(){
		return is_executable($this->_fileName);
	}

	/**
	 * Tells if the object references a regular file
	 *
	 * @return bool
	 */
	public function isFile(){
		return is_file($this->_fileName);
	}

	/**
	 * Tells if the file is a link
	 *
	 * @return bool
	 */
	public function isLink(){
		return is_link($this->_fileName);
	}

	/**
	 * Tells if file is readable
	 *
	 * @return bool
	 */
	public function isReadable(){
		return is_readable($this->_fileName);
	}

	/**
	 * Tells if the entry is writable
	 *
	 * @return bool
	 */
	public function isWritable(){
		return is_writable($this->_fileName);
	}

	/**
	 * Gets an SplFileObject object for the file
	 *
	 * @return SplFileObject
	 */
	public function openFile(){

	}

	/**
	 * Sets the class name used with SplFileInfo::openFile()
	 *
	 */
	public function setFileClass(){

	}

	/**
	 * Sets the class used with getFileInfo and getPathInfo
	 *
	 */
	public function setInfoClass(){

	}

	/**
	 * Returns the path to the file as a string
	 *
	 * @return string
	 */
	public function __toString(){
		return $this->_fileName;
	}

	/**
	 * Returns true if current entry is '.' or '..'
	 *
	 * @return bool
	 */
	public function isDot(){
		if(is_dir($this->_fileName)){
			if($this->getFileName()=='.'||$this->getFileName()=='..'){
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Return this (needed for Iterator interface)
	 *
	 */
	public function current(){
		return $this;
	}

	/**
	 * Clave de la entrada activa
	 *
	 * @return int
	 */
	public function key(){
		return $this->_pointer;
	}

	/**
	 * Mueve el cursor al siguiente registro
	 *
	 * @return int
	 */
	public function next(){
		$this->position++;
	}

	/**
	 * Resetea el cursor
	 *
	 */
	public function rewind(){
		$this->_position = 0;
		rewinddir($this->_directoryHandler);
	}

	/**
	 * Indica si el iterador tiene mas entradas por devolver
	 *
	 * @return bool
	 */
	public function valid(){
		$file = readdir($this->_directoryHandler);
		if($file!==false){
			$this->_fileName = $file;
			return true;
		} else {
			closedir($this->_directoryHandler);
			return false;
		}
	}

}

/**
 * Registra una funcion de autocarga
 *
 * @param callback $callback
 */
function spl_autoload_register($callback){
	return false;
}

/**
 * Memoria utilizada
 *
 * @param boolean $real
 * @return int
 */
function memory_get_peak_usage($real){
	return memory_get_usage($real);
}

//Establece que se esta ejecutando en Websphere
Core::setIsWebsphere(true);