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
 * @category Kumbia
 * @package Scripts
 * @copyright Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license New BSD License
 */

require 'Library/Kumbia/Core/ClassPath/CoreClassPath.php';
require 'Library/Kumbia/Autoload.php';

class Package extends Script {

	private function _recursiveScan($directory){
		$directoryIterator = new DirectoryIterator($directory);
		$files = array();
		foreach($directoryIterator as $file){
			if($file->isDot()==false){
				if($file->isDir()){
					$files = array_merge($files, $this->_recursiveScan($directory."/".$file));
				} else {
					$files[] = "$directory/$file";
				}
			}
		}
		return $files;
	}

	private function _scan($directory){
		$directoryIterator = new DirectoryIterator($directory);
		$files = array();
		foreach($directoryIterator as $file){
			if($file->isDot()==false){
				if(!$file->isDir()){
					$files[] = "$directory/$file";
				}
			}
		}
		return $files;
	}

	public function __construct(){

		$posibleParameters = array(
			"application=s" => "--application nombre \tNombre de la aplicaci&oacute;n",
			"descriptor=s" => "--descriptor archivo \tArchivo del descriptor XML [opcional]",
			"extra-dirs=s" => "--extra-dirs directorio1,directorio2 \tDirectorios extra que deban empaquetarse con el contenido [opcional]",
			"help" => "--help \t\t\tMuestra esta ayuda"
			);

			$this->parseParameters($posibleParameters);

			$this->checkRequired(array('application'));

			$application = $this->getOption('application');
			if(!file_exists("apps/$application")){
				throw new CoreException("No existe la aplicaci&oacute;n '$application'");
			}
			if(!$this->isReceivedOption('application')){
				$descriptor = "apps/$application/package.xml";
			}
			if(!$this->isReceivedOption('output-dir')){
				$outputDir = "deploy/";
			}
			$includeDirectories = array(
			#"apps/admin",
			"apps/$application",
			"config",
			"languages",
			"Library",
			"scripts",
			"public/img/upload",
			#"public/javascript/admin",
			"public/javascript/core",
			"public/javascript/core/framework/ext",
			"public/javascript/core/framework/scriptaculous",
			"public/css/ext",
			#"public/css/admin",
			"public/files",
			"public/temp"
			);
			$files = array(
			".htaccess",
			"index.php",
			"public/.htaccess",
			"public/index.php",
			"public/css.php",
			"public/css/index.html",
			"public/css/style.css",
			"public/css/exception.css",
			);
			$includeNonRecursive = array(
			"public/img"
			);
			foreach($includeNonRecursive as $directory){
				$files = array_merge($files, $this->_scan($directory));
			}
			foreach($includeDirectories as $directory){
				$files = array_merge($files, $this->_recursiveScan($directory));
			}
			if($this->isReceivedOption('extra-dirs')){
				$extraDirs = explode(",", $this->getOption('extra-dirs'));
				foreach($extraDirs as $directory){
					$files = array_merge($files, $this->_recursiveScan($directory));
				}
			}
			if(PHP_OS!="WINNT"){
				if(file_exists("{$outputDir}/$application.zip")){
					unlink("{$outputDir}/$application.zip");
				}
				print "zip -q {$outputDir}/$application.zip ".join(" ", $files);
				system("zip -q {$outputDir}/$application.zip ".join(" ", $files));
			}
	}

}

try {
	$script = new Package();
}
catch(CoreException $e){
	print get_class($e)." : ".$e->getConsoleMessage()."\n";
}
catch(Exception $e){
	print "Exception : ".$e->getMessage()."\n";
}
