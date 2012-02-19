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
 * @package		Core
 * @subpackage	CoreClassPath
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @version 	$Id: CoreClassPath.php 111 2009-10-23 20:57:52Z gutierrezandresfelipe $
 */

/**
 * CoreClassPath
 *
 * Mantiene un directorio de rutas a las clases del framework de tal
 * forma que se pueda realizar la inyección de dependencia en la
 * aplicación cuando sean requeridos.
 *
 * @category	Kumbia
 * @package		Core
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @abstract
 */
abstract class CoreClassPath {

	/**
	 * Directorio absoluto al Framework
	 *
	 * @var string
	 */
	static private $_dirName = '';

	/**
	 * Indica si se ha inicializado el directorio absoluto
	 *
	 * @var boolean
	 */
	static private $_iniDirName = false;

	/**
	 * Directorio de Recursos
	 *
	 * @var array
	 */
	static private $_classPath = array(
		'Acl' => 'Acl/Acl',
		'AclException' => 'Acl/AclException',
		'AclRoleException' => 'Acl/Role/AclRoleException',
		'AclResourceException' => 'Acl/Role/AclResourceException',
		'ActiveRecordBase' => 'ActiveRecord/Base/ActiveRecordBase',
		'ActiveRecordCriteria' => 'ActiveRecord/Criteria/ActiveRecordCriteria',
		'ActiveRecordJoin' => 'ActiveRecord/Join/ActiveRecordJoin',
		'ActiveRecordGenerator' => 'ActiveRecord/Generator/ActiveRecordGenerator',
		'ActiveRecordException' => 'ActiveRecord/ActiveRecordException',
		'ActiveRecordMessage' => 'ActiveRecord/Message/ActiveRecordMessage',
		'ActiveRecordMetaData' => 'ActiveRecord/MetaData/ActiveRecordMetaData',
		'ActiveRecordMetaDataException' => 'ActiveRecord/MetaData/ActiveRecordMetadataException',
		'ActiveRecordResultset' => 'ActiveRecord/Resultset/ActiveRecordResultset',
		'ActiveRecordRow' => 'ActiveRecord/Row/ActiveRecordRow',
		'ActiveRecordTransaction' => 'ActiveRecord/Transaction/ActiveRecordTransaction',
		'ActiveRecordTransactionException' => 'ActiveRecord/Transaction/ActiveRecordTransactionException',
		'ActiveRecordValidatorException' => 'ActiveRecord/Validator/ActiveRecordValidatorException',
		'ActiveRecordUtils' => 'ActiveRecord/Utils/ActiveRecordUtils',
		'ApplicationPlugin' => 'Plugin/Abstract/ApplicationPlugin',
		'ApplicationMonitor' => 'ApplicationMonitor/ApplicationMonitor',
		'ApplicationController' => 'Controller/Application/ApplicationController',
		'ApplicationControllerException' => 'Controller/Application/ApplicationControllerException',
		'AuditLogger' => 'AuditLogger/AuditLogger',
		'AuditLoggerException' => 'AuditLogger/AuditLoggerException',
		'Auth' => 'Auth/Auth',
		'AuthException' => 'Auth/AuthException',
		'AssertionFailed' => 'PHPUnit/AssertionFailed',
		'Browser' => 'ActionHelpers/Browser/Browser',
		'Cache' => 'Cache/Cache',
		'CacheException' => 'Cache/CacheException',
		'CommonEvent' => 'CommonEvent/Base/CommonEvent',
		'Compiler' => 'Compiler/Compiler',
		'CompilerException' => 'Compiler/CompilerException',
		'ComponentBuilder' => 'ComponentBuilder/ComponentBuilder',
		'ComponentBuilderException' => 'ComponentBuilder/ComponentBuilderException',
		'ComponentPlugin' => 'Plugin/Abstract/ComponentPlugin',
		'Config' => 'Config/Config',
		'ConfigException' => 'Config/ConfigException',
		'Controller' => 'Controller/Controller',
		'ControllerException' => 'Controller/ControllerException',
		'ControllerRequest' => 'Controller/ControllerRequest',
		'ControllerResponse' => 'Controller/ControllerResponse',
		'ControllerPlugin' => 'Plugin/Abstract/ControllerPlugin',
		'ControllerUploadFile' => 'Controller/ControllerUploadFile',
		'Core' => 'Core/Core',
		'CoreClassPath' => 'Core/ClassPath/CoreClassPath',
		'CoreConfig' => 'Core/Config/CoreConfig',
		'CoreConfigException' => 'Core/Config/CoreConfigException',
		'CoreInfo' => 'Core/Info/CoreInfo',
		'CoreLocale' => 'Core/Locale/CoreLocale',
		'CoreLocaleException' => 'Core/Locale/CoreLocaleException',
		'CoreException' => 'Core/CoreException',
		'CoreType' => 'Core/Type/CoreType',
		'Currency' => 'Currency/Currency',
		'CurrencyFormat' => 'Currency/Format/CurrencyFormat',
		'Date' => 'Date/Date',
		'DateException' => 'Date/DateException',
		'DateFormat' => 'Date/Format/DateFormat',
		'DbBase' => 'Db/DbBase',
		'DbException' => 'Db/DbException',
		'DbConstraintViolationException' => 'Db/DbConstraintViolationException',
		'DbInvalidFormatException' => 'Db/DbInvalidFormatException',
		'DbLockAdquisitionException' => 'Db/DbLockAdquisitionException',
		'DbLoader' => 'Db/Loader/DbLoader',
		'DbLoaderException' => 'Db/Loader/DbLoaderException',
		'DbRawValue' => 'Db/DbRawValue/DbRawValue',
		'DbProfiler' => 'Db/DbProfiler/DbProfiler',
		'DbSQLGrammarException' => 'Db/DbSQLGrammarException',
		'Debug' => 'Debug/Debug',
		'DebugException' => 'Debug/DebugException',
		'DebugRemote' => 'Debug/Remote/DebugRemote',
		'DispatcherException' => 'Dispatcher/DispatcherException',
		'EntityManager' => 'EntityManager/EntityManager',
		'EntityManagerException' => 'EntityManager/EntityManagerException',
		'EventManager' => 'Event/EventManager',
		'Facility' => 'Facility/Facility',
		'Feed' => 'Feed/Feed',
		'FeedItem' => 'Feed/Item/FeedItem',
		'FileLogger' => 'Logger/Adapters/File',
		'Filter' => 'Filter/Filter',
		'FilterException' => 'Filter/FilterException',
		'Flash' => 'ActionHelpers/Flash/Flash',
		'FormCriteria' => 'ActionHelpers/FormCriteria/FormCriteria',
		'Generator' => 'Generator/Generator',
		'Helpers' => 'Helpers/Helpers',
		'Highlight' => 'Highlight/Highlight',
		'HttpUri' => 'HttpUri/HttpUri',
		'i18n' => 'i18n/i18n',
		'GarbageCollector' => 'GarbageCollector/GarbageCollector',
		'GeneratorReport' => 'Generator/GeneratorReport/GeneratorReport',
		'Locale' => 'Locale/Locale',
		'LocaleData' => 'Locale/Data/LocaleData',
		'LocaleException' => 'Locale/LocaleException',
		'LocaleMath' => 'Locale/Math/LocaleMath',
		'LocaleMathException' => 'Locale/LocaleMath/LocaleMathException',
		'Logger' => 'Logger/Logger',
		'LoggerException' => 'Logger/LoggerException',
		'Migrate' => 'Migrate/Migrate',
		'MultiThreadController' => 'Controller/Application/MultiThread/MultiThreadController',
		'NamespaceContainer' => 'Session/Namespace/NamespaceContainer',
		'Object' => 'Object',
		'PdfDocument' => 'PdfDocument/PdfDocument',
		'PdfDocumentException' =>  'PdfDocument/PdfDocumentException',
		'PHPUnit' => 'PHPUnit/PHPUnit',
		'PHPUnitTestCase' => 'PHPUnit/PHPUnitTestCase',
		'PHPUnitException' => 'PHPUnit/PHPUnitException',
		'PluginManager' => 'Plugin/Plugin',
		'PluginException' => 'Plugin/PluginException',
		'Scriptaculous' => 'ActionHelpers/Scriptaculous/Scriptaculous',
		'ScriptaculousException' => 'ActionHelpers/Scriptaculous/ScriptaculousException',
		'Script' => 'Script/Script',
		'ScriptException' => 'Script/ScriptException',
		'SessionException' => 'Session/SessionException',
		'SessionNamespace' => 'Session/Namespace/Namespace',
		'SessionRecord' => 'ActiveRecord/SessionRecord/SessionRecord',
		'StandardForm' => 'Controller/StandardForm/StandardFormController',
		'StandardFormException' => 'Controller/StandardForm/StandardFormException',
		'Security' => 'Security/Security',
		'SecurityFirewall' => 'Security/Firewall/SecurityFirewall',
		'Soap' => 'Soap/Soap',
		'SoapException' => 'Soap/SoapException',
		'Registry' => 'Registry/Registry',
		'Report' => 'Report/Report',
		'ReportAdapter' => 'Report/ReportAdapter/ReportAdapter',
		'ReportComponent' => 'Report/ReportComponent/ReportComponent',
		'ReportException' => 'Report/ReportException',
		'Resolver' => 'Resolver/Resolver',
		'Router' => 'Router/Router',
		'RouterException' => 'Router/RouterException',
		'UserComponent' => 'UserComponent/UserComponent',
		'UserComponentException' => 'UserComponent/UserComponentException',
		'Utils' => 'Utils/Utils',
		'Tag' => 'Tag/Tag',
		'TagException' => 'Tag/TagException',
		'TemporaryActiveRecord' => 'ActiveRecord/Temporary/TemporaryActiveRecord',
		'TransactionDefinition' => 'Transactions/TransactionDefinition',
		'TransactionFailed' => 'ActiveRecord/Transaction/TransactionFailed',
		'TransactionManager' => 'Transactions/TransactionManager',
		'TransactionManagerException' => 'Transactions/TransactionExceptionManager',
		'Traslate' => 'Traslate/Traslate',
		'View' => 'View/View',
		'ViewException' => 'View/ViewException',
		'Validation' => 'Validation/Validation',
		'ValidationException' => 'Validation/ValidationException',
		'ValidationMessage' => 'Validation/ValidationMessage',
		'WebServiceController' => 'Controller/WebService/WebServiceController',
		'WebServiceClient' => 'Soap/Client/WebServiceClient',
		'WebServiceException' => 'Controller/WebService/WebServiceControllerException'
		);

		/**
		 * Verifica si una clase existe en el CLASSPATH
		 *
		 * @param string $className
		 * @return boolean
		 */
		static public function lookupClass($className){
			return isset(self::$_classPath[$className]);
		}

		/**
		 * Devuelve el PATH de la clase Solicitada
		 *
		 * @param string $className
		 */
		static public function getClassPath($className){
			if(self::$_iniDirName==false){
				self::$_dirName = getcwd().'/';
				self::$_iniDirName = true;
			}
			return self::$_dirName.'Library/Kumbia/'.self::$_classPath[$className].'.php';
		}

		/**
		 * Reemplaza una entrada en el CLASSPATH
		 *
		 * @param string $className
		 * @param string $path
		 */
		static public function replacePath($className, $path){
			self::$_classPath[$className] = $path;
		}

		/**
		 * Agrega una entrada en el CLASSPATH
		 *
		 * @param string $className
		 * @param string $path
		 */
		static public function addToPath($className, $path){
			if(!isset(self::$_classPath[$className])){
				self::$_classPath[$className] = $path;
			}
		}

}
