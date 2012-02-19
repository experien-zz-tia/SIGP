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

class HttpUriTest extends PHPUnitTestCase {

	public function testSchema1(){
		$uri = new HttpUri('https://127.0.0.1/instance/app/services/authentication');
		$this->assertEquals($uri->getSchema(), 'https');
	}

	public function testSchema2(){
		$uri = new HttpUri('127.0.0.1/instance/app/services/authentication');
		$this->assertEquals($uri->getSchema(), 'http');
	}

	public function testHostname1(){
		$uri = new HttpUri('http://127.0.0.1/instance/app/services/authentication');
		$this->assertEquals($uri->getHostname(), '127.0.0.1');
	}

	public function testHostname2(){
		$uri = new HttpUri('http://nombredemaquina/instance/app/services/authentication');
		$this->assertEquals($uri->getHostname(), 'nombredemaquina');
	}

	public function testHostname3(){
		$uri = new HttpUri('https://www.loudertechnology.com/instance/app/services/authentication');
		$this->assertEquals($uri->getHostname(), 'www.loudertechnology.com');
	}

	public function testPort1(){
		$uri = new HttpUri('http://127.0.0.1:8080/instance/app/services/authentication');
		$this->assertEquals($uri->getPort(), 8080);
	}

	public function testPort2(){
		$uri = new HttpUri('https://www.loudertechnology.com:443/service?hl=es');
		$this->assertEquals($uri->getPort(), 443);
	}

	public function testUri1(){
		$uri = new HttpUri('https://www.loudertechnology.com:443/service?hl=es');
		$this->assertEquals($uri->getUri(), 'service');
	}

	public function testUri2(){
		$uri = new HttpUri('https://www.loudertechnology.com:443');
		$this->assertEquals($uri->getUri(), '');
	}

	public function testUri3(){
		$uri = new HttpUri('https://www.loudertechnology.com:443/?var=123&var2=test');
		$this->assertEquals($uri->getUri(), '');
	}

	public function testUri4(){
		$uri = new HttpUri('ftp://www.loudertechnology.com/instance/services/?var=123&var2=test');
		$this->assertEquals($uri->getUri(), 'instance/services/');
	}

}

print "Probando Componente HttpUri...\n";
PHPUnit::testClass("HttpUriTest");


