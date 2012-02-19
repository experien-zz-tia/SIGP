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
 * @category 	Kumbia
 * @package 	Soap
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2008 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @version 	$Id: Soap.php 82 2009-09-13 21:06:31Z gutierrezandresfelipe $
 */

/**
 * Soap
 *
 * Clase que administra el SoapServer
 *
 * @category 	Kumbia
 * @package 	Soap
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2008 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license 	New BSD License
 * @abstract
 */
abstract class Soap {

	/**
	 * Namespace de nodos Envelope
	 *
	 * @var string
	 * @staticvar
	 */
	private static $_envelopeNS = 'http://www.w3.org/2003/05/soap-envelope';

	/**
	 * Namespace para información de SoapFaults
	 *
	 * @var string
	 */
	private static $_faultsNS = 'http://schemas.loudertechnology.com/general/soapFaults';

	/**
	 * Namespace del XML Schema Instance (xsi)
	 *
	 * @var string
	 */
	private static $_xmlSchemaInstanceNS = 'http://www.w3.org/2001/XMLSchema-instance';

	/**
	 * DOMDocument Base
	 *
	 * @var DOMDocument
	 * @staticvar
	 */
	private static $_domDocument;

	/**
	 * Nodo Raiz de la respuesta SOAP
	 *
	 * @var DOMElement
	 */
	private static $_rootElement;

	/**
	 * Nodo Body de la respuesta SOAP
	 *
	 * @var DOMElement
	 */
	private static $_bodyElement;

	/**
	 * Crea un Envelope SOAP apto para SoapFaults y Respuestas
	 *
	 * @access private
	 * @return DOMElement
	 * @static
	 */
	static private function _createSOAPEnvelope(){
		self::$_domDocument = new DOMDocument('1.0', 'UTF-8');
		self::$_rootElement = self::$_domDocument->createElementNS(self::$_envelopeNS, 'SOAP-ENV:Envelope');
		self::$_domDocument->appendChild(self::$_rootElement);
		self::$_bodyElement = new DOMElement('Body', '', self::$_envelopeNS);
		self::$_rootElement->appendChild(self::$_bodyElement);
		return self::$_bodyElement;
	}

	/**
	 * Administra el objeto SoapServer y genera la respuesta SOAP
	 *
	 * @access public
	 * @param mixed $controller
	 * @static
	 */
	static public function serverHandler($controller){
		$response = ControllerResponse::getInstance();
		$response->setContentType('application/soap+xml; charset=utf-8');
		$soapAction = explode('#', str_replace('"', '', $_SERVER['HTTP_SOAPACTION'])); ;
		$serviceNamespace = $soapAction[0];
		$bodyElement = self::_createSOAPEnvelope();
		self::$_domDocument->createAttributeNS($serviceNamespace, 'ns1:dummy');
		self::$_domDocument->createAttributeNS('http://www.w3.org/2001/XMLSchema', 'xsd:dummy');
		self::$_domDocument->createAttributeNS('http://schemas.xmlsoap.org/soap/encoding', 'SOAP-ENC:dummy');
		self::$_domDocument->createAttributeNS(self::$_xmlSchemaInstanceNS, 'xsi:dummy');
		self::$_rootElement->setAttributeNS(self::$_envelopeNS, 'encondingStyle', 'http://schemas.xmlsoap.org/soap/encoding/');

		$responseElement = self::$_domDocument->createElementNS($serviceNamespace, $soapAction[1].'Response');
		$dataEncoded = self::_getDataEncoded();
		if($dataEncoded!=null){
			$responseElement->appendChild($dataEncoded);
		}
		$bodyElement->appendChild($responseElement);

		#file_put_contents('fx.txt', self::$_domDocument->saveXML());

		echo self::$_domDocument->saveXML();
	}

	/**
	 * Devuelve el tipo de dato XSD de acuerdo al tipo de dato Nativo en PHP
	 *
	 * @param 	string $nativeDataType
	 * @return 	string
	 */
	private static function _getDataXSD($nativeDataType){
		if($nativeDataType=='int'){
			return 'int';
		}
		return 'ur-type';
	}

	/**
	 * Formatea el valor devuelto por el metodo accion en el controlador
	 * usando el tipo de dato SOAP adecuado
	 *
	 * @access private
	 * @return DOMElement
	 * @static
	 */
	private static function _getDataEncoded($valueReturned=null, $nodeType='return'){
		if($valueReturned===null){
			$valueReturned = Dispatcher::getValueReturned();
		}
		if(!is_array($valueReturned)){
			if(is_resource($valueReturned)){
				throw new SoapException('Los recursos no pueden ser enviados como parte de un mensaje SOAP');
			}
			$element = self::$_domDocument->createElement($nodeType, $valueReturned);
			if(is_integer($valueReturned)==true){
				$element->setAttribute('xsi:type', 'xsd:int');
				$element->nodeValue = $valueReturned;
			} else {
				if(is_string($valueReturned)==true){
					$element->setAttribute('xsi:type', 'xsd:string');
					$element->nodeValue = $valueReturned;
				} else {
					if(is_float($valueReturned)==true){
						$element->setAttribute('xsi:type', 'xsd:float');
						$element->nodeValue = $valueReturned;
					} else {
						if(is_bool($valueReturned)==true){
							$element->setAttribute('xsi:type', 'xsd:boolean');
							if($valueReturned===false){
								$stringValue = 'false';
							} else {
								$stringValue = 'boolean';
							}
							$element->nodeValue = $stringValue;
						}
					}
				}
			}
			return $element;
		} else {
			$element = self::$_domDocument->createElement($nodeType);
			$dataType = '';
			$oldDataType = '';
			foreach($valueReturned as $key => $value){
				if($dataType!='mixed'){
					$dataType = gettype($value);
					if(!$oldDataType){
						$oldDataType = $dataType;
					} else {
						if($dataType!=$oldDataType){
							$dataType = 'mixed';
						}
						$oldDataType = $dataType;
					}
				}
			}
			$returnString = '';
			if($dataType=='mixed'){
				$element->setAttribute('SOAP-ENC:arrayType', 'xsd:ur-type['.count($valueReturned).']');
			} else {
				$element->setAttribute('SOAP-ENC:arrayType', 'xsd:'.self::_getDataXSD($dataType).'['.count($valueReturned).']');
			}
			$element->setAttribute('xsi:type', 'SOAP-ENC:Array');
			foreach($valueReturned as $key => $value){
				$element->appendChild(self::_getDataEncoded($value, 'item'));
			}
			return $element;
		}
	}

	/**
	 * Genera las fault exceptions del Servidor SOAP
	 *
	 * @access 	public
	 * @param 	Exception $e
	 * @param 	mixed $controller
	 * @static
	 */
	static public function faultSoapHandler($e, $controller){

		//Genera una respuesta HTTP de error
		$controllerResponse = ControllerResponse::getInstance();
		$controllerResponse->setHeader('X-Application-State: Exception', true);
		$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);

		if(isset($_SERVER['HTTP_SOAPACTION'])){
			$faultMessage = str_replace('\n', '', html_entity_decode($e->getMessage(), ENT_COMPAT, 'UTF-8'));
			$controllerResponse->setResponseType(ControllerResponse::RESPONSE_OTHER);
			$controllerResponse->setResponseAdapter('soap');
			$bodyElement = self::_createSOAPEnvelope();
			self::$_domDocument->createAttributeNS(self::$_faultsNS, 'fault:dummy');
			$faultElement = new DOMElement('Fault', '', self::$_envelopeNS);
			$bodyElement->appendChild($faultElement);

			//SOAP 1.1
			#$faultElement->appendChild(new DOMElement('faultcode', 'Server'));
			#$faultElement->appendChild(new DOMElement('faultstring', $faultMessage));

			//Código de la excepcion
			$codeElement = new DOMElement('Code', '', self::$_envelopeNS);
			$faultElement->appendChild($codeElement);

			if(get_class($e)=='SoapException'){
				$faultCode = $e->getFaultCode();
			} else {
				$faultCode = 'Receiver';
			}
			$codeValue = new DOMElement('Value', 'SOAP-ENV:'.$faultCode, self::$_envelopeNS);
			$codeElement->appendChild($codeValue);

			//Motivo de la excepcion
			$reasonElement = new DOMElement('Reason', '', self::$_envelopeNS);
			$faultElement->appendChild($reasonElement);
			$reasonText = new DOMElement('Text', $e->getMessage(), self::$_envelopeNS);
			$reasonElement->appendChild($reasonText);

			//Idioma del mensaje
			$locale = Locale::getApplication();
			$reasonText->setAttribute('xml:lang', $locale->getRFC4646String());

			//Subcodigo de la excepcion
			$subcodeElement = new DOMElement('Subcode', '', self::$_envelopeNS);
			$codeElement->appendChild($subcodeElement);
			$subcodeValue = new DOMElement('Value', 'fault:'.get_class($e), self::$_envelopeNS);
			$subcodeElement->appendChild($subcodeValue);

			//Detalle de la excepcion
			$detailElement = new DOMElement('Detail', '', self::$_envelopeNS);
			$faultElement->appendChild($detailElement);
			$faultType = new DOMElement('Type', get_class($e), self::$_faultsNS);
			$faultCode = new DOMElement('Code', $e->getCode(), self::$_faultsNS);
			$faultTime = new DOMElement('Time', @date('r'), self::$_faultsNS);
			$faultFile = new DOMElement('File', $e->getSafeFile(), self::$_faultsNS);
			$faultLine = new DOMElement('Line', $e->getLine(), self::$_faultsNS);

			$detailElement->appendChild($faultType);
			$detailElement->appendChild($faultCode);
			$detailElement->appendChild($faultFile);
			$detailElement->appendChild($faultLine);
			$detailElement->appendChild($faultTime);

			//Remote backtrace
			$config = CoreConfig::readAppConfig();
			if(isset($config->application->debug)&&$config->application->debug){
				$faultBacktrace = new DOMElement('Backtrace', '', self::$_faultsNS);
				$detailElement->appendChild($faultBacktrace);
				foreach($e->getTrace() as $trace){
					$faultTrace = new DOMElement('Trace', '', self::$_faultsNS);
					$faultBacktrace->appendChild($faultTrace);
					if(isset($trace['file'])){
						$faultFile = new DOMElement('File', CoreException::getSafeFileName($trace['file']), self::$_faultsNS);
						$faultTrace->appendChild($faultFile);
					}
					if(isset($trace['line'])){
						$faultLine = new DOMElement('Line', $trace['line'], self::$_faultsNS);
						$faultTrace->appendChild($faultLine);
					}
					if(!isset($trace['class'])){
						$trace['class'] = "";
						$trace['type'] = "";
					}
					if(!isset($trace['function'])){
						$trace['function'] = "";
					}
					$functionLocation = $trace['class'].$trace['type'].$trace['function'];
					$faultFunction = new DOMElement('Function', $functionLocation, self::$_faultsNS);
					$faultTrace->appendChild($faultFunction);
				}
			}
			echo self::$_domDocument->saveXML();
		}
	}

}
