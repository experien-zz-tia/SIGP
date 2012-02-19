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
 * @package		Date
 * @subpackage	Format
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @version 	$Id: DateFormat.php 82 2009-09-13 21:06:31Z gutierrezandresfelipe $
 */

/**
 * DateFormat
 *
 * Esta clase es utilizada para aplicar formatos segun identificadores
 * UNICODE a objetos Fecha
 *
 * @category	Kumbia
 * @package		Date
 * @subpackage	Format
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 */
class DateFormat {

	/**
	 * Fecha formateada
	 *
	 * @var string
	 */
	private $_formatedDate;

	/**
	 * Constructor de DateFormat
	 *
	 * @param string $format
	 * @param Date $date
	 */
	public function __construct($format, Date $date){
		$token = "";
		$stoken = "";
		$st = false;
		$formatParts = array();
		$n = strlen($format);
		$tokens = array(
			'y', 'm', 'E', 'M', 'd', 'e', 'N', '\'',
			'D', 'L', 'z', 'r', 'c', 't', 'o', 'W',
		);
		$posibleTokens = array(
			'MMMM', 'MM', 'm','mm', 'M', 'EEEE', 'EEE',
			'DD', 'dd', 'z', 'd', 'EE', 'E',
			't', 'e', 'L', 'r', 'o', 'W', 'N',
			'yyy', 'yyyy', 'y'
			);
			$quote = false;
			for($i=0;$i<$n;++$i){
				$ch = substr($format, $i, 1);
				if(in_array($ch, $tokens)){
					if($ch!='\''){
						if($quote==false){
							if($token!=''){
								$someToken = false;
								foreach($posibleTokens as $stoken){
									#print substr($stoken, 0, strlen($token.$ch)).' '.$token.$ch.'<br>';
									if(substr($stoken, 0, strlen($token.$ch))==$token.$ch){
										$someToken = true;
										break;
									}
								}
								if($someToken==false){
									$formatParts[] = $token;
									$token = $ch;
								} else {
									$token.=$ch;
								}
							} else {
								$token.=$ch;
							}
						} else {
							$token.=$ch;
						}
					} else {
						if($quote==false){
							$quote = true;
						} else {
							$quote = false;
						}
					}
				} else {
					$formatParts[] = $token;
					$formatParts[] = $ch;
					$token = '';
				}
			}
			$formatParts[] = $token;
			$toReplace = array();
			$defaultPart = false;
			#print_r($formatParts);
			foreach($formatParts as $formatPart){
				switch($formatPart){
					case 'MMMM':
						$this->_formatedDate.=$date->getMonthName();
						break;
					case 'MM':
					case 'm':
					case 'mm':
						$this->_formatedDate.=sprintf('%02s', $date->getMonth());
						break;
					case 'M':
						$this->_formatedDate.=$date->getMonth();
						break;
					case 'EEEE':
						$this->_formatedDate.=$date->getDayOfWeek();
						break;
					case 'EEE':
						$this->_formatedDate.=$date->getAbrevDayOfWeek();
						break;
					case 'DD':
						$this->_formatedDate.=$date->getDayOfYear();
						break;
					case 'z':
						$this->_formatedDate.=$date->getDayOfYear();
						break;
					case 'dd':
						$this->_formatedDate.=sprintf('%02s', $date->getDay());
						break;
					case 'd':
						$this->_formatedDate.=$date->getDay();
						break;
					case 'yyyy':
					case 'y':
						$this->_formatedDate.=$date->getYear();
						break;
					case 'yy':
						$this->_formatedDate.=$date->getShortYear();
						break;
					case 'EE':
					case 'E':
					case 'N':
						$this->_formatedDate.=$date->getDayNumberOfWeek();
						break;
					case 'L':
						$this->_formatedDate.=($date->isLeapYear() ? 1 : 0);
						break;
					case 'r':
						$this->_formatedDate.=$date->getRFC2822Date();
						break;
					case 'c':
						$this->_formatedDate.=$date->getISO8601Date();
						break;
					case 't':
						$this->_formatedDate.=$date->getTimestamp();
						break;
					case 'e':
						$this->_formatedDate.=$date->getTimezone();
						break;
					default:
						$this->_formatedDate.=$formatPart;
						$defaultPart = true;
				}
			}
	}

	/**
	 * Devuelve la fecha formateada
	 *
	 * @return string
	 */
	public function getDate(){
		return $this->_formatedDate;
	}

}
