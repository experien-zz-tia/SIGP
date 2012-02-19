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
 * @subpackage 	Client
 * @subpackage 	Client
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @license 	New BSD License
 * @version 	$Id: Sockets.php 88 2009-09-19 19:10:13Z gutierrezandresfelipe $
 */

/**
 * SocketsCommunicator
 *
 * Cliente para realizar peticiones HTTP
 *
 * @category	Kumbia
 * @package 	Soap
 * @subpackage 	Client
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @license 	New BSD License
 * @abstract
 */
class SocketsCommunicator {

	/**
	 * Handler del Socket
	 *
	 * @var resource
	 */
	private $_socketHandler;

	/**
	 * Peticion HTTP a realizar
	 *
	 * @var string
	 */
	private $_httpRequest;

	/**
	 * Metodo utilizado para realizar la peticion
	 *
	 * @var string
	 */
	private $_method;

	/**
	 * URI solicitada
	 *
	 * @var string
	 */
	private $_uri;

	/**
	 * Parametros pasados por GET
	 *
	 * @var array
	 */
	private $_queryParams = array();

	/**
	 * Encabezados de la peticion
	 *
	 * @var array
	 */
	private $_headers = array('Accept' => '*/*');

	/**
	 * Response status de la respuesta
	 *
	 * @var string
	 */
	private $_responseStatus;

	/**
	 * Response code de la respuesta
	 *
	 * @var int
	 */
	private $_responseCode;

	/**
	 * Encabezados de Respuesta
	 *
	 * @var array
	 */
	private $_responseHeaders = array();

	/**
	 * Cuerpo de la respuesta
	 *
	 * @var string
	 */
	private $_responseBody;

	/**
	 * Raw Post Data
	 *
	 * @var string
	 */
	private $_rawPostData;

	/**
	 * Metodos HTTP soportados por el Adaptador
	 *
	 * @var array
	 */
	private static $_supportedMethods = array('POST', 'GET');

	/**
	 * Constructor del SocketCommunicator
	 *
	 * @param string $scheme
	 * @param string $uri
	 * @param string $method
	 * @param int $port
	 */
	public function __construct($scheme, $address, $uri, $method, $port=80){
		if($scheme=='https'){
			$address = "ssl://$address";
		} else {
			$address = "tcp://$address";
		}
		$this->_socketHandler = @fsockopen($address, $port, $errorString);
		if(!$this->_socketHandler){
			throw new SoapException($errorString);
		}
		if($this->_isSupportedMethod($method)==false){
			throw new SoapException('El tipo de metodo HTTP "'.$method.'" no está soportado');
		}
		$this->_method = $method;
		$this->_uri = $uri;
	}

	/**
	 * Valida si un metodo HTTP está soportado por el Adaptador
	 *
	 * @param string $method
	 */
	private function _isSupportedMethod($method){
		return in_array($method, self::$_supportedMethods);
	}

	/**
	 * Establece los encabezados de la peticion
	 *
	 * @param array $headers
	 */
	public function setHeaders($headers){
		foreach($headers as $headerName => $headerValue){
			$this->_headers[$headerName] = $headerValue;
		}
		$this->_headers['Connection'] = 'Close';
		unset($this->_headers['Accept-Encoding']);
	}

	/**
	 * Establece los encabezados de la peticion
	 *
	 * @param array $headers
	 */
	public function addHeaders($headers){
		CoreType::assertArray($headers);
		foreach($headers as $headerName => $headerValue){
			$this->_headers[$headerName] = $headerValue;
		}
	}

	/**
	 * Agrega un encabezado a la petición
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function addHeader($name, $value){
		$this->_headers[$name] = $value;
	}

	/**
	 * Agrega los parametros por GET a la peticion
	 *
	 * @access public
	 */
	public function prepareQueryData($queryParams){
		$query = array();
		foreach($queryParams as $paramName => $paramValue){
			$query[] = $paramName.'='.urlencode($paramValue);
		}
		if(count($query)){
			if(strpos($this->_uri, '?')===false){
				$this->_uri.='?'.join('&', $query);
			}
		}
	}

	/**
	 * Establece el Raw POST data
	 *
	 * @param string $rawPostData
	 */
	public function setRawPostData($rawPostData){
		$this->_rawPostData = $rawPostData;
	}

	/**
	 * Envia la peticion HTTP
	 *
	 * @access public
	 */
	public function send(){
		if($this->_method=='GET'){
			$this->_httpRequest = "GET /{$this->_uri} HTTP/1.1\r\n";
		} else {
			if($this->_method=='POST'){
				$this->_httpRequest = "POST /{$this->_uri} HTTP/1.1\r\n";
			}
		}
		foreach($this->_headers as $headerName => $headerValue){
			$this->_httpRequest.="$headerName: $headerValue\r\n";
		}
		if($this->_method=='POST'){
			if($this->_rawPostData==''){
				$this->_httpRequest.="Content-Length: 28\r\n";
				$this->_httpRequest.="Content-Type: application/x-www-form-urlencoded\r\n";
				$postData = array();
				/*if(isset($_POST)){
				 foreach($_POST as $key => $value){
				 $postData[] = $key."=".urlencode($value);
				 }
				 $this->_httpRequest.="\r\n".join("&", $postData);
				 } else {
				 $this->_httpRequest.="\r\n";
				 }*/
				$this->_httpRequest.="\r\n";
			} else {
				$this->_httpRequest.="Content-Length: ".strlen($this->_rawPostData)."\r\n";
				$this->_httpRequest.="\r\n";
				$this->_httpRequest.=$this->_rawPostData;
			}
		} else {
			$this->_httpRequest.="\r\n";
		}
		fwrite($this->_socketHandler, $this->_httpRequest);
		$response = '';
		$header = true;
		$i = 0;
		$this->_responseBody = '';
		$this->_responseHeaders = array();
		$all = '';
		while(!feof($this->_socketHandler)){
			$line = fgets($this->_socketHandler);
			if($header==true){
				if($i==0){
					$fline = explode(' ', $line);
					$this->_responseCode = $fline[1];
					$this->_responseStatus = rtrim($fline[2]);
				} else {
					if($line!="\r\n"){
						$pline = explode(": ", $line, 2);
						if(count($pline)==2){
							$this->_responseHeaders[$pline[0]] = substr($pline[1], 0, strlen($pline[1])-2);
						} else {
							$header = false;
						}
					} else {
						$header = false;
					}
				}
				++$i;
			} else {
				$this->_responseBody.=$line;
			}
		}
	}

	/**
	 * Devuelve los headers recibidos de la peticion
	 *
	 * @return array
	 */
	public function getResponseHeaders(){
		return $this->_responseHeaders;
	}

	/**
	 * Devuelve el cuerpo de la respuesta HTTP
	 *
	 * @return string
	 */
	public function getResponseBody(){
		return $this->_responseBody;
	}

	/**
	 * Devuelve el codigo de la respuesta HTTP
	 *
	 * @return string
	 */
	public function getResponseCode(){
		return $this->_responseCode;
	}

	/**
	 * Devuelve las COOKIES enviadas por el servidor
	 *
	 * @return array
	 */
	public function getResponseCookies(){
		if(isset($this->_responseHeaders['Set-Cookie'])){
			$responseCookies = array();
			$cookies = explode(';', $this->_responseHeaders['Set-Cookie']);
			foreach($cookies as $cookie){
				$cook = explode('=', $cookie);
				$responseCookies[$cook[0]] = $cook[1];
			}
			return $responseCookies;
		}
		return array();
	}

}
