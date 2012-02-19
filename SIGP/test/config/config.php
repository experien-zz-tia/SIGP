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
 * @license 	New BSD License
 */

require 'Library/Kumbia/Core/ClassPath/CoreClassPath.php';
require 'Library/Kumbia/Autoload.php';
require 'Library/Kumbia/Config/Config.php';

class ConfigTest extends PHPUnitTestCase {

	public function testIniAdapter(){
		$config = Config::read('test/config/data/config.ini', 'ini');
		$this->assertEquals($config->example->variable, 'value');
		$this->assertEquals($config->development->{"database.host"}, 'localhost');
		$this->assertEquals($config->development->{"database.username"}, 'jasmin');
		$this->assertEquals($config->development->{"database.password"}, 'secret');
	}

	public function testArrayAdapter(){
		$config = Config::read('test/config/data/config.php', 'array');
		$this->assertEquals($config->example->variable, 'value');
		$this->assertEquals($config->development->database->host, 'localhost');
		$this->assertEquals($config->development->database->username, 'jasmin');
		$this->assertEquals($config->development->database->password, 'secret');
	}

	public function testYamlAdapter(){
		$config = Config::read('test/config/data/config.yaml', 'yaml');
		$this->assertEquals($config->example->variable, 'value');
		$this->assertEquals($config->development->database->host, 'localhost');
		$this->assertEquals($config->development->database->username, 'jasmin');
		$this->assertEquals($config->development->database->password, 'secret');
	}


}

print "Probando Componente Config...\n";
PHPUnit::testClass("ConfigTest");
