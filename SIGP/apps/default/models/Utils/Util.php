<?php

/**
 * Clase utilitaria
 * @author Robert A
 * @package Utils
 *
 */
class Util extends ActiveRecord{

	
	/**
	 * Cambia el formato de la fecha Y-m-d a m/d/Y
	 * @param string $fecha 0000-00-00
	 * @return string
	 */
	static  function cambiarFechaMDY($fecha){
		ereg( "([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $fecha, $mifecha);
		$lafecha=$mifecha[2]."/".$mifecha[3]."/".$mifecha[1];
		return $lafecha;
	}
	static function cambiarFechaMDYtoYMD($fecha,$separador){
		$fechaExplode = explode($separador, $fecha);
		$lafecha = date("Y/m/d", mktime(0,0,0,$fechaExplode[1], $fechaExplode[0], $fechaExplode[2]));

		return $lafecha;
	}


	/**
	 * Cambia el formato de la fecha Y-m-d a d/m/Y
	 * @param string $fecha 0000-00-00
	 * @return string
	 */
	static function cambiarFechaDMY($fecha){
		ereg( "([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $fecha, $mifecha);
		$lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1];
		return $lafecha;
	}


	static function cambiarFechaMYSQL($fecha){
		ereg( "([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha, $mifecha);
		$lafecha=$mifecha[3]."-".$mifecha[2]."-".$mifecha[1];
		return $lafecha;
	}


}