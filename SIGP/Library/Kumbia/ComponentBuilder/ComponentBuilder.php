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
 * @package		ComponentBuilder
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @version 	$Id: ComponentBuilder.php 86 2009-09-13 21:45:31Z gutierrezandresfelipe $
 */

/**
 * ComponentBuilder
 *
 * Permite la creacion de componentes de aplicacion en forma dinamica
 *
 * @category	Kumbia
 * @package		ComponentBuilder
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2008-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @access		public
 * @abstract
 */
abstract class ComponentBuilder {

	/**
	 * Crea los archivos .INI por defecto de una aplicacion
	 *
	 * @param string $name
	 */
	private static function createINIFiles($name){
		$str = "; Usa este archivo para definir el enrutamiento estatico entre
; controladores y sus acciones
;
; Un controlador se puede enrutar a otro controlador utlizando '*' como
; comodin asi:
; controlador1/accion1/valor_id1  =  controlador2/accion2/valor_id2
;
; Ej:
; Enrutar cualquier peticion a posts/adicionar a posts/insertar/*
; posts/adicionar/* =	posts/insertar/*
;
; Enrutar cualquier peticion a cualquier controlador en la accion
; adicionar a posts/adicionar/*
; */adicionar/* =	posts/insertar/*

[routes]
;prueba/ruta1/* = prueba/ruta2/*
;prueba/ruta2/* = prueba/ruta3/*
";
		file_put_contents("apps/$name/config/routes.ini", $str);
		$str = "; Kumbia Enterprise Framework\n; Configuracion de Aplicaciones

; mode: Es el entorno en el que se esta trabajando que esta definido en /app-dir/config/config
; name: Es el nombre de la aplicacion

; debug: indica si la aplicacion se encuentra en modo debug,
; las excepciones generan mas informacion

; controllers_dir: Indica en que directorio se encuentran los controladores
; modelsDir: Indica en que directorio se encuentran los modelos
; viewsDir: Indica en que directorio se encuentran las vistas
; pluginsDir: Indica en que directorio se encuentran las vistas

; sessionAdapter: Nombre del adaptador de sesion usado
; sessionSaveHandler: Parametro save handler usado por el Session Adapter

; dbdate: Formato de Fecha por defecto de la Applicacion

[application]
mode = development
name = \"Project Name\"
dbdate = YYYY-MM-DD
debug = On
";
		file_put_contents("apps/$name/config/config.ini", $str);
		$str = "; Kumbia Enterprise Framework Configuration

; Parametros de base de datos
; Utiliza el nombre del controlador nativo en database.type (mysql, pgsql, oracle, informix)
; Colocar database.layer = \"pdo\" si se usa PHP Data Objects
; Colocar database.layer = \"jdbc\" si se usa JDBC

[development]
database.type = mysql
database.host = localhost
database.username = root
database.password =
database.name = development_db

[production]
database.type = mysql
database.host = localhost
database.username = root
database.password =
database.name = production_db

[test]
database.type = mysql
database.host = localhost
database.username = root
database.password =
database.name = test_db

";
		file_put_contents("apps/$name/config/environment.ini", $str);
		$str = "; Cargar los modulos de Kumbia en Library\n\n[modules]\nextensions = \"\"";
		file_put_contents("apps/$name/config/boot.ini", $str);
	}

	/**
	 * Crea el archivo ControllerBase por defecto
	 *
	 * @param string $name
	 */
	private static function createControllerBase($name){
		$str = "<?php

/**
 * Todas las controladores heredan de esta clase en un nivel superior
 * por lo tanto los metodos aqui definidos estan disponibles para
 * cualquier controlador.
 *
 * @category Kumbia
 * @package Controller
 * @access public
 **/
class ControllerBase {

	public function init(){
		Core::info();
	}

}

";
		file_put_contents("apps/$name/controllers/application.php", $str);
	}

	/**
	 * Crea el archivo modelbase por defecto
	 *
	 * @param string $name
	 */
	private static function createModelBase($name){
		$str = "<?php\n\n/**\n * ActiveRecord\n *\n * Esta clase es la clase padre de todos los modelos\n * de la aplicacion\n *\n * @category Kumbia\n * @package ActiveRecord\n */\nabstract class ActiveRecord extends ActiveRecordBase {\n\n}\n\n";
		file_put_contents("apps/$name/models/base/modelBase.php", $str);
	}

	/**
	 * Crea el archivo views/index.phtml por defecto
	 *
	 * @param string $name
	 */
	private static function createIndexView($name){
		$str = "<?php echo \"<?xml version=\\\"1.0\\\" encoding=\\\"UTF-8\\\"?>\\n\" ?>
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
 <head>
  <meta http-equiv='Content-type' content='text/html; charset=UTF-8' />
  <title>Application Title</title>
  <?php Tag::stylesheetLink('style', true) ?>
  <?php echo Tag::stylesheetLinkTags() ?>
  <?php echo Tag::javascriptLibrary('framework/scriptaculous/protoculous') ?>
  <?php echo Tag::javascriptBase() ?>
 </head>
 <body>
    <?php View::getContent(); ?>
 </body>
</html>
";
		file_put_contents("apps/$name/views/index.phtml", $str);
	}

	/**
	 * Devuelve el tipo PHP asociado
	 *
	 * @param string $type
	 * @return string
	 * @static
	 */
	public static function _getPHPType($type){
		if(stripos($type, 'int')!==false){
			return 'integer';
		}
		if(stripos($type, 'decimal')!==false){
			return 'double';
		}
		if(strtolower($type)=='date'){
			return 'Date';
		}
		return 'string';
	}

	/**
	 * Crea una aplicacion
	 *
	 * @param string $name
	 */
	public static function createApplication($name){
		if(file_exists("apps/$name")){
			throw new ComponentBuilderException("La aplicación '$name 'ya existe");
		}
		@mkdir("apps/$name");
		@mkdir("apps/$name/controllers");
		@mkdir("apps/$name/config");
		@mkdir("apps/$name/models");
		@mkdir("apps/$name/models/base");
		@mkdir("apps/$name/views");
		@mkdir("apps/$name/logs");
		@mkdir("apps/$name/views/layouts");
		self::createINIFiles($name);
		self::createModelBase($name);
		self::createIndexView($name);
		self::createControllerBase($name);
	}

	/**
	 * Crea un modelo
	 *
	 * @param DbBase $db
	 * @param string $modelsDir
	 * @param string $name
	 * @param string $schema
	 */
	public static function createModel($db, $modelsDir, $name, $schema='', $defineRelations=false){
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
				if($defineRelations==true){
					if(preg_match('/([a-zA-Z0-9\_]+)_id$/', $field['Field'], $matches)){
						$initialize[] = "\t\t\$this->belongsTo('{$matches[1]}');";
					}
				}
				$type = self::_getPHPType($field['Type']);
				$attributes[] = "\t/**\n\t * @var $type\n\t */\n\tprotected \${$field['Field']};\n";
				$setterName = Utils::camelize($field['Field']);
				$setters[] = "\t/**\n\t * Método para establecer el valor del campo {$field['Field']}\n\t * @param $type \${$field['Field']}\n\t */\n\tpublic function set$setterName(\${$field['Field']}){\n\t\t\$this->{$field['Field']} = \${$field['Field']};\n\t}\n";
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
		}
	}

}
