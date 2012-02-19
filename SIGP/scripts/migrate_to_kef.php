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
 * to kumbia@kumbia.org so we can send you a copy immediately.
 *
 * @category 	Kumbia
 * @package 	Scripts
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license 	New BSD License
 * @version 	$Id$
 */

require 'Library/Kumbia/Core/ClassPath/CoreClassPath.php';
require 'Library/Kumbia/Autoload.php';

class MigrateToEnterprise extends Script {

	/**
	 * Escanea recursivamente un directorio
	 *
	 * @param 	string $dir
	 * @return  array
	 */
	private function _recursiveScandir($dir){
		$files = array();
		foreach(scandir($dir) as $file){
			if($file!='.'&&$file!='..'){
				$path = $dir.DIRECTORY_SEPARATOR.$file;
				if(!is_dir($path)){
					$files[] = $path;
				} else {
					$dirFiles = $this->_recursiveScandir($path);
					$files = array_merge($files, $dirFiles);
				}
			}
		}
		return $files;
	}

	/**
	 * Crea un directorio en n-niveles
	 *
	 * @param	string $path
	 */
	private function _makeDirectoryRecursive($path){
		$pathParts = explode('/', $path);
		$i = 1;
		foreach($pathParts as $part){
			$part = join('/', array_slice($pathParts, 0, $i));
			@mkdir($part);
			$i++;
		}
	}

	/**
	 * Constructor de MigrateToEnterprise
	 *
	 */
	public function __construct(){
		$iniApp = 'default';
		$destApp = 'hot';
		if(!file_exists('apps/'.$destApp)){
			throw new ScriptException('No existe la aplicación destino '.$destApp);
		}

		if(!file_exists('apps/'.$destApp.'/controllers')){
			mkdir('apps/'.$destApp.'/controllers');
		}

		foreach(scandir('apps/'.$iniApp.'/controllers') as $file){
			$path = 'apps/'.$iniApp.'/controllers/'.$file;
			if(!is_dir($path)){
				if(preg_match('#_controller\.php$#', $file)){
					$migrate = new Migrate();
					$migratedSource = $migrate->migrateController(file_get_contents($path));
					file_put_contents('apps/'.$destApp.'/controllers/'.$file, $migratedSource);
				} else {
					if($file=='application.php'){
						$migrate = new Migrate();
						$migratedSource = $migrate->migrateAppController(file_get_contents($path));
						file_put_contents('apps/'.$destApp.'/controllers/'.$file, $migratedSource);
					}
				}
			}
		}

		/*foreach($this->_recursiveScandir('apps/'.$iniApp.'/models') as $file){
			if(preg_match('#\.php$#', $file)){
			if($file!='apps/'.$iniApp.'/models/base/model_base.php'){
			$migrate = new Migrate();
			$migratedSource = $migrate->migrateModel(file_get_contents($file));
			$destFile = str_replace('apps/'.$iniApp.'/models', 'apps/'.$destApp.'/models', $file);
			$this->_makeDirectoryRecursive(dirname($destFile));
			file_put_contents($destFile, $migratedSource);
			}
			}
			}*/

		foreach($this->_recursiveScandir('apps/'.$iniApp.'/views') as $file){
			if(preg_match('#\.phtml$#', $file)){
				$migrate = new Migrate();
				$migratedSource = $migrate->migrateView(file_get_contents($file));
				$destFile = str_replace('apps/'.$iniApp.'/views', 'apps/'.$destApp.'/views', $file);
				$this->_makeDirectoryRecursive(dirname($destFile));
				file_put_contents($destFile, $migratedSource);
			}
		}
	}

}

try {
	$script = new MigrateToEnterprise();
}
catch(CoreException $e){
	print get_class($e)." : ".$e->getConsoleMessage()."\n";
}
catch(Exception $e){
	print "Exception : ".$e->getMessage()."\n";
}
