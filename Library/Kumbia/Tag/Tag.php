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
 * @package 	Tag
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright 	Copyright (c) 2007-2008 Emilio Rafael Silveira Tovar(emilio.rst at gmail.com)
 * @copyright 	Copyright (c) 2007-2008 Deivinson Tejeda Brito (deivinsontejeda at gmail.com)
 * @license 	New BSD License
 * @version 	$Id: Tag.php 114 2009-11-09 15:30:28Z game013 $
 */

/**
 * Tag
 *
 * Este componente actua como una biblioteca de etiquetas que permite generar
 * tags XHTML en la presentación de una aplicación mediante métodos estáticos
 * PHP predefinidos flexibles que integran tecnología del lado del cliente
 * como CSS y Javascript.
 *
 * @category 	Kumbia
 * @package	Tag
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright 	Copyright (c) 2007-2008 Emilio Rafael Silveira Tovar(emilio.rst at gmail.com)
 * @copyright 	Copyright (c) 2007-2008 Deivinson Tejeda Brito (deivinsontejeda at gmail.com)
 * @license 	New BSD License
 * @abstract
 */
abstract class Tag {

	/**
	 * Indica si se debe usar localizacion
	 *
	 * @var boolean
	 */
	private static $_useLocale = true;

	/**
	 * Valores de los componentes
	 *
	 * @var array
	 */
	private static $_displayValues = array();

	/**
	 * Titulo del Documento HTML
	 *
	 * @var string
	 */
	private static $_documentTitle = '';

	/**
	 * Establece el valor de un componente de UI
	 *
	 * @param string $id
	 * @param string $value
	 */
	public static function displayTo($id, $value){
		if(is_object($value)||is_array($value)||is_resource($value)){
			throw new TagException('Solo valores escalares pueden ser asiganados a los componentes UI');
		}
		self::$_displayValues[$id] = $value;
	}

	/**
	 * Obtiene el valor de un componente tomado
	 * del mismo valor del nombre del campo en $_displayValues
	 * del mismo nombre del controlador o el indice en
	 * $_POST
	 *
	 * @param string $name
	 * @return mixed
	 * @static
	 */
	public static function getValueFromAction($name){
		if(@isset(self::$_displayValues[$name])){
			return self::$_displayValues[$name];
		} else {
			if(@isset($_POST[$name])){
				if(get_magic_quotes_gpc()==false){
					return $_POST[$name];
				} else {
					return stripslashes($_POST[$name]);
				}
			} else {
				$controller = Dispatcher::getController();
				if(isset($controller->$name)){
					return $controller->$name;
				} else {
					return "";
				}
			}
		}
	}

	/**
	 * Crea un enlace en una aplicacion respetando las convenciones del framework
	 *
	 * @param string $action
	 * @param string $text
	 * @return string
	 */
	public static function linkTo($action, $text=''){
		if(func_num_args()>2){
			$numberArguments = func_num_args();
			$action = Utils::getParams(func_get_args(), $numberArguments);
		}
		if(is_array($action)){
			if(isset($action['confirm'])&&$action['confirm']){
				if(!isset($action['onclick'])){
					$action['onclick'] = "";
				}
				$action['onclick'] = "if(!confirm(\"{$action['confirm']}\")) { return false; }; ".$action['onclick'];
				unset($action['confirm']);
			}
			$code = "<a href='".Utils::getKumbiaUrl($action)."' ";
			if(!isset($action['text'])||!$action['text']){
				$action['text'] = $action[1];
			}
			foreach($action as $key => $value){
				if(!is_integer($key)&&$key!='text'){
					$code.=" $key='$value' ";
				}
			}
			$code.='>'.$action['text'].'</a>';
			return $code;
		} else {
			if($text==="") {
				$text = str_replace('_', ' ', $action);
				$text = str_replace('/', ' ', $text);
				$text = ucwords($text);
			}
			return "<a href='".Utils::getKumbiaUrl($action)."'>".$text."</a>";
		}
	}

	/**
	 * Crea un enlace a una accion dentro del controlador Actual
	 *
	 * @param string $action
	 * @param string $text
	 * @return string
	 */
	static public function linkToAction($action, $text=''){
		if(func_num_args()>2){
			$numberArguments = func_num_args();
			$params = Utils::getParams(func_get_args(), $numberArguments);
		}
		$controller_name = Router::getController();
		if(is_array($action)){
			if(isset($action['confirm'])){
				$action['onclick'] = "if(!confirm(\"{$action['confirm']}\")) if(document.all) event.returnValue = false; else event.preventDefault(); ".$action['onclick'];
				unset($action['confirm']);
			}
			$code = "<a href='".Utils::getKumbiaUrl("$controller_name/{$action[0]}")."' ";
			foreach($action as $key => $value){
				if(!is_integer($key)){
					$code.=' '.$key.'=\''.$value.'\'';
				}
			}
			$code.=">{$action[1]}</a>";
			return $code;
		} else {
			if(!$text) {
				$text = str_replace('_', ' ', $action);
				$text = str_replace('/', ' ', $text);
				$text = ucwords($text);
			}
			return "<a href='".Utils::getKumbiaUrl("$controller_name/$action")."'>$text</a>";
		}
	}

	/**
	 * Permite ejecutar una acción en la vista actual dentro de un contenedor
	 * HTML usando AJAX
	 *
	 * confirm: Texto de Confirmación
	 * success: Codigo JavaScript a ejecutar cuando termine la petición AJAX
	 * before: Codigo JavaScript a ejecutar antes de la petición AJAX
	 * oncomplete: Codigo JavaScript que se ejecuta al terminar la petición AJAX
	 * update: Que contenedor HTML seró actualizado
	 * action: Accion que ejecutaró la petición AJAX
	 * text: Texto del Enlace
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	static public function linkToRemote(){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params['update'])||!$params['update']){
			$update = isset($params[2]) ? $params[2] : "";
		} else {
			$update = $params['update'];
			unset($params['update']);
		}
		if(!isset($params['text'])||!$params['text']){
			$text = isset($params[1]) ? $params[1] : "";
		} else {
			$text = $params['text'];
		}
		if(!$text){
			$text = $params[0];
		}
		if(!isset($params['action'])||!$params['action']){
			$action = $params[0];
		} else {
			$action = $params['action'];
		}
		$code = "<a href=\"#\" onclick=\"";
		if(isset($params['confirm'])){
			$code.= "if(confirm('{$params['confirm']}')){";
		}
		$code.= "new Ajax.Request(Utils.getKumbiaURL('$action'), {";
		$call = array();
		if(isset($params['asynchronous'])){
			if($params['asynchronous']=='false'||!$params['asynchronous']){
				$call[] = "asynchronous: false";
			} else {
				$call[] = "asynchronous: true";
			}
			unset($params['asynchronous']);
		}
		if(isset($params['onLoading'])){
			$call[] = "onLoading: function(){ {$params['onLoading']} }";
			unset($params['onLoading']);
		}
		if(isset($params['onSuccess'])){
			$call[] = "onSuccess: function(transport){ {$params['onSuccess']} }";
			unset($params['onSuccess']);
		}
		if(isset($params['onFailure'])){
			$call[] = "onFailure: function(transport){ {$params['onFailure']} }";
			unset($params['onFailure']);
		}
		if(isset($params['onComplete'])){
			$call[] = "onComplete: function(transport){ {$params['onComplete']}; $('$update').update(transport.responseText); }";
			unset($params['onComplete']);
		} else {
			$call[] = "onComplete: function(transport){ $('$update').update(transport.responseText); }";
		}
		if(count($call)>0){
			$code.= join(',', $call);
		}
		$code.="})";
		if(isset($params['confirm'])){
			$code.=" }";
			unset($params['confirm']);
		}
		$code.="; return false\"";
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$code.=" $key='$value' ";
			}
		}
		return $code.">$text</a>";
	}

	/**
	 * Caja de Texto que autocompleta los resultados
	 *
	 * @param mixed $params
	 * @return string
	 * @static
	 */
	public static function textFieldWithAutocomplete($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params['value'])){
			$params['value'] = self::getValueFromAction($params[0]);
		}
		$hash = mt_rand(1, 100);
		if(!isset($params['name'])||!$params['name']) {
			$params['name'] = $params[0];
		}
		if(!isset($params['after_update'])||!$params['after_update']) {
			$params['after_update'] = "function(){}";
		}
		if(!isset($params['id'])||!$params['id']) {
			$params['id'] = $params['name'] ? $params['name'] : $params[0];
		}
		if(!isset($params['message'])||!$params['message']) {
			$params['message'] = "Consultando...";
		}
		if(!isset($params['param_name'])||!$params['param_name']) {
			$params['param_name'] = $params[0];
		}
		$code = "<input type='text' id='{$params[0]}' name='{$params['name']}'";
		foreach($params as $key => $value){
			if(!in_array($key, array('id', 'name', 'param_name', 'message', 'action', 'after_update'))){
				if(!is_integer($key)){
					$code.="$key='$value' ";
				}
			}
		}
		$instancePath = Core::getInstancePath();
		$code.= " />
		<span id='indicator$hash' style='display: none'><img src='{$instancePath}img/spinner.gif' alt='{$params['message']}'/></span>
		<div id='{$params[0]}_choices' class='autocomplete'></div>
		<script type='text/javascript'>
		// <![CDATA[
		new Ajax.Autocompleter(\"{$params[0]}\", \"{$params[0]}_choices\", Utils.getKumbiaURL(\"{$params['action']}\"), { minChars: 2, indicator: 'indicator$hash', afterUpdateElement : {$params['after_update']}, paramName: '{$params['param_name']}'});
		// ]]>
		</script>";
		return $code;
	}

	/**
	 * Crea un TextArea
	 *
	 * @access	public
	 * @param	array $configuration
	 * @return	string
	 * @static
	 */
	public static function textArea($configuration){
		$numberArguments = func_num_args();
		$configuration = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($configuration['name'])||$configuration['name']) {
			$configuration['name'] = $configuration[0];
		}
		if(!isset($configuration['cols'])||!$configuration['cols']) {
			$configuration['cols'] = 40;
		}
		if(!isset($configuration['rows'])||!$configuration['rows']) {
			$configuration['rows'] = 25;
		}
		if(!isset($configuration['value'])){
			$value = self::getValueFromAction($configuration[0]);
		} else {
			$value = $configuration['value'];
		}
		return "<textarea id=\"{$configuration['name']}\" name=\"{$configuration['name']}\" cols=\"{$configuration['cols']}\" rows=\"{$configuration['rows']}\">$value</textarea>\r\n";
	}

	/**
	 * Crea una caja de texto que solo acepta numeros
	 *
	 * @param 	mixed $params
	 * @return 	string
	 * @static
	 */
	public static function numericField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])){
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']){
			$params['name'] = $params[0];
		}
		if(isset($params['value'])){
			$value = $params['value'];
			unset($params['value']);
		} else {
			$value = self::getValueFromAction($params[0]);
		}
		if(!isset($params['onkeydown'])) {
			$params['onkeydown'] = "valNumeric(event)";
		} else {
			$params['onkeydown'].=";valNumeric(event)";
		}
		$code = "<input type='text' id='{$params[0]}' value='$value' ";
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$code.="$key='$value' ";
			}
		}
		$code.=" />\r\n";
		return $code;
	}

	/**
	 * Crea una caja de texto que solo acepta numeros y los formatea como moneda
	 *
	 * @param 	mixed $params
	 * @return 	string
	 * @static
	 */
	public static function moneyField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])){
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']){
			$params['name'] = $params[0];
		}
		if(isset($params['value'])){
			$value = $params['value'];
			unset($params['value']);
		} else {
			$value = self::getValueFromAction($params[0]);
		}
		if(!isset($params['onkeydown'])) {
			$params['onkeydown'] = "valNumeric(event)";
		} else {
			$params['onkeydown'].=";valNumeric(event)";
		}
		if(!isset($params['formatOptions'])){
			$params['formatOptions'] = '';
		}
		if(isset($params['objectFormat'])){
			if(!isset($params['onblur'])) {
				$params['onblur'] = "this.value={$params['objectFormat']}.money(this.value);";
			} else {
				$params['onblur'].= ";this.value={$params['objectFormat']}.money(this.value);";
			}
			if(!isset($params['onfocus'])) {
				$params['onfocus'] = "this.value={$params['objectFormat']}.deFormat(this.value,\"money\");this.activate();";
			} else {
				$params['onfocus'].= ";this.value={$params['objectFormat']}.deFormat(this.value,\"money\");this.activate();";
			}
			$codeAlt = "<script type='text/javascript'>\n\t$('{$params[0]}').value={$params['objectFormat']}.money($('{$params[0]}').value);\n</script>\r\n";
			unset($params['objectFormat']);
		}else{
			if(!isset($params['onblur'])) {
				$params['onblur'] = "defaultFormater=new Format({$params['formatOptions']});this.value=defaultFormater.money(this.value);";
			} else {
				$params['onblur'].=";defaultFormater=new Format({$params['formatOptions']});this.value=defaultFormater.money(this.value);";
			}
			if(!isset($params['onfocus'])) {
				$params['onfocus'] = "defaultFormater=new Format({$params['formatOptions']});this.value=defaultFormater.deFormat(this.value,\"money\");this.activate();";
			} else {
				$params['onfocus'].= ";defaultFormater=new Format({$params['formatOptions']});this.value=defaultFormater.deFormat(this.value,\"money\");this.activate();";
			}
			$codeAlt = "<script type='text/javascript'>\n\tdefaultFormater=new Format({$params['formatOptions']});\n\t$('{$params[0]}').value=defaultFormater.money($('{$params[0]}').value);\n</script>\r\n";
		}
		unset($params['formatOptions']);
		$params['format'] = 'money';
		$code = "<input type='text' id='{$params[0]}' value='$value' ";
		foreach($params as $key => $val){
			if(!is_integer($key)){
				$code.="$key='$val' ";
			}
		}
		$code.=" />\r\n";
		if(isset($codeAlt) && (!empty($value) || $value == 0)){
			$code.= $codeAlt;
		}
		return $code;
	}

	/**
	 * Crea una caja de texto que solo acepta numeros y los formatea como porcentaje
	 *
	 * @param 	mixed $params
	 * @return 	string
	 * @static
	 */
	public static function percentField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])){
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']){
			$params['name'] = $params[0];
		}
		if(isset($params['value'])){
			$value = $params['value'];
			unset($params['value']);
		} else {
			$value = self::getValueFromAction($params[0]);
		}
		if(!isset($params['onkeydown'])) {
			$params['onkeydown'] = "valNumeric(event)";
		} else {
			$params['onkeydown'].=";valNumeric(event)";
		}
		if(!isset($params['formatOptions'])){
			$params['formatOptions'] = '';
		}
		if(isset($params['objectFormat'])){
			if(!isset($params['onblur'])) {
				$params['onblur'] = "this.value={$params['objectFormat']}.percent(this.value);";
			} else {
				$params['onblur'].= ";this.value={$params['objectFormat']}.percent(this.value);";
			}
			if(!isset($params['onfocus'])) {
				$params['onfocus'] = "this.value={$params['objectFormat']}.deFormat(this.value,\"percent\");this.activate();";
			} else {
				$params['onfocus'].= ";this.value={$params['objectFormat']}.deFormat(this.value,\"percent\");this.activate();";
			}
			$codeAlt = "<script type='text/javascript'>\n\t$('{$params[0]}').value={$params['objectFormat']}.percent($('{$params[0]}').value);\n</script>\r\n";
			unset($params['objectFormat']);
		}else{
			if(!isset($params['onblur'])) {
				$params['onblur'] = "defaultFormater=new Format({$params['formatOptions']});this.value=defaultFormater.percent(this.value);";
			} else {
				$params['onblur'].=";defaultFormater=new Format({$params['formatOptions']});this.value=defaultFormater.percent(this.value);";
			}
			if(!isset($params['onfocus'])) {
				$params['onfocus'] = "defaultFormater=new Format({$params['formatOptions']});this.value=defaultFormater.deFormat(this.value,\"percent\");this.activate();";
			} else {
				$params['onfocus'].= ";defaultFormater=new Format({$params['formatOptions']});this.value=defaultFormater.deFormat(this.value,\"percent\");this.activate();";
			}
			$codeAlt = "<script type='text/javascript'>\n\tdefaultFormater=new Format({$params['formatOptions']});\n\t$('{$params[0]}').value=defaultFormater.percent($('{$params[0]}').value);\n</script>\r\n";
		}
		unset($params['formatOptions']);
		$params['format'] = 'percent';
		$code = "<input type='text' id='{$params[0]}' value='$value' ";
		foreach($params as $key => $val){
			if(!is_integer($key)){
				$code.="$key='$val' ";
			}
		}
		$code.=" />\r\n";
		if(isset($codeAlt) && (!empty($value) || $value == 0)){
			$code.= $codeAlt;
		}
		return $code;
	}

	/**
	 * Crea una caja de password que solo acepta numeros
	 *
	 * @param 	mixed $params
	 * @return 	string
	 */
	public static function numericPasswordField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		$value = self::getValueFromAction($params);
		if(!isset($params[0])||!$params[0]) {
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']){
			$params['name'] = $params[0];
		}
		if(!$value) {
			$value = isset($params['value']) ? $params['value'] : "";
		}
		if(!isset($params['onkeydown'])) {
			$params['onkeydown'] = "valNumeric(event)";
		} else {
			$params['onkeydown'].=";valNumeric(event)";
		}
		$code = "<input type='password' id='{$params[0]}' value='$value' ";
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$code.="$key='$value' ";
			}
		}
		$code.=" />\r\n";
		return $code;
	}

	/**
	 * Crea un campo que acepta solo fechas
	 *
	 * @access 	public
	 * @param 	mixed $params
	 * @return 	string
	 * @static
	 */
	public static function dateField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])){
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']) {
			$params['name'] = $params[0];
		}
		if(isset($params['value'])){
			$value = $params['value'];
			unset($params['value']);
		} else {
			$value = self::getValueFromAction($params[0]);
		}
		if($value){
			$ano = substr($value, 0, 4);
			$mes = substr($value, 5, 2);
			$dia = substr($value, 8, 2);
		} else {
			$ano = date('Y');
			$mes = 0;
			$dia = 0;
		}
		if(isset($params['useDummy'])&&$params['useDummy']){
			$useDummy = true;
			unset($params['useDummy']);
		} else {
			$useDummy = false;
		}
		$attributes = array();
		foreach($params as $_key => $_value){
			if(in_array($_key, array('name'))==false&&!is_integer($_key)){
				$attributes[] = "$_key='$_value'";
			}
		}
		$code ="<table ".join(" ", $attributes)."><tr><td>";
		if(self::$_useLocale){
			$locale = Locale::getApplication();
			if($locale->isDefaultLocale()==false){
				$meses = array();
				$i = 1;
				foreach($locale->getAbrevMonthList() as $month){
					$meses[sprintf('%02s', $i)] = ucfirst($month);
					++$i;
				}
			}
		}
		if(!isset($meses)){
			$meses = array(
				'01' => 'Ene', '02' => 'Feb',
				'03' => 'Mar', '04' => 'Abr',
				'05' => 'May', '06' => 'Jun',
				'07' => 'Jul', '08' => 'Ago',
				'09' => 'Sep', '10' => 'Oct',
				'11' => 'Nov', '12' => 'Dic'
				);
		}
		if($useDummy){
			$displayJS = 'if(this.selectedIndex>0){$(\''.$params[0].'_day\').show();$(\''.$params[0].'_year\').show();}else{$(\''.$params[0].'_day\').hide();$(\''.$params[0].'_year\').hide();$(\''.$params[0].'\').value = \'\'};';
		} else {
			$displayJS = '';
		}
		$code .= "<select id='{$params[0]}_month' onchange=\"$displayJS$('{$params[0]}').value = $('{$params[0]}_year').options[$('{$params[0]}_year').selectedIndex].value+'-'+\$F('{$params[0]}_month')+'-'+\$F('{$params[0]}_day')\">";
		if($useDummy){
			$code.="<option value='@'>Sel...</option>\n";
		}
		foreach($meses as $numero_mes => $nombre_mes){
			if($numero_mes==$mes){
				$code.="<option value='$numero_mes' selected='selected'>$nombre_mes</option>\n";
			} else {
				$code.="<option value='$numero_mes'>$nombre_mes</option>\n";
			}
		}
		$code.="</select></td><td>";

		if($useDummy){
			$display = 'style="display:none"';
		} else {
			$display = '';
		}
		$code.="<select id='{$params[0]}_day' onchange=\"$('{$params[0]}').value = $('{$params[0]}_year').options[$('{$params[0]}_year').selectedIndex].value+'-'+$('{$params[0]}_month').options[$('{$params[0]}_month').selectedIndex].value+'-'+$('{$params[0]}_day').options[$('{$params[0]}_day').selectedIndex].value;\" $display>";
		for($i=1;$i<=31;++$i){
			$n = $i<10 ? '0'.$i : $i;
			if($n==$dia){
				$code.="<option value='$n' selected='selected'>$n</option>\n";
			} else {
				$code.="<option value='$n'>$n</option>\n";
			}
		}
		$code.="</select></td><td>";
		if($useDummy){
			$display = 'style="display:none"';
		} else {
			$display = '';
		}
		$code.="<select id='{$params[0]}_year' onchange=\"$('{$params[0]}').value = $('{$params[0]}_year').options[$('{$params[0]}_year').selectedIndex].value+'-'+$('{$params[0]}_month').options[$('{$params[0]}_month').selectedIndex].value+'-'+$('{$params[0]}_day').options[$('{$params[0]}_day').selectedIndex].value\" $display>\n";
		if(isset($params['startYear'])){
			$startYear = $params['startYear'];
		} else {
			$startYear = 1900;
		}
		if(isset($params['finalYear'])){
			$finalYear = $params['finalYear'];
		} else {
			$finalYear = date('Y')+5;
		}
		for($i=$finalYear;$i>=$startYear;$i--){
			if($i==$ano){
				$code.="<option value='$i' selected='selected'>$i</option>\n";
			} else {
				$code.="<option value='$i'>$i</option>\n";
			}
		}
		$code.="</select></td><td>";
		$code.="</table>";
		$code.="<input type='hidden' id='{$params[0]}' name='{$params[0]}' value='$value' />";

		return $code;
	}

	/**
	 * Crea un campo para la captura de fechas que permite personalizar
	 * los meses de acuerdo a la localizacion
	 *
	 * @access 	public
	 * @param 	mixed $params
	 * @param 	Traslate $traslate
	 * @return 	string
	 * @static
	 */
	public static function localeDateField($params, $traslate){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])){
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']) {
			$params['name'] = $params[0];
		}

		if(isset($params['value'])){
			$value = $params['value'];
			unset($params['value']);
		} else {
			$value = self::getValueFromAction($params[0]);
		}

		if($value){
			$ano = substr($value, 0, 4);
			$mes = substr($value, 5, 2);
			$dia = substr($value, 8, 2);
		} else {
			$ano = date('Y');
			$mes = 0;
			$dia = 0;
		}

		$attributes = array();
		foreach($params as $_key => $_value){
			if(in_array($_key, array("name"))==false&&!is_integer($_key)){
				$attributes[] = "$_key = '$_value'";
			}
		}

		$code ="<table ".join(" ", $attributes)."><tr><td>";

		$meses = array(
			'01' => $traslate->_('Ene'),
			'02' => $traslate->_('Feb'),
			'03' => $traslate->_('Mar'),
			'04' => $traslate->_('Abr'),
			'05' => $traslate->_('May'),
			'06' => $traslate->_('Jun'),
			'07' => $traslate->_('Jul'),
			'08' => $traslate->_('Ago'),
			'09' => $traslate->_('Sep'),
			'10' => $traslate->_('Oct'),
			'11' => $traslate->_('Nov'),
			'12' => $traslate->_('Dic'),
		);
		$code .= "<select name='{$params[0]}_month' id='{$params[0]}_month' onchange=\"$('{$params[0]}').value = $('{$params[0]}_year').options[$('{$params[0]}_year').selectedIndex].value+'-'+$('{$params[0]}_month').options[$('{$params[0]}_month').selectedIndex].value+'-'+$('{$params[0]}_day').options[$('{$params[0]}_day').selectedIndex].value\">";
		foreach($meses as $numero_mes => $nombre_mes){
			if($numero_mes==$mes){
				$code.="<option value='$numero_mes' selected='selected'>$nombre_mes</option>\n";
			} else {
				$code.="<option value='$numero_mes'>$nombre_mes</option>\n";
			}
		}
		$code.="</select></td><td>";

		$code.="<select name='{$params[0]}_day' id='{$params[0]}_day' onchange=\"$('{$params[0]}').value = $('{$params[0]}_year').options[$('{$params[0]}_year').selectedIndex].value+'-'+$('{$params[0]}_month').options[$('{$params[0]}_month').selectedIndex].value+'-'+$('{$params[0]}_day').options[$('{$params[0]}_day').selectedIndex].value\">";
		for($i=1;$i<=31;++$i){
			$n = sprintf("%02s", $i);
			if($n==$dia){
				$code.="<option value='$n' selected='selected'>$n</option>\n";
			} else {
				$code.="<option value='$n'>$n</option>\n";
			}
		}
		$code.="</select></td><td>";

		$code.="<select name='{$params[0]}_year' id='{$params[0]}_year' onchange=\"$('{$params[0]}').value = $('{$params[0]}_year').options[$('{$params[0]}_year').selectedIndex].value+'-'+$('{$params[0]}_month').options[$('{$params[0]}_month').selectedIndex].value+'-'+$('{$params[0]}_day').options[$('{$params[0]}_day').selectedIndex].value\">";
		if(isset($params['startYear'])){
			$startYear = $params['startYear'];
		} else {
			$startYear = 1900;
		}
		if(isset($params['finalYear'])){
			$finalYear = $params['finalYear'];
		} else {
			$finalYear = date('Y')+5;
		}
		for($i=$finalYear;$i>=$startYear;$i--){
			if($i==$ano){
				$code.="<option value='$i' selected='selected'>$i</option>\n";
			} else {
				$code.="<option value='$i'>$i</option>\n";
			}
		}
		$code.="</select></td><td>";
		$code.="</table>";

		$code.="<input type='hidden' id='{$params[0]}' name='{$params[0]}' value='$value' />";

		return $code;
	}

	/**
	 * Crea un combo que toma los valores de un array
	 *
	 * @param 	mixed $params
	 * @param 	string $data
	 * @return 	string
	 */
	public static function selectStatic($params='', $data=''){
		$numberArguments = func_num_args();
		$arguments = func_get_args();
		$params = Utils::getParams($arguments, $numberArguments);
		if(is_array($params)){
			$value = "";
			if(!isset($params['value'])){
				$value = self::getValueFromAction($params[0]);
			} else {
				$value = $params['value'];
			}
			$code ="<select id='{$params[0]}' name='{$params[0]}' ";
			if(!isset($params['dummyValue'])){
				$dummyValue = '@';
			} else {
				$dummyValue = $params['dummyValue'];
				unset($params['dummyValue']);
			}
			if(!isset($params['dummyText'])){
				$dummyText = 'Seleccione...';
			} else {
				$dummyText = $params['dummyText'];
				unset($params['dummyText']);
			}
			if(is_array($params)){
				foreach($params as $at => $val){
					if(!is_integer($at)){
						if(!is_array($val)){
							$code.="$at='".$val."' ";
						}
					}
				}
			}
			$code.=">\r\n";
			if(isset($params['use_dummy'])&&$params['use_dummy']){
				$code.="\t<option value='$dummyValue'>$dummyText</option>\r\n";
				unset($params['use_dummy']);
			} else {
				if(isset($params['useDummy'])&&$params['useDummy']){
					$code.="\t<option value='$dummyValue'>$dummyText</option>\r\n";
					unset($params['useDummy']);
				}
			}
			if(is_array($params[1])){
				foreach($params[1] as $k => $d){
					if($k==$value && $value!==''){
						$code.="\t<option value='$k' selected='selected'>$d</option>\r\n";
					} else {
						$code.="\t<option value='$k'>$d</option>\r\n";
					}
				}
			}
			$code.= "</select>\r\n";
		}
		return $code;
	}

	/**
	 * Crea una lista SELECT
	 *
	 * @access 	public
	 * @param 	mixed $params
	 * @param 	array $data
	 * @static
	 */
	public static function select($params='', $data=''){
		if(func_num_args()>1){
			$numberArguments = func_num_args();
			$params = Utils::getParams(func_get_args(), $numberArguments);
		}
		if(is_array($params)){
			if(!isset($params['value'])){
				$value = self::getValueFromAction($params[0]);
			} else {
				$value = $params['value'];
			}
			$callback = false;
			if(isset($params['option_callback'])){
				if(strpos($params['option_callback'], ".")){
					$callback = explode(".", $params['option_callback']);
				} else {
					$callback = $params['option_callback'];
				}
				if(is_callable($callback)==false){
					throw new TagException("El option_callback no es valido");
				}
				unset($params['option_callback']);
			}
			$code ="<select id='{$params[0]}' name='{$params[0]}' ";
			if(is_array($params)){
				foreach($params as $at => $val){
					if(!is_integer($at)){
						if(!is_array($val)&&!in_array($at, array('using', 'use_dummy'))){
							$code.="$at='".$val."' ";
						}
					}
				}
			}
			$code.=">\r\n";

			if(!isset($params['dummyValue'])){
				$dummyValue = '@';
			} else {
				$dummyValue = $params['dummyValue'];
				unset($params['dummyValue']);
			}
			if(!isset($params['dummyText'])){
				$dummyText = 'Seleccione...';
			} else {
				$dummyText = $params['dummyText'];
				unset($params['dummyText']);
			}

			if(isset($params['use_dummy'])&&$params['use_dummy']==true){
				$code.="\t<option value='$dummyValue'>$dummyText</option>\r\n";
			} else {
				if(isset($params['useDummy'])&&$params['useDummy']==true){
					$code.="\t<option value='$dummyValue'>$dummyText...</option>\r\n";
				}
			}
			if(is_object($params[1])){
				if(!isset($params['using'])){
					throw new TagException("Debe indicar el parámetro 'using' para el helper Tag::select()");
				}
				$using = explode(",", $params['using']);
				foreach($params[1] as $o){
					if($callback==false){
						if($value==$o->readAttribute($using[0])){
							$code.="\t<option selected='selected' value='".trim($o->readAttribute($using[0]))."'>".trim($o->readAttribute($using[1]))."</option>\r\n";
						} else {
							$code.="\t<option value='".trim($o->readAttribute($using[0]))."'>".trim($o->readAttribute($using[1]))."</option>\r\n";
						}
					} else {
						$code.=call_user_func_array($callback, array($o, $value));
					}
				}
			} else {
				if(is_array($params[1])){
					foreach($params[1] as $d){
						$code.="\t<option value='{$d[0]}'>{$d[1]}</option>\r\n";
					}
				} else {
					throw new TagException("La collección de opciones no es valida");
				}
			}
			$code.= "</select>\r\n";
		} else {
			$code = "<select id='$params' name='$params'></select>";
		}
		return $code;
	}

	/**
	 * Crea una lista SELECT cuyos textos de las opciones estan localizados
	 *
	 * @access 	public
	 * @param 	mixed $params
	 * @param 	array $data
	 * @param 	Traslate $traslate
	 * @return 	string
	 * @static
	 */
	public static function localeSelect($params='', $data='', $traslate){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(is_array($params)){
			if(!isset($params['value'])){
				$value = self::getValueFromAction($params[0]);
			} else {
				$value = $params['value'];
			}
			$callback = false;
			if(isset($params['option_callback'])){
				if(strpos($params['option_callback'], '.')){
					$callback = explode('.', $params['option_callback']);
				} else {
					$callback = $params['option_callback'];
				}
				if(is_callable($callback)==false){
					throw new TagException('El option_callback no es valido');
				}
				unset($params['option_callback']);
			}
			$code ="<select id='{$params[0]}' name='{$params[0]}' ";
			if(is_array($params)){
				foreach($params as $at => $val){
					if(!is_integer($at)){
						if(!is_array($val)&&!in_array($at, array('using', 'use_dummy'))){
							$code.="$at='".$val."' ";
						}
					}
				}
			}
			$code.=">\r\n";
			if(isset($params['use_dummy'])&&$params['use_dummy']==true){
				$code.="\t<option value='@'>Seleccione...</option>\r\n";
			}
			if(is_object($params[1])){
				if(!isset($params['using'])){
					throw new TagException("Debe indicar el parámetro 'using' para el helper Tag::select()");
				}
				$using = explode(",", $params['using']);
				foreach($params[1] as $o){
					if($callback==false){
						if($value==$o->readAttribute($using[0])){
							$code.="\t<option selected='selected' value='{$o->readAttribute($using[0])}'>".$traslate->_($o->readAttribute($using[1]))."</option>\r\n";
						} else {
							$code.="\t<option value='{$o->readAttribute($using[0])}'>".$traslate->_($o->readAttribute($using[1]))."</option>\r\n";
						}
					} else {
						$code.=call_user_func_array($callback, array($o, $value));
					}
				}
			} else {
				foreach($params[1] as $d){
					$code.="\t<option value='{$d[0]}'>{$d[1]}</option>\r\n";
				}
			}
			$code.= "</select>\r\n";
		} else {
			$code.="<select id='$params' name='$params'></select>";
		}
		return $code;
	}

	/**
	 * Crea una lista SELECT con datos de modelos y de arrays
	 *
	 * @access 	public
	 * @param 	string $name
	 * @param 	string $modelData
	 * @param 	array $arrayData
	 * @return 	string
	 * @static
	 */
	public static function selectMixed($name='', $modelData='', $arrayData=''){
		if(func_num_args()>1){
			$numberArguments = func_num_args();
			$params = Utils::getParams(func_get_args(), $numberArguments);
		}
		if(is_array($params)){
			if(!isset($params['value'])){
				$value = self::getValueFromAction($params[0]);
			} else {
				$value = $params['value'];
			}
			$callback = false;
			if(isset($params['option_callback'])){
				if(strpos($params['option_callback'], ".")){
					$callback = explode(".", $params['option_callback']);
				} else {
					$callback = $params['option_callback'];
				}
				if(is_callable($callback)==false){
					throw new TagException("El option_callback no es valido");
				}
				unset($params['option_callback']);
			}
			$code ="<select id='{$params[0]}' name='{$params[0]}' ";
			if(is_array($params)){
				foreach($params as $_attribute => $_value){
					if(!is_integer($_attribute)){
						if(!is_array($_value)&&!in_array($_attribute, array('using', 'use_dummy'))){
							$code.="$_attribute='$_value' ";
						}
					}
				}
			}
			$code.=">\r\n";
			if(isset($params['use_dummy'])&&$params['use_dummy']==true){
				$code.="\t<option value='@'>Seleccione...</option>\r\n";
			}
			if(is_array($arrayData)){
				foreach($arrayData  as $k => $d){
					if($k==$value){
						$code.="\t<option value='$k' selected='selected'>$d</option>\r\n";
					} else {
						$code.="\t<option value='$k'>$d</option>\r\n";
					}
				}
			}
			if(is_object($params[1])){
				if(!isset($params['using'])){
					throw new TagException("Debe indicar el parámetro 'using' para el helper Tag::select()");
				}
				$using = explode(",", $params['using']);
				foreach($params[1] as $o){
					if($callback==false){
						if($value==$o->readAttribute($using[0])){
							$code.="\t<option selected='selected' value='{$o->readAttribute($using[0])}'>{$o->readAttribute($using[1])}</option>\r\n";
						} else {
							$code.="\t<option value='{$o->readAttribute($using[0])}'>{$o->readAttribute($using[1])}</option>\r\n";
						}
					} else {
						$code.=call_user_func_array($callback, array($o, $value));
					}
				}
			} else {
				foreach($params[1] as $d){
					$code.="\t<option value='{$d[0]}'>{$d[1]}</option>\r\n";
				}
			}
			$code.= "</select>\r\n";
		} else {
			$code.="<select id='$params' name='$params'></select>";
		}
		return $code;
	}

	/**
	 * Carga el framework javascript y funciones auxiliares
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	public static function javascriptBase($validations=true){
		$path = Core::getInstancePath();
		$code = "<script type='text/javascript' src='".$path."javascript/core/base.js'></script>\r\n";
		if($validations==true){
			$code.= "<script type='text/javascript' src='".$path."javascript/core/validations.js'></script>\r\n";
		}
		$code.= Tag::javascriptLocation();
		return $code;
	}

	/**
	 * Imprime la ubicación javascript
	 *
	 * @return string
	 */
	public static function javascriptLocation(){
		$application = Router::getActiveApplication();
		$controllerName = Router::getController();
		$actionName = Router::getAction();
		$module = Router::getModule();
		$id = Router::getId();
		$path = Core::getInstancePath();
		return "<script type='text/javascript' src='".$path."javascript/core/main.php?app=$application&module=$module&path=".urlencode($path)."&controller=$controllerName&action=$actionName&id=$id'></script>\r\n";
	}

	/**
	 * Devuelve la ubicación javascript
	 *
	 * @return string
	 */
	public static function getJavascriptLocation(){
		$application = Router::getActiveApplication();
		$controllerName = Router::getController();
		$actionName = Router::getAction();
		$module = Router::getModule();
		$id = Router::getId();
		$path = Core::getInstancePath();
		return "<script type=\"text/javascript\">\$Kumbia={app:\"$application\",path:\"$path\",controller:\"$controllerName\",action:\"$actionName\",id:\"$id\"}</script>\n";
	}

	/**
	 * Genera una etiqueta script que apunta a un archivo JavaScript
	 * respetando las rutas y convenciones de Kumbia
	 *
	 * @param string $src
	 * @param string $cache
	 * @return string
	 */
	public static function javascriptInclude($src='', $noCache=true, $parameters=""){
		if($src==""){
			$src = Router::getController();
		}
		$src.='.js';
		if(!$noCache){
			$cache = mt_rand(0, 999999);
			$src.="?nocache=".$cache;
			if($parameters){
				$src.="&".$parameters;
			}
		} else {
			if($parameters){
				$src.="?".$parameters;
			}
		}
		$instancePath = Core::getInstancePath();
		return "<script type='text/javascript' src='{$instancePath}javascript/$src'></script>\r\n";
	}

	/**
	 * Incluye una etiqueta SCRIPT con un recurso javascript minizado
	 *
	 * @param string $src
	 */
	public static function javascriptMinifiedInclude($src){
		$jsSource = 'public/javascript/'.$src.'.js';
		$jsMinSource = 'public/javascript/'.$src.'.min.js';
		if(file_exists($jsMinSource)==false){
			if(class_exists('Jsmin')==false){
				require 'Library/Kumbia/Tag/Jsmin/Jsmin.php';
			}
			$minified = Jsmin::minify(file_get_contents($jsSource));
			file_put_contents($jsMinSource, $minified);
		} else {
			if(filemtime($jsSource)>filemtime($jsMinSource)){
				if(class_exists('Jsmin')==false){
					require 'Library/Kumbia/Tag/Jsmin/Jsmin.php';
				}
				$minified = Jsmin::minify(file_get_contents($jsSource));
				file_put_contents($jsMinSource, $minified);
			}
		}
		return self::javascriptInclude($src.'.min');
	}

	/**
	 * Crea un boton de submit tipo imagen para el formulario actual
	 *
	 * @access 	public
	 * @param 	string $caption
	 * @param 	string $src
	 * @return 	string
	 * @static
	 */
	public static function submitImage($caption, $src){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params['caption'])){
			$params['caption'] = $params[0];
		}
		if(!isset($params['src'])){
			$params['src'] = $params[1];
		}
		$code = "<input type='image' src='{$params['src']}' value='{$params['caption']}' ";
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$code.="$key='$value' ";
			}
		}
		$code.=" />\r\n";
		return $code;
	}

	/**
	 * Crea un boton HTML
	 *
	 * @return string
	 * @static
	 */
	public static function button(){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params['value'])){
			$params['value'] = $params[0];
		}
		if(isset($params['id'])&&$params['id']&&!isset($params['name'])) {
			$params['name'] = $params['id'];
		}
		if(!isset($params['id'])) {
			$params['id'] = isset($params['name']) ? $params['name'] : "";
		}
		$code = "<input type='button' ";
		foreach($params as $key => $value){
			if(!is_integer($key)&&$key!=$params){
				$code.="$key=\"$value\" ";
			}
		}
		return $code." />\r\n";
	}

	/**
	 * Agrega una etiqueta script que apunta a un archivo en public/javascript/kumbia
	 *
	 * @param string $src
	 * @return string
	 */
	public static function javascriptLibrary($src){
		$instancePath = Core::getInstancePath();
		return "<script type='text/javascript' src='".$instancePath."javascript/core/$src.js'></script>\r\n";
	}

	/**
	 * Permite incluir una imagen dentro de una vista respetando
	 * las convenciones de directorios y rutas en Kumbia
	 *
	 * @param string $img
	 * @return string
	 * @static
	 */
	public static function image($img){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		$code = "";
		if(!isset($params['src'])||!$params['src']){
			$instancePath = Core::getInstancePath();
			$code.="<img src=\"{$instancePath}img/{$params[0]}\" ";
		} else {
			$code.="<img src=\"{$params['src']}\" ";
			unset($params['src']);
		}
		if(!isset($params['alt'])||!$params['alt']) {
			$params['alt'] = "";
		}
		if(is_array($params)){
			if(!$params['alt']){
				$params['alt'] = "";
			}
			foreach($params as $at => $val){
				if(!is_integer($at)){
					$code.="$at=\"".$val."\" ";
				}
			}
		}
		$code.= "/>";
		return $code;
	}

	/**
	 * Permite generar un formulario remoto
	 *
	 * @param 	mixed $params
	 * @return 	string
	 */
	public static function formRemote($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params['action'])||!$params['action']) {
			$params['action'] = $params[0];
		}
		$params['callbacks'] = array();
		$id = Router::getId();
		if(isset($params['complete'])&&$params['complete']){
			$params['callbacks'][] = ' complete: function(){ '.$params['complete'].' }';
		}
		if(isset($params['before'])&&$params['before']){
			$params['callbacks'][] = ' before: function(){ '.$params['before'].' }';
		}
		if(isset($params['success'])&&$params['success']){
			$params['callbacks'][] = ' success: function(){ '.$params['success'].' }';
		}
		if(isset($params['required'])&&$params['required']){
			$requiredFields = array();
			foreach($params['required'] as $required){
				$requiredFields[] = "'".$required."'";
			}
			$requiredFields = join(',', $requiredFields);
			$code = "<form action='".Utils::getKumbiaUrl($params['action'].'/'.$id)."' method='post'
			onsubmit='if(validaForm(this,new Array({$requiredFields}))){ return ajaxRemoteForm(this,\"{$params['update']}\",{".join(",",$params['callbacks'])."}); } else{ return false; }'";
			unset($params['required']);
		} else{
			if(!isset($params['update'])){
				throw new ViewException('Debe indicar el contenedor a actualizar con el parámetro "update"');
			}
			$code = "<form action='".Utils::getKumbiaUrl($params['action'].'/'.$id)."' method='post'
			onsubmit='return ajaxRemoteForm(this, \"{$params['update']}\", { ".join(",", $params['callbacks'])." });'";
		}
		foreach($params as $at => $val){
			if(!is_integer($at)&&(!in_array($at, array('action', 'complete', 'before', 'success', 'callbacks')))){
				$code.="$at=\"".$val."\" ";
			}
		}
		return $code.=">\r\n";
	}

	/**
	 * Crea un boton de submit para el formulario remoto actual
	 *
	 * @param string $caption
	 * @return string
	 */
	public static function submitRemote($caption){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!$params['caption']) {
			$params['caption'] = $params[0];
		}
		$params['callbacks']	= array();
		if($params['complete']){
			$params['callbacks'][] = " complete: function(){ ".$params['complete']." }";
		}
		if($params['before']){
			$params['callbacks'][] = " before: function(){ ".$params['before']." }";
		}
		if($params['success']){
			$params['callbacks'][] = " success: function(){ ".$params['success']." }";
		}
		$code = "<input type='submit' value='{$params['caption']}' ";
		foreach($params as $at => $value){
			if(!is_integer($at)&&(!in_array($at, array("action", "complete", "before", "success", "callbacks", "caption", "update")))){
				$code.="$at='$value' ";
			}
		}
		$code.=" onclick='return ajaxRemoteForm(this.form, \"{$params['update']}\")' />\r\n";
		return $code;
	}

	/**
	 * Establece una etiqueta meta
	 *
	 * @access public
	 * @param string $name
	 * @param string $content
	 * @static
	 */
	public static function setMeta($name, $content){
		MemoryRegistry::prepend('CORE_META_TAGS', "<meta name='$name' content='$content'/>\r\n");
	}

	/**
	 * Imprime las metas cargadas
	 *
	 * @access public
	 * @static
	 */
	public static function getMetas(){
		$metas = MemoryRegistry::get('CORE_META_TAGS');
		if(is_array($metas)){
			foreach($metas as $meta){
				echo $meta;
			}
		}
	}

	/**
	 * Establece el titulo del documento HTML
	 *
	 * @access public
	 * @param string $title
	 * @static
	 */
	public static function setDocumentTitle($title){
		self::$_documentTitle = $title;
	}

	/**
	 * Agrega al final un texto del titulo actual del documento HTML
	 *
	 * @access public
	 * @param string $title
	 * @static
	 */
	public static function appendDocumentTitle($title){
		self::$_documentTitle.= $title;
	}

	/**
	 * Agrega al prinicipio un texto del titulo actual del documento HTML
	 *
	 * @access public
	 * @param string $title
	 * @static
	 */
	public static function prependDocumentTitle($title){
		self::$_documentTitle = $title.self::$_documentTitle;
	}

	/**
	 * Devuelve el titulo del documento HTML
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	public static function getDocumentTitle(){
		return '<title>'.self::$_documentTitle.'</title>'."\r\n";
	}

	/**
	 * Agrega una etiqueta link para incluir un archivo CSS respetando
	 * las rutas y convenciones de Kumbia
	 *
	 * @access public
	 * @param string $src
	 * @param boolean $useVariables
	 * @static
	 */
	public static function stylesheetLink($src='', $useVariables=false, $parameters=""){
		if(!$src) {
			$src = Router::getController();
		}
		$instancePath = Core::getInstancePath();
		if($useVariables==true){
			if($instancePath){
				$kb = substr($instancePath, 0, strlen($instancePath)-1);
			} else {
				$kb = '/';
			}
			$code = "<link rel='stylesheet' type='text/css' href='".$instancePath."css.php?c=$src&p=$kb&$parameters' />\r\n";
		} else {
			if($parameters!=""){
				$parameters = "?".$parameters;
			}
			$code = "<link rel='stylesheet' type='text/css' href='".$instancePath."css/$src.css$parameters' />\r\n";
		}
		MemoryRegistry::prepend('CORE_CSS_IMPORTS', $code);
		return $code;
	}

	/**
	 * Devuelve los CSS cargados mediante Tag::stylesheetLink
	 *
	 * @access 	public
	 * @return 	string
	 * @static
	 */
	public static function stylesheetLinkTags(){
		$styleSheets = MemoryRegistry::get('CORE_CSS_IMPORTS');
		$code = '';
		if(is_array($styleSheets)){
			foreach($styleSheets as $css){
				$code.= $css;
			}
		}
		return $code;
	}

	/**
	 * Resetea los CSS cargados mediante Tag::styleSheetLink
	 *
	 * @access public
	 * @static
	 */
	public static function resetStylesheetLinks(){
		MemoryRegistry::reset('CORE_CSS_IMPORTS');
	}

	/**
	 * Elimina los tags agregados a la salida
	 *
	 * @access public
	 * @static
	 */
	public static function removeStylesheets(){
		MemoryRegistry::reset('CORE_CSS_IMPORTS');
	}

	/**
	 * Crea una etiqueta de formulario
	 *
	 * @access 	public
	 * @param 	string $action
	 * @return 	string
	 * @static
	 */
	public static function form($action){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		$id = Router::getId();
		if($action==''){
			$action = isset($params['action']) ? $params['action'] : '';
		}
		if(!isset($params['method'])||!$params['method']) {
			$params['method'] = 'post';
		}
		if(isset($params['confirm'])&&$params['confirm']){
			$params['onsubmit'].= $params['onsubmit'].";if(!confirm(\"{$params['confirm']}\")) { return false; }";
			unset($params['confirm']);
		}
		if($id===null||$id===''){
			$action = Utils::getKumbiaUrl($action);
		} else {
			$action = Utils::getKumbiaUrl($action.'/'.$id);
		}
		if(isset($params['parameters'])){
			$action.= '?'.$params['parameters'];
		}
		$str = "<form action='".$action."' ";
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$str.= "$key='$value' ";
			}
		}
		return $str.">\r\n";
	}

	/**
	 * Etiqueta para cerrar un formulario
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	public static function endForm(){
		return "</form>\r\n";
	}

	/**
	 * Crea una caja de Texto
	 *
	 * @access 	public
	 * @param 	mixed $params
	 * @return 	string
	 * @static
	 */
	static public function textField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])) {
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']){
			$params['name'] = $params[0];
		}
		if(isset($params['value'])){
			$value = $params['value'];
			unset($params['value']);
		} else {
			$value = self::getValueFromAction($params[0]);
		}
		$code = "<input type='text' id='{$params[0]}' value='$value' ";
		foreach($params as $_key => $_value){
			if(!is_integer($_key)){
				$code.="$_key='$_value' ";
			}
		}
		$code.=" />\r\n";
		return $code;
	}

	/**
	 * Crea un componente para capturar Passwords
	 *
	 * @param 	mixed $params
	 * @return 	string
	 */
	static public function passwordField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!is_array($params)){
			return "<input type='password' id='$params' name='$params'/>\r\n";
		} else {
			if(!isset($params[0])) {
				$params[0] = $params['id'];
			}
			if(!isset($params['name'])||!$params['name']) {
				$params['name'] = $params[0];
			}
			if(!isset($params['value'])){
				$params['value'] = self::getValueFromAction($params[0]);
			}
			$code = "<input type='password' id='{$params[0]}' ";
			foreach($params as $key => $value){
				if(!is_integer($key)){
					$code.="$key='$value' ";
				}
			}
			$code.=" />\r\n";
			return $code;
		}
	}

	/**
	 * Crea un botón de submit para el formulario actual
	 *
	 * @access	public
	 * @param	string $caption
	 * @return	string
	 * @static
	 */
	public static function submitButton($caption){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params['caption'])) {
			$params['caption'] = $params[0];
		} else {
			if(!$params['caption']) {
				$params['caption'] = $params[0];
			}
		}
		$code = "<input type='submit' value='{$params['caption']}' ";
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$code.="$key='$value' ";
			}
		}
		$code.=" />\r\n";
		return $code;
	}

	/**
	 * Crea un CheckBox
	 *
	 * @access 	public
	 * @param 	mixed $params
	 * @return 	string
	 * @static
	 */
	public static function checkboxField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		$value = self::getValueFromAction($params[0]);
		if(!isset($params[0])||!$params[0]) {
			$params[0] = isset($params['id']) ? $params['id'] : "";
		}
		if(!isset($params['name'])||!$params['name']) {
			$params['name'] = $params[0];
		}

		if($value!==""&&$value!==null){
			$params['checked'] = "checked";
		}
		$code = "<input type='checkbox' id='{$params[0]}' ";
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$code.="$key='$value' ";
			}
		}
		$code.=" />\r\n";
		return $code;
	}

	/**
	 * Crea una caja de texto que acepta solo texto en Mayuscula
	 *
	 * @access 	public
	 * @param 	mixed $params
	 * @return 	string
	 * @static
	 */
	public static function textUpperField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])){
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||$params['name']==""){
			$params['name'] = $params[0];
		}
		if(isset($params['value'])){
			$value = $params['value'];
			unset($params['value']);
		} else {
			$value = self::getValueFromAction($params[0]);
		}
		if(!isset($params['onblur'])){
			$params['onblur'] = "keyUpper2(this)";
		} else {
			$params['onblur'].=";keyUpper2(this)";
		}
		$code = "<input type='text' id='{$params[0]}' value='$value' ";
		foreach($params as $_key => $_value){
			if(!is_integer($_key)){
				$code.="$_key='$_value' ";
			}
		}
		$code.=" />\r\n";
		return $code;
	}

	/**
	 * Crea un Input tipo Text
	 *
	 * @access 	public
	 * @param 	string $name
	 * @return 	string
	 * @static
	 */
	public static function fileField($name){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		$value = self::getValueFromAction($name);
		if(!isset($params[0])) {
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']){
			$params['name'] = $params[0];
		}
		$code = "<input type='file' id='{$params[0]}' ";
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$code.="$key='$value' ";
			}
		}
		$code.=" />\r\n";
		return $code;
	}

	/**
	 * Crea un input tipo Radio
	 *
	 * @access 	public
	 * @param 	mixed $params
	 * @return 	string
	 * @static
	 */
	public static function radioField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])){
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])){
			$params['name'] = $params[0];
		}
		if(isset($params['value'])){
			$value = $params['value'];
		} else {
			$value = self::getValueFromAction($params[0]);
		}
		if(isset($params[1])&&is_array($params[1])){
			$code = "<table><tr>";
			foreach($params[1] as $key=>$text){
				if($value==$key){
					$code.= "<td><input type='radio' name='{$params[0]}' id='{$params[0]}' value='$key' checked='checked' /></td><td>$text</td>\r\n";
				} else {
					$code.= "<td><input type='radio' name='{$params[0]}' id='{$params[0]}' value='$key' /></td><td>$text</td>\r\n";
				}
			}
			$code.= "</tr></table>";
		} else {
			$code = "<input type='radio' name='{$params[0]}' value='$value' ";
			foreach($params as $key => $value){
				if(!is_integer($key)){
					$code.="$key='$value' ";
				}
			}
			$code.="/>";
		}
		return $code;
	}

	/**
	 * Crea un Componente Oculto
	 *
	 * @access 	public
	 * @param 	mixed $params
	 * @return 	string
	 * @static
	 */
	public static function hiddenField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])){
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])){
			$params['name'] = $params[0];
		}
		if(!isset($params['value'])){
			$params['value'] = self::getValueFromAction($params[0]);
		}
		$code="<input type='hidden' id='{$params[0]}'";
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$code.="$key='$value' ";
			}
		}
		$code.=" />\r\n";
		return $code;
	}

	/**
	 * Crea una opcion de un SELECT
	 *
	 * @access 	public
	 * @param	string $value
	 * @param 	string $text
	 * @static
	 */
	public static function option($value, $text){
		if(func_num_args()>1){
			$numberArguments = func_num_args();
			$params = Utils::getParams(func_get_args(), $numberArguments);
			$value = $params[0];
			$text = $params[1];
		} else {
			$value = '';
		}
		$code = "<option value='$value' ";
		if(is_array($params)){
			foreach($params as $at => $val){
				if(!is_integer($at)){
					$code.="$at='".$val."' ";
				}
			}
		}
		$code.= ">$text</option>\r\n";
		return $code;
	}

	/**
	 * Crea un componente para Subir Imagenes
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	public static function uploadImage(){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params['name'])){
			$params['name'] = $params[0];
		}
		$code.="<span id='{$params['name']}_span_pre'>
		<select name='{$params[0]}' id='{$params[0]}' onchange='show_upload_image(this)'>";
		$code.="<option value='@'>Seleccione...\n";
		foreach(scandir("public/img/upload") as $file){
			if($file!='index.html'&&$file!='.'&&$file!='..'&&$file!='Thumbs.db'&&$file!='desktop.ini'){
				$nfile = str_replace('.gif', '', $file);
				$nfile = str_replace('.jpg', '', $nfile);
				$nfile = str_replace('.png', '', $nfile);
				$nfile = str_replace('.bmp', '', $nfile);
				$nfile = str_replace('_', ' ', $nfile);
				$nfile = ucfirst($nfile);
				if(urlencode("upload/$file")==$params['value']){
					$code.="<option selected='selected' value='upload/$file' style='background: #EAEAEA'>$nfile</option>\n";
				} else {
					$code.="<option value='upload/$file'>$nfile</option>\n";
				}
			}
		}
		$code.="</select> <a href='#{$params['name']}_up' name='{$params['name']}_up' id='{$params['name']}_up' onclick='enable_upload_file(\"{$params['name']}\")'>Subir Imagen</a></span>
		<span style='display:none' id='{$params['name']}_span'>
		<input type='file' id='{$params['name']}_file' onchange='upload_file(\"{$params['name']}\")' />
		<a href='#{$params['name']}_can' name='{$params['name']}_can' id='{$params['name']}_can' style='color:red' onclick='cancel_upload_file(\"{$params['name']}\")'>Cancelar</a></span>
		";
		if(!isset($params['width'])){
			$params['width'] = 128;
		}
		if($params['value']){
			$params['style']="border: 1px solid black;margin: 5px;".$params['value'];
		} else {
			$params['style']="border: 1px solid black;display:none;margin: 5px;".$params['value'];
		}
		$code.="<div>".Tag::image(urldecode($params['value']), 'width: '.$params['width'], 'style: '.$params['style'], 'id: '.$params['name']."_im")."</div>";
		return $code;
	}

	/**
	 * Hace que un elemento reciba items con drag-n-drop
	 *
	 * @access 	public
	 * @param 	string $obj
	 * @param 	string $action
	 * @return 	string
	 * @static
	 */
	public static function setDroppable($obj, $action=''){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!$params['name']){
			$params['name'] = $params[0];
		}
		return "<script type=\"text/javascript\">Droppables.add('{$params['name']}', {hoverclass: '{$params['hover_class']}',onDrop:{$params['action']}})</script>";
	}

	/**
	 * Hace que un elemento reciba items con drag-n-drop
	 *
	 * @access 	public
	 * @param 	string $action
	 * @param 	double $seconds
	 * @return 	string
	 * @static
	 */
	public static function redirectTo($action, $seconds = 0.01){
		$seconds*=1000;
		return "<script type=\"text/javascript\">setTimeout('window.location=\"?/$action\"', $seconds)</script>";
	}

	/**
	 * Imprime una etiqueta TR cada $n llamados a este helper
	 *
	 * @access public
	 * @param int $n
	 * @static
	 */
	public static function trBreak($n=''){
		static $l;
		if($n=='') {
			$l = 0;
			return;
		}
		if(!$l) {
			$l = 1;
		} else {
			++$l;
		}
		if(($l%$n)==0) {
			echo "</tr><tr>";
		}
	}

	/**
	 * Imprime una etiqueta BR cada $n llamados a este helper
	 *
	 * @access public
	 * @param int $n
	 * @static
	 */
	public static function brBreak($n=''){
		static $l;
		if($n=='') {
			$l = 0;
			return;
		}
		if(!$l) {
			$l = 1;
		} else {
			++$l;
		}
		if(($l%$n)==0) {
			echo "<br/>\n";
		}
	}

	/**
	 * Intercala entre llamados una lista de colores para etiquetas TR
	 *
	 * @access 	public
	 * @param 	array $colors
	 * @static
	 */
	public static function trColor($colors){
		static $i;
		if(func_num_args()>1){
			$numberArgs = func_num_args();
			$params = Utils::getParams(func_get_args(), $numberArgs);
		}
		if(!$i) {
			$i = 1;
		}
		print "<tr bgcolor=\"{$colors[$i-1]}\"";
		if(count($colors)==$i) {
			$i = 1;
		} else {
			++$i;
		}
		if(isset($params)){
			if(is_array($params)){
				foreach($params as $key => $value){
					if(!is_integer($key)){
						echo " $key = '$value'";
					}
				}
			}
		}
		echo ">";
	}

	/**
	 * Intercala entre llamados una lista de clases CSS para etiquetas TR
	 *
	 * @access 	public
	 * @param 	array $classes
	 * @static
	 */
	public static function trClassName($classes){
		static $i;
		if(func_num_args()>1){
			$params = Utils::getParams(func_get_args());
		}
		if(!$i) {
			$i = 1;
		}
		$code = "<tr class=\"{$classes[$i-1]}\"";
		if(count($classes)==$i) {
			$i = 1;
		} else {
			++$i;
		}
		if(isset($params)){
			if(is_array($params)){
				foreach($params as $key => $value){
					if(!is_integer($key)){
						$code.= " $key = '$value'";
					}
				}
			}
		}
		$code.=">";
		return $code;
	}

	/**
	 * Crea un botón que al hacer click carga un controlador y una acción determinada
	 *
	 * @access 	public
	 * @param 	string $caption
	 * @param 	string $action
	 * @param 	string $classCSS
	 * @return 	string
	 * @static
	 */
	static public function buttonToAction($caption, $action, $classCSS=''){
		if($classCSS!=''){
			$classCSS = "class='$classCSS'";
		}
		return "<input type='button' $classCSS onclick='window.location=\"".Utils::getKumbiaUrl($action)."\"' value='$caption' />";
	}

	/**
	 * Crea un Button que al hacer click carga con AJAX un controlador y una accion determinada
	 *
	 * @param 	string $caption
	 * @param 	string $action
	 * @param 	string $classCSS
	 * @return 	string
	 */
	static public function buttonToRemoteAction($caption, $action, $classCSS=''){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(func_num_args()==2){
			$params['action'] = $params[1];
			$params['caption'] = $params[0];
		} else {
			if(!isset($params['action'])||!$params['action']) {
				$params['action'] = $params[1];
			}
			if(!isset($params['caption'])||!$params['caption']) {
				$params['caption'] = $params[0];
			}
		}
		if(!isset($params['update'])){
			$params['update'] = "";
		}
		$code = "<button onclick='AJAX.execute({action:\"{$params['action']}\", container:\"{$params['update']}\", callbacks: { success: function(){{$params['success']}}, before: function(){{$params['before']}} } })'";
		unset($params['action']);
		unset($params['success']);
		unset($params['before']);
		unset($params['complete']);
		foreach($params as $k => $v){
			if(!is_integer($k)&&$k!='caption'){
				$code.=" $k='$v' ";
			}
		}
		$code.=">{$params['caption']}</button>";
		return $code;
	}

	/**
	 * Crea un select multiple que actualiza un container
	 * usando una accion ajax que cambia dependiendo del id
	 * selecionado en el select
	 *
	 * @access 	public
	 * @param 	string $id
	 * @return 	string
	 * @static
	 */
	public static function updaterSelect($id){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(func_num_args()==1){
			$params['id'] = $id;
		}
		if(!$params['id']){
			$params['id'] = $params[0];
		}
		if(!$params['container']){
			$params['container'] = $params['update'];
		}
		$code = "
		<select multiple onchange='AJAX.viewRequest({
			action: \"{$params['action']}/\"+selectedItem($(\"{$params['id']}\")).value,
			container: \"{$params['container']}\"
		})' ";
		unset($params['container']);
		unset($params['update']);
		unset($params['action']);
		foreach($params as $k => $v){
			if(!is_integer($k)){
				$code.=" $k='$v' ";
			}
		}
		$code.=">\n";
		return $code;
	}

	/**
	 * Helper de Paginacion
	 *
	 * @param array $items
	 * @param integer $pageNumber
	 * @param integer $show
	 * @return object
	 */
	public static function paginate($items, $pageNumber=null, $show=10){
		$n = count($items);
		$page = new stdClass();
		$start = $show*($pageNumber-1);
		if(is_array($items)){
			if($pageNumber===null){
				$pageNumber = 1;
			}
			$page->items = array_slice($items, $start, $show);
		} else {
			if($pageNumber===null){
				$pageNumber = 0;
			}
			if(is_object($items)){
				if($items instanceof ActiveRecordResultset){
					if($start<0){
						throw new CoreException("El n&uacute;mero de la página es negativo ó cero ($start)");
					}
					$page->items = array();
					$total = count($items);
					if($total>0){
						if($start<=$total){
							$items->seek($start);
						} else {
							$items->seek(1);
							$pageNumber = 1;
						}
						$i = 1;
						while($items->valid()==true){
							$page->items[] = $items->current();
							if($i>=$show){
								break;
							}
							++$i;
						}
					}
				}
			}
		}
		$page->first = 1;
		$page->next = ($start + $show)<$n ? ($pageNumber+1) : (($start + $show)==$n ? $n : ((int)($n/$show) + 1));
		$page->before = ($pageNumber>1) ? ($pageNumber-1) : 1;
		$page->current = $pageNumber;
		$page->total_pages = ($n % $show) ? ((int)($n/$show) + 1) : ($n/$show);
		$page->last = $page->total_pages;
		return $page;
	}

	/**
	 * Crea pestañas de diferentes colores
	 *
	 * @access public
	 * @param array $tabs
	 * @param string $color
	 * @param int $width
	 * @static
	 */
	static public function tab($tabs, $width=800){
		$code = "<table cellspacing='0' cellpadding='0' width='$width'><tr>";
		$p = 1;
		$w = $width;
		foreach($tabs as $tab){
			if($p==1){
				$className = 'tab_active';
			} else {
				$className = 'tab_inactive';
			}
			$ww = (int) ($width * 0.22);
			$www = (int) ($width * 0.21);
			$code.="<td align='center' width='{$ww}' class='tab_td'><div style='width:{$www}px;' id='tabdiv_$p' onclick='showTab($p, this)' class='tab_div $className'>".$tab['caption']."</div></td>";
			++$p;
			$w-=$ww;
		}
		$code.= "
			<script type='text/javascript'>
				function showTab(p, obj){
					for(var i=1;i<$p;i++){
					    $('tab_'+i).hide();
						$('tabdiv_'+i).removeClassName('tab_active');
						$('tabdiv_'+i).addClassName('tab_inactive');
					};
					$('tab_'+p).show();
					$('tabdiv_'+p).removeClassName('tab_inactive');
					$('tabdiv_'+p).addClassName('tab_active');
				}
			</script>
			";
		++$p;
		//$w = $width/2;
		$code.="<td width='$w'></td><tr>";
		$code.="<td colspan='$p' class='tab_con'>";
		$p = 1;
		foreach($tabs as $tab){
			if($p!=1){
				$code.="<div id='tab_$p' style='display:none'>";
			} else {
				$code.="<div id='tab_$p'>";
			}
			ob_start();
			View::renderPartial($tab['partial']);
			$code.=ob_get_contents();
			ob_end_clean();
			$code.="</div>";
			++$p;
		}
		$code.="<br></td><td width='30'></td></table>";
		return $code;
	}

	static public function updateDiv(){
		$params = Utils::getParams(func_get_args());
		$name = $params[0];
		if(isset($params['value'])){
			$value = $params['value'];
		} else {
			$value = "";
		}
		$html = "<div><div id='{$name}1' ondblclick=\"$('$name').show();$('$name').activate();this.hide()\" onmouseover='this.style.background=\"#ffffcc\"' onmouseout='this.style.background=\"transparent\"'>$value</div>";
		$html.= "<input id='{$name}' type='text' value='$value' style='display:none' onblur='$(\"{$name}1\").show();this.hide();$(\"{$name}1\").innerHTML=this.value'";
		unset($params['value']);
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$html.= "$key = '$value'";
			}
		}
		$html.= "/></div>";
		return $html;
	}

}
