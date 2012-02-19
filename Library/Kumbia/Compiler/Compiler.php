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
 * @package		Compiler
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2008-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @version 	$Id: Compiler.php 97 2009-09-30 19:28:13Z gutierrezandresfelipe $
 */

/**
 * Compiler
 *
 * El componente Compiler genera un solo archivo con todos los componentes
 * y archivos del framework en una versión ‘optimizada’ que son utilizados
 * en una petición regular y dejándole el resto del trabajo al inyector
 * de dependencias. El uso de este componente puede aumentar el rendimiento
 * del framework de 4 a 5 veces. Si se cuenta ó no se cuenta con un
 * optimizador y cacheador de código intermedio este componente siempre
 * puede ser de gran ayuda para el mejoramiento del rendimiento de una
 * aplicación.
 *
 * @category	Kumbia
 * @package		Compiler
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2008-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 */
class Compiler {

	/**
	 * Token Anterior
	 *
	 * @var int
	 */
	private static $_oldToken = 0;

	/**
	 * Scope actual
	 *
	 * @var int
	 */
	private static $_levelScope = 0;

	/**
	 * Numero de caracteres
	 *
	 * @var int
	 */
	private static $_tokenCount = 0;

	/**
	 * Archivos Incluidos
	 *
	 * @var array
	 */
	private static $_requiredFiles = array();

	/**
	 * Tokens que deben añadir un espacio al principio
	 *
	 * @var array
	 */
	private static $_beforeSpaceTokens  = array(
	T_AS, T_EXTENDS, T_INSTANCEOF, T_IMPLEMENTS, T_NEW, T_CLONE
	);

	/**
	 * Tokens que deben añadir un espacio al final
	 *
	 * @var array
	 */
	private static $_afterSpaceTokens = array(
	T_FUNCTION, T_REQUIRE, T_INCLUDE, T_INCLUDE_ONCE, T_REQUIRE_ONCE,
	T_ABSTRACT, T_CLASS, T_PRIVATE, T_PROTECTED, T_PUBLIC, T_STATIC,
	T_PRINT, T_ECHO, T_AS, T_THROW, T_RETURN, T_CONST, T_INTERFACE,
	T_CLASS_C, T_EXTENDS, T_INSTANCEOF, T_CASE, T_FINAL, T_IMPLEMENTS,
	T_NEW, T_CLONE
	);

	/**
	 * Token que deben añadir un espacio al prinicio
	 *
	 * @var array
	 */
	private static $_forcedSpace = array(
	T_VARIABLE => array(T_STRING, T_ELSE),
	T_RETURN => array(T_ELSE),
	T_REQUIRE => array(T_ELSE),
	T_ECHO => array(T_ELSE),
	T_PRINT => array(T_ELSE),
	T_INCLUDE_ONCE => array(T_ELSE),
	T_REQUIRE_ONCE => array(T_ELSE)
	);

	/**
	 * Tokens que deben omitirse
	 *
	 * @var string
	 */
	private static $_breakTokens = array(
	T_OPEN_TAG, T_COMMENT, T_DOC_COMMENT, T_WHITESPACE, T_BAD_CHARACTER
	);

	/**
	 * Reflected Classes
	 *
	 * @var array
	 */
	private static $_reflectedClasses = array();

	/**
	 * Array de Mensajes Producidos en la compilación
	 *
	 * @var array
	 */
	private static $_messages = array();

	/**
	 * Compilado
	 *
	 * @var string
	 */
	private static $_compilation = '';

	/**
	 * Indica secciones de codigo que no deben incluirse en la compilación
	 *
	 * @var boolean
	 */
	private static $_deusableCode = false;

	/**
	 * Mensaje de Error
	 *
	 */
	const MESSAGE_ERROR = 0;

	/**
	 * Mensaje de Advertencia
	 *
	 */
	const MESSAGE_WARNING = 1;

	/**
	 * Mensaje de Información
	 *
	 */
	const MESSAGE_NOTICE = 2;

	/**
	 * Compila la aplicacion actual
	 *
	 */
	public static function compileFramework($otherFiles=array()){
		set_time_limit(0);
		$exceptions = array('public/index.php');
		self::$_requiredFiles = get_required_files();
		$i = 0;
		$currentDirectory = getcwd();
		foreach(self::$_requiredFiles as $requiredFile){
			self::$_requiredFiles[$i] = str_replace('/Applications/MAMP/htdocs/kef/kumbia-ef', $currentDirectory, $requiredFile);
			++$i;
		}
		self::compileFile('Library/Kumbia/Autoload.php');
		$includeControllerBase = false;
		foreach(self::$_requiredFiles as $requiredFile){
			if(
			strpos($requiredFile, '/Library/Kumbia/Controller/')===false&&
			//strpos($requiredFile, '/Library/Kumbia/Tag/')===false&&
			strpos($requiredFile, '/Library/Kumbia/Compiler/')===false&&
			strpos($requiredFile, '/Library/Kumbia/Autoload.php')===false&&
			strpos($requiredFile, 'public/index.php')===false&&
			strpos($requiredFile, '/controllers/')===false&&
			strpos($requiredFile, '/library/')===false&&
			strpos($requiredFile, '/models/')===false&&
			strpos($requiredFile, '/views/')===false&&
			strpos($requiredFile, '/plugins/')===false){
				/*if($includeControllerBase==false){
				 if(strpos($requiredFile, '/Library/Kumbia/Controller')){
				 self::$_compilation.='Core::includeControllerBase();';
				 $includeControllerBase = true;
				 }
				 }*/
				self::compileFile($requiredFile);
			}
		}
		foreach($otherFiles as $requiredFile){
			self::compileFile($requiredFile);
		}
		/*foreach(self::$_requiredFiles as $requiredFile){
			if(strpos($requiredFile, '/library')!==false){
			self::compileFile($requiredFile);
			}
			}*/
		$compilation = self::$_compilation;
		self::$_compilation = '';
		self::compileFile('public/index.php');
		self::$_compilation = str_replace('chdir(\'..\');', 'chdir(\'..\');'.$compilation, self::$_compilation);
		file_put_contents('compile.php', '<?php '.self::$_compilation);
		if(isset(self::$_messages[self::MESSAGE_ERROR])){
			if(count(self::$_messages[self::MESSAGE_ERROR])>0){
				return false;
			} else {
				return true;
			}
		}
	}

	public static function compileSource($source){
		self::$_oldToken = 0;
		$tokens = token_get_all($source);
		$numberTokens = count($tokens);
		#print '<table>';
		for($i=0;$i<$numberTokens;++$i){
			$jp = false;
			$token = $tokens[$i];
			if(is_array($token)){
				#print '<tr>';
				#print '<td>'.$i.'</td>';
				#print '<td>'.token_name($token[0]).'</td>';
				#print '<td>'.$token[1].'</td>';
				#print '</tr>';
				if(!in_array($token[0], self::$_breakTokens)){
					if(self::$_deusableCode==false){
						switch($token[0]){
							case T_CONSTANT_ENCAPSED_STRING:
								if($token[1]!='"\'"'){
									if(preg_match('/^"(.*)"/', $token[1], $matches)){
										if(strpos($matches[1], "\\")===false&&strpos($matches[1], '$')===false){
											$token[1] = "'".addslashes($matches[1])."'";
										}
									}
								}
								break;
							case T_FILE:
								self::$_compilation.="'$file'";
								$jp = true;
								break;
							case T_LINE:
								self::$_compilation=$token[2];
								$jp = true;
								break;
							case T_REQUIRE:
							case T_INCLUDE:
							case T_REQUIRE_ONCE:
							case T_INCLUDE_ONCE:
								$r = self::_checkRequire($tokens[$i+2]);
								if($r==-1){
									$jp = true;
									$i+=2;
									if(!is_array($tokens[$i])){
										if($tokens[$i]==';'){
											++$i;
										}
									}
								}
								break;
							case T_STRING:
								if(is_array($tokens[$i+1])){
									if($tokens[$i+1][0]==T_PAAMAYIM_NEKUDOTAYIM){
										if(is_array($tokens[$i+2])){
											if($tokens[$i+2][0]==T_STRING){
												if(!is_array($tokens[$i+3])&&$tokens[$i+3]=='('){
													self::_checkStaticMethod($token[1], $tokens[$i+2][1]);
												} else {
													self::_checkClassConstant($token[1], $tokens[$i+2][1]);
												}
											}
										}
									}
								}
							case T_FOR:
								/*$r = self::_analizeForStatement($token, $i, &$tokens);
								 if($r>=0){
									$jp = true;
									$i = $r+1;
									}
									break;*/
						}
						if($jp==false){
							if(in_array($token[0], self::$_beforeSpaceTokens)){
								if(self::$_oldToken!=T_WHITESPACE){
									self::$_compilation.=" ";
								}
							} else {
								if(isset(self::$_forcedSpace[$token[0]])){
									if(in_array(self::$_oldToken, self::$_forcedSpace[$token[0]])){
										self::$_compilation.=" ";
									}
								}
							}
							if(isset($token[1])){
								self::$_compilation.=$token[1];
							}
							if(in_array($token[0], self::$_afterSpaceTokens)){
								self::$_compilation.=" ";
							}
						}
						self::$_oldToken = $token[0];
					}
				} else {
					if(isset($token[1])){
						if($token[0]==T_COMMENT){
							if(strpos($token[1], '#if[compile-time]')===0){
								self::$_deusableCode = true;
								self::$_compilation.=';';
							}
							if(strpos($token[1], '#endif')===0){
								self::$_deusableCode = false;
							}
						}
					}
				}
			} else {
				#print '<tr>';
				#print '<td>'.$i.'</td>';
				#print '<td>T_NPI</td>';
				#print '<td>'.$token.'</td>';
				#print '</tr>';
				if(self::$_deusableCode==false){
					self::$_compilation.=$token;
					self::$_oldToken = 0;
				}
			}
			if(self::$_deusableCode==false){
				if(self::$_tokenCount>1024){
					self::$_compilation.=PHP_EOL;
					self::$_tokenCount = 0;
				} else {
					++self::$_tokenCount;
				}
			}
		}
		#print '</table>';
		#print '</pre>';
		self::$_compilation.=';';
		if(isset(self::$_messages[self::MESSAGE_ERROR])){
			if(count(self::$_messages[self::MESSAGE_ERROR])>0){
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}

	public static function getMessages(){
		$messages = array();
		foreach(self::$_messages as $type => $gmessages){
			foreach($gmessages as $message){
				$messages[] = array(
					'type' => $type,
					'message' => $message
				);
			}
		}
		return $messages;
	}

	private static function _addMessage($message, $type){
		self::$_messages[$type][] = $message;
	}

	public static function _checkClassConstant($className, $constant){
		if(class_exists($className)){
			if(!isset(self::$_reflectedClasses[$className])){
				$reflectionClass = new ReflectionClass($className);
				self::$_reflectedClasses[$className] = $reflectionClass;
			} else {
				$reflectionClass = self::$_reflectedClasses[$className];
			}
			if($reflectionClass->hasConstant($constant)){
				return false;
			} else {
				self::_addMessage('No se pudo determinar la existencia de la constante "'.$constant.'" en la clase "'.$className.'"', self::MESSAGE_ERROR);
			}
		}
	}

	public static function _checkStaticMethod($className, $method){
		if(class_exists($className)){
			if(!isset(self::$_reflectedClasses[$className])){
				$reflectionClass = new ReflectionClass($className);
				self::$_reflectedClasses[$className] = $reflectionClass;
			} else {
				$reflectionClass = self::$_reflectedClasses[$className];
			}
			if($reflectionClass->hasMethod($method)){
				$classMethod = $reflectionClass->getMethod($method);
				if($classMethod->getName()==$method){
					if($classMethod->isStatic()==false){
						self::_addMessage('El método "'.$method.'" de la clase "'.$className.'" se está llamado estáticamente, esto generará una excepción', self::MESSAGE_ERROR);
					}
					return;
				}
			}
			self::_addMessage("No se pudo determinar la existencia del metodo '$method' en la clase '$className'", self::MESSAGE_WARNING);
		} else {
			self::_addMessage("No se pudo determinar la existencia de la clase '$className'", self::MESSAGE_WARNING);
		}
	}

	/**
	 * Compila un archivo
	 *
	 * @param string $file
	 */
	public static function compileFile($file){
		#self::$_compilation.="#$file".PHP_EOL;
		#file_put_contents('file.txt', $file);
		print $file."<br>";
		self::compileSource(file_get_contents($file));
	}

	/**
	 * Optimiza sentencias FOR con conteos en su evaluación
	 *
	 * @param array $token
	 * @param int $i
	 * @param array $tokens
	 * @return int
	 */
	private static function _analizeForStatement($token, $i, $tokens){
		$ii = $i;
		$tempVar = "";
		$forCode = $token[1];
		$notEvalCode = "\$_sp=";
		$posibleSource = array();
		$compOperators = array(
		T_IS_EQUAL, T_IS_NOT_EQUAL,
		T_IS_GREATER_OR_EQUAL, T_IS_SMALLER_OR_EQUAL,
		T_IS_IDENTICAL, T_IS_NOT_IDENTICAL
		);
		if($tokens[$i+1]=='('){
			$forCode.='(';
			if(is_array($tokens[$i+2])&&$tokens[$i+2][0]==T_VARIABLE){
				$tempVar = $tokens[$i+2][1];
				$j = $i+2;
				while($tokens[$j]!=';'){
					if(is_array($tokens[$j])){
						$forCode.=$tokens[$j][1];
					} else {
						$forCode.=$tokens[$j];
					}
					++$j;
				}
				$forCode.=';';
				$i = $j+1;
				if(is_array($tokens[$i])&&$tokens[$i][0]==T_VARIABLE){
					$forCode.=$tokens[$i][1];
					if($tokens[$i][1]==$tempVar){
						$isComparator = false;
						if(is_array($tokens[$i+1])){
							if(in_array($tokens[$i+1][0], $compOperators)){
								$isComparator = true;
								$forCode.=$tokens[$i+1][1];
							}
						} else {
							if(in_array($tokens[$i+1], array('<', '>'))){
								$isComparator = true;
								$forCode.=$tokens[$i+1];
							}
						}
						if($isComparator==false){
							return false;
						}
						$i+=2;
						if(is_array($tokens[$i])){
							if($tokens[$i][0]==T_STRING||$tokens[$i][0]==T_ARRAY){
								if(in_array($tokens[$i][1], array('count', 'sizeof', 'strlen', 'mb_strlen'))){
									$notEvalCode.=$tokens[$i][1];
									$j = $i+1;
									while($tokens[$j]!=';'){
										if(is_array($tokens[$j])){
											if(!in_array($tokens[$j][0], self::$_breakTokens)){
												if($tokens[$j][0]==T_VARIABLE){
													$posibleSource[] = $tokens[$j][1];
												}
												$notEvalCode.=$tokens[$j][1];
											}
										} else {
											$notEvalCode.=$tokens[$j];
										}
										++$j;
									}
									$notEvalCode.=';';
									$forCode.='$_sp;';
									$cOpen = 0;
									$pOpen = 1;
									++$j;
									while(true){
										if(is_array($tokens[$j])){
											if(!in_array($tokens[$j][0], self::$_breakTokens)){
												if($tokens[$j][0]==T_VARIABLE){
													if(in_array($tokens[$j][1], $posibleSource)){
														$k = $j+1;
														while(in_array(is_array($tokens[$k]) ? $tokens[$k][0] : '', self::$_breakTokens)){
															++$k;
														}
														if(in_array(
														is_array($tokens[$k]) ? $tokens[$k][0] : $tokens[$k], array(
																'=', T_INC, T_DEC, T_MUL_EQUAL, T_DIV_EQUAL, T_PLUS_EQUAL,
														T_CONCAT_EQUAL, T_OR_EQUAL, T_MINUS_EQUAL, T_MOD_EQUAL,
														T_SL_EQUAL, T_SR_EQUAL, T_XOR_EQUAL, T_AND_EQUAL
														))||($tokens[$k]=='['&&$tokens[$k+1]==']')){
															return false;
														}
													}
												}
												$forCode.=$tokens[$j][1];
											}
										} else {
											$forCode.=$tokens[$j];
											if($tokens[$j]=='{'){
												++$cOpen;
											}
											if($tokens[$j]=='}'){
												$cOpen--;
												if($cOpen==0){
													break;
												}
											}
										}
										++$j;
									}
								} else {
									return -1;
								}
							} else {
								return -1;
							}
						}
					} else {
						return -1;
					}
				}
				self::$_compilation = $notEvalCode.$forCode;
				return $j-$ii;
			} else {
				return -1;
			}
		} else {
			return -1;
		}
	}

	/**
	 * Verifica si es necesario incluir un archivo
	 *
	 * @param array $requireToken
	 * @param int $position
	 * @return boolean
	 */
	private static function _checkRequire($requireToken){
		if($requireToken[0]==T_CONSTANT_ENCAPSED_STRING){
			if(preg_match('/.php[\'"]$/', $requireToken[1])){
				$posiblePath = getcwd().'/'.substr($requireToken[1], 1, strlen($requireToken[1])-2);
				foreach(self::$_requiredFiles as $file){
					if($file==$posiblePath){
						#print '<tr bgcolor=yellow>';
						#print '<td>?</td>';
						#print '<td>'.token_name($requireToken[0]).'</td>';
						#print '<td>'.$requireToken[1].'</td>';
						#print '</tr>';
						return -1;
					}
				}
				return 1;
			} else {
				return -2;
			}
		} else {
			return -2;
		}
	}

	public static function getCompilation(){
		return self::$_compilation;
	}

}
