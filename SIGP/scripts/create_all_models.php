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

/**
 * Permite crear todos los modelos de una aplicacion por linea de comandos
 *
 * @category Kumbia
 * @package Scripts
 * @copyright Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license New BSD License
 */
class CreateAllModels extends Script {

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
			'application=s' => "--application nombre \tNombre de la aplicación [opcional]",
			'force' => "--force \t\tForza a que se reescriba los modelos existentes [opcional]",
			'define-relations' => "--define-relations \t\tDefine posibles relaciones existentes de acuerdo a convenciones [opcional]",
			'help' => "--help \t\t\tVisualiza esta ayuda"
			);

			$this->parseParameters($posibleParameters);

			if($this->isReceivedOption('help')){
				$this->showHelp($posibleParameters);
				return;
			}

			$application = $this->getOption('application');
			if(!$application){
				$application = 'default';
			}
			Router::setActiveApplication($application);
			Core::reloadMVCLocations();

			$modelsDir = Core::getActiveModelsDir();
			if(!DbLoader::loadDriver()){
				throw new DbException("No se puede conectar a la base de datos");
			}
			$forceProcess = $this->isReceivedOption('force');
			$defineRelations = $this->isReceivedOption('define-relations');
			$db = DbBase::rawConnect();
			foreach($db->listTables() as $name){
				if($db->tableExists($name)){
					if(!file_exists($modelsDir.'/'.$name.'.php')||$forceProcess){
						$fields = $db->describeTable($name);
						$attributes = array();
						$setters = array();
						$getters = array();
						$initialize = array();
						foreach($fields as $field){
							if($defineRelations==true){
								if(preg_match('/([a-zA-Z0-9_]+)_id$/', $field['Field'], $matches)){
									$initialize[] = "\t\t\$this->belongsTo('{$matches[1]}');";
								}
							}
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
							$initCode = "\n\t/**\n\t * Método inicializador de la Entidad\n\t */\n\tprotected function initialize(){\t\t\n".join("\n", $initialize)."\n\t}\n";
						} else {
							$initCode = "";
						}
						$code = "<?php\n\nclass ".Utils::camelize($name)." extends ActiveRecord {\n\n".join("\n", $attributes)."\n\n".join("\n", $setters)."\n\n".join("\n", $getters)."$initCode\n}\n\n";
						file_put_contents("$modelsDir/$name.php", $code);
					} else {
						print "INFO: Saltando el modelo \"$name\" ya que el archivo de modelo ya existe\n";
					}
				} else {
					throw new ScriptException("No existe la tabla '$name'");
				}
			}
	}

}

try {
	$script = new CreateAllModels();
}
catch(CoreException $e){
	print get_class($e)." : ".$e->getConsoleMessage()."\n";
}
catch(Exception $e){
	print "Exception : ".$e->getMessage()."\n";
}
