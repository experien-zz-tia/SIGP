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
 * @package 	Bootstrap
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright 	Copyright (c) 2007-2007 Emilio Rafael Silveira Tovar (emilio.rst@gmail.com)
 * @license 	New BSD License
 * @version 	$Id: index.php 59 2009-05-15 13:02:23Z gutierrezandresfelipe $
 */

/**
 * Establece tipo de notificacion de errores
 */
error_reporting(E_ALL | E_NOTICE | E_STRICT);

/**
 * Activa el track_errors
 */
ini_alter('track_errors', true);

/**
 * Cambiar el directorio para ocultar el framework y los archivos de aplicacion
 */
chdir('..');

/**
 * Cargar componentes principales
 */
require 'Library/Kumbia/Autoload.php';
require 'Library/Kumbia/Object.php';
require 'Library/Kumbia/Core/Core.php';
require 'Library/Kumbia/Session/Session.php';
require 'Library/Kumbia/Config/Config.php';
require 'Library/Kumbia/Core/Config/CoreConfig.php';
require 'Library/Kumbia/Core/Type/CoreType.php';
require 'Library/Kumbia/Core/ClassPath/CoreClassPath.php';
require 'Library/Kumbia/Router/Router.php';
require 'Library/Kumbia/Plugin/Plugin.php';
require 'Library/Kumbia/Registry/Memory/MemoryRegistry.php';

try {

	//Inicializar el ExceptionHandler
	set_exception_handler(array('Core', 'manageExceptions'));
	set_error_handler(array('Core', 'manageErrors'));

	//Detecta la forma en que se debe tratar los parametros del
	Router::handleRouterParameters();

	//Inicializa el entorno de ejecución de la petición
	Core::initApplication();

	//Atender la petición
	Core::main();


}
catch(CoreException $e){
	try {
		Session::startSession();
		$exceptionHandler = Core::determineExceptionHandler();
		call_user_func_array($exceptionHandler, array($e, null));
	}
	catch(Exception $e){
		//Pueden ocurrir mas excepciones en los componentes de inicialización
		CoreException::showSimpleMessage($e);
	}
}
catch(Exception $e){
	//Se trata de mostrar la excepción de la forma mas segura posible
	print 'Exception: '.$e->getMessage();
	foreach(debug_backtrace() as $debug){
		print $debug['file'].' ('.$debug['line'].") <br>\n";
	}
}
