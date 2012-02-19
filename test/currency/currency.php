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
 */

require 'Library/Kumbia/Currency/Currency.php';
require 'Library/Kumbia/Core/ClassPath/CoreClassPath.php';
require 'Library/Kumbia/Autoload.php';

class CurrencyTest extends PHPUnitTestCase {

	public function __construct(){
		LocaleMath::enableBcMath();
	}

	public function testMoneyToWords(){
		$this->assertEquals(Currency::moneyToWords(1, 'PESOS', 'CENTAVOS'), 'UN PESOS');
		$this->assertEquals(Currency::moneyToWords(2, 'PESOS', 'CENTAVOS'), 'DOS PESOS');
	}

	public function testMoneyToWordsDec(){
		$this->assertEquals(Currency::moneyToWords(21, 'PESOS', 'CENTAVOS'), 'VENTIUN PESOS');
		$this->assertEquals(Currency::moneyToWords(90, 'PESOS', 'CENTAVOS'), 'NOVENTA PESOS');
	}

	public function testMoneyToWordsHun(){
		$this->assertEquals(Currency::moneyToWords(677, 'PESOS', 'CENTAVOS'), 'SEICIENTOS SETENTA Y SIETE PESOS');
		$this->assertEquals(Currency::moneyToWords(581, 'PESOS', 'CENTAVOS'), 'QUINIENTOS OCHENTA Y UN PESOS');
	}

	public function testMoneyToWordsTho(){
		$this->assertEquals(Currency::moneyToWords(1250, 'PESOS', 'CENTAVOS'), 'MILDOSCIENTOS CINCUENTA PESOS');
		$this->assertEquals(Currency::moneyToWords(3482, 'PESOS', 'CENTAVOS'), 'TRESMIL CUATROCIENTOS OCHENTA Y DOS PESOS');
		$this->assertEquals(Currency::moneyToWords(180001, 'PESOS', 'CENTAVOS'), 'CIENTO OCHENTAMIL UN PESOS');
		$this->assertEquals(Currency::moneyToWords(222222, 'PESOS', 'CENTAVOS'), 'DOSCIENTOS VENTIDOSMIL DOSCIENTOS VENTIDOS PESOS');
	}

	public function testMoneyToWordsMil(){
		$this->assertEquals(Currency::moneyToWords("1003482", 'PESOS', 'CENTAVOS'), 'UN MILLON TRESMIL CUATROCIENTOS OCHENTA Y DOS PESOS');
		$this->assertEquals(Currency::moneyToWords("180000000", 'PESOS', 'CENTAVOS'), 'CIENTO OCHENTA MILLONES DE PESOS');
		$this->assertEquals(Currency::moneyToWords("876543211", 'PESOS', 'CENTAVOS'), 'OCHOCIENTOS SETENTA Y SEIS MILLONES QUINIENTOS CUARENTA Y TRESMIL DOSCIENTOS ONCE PESOS');
	}

	public function testMoneyToWordsMMil(){
		$this->assertEquals(Currency::moneyToWords("1250000000", 'PESOS', 'CENTAVOS'), 'MILDOSCIENTOS CINCUENTA MILLONES DE PESOS');
		$this->assertEquals(Currency::moneyToWords("99999999999", 'PESOS', 'CENTAVOS'), 'NOVENTA Y NUEVEMIL NOVECIENTOS NOVENTA Y NUEVE MILLONES NOVECIENTOS NOVENTA Y NUEVEMIL NOVECIENTOS NOVENTA Y NUEVE PESOS');
	}

}

print "Probando Componente Currency...\n";
PHPUnit::testClass("CurrencyTest");
