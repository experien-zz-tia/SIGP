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
 * @license New BSD License
 */

require "Library/Kumbia/Exception.php";
require "Library/Kumbia/Kumbia.php";
require "Library/Kumbia/PHPUnit/PHPUnit.php";
require "Library/Kumbia/Acl/Acl.php";

class AclTestMemory extends PHPUnitTest {

	/**
	 * Acl List
	 *
	 * @var Acl
	 */
	private $_acl;

	public function __construct(){
		$this->_acl = new Acl();
	}

	public function testCreateAclObject(){
		$this->assertInstanceOf($this->_acl, "Acl");
	}

	public function testAddRoleToList(){
		$this->_acl->addRole(new AclRole("Administradores", "Administran el sistema"));
		$this->assertEquals($this->_acl->isRole("Administradores"), true);
	}

	public function testAddResourceToList(){
		$this->_acl->addResource(new AclResource("Clientes", "Modulo de Clientes"));
		$this->assertEquals($this->_acl->isResource("Clientes"), true);
	}

	public function testAddResourceToListWithOperations(){
		$this->_acl->addResource(new AclResource("Facturas", "Modulo de Facturas"), array('nuevo', 'consultar'));
		$this->assertEquals($this->_acl->isResource("Facturas"), true);
	}

	public function testAddResourceToListWithSingleOperation(){
		$this->_acl->addResource(new AclResource("Pedidos", "Modulo de Pedidos"), 'consultar');
		$this->assertEquals($this->_acl->isResource("Pedidos"), true);
	}

}

print "Probando Componente Acl con adaptador Memory...\n";
PHPUnit::testClass("AclTestMemory");

?>
