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
 * @version 	$Id: create_model.php 31 2009-05-02 21:59:07Z gutierrezandresfelipe $
 */

require 'Library/Kumbia/Core/ClassPath/CoreClassPath.php';
require 'Library/Kumbia/Autoload.php';

/**
 * CreateModel
 *
 * Permite crear un modelo por linea de comandos
 *
 * @category 	Kumbia
 * @package 	Scripts
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license 	New BSD License
 * @version 	$Id: create_model.php 31 2009-05-02 21:59:07Z gutierrezandresfelipe $
 */
class CreateModel extends Script {

	/**
	 * Devuelve el tipo PHP asociado
	 *
	 * @param string $type
	 * @return string
	 */
	public function getPHPType($type){
		if(stripos($type, 'int')!==false){
			return 'integer';
		}
		if(stripos($type, 'int')!==false){
			return 'integer';
		}
		if(strtolower($type)=='date'){
			return 'Date';
		}
		return 'string';
	}

	public function __construct(){

		$posibleParameters = array(
			'table-name=s' => '--table-name nombre \t\tNombre de la tabla source del modelo',
			'schema=s' => '--schema nombre \tNombre del schema donde est&aacute; la tabla si este difiere del schema por defecto [opcional]',
			'application=s' => '--application nombre \tNombre de la aplicaci&oacute;n [opcional]',
			'force' => '--force \t\tForza a que se reescriba el modelo [opcional]',
			'help' => '--help \t\t\tMuestra esta ayuda'
			);

			$this->parseParameters($posibleParameters);

			if($this->isReceivedOption('help')){
				$this->showHelp($posibleParameters);
				return;
			}

			$this->checkRequired(array('table-name'));

			$name = $this->getOption('table-name');
			$application = $this->getOption('application');
			$schema = $this->getOption('schema');
			if(!$application){
				$application = 'default';
			}
			Router::setActiveApplication($application);
			Core::reloadMVCLocations();
			if($name){
				$modelsDir = Core::getActiveModelsDir();
				if(!$this->isReceivedOption('force')){
					if(file_exists("$modelsDir/$name.php")){
						throw new ScriptException("El archivo del modelo '$name.php' ya existe en el directorio de modelos");
					}
				}
				if(!DbLoader::loadDriver()){
					throw new DbException("No se puede conectar a la base de datos");
				}
				$db = DbBase::rawConnect();
				$initialize = array();
				if($schema){
					$initialize[] = "\t\t\$this->setSchema(\"$schema\");";
				}
				$table = $name;
				if($db->tableExists($table, $schema)){
					$fields = $db->describeTable($name, $schema);
					$attributes = array();
					$setters = array();
					$getters = array();
					foreach($fields as $field){
						$type = $this->getPHPType($field['Type']);
						$attributes[] = "\t/**\n\t * @var $type\n\t */\n\tprotected \${$field['Field']};\n";
						$setterName = Utils::camelize($field['Field']);
						$setters[] = "\t/**\n\t * Metodo para establecer el valor del campo {$field['Field']}\n\t * @param $type \${$field['Field']}\n\t */\n\tpublic function set$setterName(\${$field['Field']}){\n\t\t\$this->{$field['Field']} = \${$field['Field']};\n\t}\n";
						if($type=="Date"){
							$getters[] = "\t/**\n\t * Devuelve el valor del campo {$field['Field']}\n\t * @return $type\n\t */\n\tpublic function get$setterName(){\n\t\treturn new Date(\$this->{$field['Field']});\n\t}\n";
						} else {
							$getters[] = "\t/**\n\t * Devuelve el valor del campo {$field['Field']}\n\t * @return $type\n\t */\n\tpublic function get$setterName(){\n\t\treturn \$this->{$field['Field']};\n\t}\n";
						}
					}
					if(count($initialize)>0){
						$initCode = "\n\t/**\n\t * Metodo inicializador de la Entidad\n\t */\n\tprotected function initialize(){\t\t\n".join(";\n", $initialize)."\n\t}\n";
					} else {
						$initCode = "";
					}
					$code = "<?php\n\nclass ".Utils::camelize($name)." extends ActiveRecord {\n\n".join("\n", $attributes)."\n\n".join("\n", $setters)."\n\n".join("\n", $getters)."$initCode\n}\n\n";
					file_put_contents("$modelsDir/$name.php", $code);
				} else {
					throw new ScriptException("No existe la tabla $table");
				}
			} else {
				throw new ScriptException("Debe indicar el nombre del modelo");
			}
	}

}

try {
	$script = new CreateModel();
}
catch(CoreException $e){
	print get_class($e)." : ".$e->getConsoleMessage()."\n";
}
catch(Exception $e){
	print "Exception : ".$e->getMessage()."\n";
}
