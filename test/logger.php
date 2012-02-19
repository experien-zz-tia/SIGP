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
 * @copyright Copyright (c) 2005-2008 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license http://www.kumbia.org/license.txt New BSD License
 */

require "Library/Kumbia/Exception.php";
require "Library/Kumbia/Kumbia.php";
require "Library/Kumbia/PHPUnit/PHPUnit.php";

class LoggerTest extends PHPUnitTest {

	private $_logger;
	private $path;

	public function __construct(){
		@unlink("test/fingerprints.log");
		$this->logger = new Logger("test/fingerprints.log");
		$this->logger->setFormat("[%type][%date] %message");
	}

	public function testCreateLogger(){
		$this->assertFileExists("test/fingerprints.log");
	}

	public function testWriteToLog(){
		$this->logger->log("Prueba", Logger::CRITICAL);
		$lines = file("test/fingerprints.log");
		$this->assertEquals(count($lines), 1);
	}

	public function testWriteToLogWithShortcut(){
		$this->logger->critical("Prueba");
	}

}

print "Probando Componente Logger...\n";
PHPUnit::testClass("LoggerTest");

?>
