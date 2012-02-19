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
 * @package		Config
 * @subpackage 	Adapters
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @license		New BSD License
 * @version 	$Id: Ini.php 88 2009-09-19 19:10:13Z gutierrezandresfelipe $
 */

/**
 * IniConfig
 *
 * Clase para la carga de archivos .ini
 *
 * @category	Kumbia
 * @package		Config
 * @subpackage 	Adapters
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @license		New BSD License
 * @access		public
 */
class IniConfig {

	/**
	 * Config soporta archivos INI estos son ampliamente usados por todo
	 * tipo de software que además son el formato predeterminado del
	 * framework. El adaptador procesa las secciones del archivo y
	 * variables compuestas. Gracias a que se usan funciones nativas
	 * del lenguaje su procesado es más rápido.
	 *
	 * @access 	public
	 * @param 	Config $config
	 * @param 	string $file
	 * @return 	Config
	 * @static
	 */
	public function read(Config $config, $file){
		$iniSettings = @parse_ini_file(Core::getFilePath($file), true);
		if($iniSettings==false){
			throw new ConfigException("El archivo de configuración '$file' tiene errores '$php_errormsg'");
		} else {
			foreach($iniSettings as $conf => $value){
				$config->$conf = new stdClass();
				foreach($value as $cf => $val){
					$config->$conf->$cf = $val;
				}
			}
		}
		return $config;
	}

}