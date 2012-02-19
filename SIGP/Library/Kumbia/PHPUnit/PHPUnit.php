<?php

/**
 * Kumbia Enterprise Framework
 *
 * LICENSE
 *
 * This source file is subject to the New BSD License that is bundled
 * with this package in the file docs/LICENSE.txt.

 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@loudertechnology.com so we can send you a copy immediately.
 *
 * @category	Kumbia
 * @package	PHPUnit
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2008 Andres Felipe Gutierrez (gutierrezandresfelipe@gmail.com)
 * @license 	New BSD License
 * @version 	$Id: PHPUnit.php 90 2009-09-21 01:29:23Z gutierrezandresfelipe@gmail.com $
 */

/**
 * PHPUnit
 *
 * Clase que permite crear entornos de pruebas
 *
 * @category 	Kumbia
 * @package 	PHPUnit
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2008 Andres Felipe Gutierrez (gutierrezandresfelipe@gmail.com)
 * @license 	New BSD License
 * @abstract
 */
abstract class PHPUnit {

	/**
	 * Proporciona un entorno controlado para la ejecuci&oacute;
	 * de pruebas de unidad
	 *
	 * @param string $className
	 */
	static public function testClass($className){
		if(!class_exists($className)){
			throw new PHPUnitException("La clase de test de unidad '$className' no existe");
		}
		try {
			$test = new $className();
			$reflector = new ReflectionClass($className);
			$assertionFailed = 0;
			$numberOfTest = 0;
			$assertionMessages = array();
			try {
				foreach($reflector->getMethods() as $method){
					try {
						if($method->isPublic()&&substr($method->getName(), 0, 4)=='test'){
							$comment = $method->getDocComment();
							$comment = trim(str_replace(array('/*', '*/', '*'), '', $comment));
							$methodName = $method->getName();
							echo sprintf('%03s', $numberOfTest+1).'. '.$methodName.' - '.$comment.' : ';
							++$numberOfTest;
							$test->$methodName();
						} else {
							continue;
						}
					}
					catch(AssertionFailed $e){
						$assertionMessages[$methodName] = get_class($e).' > '.$e->getMessage();
						++$assertionFailed;
						echo 'FAIL', "\n";
						continue;
					}
					catch(Exception $e){
						#print_r($e->getTrace());
						$assertionMessages[$methodName] = get_class($e).' > '.$e->getConsoleMessage();
						++$assertionFailed;
					}
					echo 'OK', "\n";
				}
			}
			catch(AssertionFailed $e){
				$assertionMessages['_start'] = get_class($e).' > '.$e->getMessage();
				$assertionFailed++;
			}
			catch(Exception $e){
				$assertionMessages['_start'] = get_class($e).' > '.$e->getConsoleMessage();
				$assertionFailed++;
			}
			echo "Total Pruebas: $numberOfTest Fallaron: $assertionFailed\n";
			echo "Total Aserciones: ".$test->getNumberAssertions()." Exitosas: ".$test->getSuccessAssertions()." Fallaron: ".$test->getFailedAssertions()."\n";
			if($assertionFailed>0){
				print "Los test han fallado con los siguientes mensajes:\n\n";
				foreach($assertionMessages as $test => $messsage){
					echo $test." : ".$messsage."\n";
				}

			}
		}
		catch(Exception $e){
			echo "Exception ".$e->getMessage()."\n";
			echo "Archivo: ".$e->getFile()."\n";
			debug_print_backtrace();
		}
	}

}
