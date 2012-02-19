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
 * to license@loudertechnology.com so we can send you a copy immediately.
 *
 * @category	Kumbia
 * @package		Currency
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @version 	$Id: Currency.php 82 2009-09-13 21:06:31Z gutierrezandresfelipe $
 */

/**
 * Currency
 *
 * El objetivo de este componente es proporcionar facilidades al desarrollador
 * para trabajar con cantidades numéricas relacionadas con dinero y monedas,
 * su representación de acuerdo a la localización activa y la generación
 * de montos en letras en diferentes idiomas.
 *
 * @category	Kumbia
 * @package		Currency
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @access		public
 */
class Currency {

	/**
	 * Valor a convertir
	 *
	 * @var integer
	 */
	private static $_value = 0;

	/**
	 * Estado de la conversion
	 *
	 * @var string
	 */
	private static $_state = '';

	/**
	 * Formateador de cantidades
	 *
	 * @var CurrencyFormat
	 */
	private static $_currencyFormater;

	/**
	 * Formateador cuando no se usa estaticamente
	 *
	 * @var unknown_type
	 */
	private $_currencyFormat;

	/**
	 * Datos de la moneda usada actualmente
	 *
	 * @var array
	 */
	private $_currencyData;

	/**
	 * Localizacion
	 *
	 * @var Locale
	 */
	private $_locale;

	/**
	 * Constructor de Currency
	 *
	 * @param Locale $locale
	 */
	public function __construct(Locale $locale=null){
		if($locale==null){
			$locale = Locale::getApplication();
		} else {
			$this->_locale = $locale;
		}
	}

	/**
	 * Establece/Cambia la localizacion
	 *
	 * @access 	public
	 * @param	Locale $locale
	 */
	public function setLocale(Locale $locale){
		$this->_locale = $locale;
		$pattern = $this->_locale->getCurrencyFormat();
		$formater = $this->_getFormater();
		$formater->setPattern($pattern);
		$this->_currencyData = null;
	}

	/**
	 * Crea/Obtiene el formateador de monedas
	 *
	 * @access	public
	 * @return	CurrencyFormat
	 */
	private function _getFormater(){
		if($this->_currencyFormat==null){
			$pattern = $this->_locale->getCurrencyFormat();
			$this->_currencyFormat = new CurrencyFormat($pattern);
		}
		return $this->_currencyFormat;
	}

	/**
	 * Obtiene una cantidad formateada de acuerdo a la localizacion interna
	 *
	 * @param 	double $quantity
	 * @param 	string $format
	 * @param 	string $codeISO
	 * @return 	string
	 */
	public function getMoney($quantity, $format='', $codeISO=''){
		$formater = $this->_getFormater();
		$formater->toCurrency($quantity);
		if($format==null){
			return $formater->getQuantity();
		} else {
			$quantity = $formater->getQuantity();
			return $this->_applyFormat($format, $quantity, $codeISO);
		}
	}

	/**
	 * Aplica el formato a la salida
	 *
	 * @param	string $format
	 * @param	int $quantity
	 * @param	string $codeISO
	 * @return	string
	 */
	private function _applyFormat($format, $quantity, $codeISO){
		if(strpos($format, '%symbol%')!==false){
			$format = str_replace('%symbol%', $this->getMoneySymbol($codeISO), $format);
		}
		if(strpos($format, '%displayName%')!==false){
			$format = str_replace('%displayName%', $this->getMoneyDisplayName($codeISO), $format);
		}
		if(strpos($format, '%name%')!==false){
			$format = str_replace('%name%', $this->getMoneyISOCode($codeISO), $format);
		}
		return str_replace('%quantity%', $quantity, $format);
	}

	/**
	 * Obtiene el simbolo de la moneda utilizada
	 *
	 * @param 	string $codeISO
	 * @return 	string
	 */
	public function getMoneySymbol($codeISO=''){
		$currency = $this->getCurrency($codeISO);
		return $currency['symbol'];
	}

	/**
	 * Obtiene el nombre de la moneda utilizada
	 *
	 * @param 	string $codeISO
	 * @param	string $type
	 * @return	string
	 */
	public function getMoneyDisplayName($codeISO='', $type=''){
		$currency = $this->getCurrency($codeISO, $type);
		return $currency['displayName'];
	}

	/**
	 * Obtiene el codigo ISO de la moneda utilizada
	 *
	 * @param	string $codeISO
	 * @return	string
	 */
	public function getMoneyISOCode($codeISO=''){
		$currency = $this->getCurrency($codeISO);
		return $currency['name'];
	}

	/**
	 * Obtiene el simbolo y nombre de la moneda especificada
	 *
	 * @param	string $codeISO
	 * @param	string $displayType
	 * @return	string
	 */
	public function getCurrency($codeISO='', $displayType=''){
		if($codeISO){
			return $this->_locale->getCurrency($codeISO, $displayType);
		} else {
			if(!$this->_currencyData){
				$this->_currencyData = $this->_locale->getCurrency(null, $displayType);
			}
			return $this->_currencyData;
		}
	}

	/**
	 * Devuelve una cantidad monetaria formateada
	 *
	 * @access 	public
	 * @param	double $quantity
	 * @return	string
	 */
	public static function money($quantity){
		if(self::$_currencyFormater==null){
			$locale = Locale::getApplication();
			$pattern = $locale->getCurrencyFormat();
			self::$_currencyFormater = new CurrencyFormat($pattern, $quantity);
		} else {
			self::$_currencyFormater->toCurrency($quantity);
		}
		return self::$_currencyFormater->getQuantity();
	}

	/**
	 * Devuelve una cantidad numerica formateada
	 *
	 * @access 	public
	 * @param 	string $quantity
	 * @static
	 */
	public static function number($quantity){
		if(self::$_currencyFormater==null){
			$locale = Locale::getApplication();
			$pattern = $locale->getNumericFormat();
			self::$_currencyFormater = new CurrencyFormat($pattern, $quantity);
		} else {
			self::$_currencyFormater->toNumeric($quantity);
		}
		return self::$_currencyFormater->getQuantity();
	}

	/**
	 * Resetea el formateador interno cuando cambia la localización
	 *
	 * @access public
	 * @static
	 */
	public static function resetFormater(){
		self::$_currencyFormater = null;
	}

	/**
	 * Obtiene una cantidad en letras
	 *
	 * @param double $quantity
	 */
	public function getMoneyAsText($quantity){
		if($quantity==1){
			$displayType = 'one';
		} else {
			$displayType = 'other';
		}
		return self::moneyToWords($quantity, $this->getMoneyDisplayName(null, $displayType), 'CENTAVOS');
	}

	/**
	 * Las siguientes funciones son utilizadas para la generación
	 * de versiones escritas de numeros
	 *
	 * @param numeric $a
	 * @return string
	 * @static
	 */
	private static function valueNumber($a){
		if($a<=21){
			switch ($a){
				case 1:
					if(self::$_state=='DEC'||self::$_state==''){
						return 'UN';
					} else {
						return 'UNO';
					}
				case 2: return 'DOS';
				case 3: return 'TRES';
				case 4: return 'CUATRO';
				case 5: return 'CINCO';
				case 6: return 'SEIS';
				case 7: return 'SIETE';
				case 8: return 'OCHO';
				case 9: return 'NUEVE';
				case 10: return 'DIEZ';
				case 11: return 'ONCE';
				case 12: return 'DOCE';
				case 13: return 'TRECE';
				case 14: return 'CATORCE';
				case 15: return 'QUINCE';
				case 16: return 'DIECISEIS';
				case 17: return 'DIECISIETE';
				case 18: return 'DIECIOCHO';
				case 19: return 'DIECINUEVE';
				case 20: return 'VEINTE';
				case 21:
					if(self::$_state==''){
						return 'VENTIUNO';
					} else {
						return 'VENTIUN';
					}
			}
		} else {
			if($a<=99){
				self::$_state = 'DEC';
				if($a>=22&&$a<=29){
					return 'VENTI'.self::valueNumber($a % 10);
				}
				if($a==30){
					return  'TREINTA';
				}
				if($a>=31&&$a<=39){
					return 'TREINTA Y '.self::valueNumber($a % 10);
				}
				if($a==40){
					return 'CUARENTA';
				}
				if($a>=41&&$a<=49){
					return 'CUARENTA Y '.self::valueNumber($a % 10);
				}
				if($a==50){
					return 'CINCUENTA';
				}
				if($a>=51&&$a<=59){
					return 'CINCUENTA Y '.self::valueNumber($a % 10);
				}
				if($a==60){
					return 'SESENTA';
				}
				if($a>=61&&$a<=69){
					return 'SESENTA Y '.self::valueNumber($a % 10);
				}
				if($a==70) {
					return 'SETENTA';
				}
				if($a>=71&&$a<=79){
					return 'SETENTA Y '.self::valueNumber($a % 10);
				}
				if($a==80){
					return 'OCHENTA';
				}
				if($a>=81&&$a<=89){
					return 'OCHENTA Y '.self::valueNumber($a % 10);
				}
				if($a==90){
					return 'NOVENTA';
				}
				if($a>=91&&$a<=99){
					return 'NOVENTA Y '.self::valueNumber($a % 10);
				}
			} else {
				self::$_state = 'CEN';
				if($a==100){
					return 'CIEN';
				}
				if($a>=101&&$a<=199){
					return 'CIENTO '.self::valueNumber($a % 100);
				}
				if($a>=200&&$a<=299){
					return 'DOSCIENTOS '.self::valueNumber($a % 100);
				}
				if($a>=300&&$a<=399){
					return 'TRECIENTOS '.self::valueNumber($a % 100);
				}
				if($a>=400&&$a<=499){
					return 'CUATROCIENTOS '.self::valueNumber($a % 100);
				}
				if($a>=500&&$a<=599){
					return 'QUINIENTOS '.self::valueNumber($a % 100);
				}
				if($a>=600&&$a<=699){
					return 'SEICIENTOS '.self::valueNumber($a % 100);
				}
				if($a>=700&&$a<=799){
					return 'SETECIENTOS '.self::valueNumber($a % 100);
				}
				if($a>=800&&$a<=899){
					return 'OCHOCIENTOS '.self::valueNumber($a % 100);
				}
				if($a>=901&&$a<=999){
					return 'NOVECIENTOS '.self::valueNumber($a % 100);
				}
			}
		}
	}

	/**
	 * Genera una cadena de millones
	 *
	 * @param double $a
	 * @return string
	 * @static
	 */
	private static function millons($number){
		self::$_state = 'MILL';
		$number = LocaleMath::div($number, '1000000');
		if($number==1){
			return 'UN MILLON ';
		} else {
			if(LocaleMath::cmp($number, '1000')>=0){
				$mod = LocaleMath::mod($number, '1000');
				$value = self::miles(LocaleMath::sub($number, $mod));
				if($mod>0){
					$value.= self::valueNumber($mod);
				}
				$value.=' MILLONES ';
			} else {
				$value = self::valueNumber($number).' MILLONES ';
			}
			self::$_state = 'MILL';
			return $value;
		}
	}

	/**
	 * Genera una cadena de miles
	 *
	 * @param double $a
	 * @return string
	 * @static
	 */
	private static function miles($number){
		self::$_state = 'MIL';
		$number = LocaleMath::div($number, '1000');
		if($number==1){
			return 'MIL';
		} else {
			return self::valueNumber($number).'MIL ';
		}
	}

	/**
	 * Escribe en letras un monto numerico
	 *
	 * @param numeric $valor
	 * @param string $moneda
	 * @param string $centavos
	 * @return string
	 * @static
	 */
	static public function moneyToWords($valor, $moneda, $centavos){
		self::$_value = $valor;
		$a = $valor;
		$p = $moneda;
		$c = $centavos;
		$val = '';
		$v = $a;
		$a = LocaleMath::round($a, 0);
		$d = (float) LocaleMath::round($v-$a, 2);
		if(LocaleMath::cmp($a, '1000000')>=0){
			$mod = LocaleMath::mod($a, '1000000');
			$val.= self::millons(LocaleMath::sub($a, $mod));
			$a = $mod;
		}
		if(LocaleMath::cmp($a, '1000')>=0){
			$mod = LocaleMath::mod($a, '1000');
			$val.= self::miles(LocaleMath::sub($a, $mod));
			$a = $mod;
		}
		$rval = self::valueNumber($a);
		if($rval==''){
			if(in_array(self::$_state, array('MILL', 'MMILL'))){
				$val.= 'DE '.strtoupper($p).' ';
			} else {
				$val.= strtoupper($p).' ';
			}
		} else {
			$val.= $rval.' '.strtoupper($p).' ';
		}
		if($d>0){
			$d*=100;
			$val.= ' CON '.self::valueNumber($d).' $c ';
		}
		return trim($val);
	}

}
