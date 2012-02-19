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
 * @version 	$Id: create_application.php 107 2009-10-11 21:58:02Z gutierrezandresfelipe $
 */

require 'Library/Kumbia/Core/ClassPath/CoreClassPath.php';
require 'Library/Kumbia/Autoload.php';

/**
 * CreateApplication
 *
 * Permite crear el esqueleto de una aplicación
 *
 * @category 	Kumbia
 * @package 	Scripts
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license 	New BSD License
 */
class CreateApplication extends Script {

	public function __construct(){

		$posibleParameters = array(
			'name=s' => '--name nombre \t\tNombre de la tabla source del modelo',
			'help' => '--help \t\t\tMuestra esta ayuda'
			);

			$this->parseParameters($posibleParameters);

			if($this->isReceivedOption('help')){
				$this->showHelp($posibleParameters);
				return;
			}

			$this->checkRequired(array('name'));

			$name = $this->getOption('name');
			ComponentBuilder::createApplication($name);
	}

}

try {
	$script = new CreateApplication();
}
catch(CoreException $e){
	print get_class($e).' : '.$e->getMessage()."\n";
}
catch(Exception $e){
	print 'Exception : '.$e->getMessage()."\n";
}

