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

class FilterTest extends PHPUnitTestCase {

	/**
	 * Filtro de alfanumericos
	 *
	 */
	public function testAlnum(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('prueba191.@dk.c', 'alnum'), 'prueba191dkc');
	}

	/**
	 * Filtro de alphabeticos
	 *
	 */
	public function testAlpha(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('prueba191.@dk.c', 'alpha'), 'prueba191dkc');
	}

	/**
	 * Filtro de fechas (1)
	 *
	 */
	public function testDate1(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('.2008-02-17.', 'date'), '2008-02-17');
	}

	/**
	 * Filtro de fechas (2)
	 *
	 */
	public function testDate2(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('#2008/02/17#', 'date'), '2008/02/17');
	}

	/**
	 * Filtro de digitos
	 *
	 */
	public function testDigits(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('a1b2c3d4.c5d6e7', 'digits'), '1234567');
	}

	/**
	 * Filtro para numeros de precision normal (1)
	 *
	 */
	public function testDouble1(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter(1.250, 'double'), 1.250);
	}

	/**
	 * Filtro para numeros de precision normal (2)
	 *
	 */
	public function testDouble2(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('prueba', 'double'), 0.0);
	}

	/**
	 * Filtro para e-mail (1)
	 *
	 */
	public function testEmail1(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('prueba', 'email'), '');
	}

	/**
	 * Filtro para e-mail (2)
	 *
	 */
	public function testEmail2(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('prueba@prueba', 'email'), 'prueba@prueba');
	}

	/**
	 * Filtro para e-mail (3)
	 *
	 */
	public function testEmail3(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('$prueba@prueba#', 'email'), 'prueba@prueba');
	}

	/**
	 * Filtro para e-mail (4)
	 *
	 */
	public function testEmail4(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('/laura.acuna@prueba.com#', 'email'), 'laura.acuna@prueba.com');
	}

	/**
	 * Filtro para e-mail (5)
	 *
	 */
	public function testEmail5(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('/laura_acuna@prueba.com#', 'email'), 'laura_acuna@prueba.com');
	}

	/**
	 * Filtro para e-mail (6)
	 *
	 */
	public function testEmail6(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('/laura-acuna@prueba.com.co#', 'email'), 'laura-acuna@prueba.com.co');
	}

	/**
	 * Filtro para e-mail (7)
	 *
	 */
	public function testEmail7(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('/laura-acuna1a90+prueba12.com.co#', 'email'), 'laura-acuna1a90+prueba12.com.co');
	}

	/**
	 * Filtro para espacios extra
	 *
	 */
	public function testExtraspaces(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter(' prueba prueba ', 'extraspaces'), 'prueba prueba');
	}

	/**
	 * Filtro para numeros de precision normal (3)
	 *
	 */
	public function testDouble3(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter(1.250, 'double'), 1.250);
	}

	/**
	 * Filtro para numeros de precision normal (4)
	 *
	 */
	public function testDouble4(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('prueba', 'double'), 0.0);
	}

	/**
	 * Filtro para entidades HTML
	 *
	 */
	public function testHtmlEntities(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('<>áéíñ', 'htmlentities'), '&lt;&gt;&aacute;&eacute;&iacute;&ntilde;');
	}

	/**
	 * Filtro para decodificar entidades HTML
	 *
	 */
	public function testHtmlDecode(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('&lt;&gt;&aacute;&eacute;&iacute;&ntilde;', 'htmldecode'), '<>áéíñ');
	}

	/**
	 * Filtro para identificadores (1)
	 *
	 */
	public function testIdentifier1(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('%#anthemfor192&?', 'identifier'), 'anthemfor192');
	}

	/**
	 * Filtro para identificadores (2)
	 *
	 */
	public function testIdentifier2(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('%#_anthemfor192&?', 'identifier'), '_anthemfor192');
	}

	/**
	 * Filtro para enteros (1)
	 *
	 */
	public function testInteger1(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter(1.2030, 'int'), 1);
	}

	/**
	 * Filtro para enteros (2)
	 *
	 */
	public function testInteger2(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('prueba', 'int'), 0);
	}

	/**
	 * Filtro para IPv4 (1)
	 *
	 */
	public function testIPv41(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('/192.168.0.1%&', 'ipv4'), '192.168.0.1');
	}

	/**
	 * Filtro para IPv4 (2)
	 *
	 */
	public function testIPv42(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('/ab.cd.ed.fg%&', 'ipv4'), '');
	}

	/**
	 * Filtro para Locale (1)
	 *
	 */
	public function testLocale1(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('?ab_CD#', 'locale'), 'ab_CD');
	}

	/**
	 * Filtro para Locale (2)
	 *
	 */
	public function testLocale2(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('ab', 'locale'), 'ab');
	}

	/**
	 * Filtro para minúsculas
	 *
	 */
	public function testLower(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('ÁÉÑLOWER', 'lower'), 'áéñlower');
	}

	/**
	 * Filtro para mayúsculas
	 *
	 */
	public function testUpper(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('upperáéñ', 'upper'), 'UPPERÁÉÑ');
	}

	/**
	 * Filtro para valores numéricos (1)
	 *
	 */
	public function testNumeric1(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('&%1.201x?', 'numeric'), '1.201');
	}

	/**
	 * Filtro para valores numéricos (2)
	 *
	 */
	public function testNumeric2(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('&%1.201x?8171', 'numeric'), '1.2018171');
	}

	/**
	 * Filtro para escapar comillas
	 *
	 */
	public function testAddSlaches(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('"andres" AND \'laura\'', 'addslaches'), '\"andres\" AND \\\'laura\\\'');
	}

	/**
	 * Filtro para escapar comillas
	 *
	 */
	public function testAddSlaches(){
		$filter = new Filter();
		$this->assertEquals($filter->applyFilter('"andres" AND \'laura\'', 'addslaches'), '\"andres\" AND \\\'laura\\\'');
	}

}

print "Probando Componente Filter...\n";
PHPUnit::testClass("FilterTest");
