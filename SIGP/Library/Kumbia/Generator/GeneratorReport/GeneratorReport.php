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
 * @package		Generator
 * @subpackage	GeneratorReport
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 * @version 	$Id: GeneratorReport.php 95 2009-09-27 03:32:04Z gutierrezandresfelipe $
 */

/**
 * Generador de Reportes
 *
 * @category	Kumbia
 * @package		Generator
 * @subpackage	GeneratorReport
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 */
abstract class GeneratorReport {

	/**
	 * Genera un reporte con las condiciones del formulario
	 *
	 * @access public
	 * @param array $form
	 * @static
	 */
	static public function generate($form){

		/*$config = CoreConfig::readEnviroment();

		$weightArray = array();
		$headerArray = array();
		$selectedFields = array();
		$tables = "";
		$whereCondition = array();
		$maxCondition = "";
		$n = 0;

		$db = Db::rawConnect();
		if(isset($form['dataFilter'])){
		if(strpos($form['dataFilter'], '@')){
		ereg('[\@][A-Za-z0-9_]+', $form['dataFilter'], $regs);
		foreach($regs as $reg){
		$form['dataFilter'] = str_replace($reg, $_REQUEST['fl_'.str_replace('@', '', $reg)], $form['dataFilter']);
		}
		}
		}

		$n = 0;
		$m = 0;
		if($form['type']=='standard'){
		if(isset($form['joinTables'])) {
		$tables = $form['joinTables'];
		}
		if(isset($form['joinConditions'])){
		$whereCondition = ' '.$form['joinConditions'];
		}
		foreach($form['components'] as $name => $com){
		if(isset($com['attributes']['value'])){
		if($_REQUEST['fl_'.$name]==$com['attributes']['value']){
		$_REQUEST['fl_'.$name] = "";
		}
		}
		if(isset($_REQUEST['fl_'.$name])){
		if(trim($_REQUEST['fl_'.$name])&&$_REQUEST['fl_'.$name]!='@'){
		if(isset($_REQUEST['op_'.$name])){
		$operator = Filter::inRange($_REQUEST['op_'.$name], array('=', '<>', '<', '>'));
		if(!$operator){
		$operator = '=';
		}
		} else {
		$operator = '=';
		}
		if($form['components'][$name]['valueType']=='date'){
		$_REQUEST["fl_".$name] = filter_date($_REQUEST["fl_".$name]);
		if(isset($_REQUEST["fl_{$name}_range"])){
		$_REQUEST["fl_{$name}_range"] = filter_date($_REQUEST["fl_{$name}_range"]);
		$whereCondition[] = $form['source'].".$name >= '".$_REQUEST["fl_".$name]."' AND ".$form['source'].".$name <= '{$_REQUEST["fl_{$name}_range"]}'";
		} else {
		$whereCondition[] = $form['source'].".$name $operator '".$_REQUEST["fl_".$name]."'";
		}
		} else {
		if($form['components'][$name]['valueType']=='numeric'){
		$_REQUEST["fl_".$name] = filter_numeric($_REQUEST["fl_".$name]);
		$whereCondition[] = $form['source'].".$name $operator '".$_REQUEST["fl_".$name]."'";
		} else {
		if($form['components'][$name]['type']=='hidden'){
		$whereCondition[] = $form['source'].".$name $operator '".$_REQUEST["fl_".$name]."'";
		} else {
		if($com['type']=='check'){
		if($_REQUEST["fl_".$name]==$form['components'][$name]['checkedValue']){
		$whereCondition[] = $form['source'].".$name $operator '".$_REQUEST["fl_".$name]."'";
		}
		} else {
		if($com['type']=='time'){
		if($_REQUEST["fl_".$name]!='00:00'){
		$_REQUEST["fl_".$name] = filter_time($_REQUEST["fl_".$name]);
		$whereCondition[] = "{$form['source']}.$name $operator '".$_REQUEST["fl_".$name]."'";
		}
		} else {
		if($com['primary']||$com['type']=='combo'){
		$whereCondition[] = $form['source'].".$name $operator '".$_REQUEST["fl_".$name]."'";
		} else {
		if($operator=='='){
		$operator = 'LIKE';
		}
		if($operator=='<>'){
		$operator = 'NOT LIKE';
		}
		$whereCondition[] = $form['source'].".$name $operator '%".$_REQUEST["fl_".$name]."%'";
		}
		}
		}
		}
		}
		}
		}
		}
		}
		} else {
		$m = 1;
		}
		$maxCondition = $whereCondition;
		$letter = 'a';
		$all_components = array();
		foreach($form['components'] as $name => $com){
		$all_components[] = $name;
		$not_alias = false;
		if($com['type']=='combo'&&$com['class']=='dynamic'){
		if(isset($com['extraTables'])&&$com['extraTables']){
		$tables.=(string) $com['extraTables'].',';
		}
		if(isset($com['whereConditionOnQuery'])&&$com['whereConditionOnQuery']){
		$whereCondition.=' AND '.$com['whereConditionOnQuery'];
		}
		if(strpos(' '.$com['detailField'], 'concat(')){
		$not_alias = true;
		}
		if($not_alias){
		$tables.=$com['foreignTable'].',';
		$tab = $com['foreignTable'];
		} else {
		$tables.=$com['foreignTable']." $letter,";
		$tab = $letter;
		}
		if($com['column_relation']){
		$whereCondition[] = $tab.".".$com['column_relation']." = ".$form['source'].".".$name;
		} else {
		$whereCondition[] = $tab.".".$name." = ".$form['source'].".".$name;
		}
		if(isset($com['whereCondition'])&&$com['whereCondition']){
		if(!$not_alias){
		$com['whereCondition'] = str_replace($com['foreignTable'].".", "$letter.", $com['whereCondition']);
		}
		$whereCondition[] = $com['whereCondition'];
		}
		if(!$not_alias){
		$letter = chr(ord($letter)+1);
		}
		} else {
		if($com['type']!='hidden'){
		if(isset($com['class'])){
		if($form['components'][$name]['type']=='helpText'){
		$tables.="{$com['foreignTable']} $letter,";
		if($com['column_relation']){
		$whereCondition[] = "{$letter}.{$com['column_relation']} = {$form['source']}.$name";
		} else {
		$whereCondition[] = " {$letter}.$name = {$form['source']}.$name";
		}
		$letter = chr(ord($letter)+1);
		}
		}
		}
		}
		}

		$n = 0;
		$m = 0;
		foreach($form['components'] as $name => $com){
		$all_components[] = $name;
		if(!isset($com['notReport'])||$com['notReport']){
		$headerArray[$n] = html_entity_decode($com['caption'], ENT_NOQUOTES, 'UTF-8');
		$headerArray[$n] = str_replace('<br>', ' ', $headerArray[$n]);
		$headerArray[$n] = str_replace('<br/>', ' ', $headerArray[$n]);
		if($com['type']=='combo'&&$com['class']=='dynamic'){
		if(strpos(' '.$com['detailField'], 'concat(')){
		$selectedFields[] = $com['detailField'];
		} else {
		$selectedFields[] = $letter.'.'.$com['detailField'];
		}
		} else {
		if($com['type']!='hidden'){
		if(isset($com['class'])){
		if($com['class']=='static'){
		$weightArray[$n] = strlen($headerArray[$n])+3;
		$numberItems = count($com['items'])-1;
		$selectedField = "";
		for($i=0;$i<$numberItems;++$i){
		$selectedField.= "if(".$form['source'].".".$name."='".$com['items'][$i][0]."', '".$com['items'][$i][1]."',";
		if($weightArray[$n]<strlen($com['items'][$i][1])) {
		$weightArray[$n] = strlen($com['items'][$i][1])+1;
		}
		}
		++$n;
		++$m;
		$selectedField.="'".$com['items'][$i][1]."')";
		//$selectedFields.="')";
		for($j=0;$j<$i-1;++$j){
		$selectedField.=")";
		}
		$selectedFields[] = $selectedField;
		} else {
		if($form['components'][$name]['type']=='helpText'){
		$selectedFields[] = $form['source'].".".$name;
		$weightArray[$n] = strlen($headerArray[$n])+3;
		++$n;
		$headerArray[$n] = ucfirst($form['components'][$name]['detailField']);
		$weightArray[$n] = strlen($headerArray[$n])+3;
		$selectedFields[] = $letter.".".$com['detailField']." AS {$letter}_{$com['detailField']}";
		$letter = chr(ord($letter)+1);
		++$n;
		++$m;
		} else {
		$selectedFields[] = $form['source'].'.'.$name;
		$weightArray[$n] = strlen($headerArray[$n])+3;
		++$n;
		++$m;
		}
		}
		}
		}
		}
		}
		}

		$tables.=$form['source'];
		$selectedFields = join(',', $selectedFields);

		if(isset($form['dataRequisite'])){
		$whereCondition[] = $form['dataFilter'];
		}

		$query = "SELECT $selectedFields FROM $tables ";
		if(count($whereCondition)>0){
		$query.=" WHERE ".join(' AND ', $whereCondition);
		}
		if(isset($_POST['orderBy'])){
		if(in_array($_POST['orderBy'], $all_components)){
		$query.=" ORDER BY ".$_POST['orderBy'];
		}
		}
		if($m>0){
		$q = $db->query($query);
		if(!is_bool($q)){
		if(!$db->numRows($q)){
		Flash::notice("No hay información para listar");
		return;
		}
		}
		}

		$result = array();
		$n = 0;
		$db->setFetchMode(db::DB_NUM);
		while($row = $db->fetchArray($q)){
		$result[$n++] = $row;
		}

		foreach($result as $row){
		$numColumns = count($row);
		for($i=0;$i<$numColumns;++$i){
		if($weightArray[$i]<strlen(trim($row[$i]))){
		$weightArray[$i] = strlen(trim($row[$i]));
		}
		}
		}

		$numArray = count($weightArray);
		for($i=0;$i<$numArray;++$i){
		$weightArray[$i]*= 1.8;
		}

		$sumArray = array_sum($weightArray);*/

		$weightArray = array();
		$headerArray = array();
		$selectedFields = "";
		$tables = "";
		$whereCondition = "";
		$maxCondition = "";
		$n = 0;
		$db = db::rawConnect();
		if(isset($form['dataFilter'])&&$form['dataFilter']){
			if(strpos($form['dataFilter'], '@')){
				ereg("[\@][A-Za-z0-9_]+", $form['dataFilter'], $regs);
				foreach($regs as $reg){
					$form['dataFilter'] = str_replace($reg, $_REQUEST["fl_".str_replace("@", "", $reg)], $form['dataFilter']);
				}
			}
		}
		if($form['type']=='standard'){
			if(isset($form['joinTables'])&&$form['joinTables']){
				$tables = $form['joinTables'];
			}
			if(isset($form['joinConditions'])&&$form['joinConditions']) {
				$whereCondition = " ".$form['joinConditions'];
			}
			foreach($form['components'] as $name => $com){
				if(!isset($com['attributes']['value'])){
					$com['attributes']['value'] = "";
				}
				if($_REQUEST['fl_'.$name]==$com['attributes']['value']){
					$_REQUEST['fl_'.$name] = "";
				}
				if(trim($_REQUEST["fl_".$name])&&$_REQUEST["fl_".$name]!='@'){
					if(!isset($form['components'][$name]['valueType'])){
						$form['components'][$name]['valueType'] = "";
					}
					if($form['components'][$name]['valueType']=='date'){
						$whereCondition.=" and ".$form['source'].".$name = '".$_REQUEST["fl_".$name]."'";
					} else {
						if($form['components'][$name]['valueType']=='numeric'){
							$whereCondition.=" and ".$form['source'].".$name = '".$_REQUEST["fl_".$name]."'";
						} else {
							if($form['components'][$name]['type']=='hidden'){
								$whereCondition.=" and ".$form['source'].".$name = '".$_REQUEST["fl_".$name]."'";
							} else {
								if($com['type']=='check'){
									if($_REQUEST["fl_".$name]==$form['components'][$name]['checkedValue'])
									$whereCondition.=" and ".$form['source'].".$name = '".$_REQUEST["fl_".$name]."'";
								} else {
									if($com['type']=='time'){
										if($_REQUEST["fl_".$name]!='00:00'){
											$whereCondition.=" and {$form['source']}.$name = '".$_REQUEST["fl_".$name]."'";
										}
									} else {
										if(!isset($com['primary'])){
											$com['primary'] = false;
										}
										if($com['primary']||$com['type']=='combo'){
											$whereCondition.=" and ".$form['source'].".$name = '".$_REQUEST["fl_".$name]."'";
										} else {
											$whereCondition.=" and ".$form['source'].".$name like '%".$_REQUEST["fl_".$name]."%'";
										}
									}
								}
							}
						}
					}
				}
			}
		}

		if(substr($_REQUEST['reportTypeField'],strlen($_REQUEST['reportTypeField']) -3,strlen($_REQUEST['reportTypeField'])) == "_id"){
			$OrderFields = substr($_REQUEST['reportTypeField'],0,strlen($_REQUEST['reportTypeField'])-3);
		} else {
			$OrderFields =$_REQUEST['reportTypeField'];
		}
		$maxCondition = $whereCondition;
		$n = 0;
		foreach($form['components'] as $name => $com){
			if(!isset($com['notReport'])){
				$com['notReport'] = false;
			}
			if(!isset($com['class'])){
				$com['class'] = false;
			}
			if(!$com['notReport']){
				if(isset($com['caption'])&&$com['caption']){
					$headerArray[$n] = html_entity_decode($com['caption']);
					$headerArray[$n] = str_replace("<br/>", " ", $headerArray[$n]);
				} else {
					$com['caption'] = "";
				}
				if($com['type']=='combo'&&$com['class']=='dynamic'){
					if(isset($com['extraTables'])&&$com['extraTables']){
						$tables.="{$com['extraTables']},";
					}
					if(isset($com['whereConditionOnQuery'])&&$com['whereConditionOnQuery']){
						$whereCondition.=" and {$com['whereConditionOnQuery']}";
					}
					if(strpos(" ".$com['detailField'], "concat(")){
						$selectedFields.=$com['detailField'].",";
					} else {
						$selectedFields.=$com['foreignTable'].".".$com['detailField'].",";
						//Comparamos la Tabla foranea que tenemos, y cuando sea igual, suponiendo no hay
						//mas de una clave foranea por tabla, sabremos a que tabla pertenece
						if ($com['foreignTable'] == $OrderFields){
							$OrderFields = $com['foreignTable'].".".$com['detailField'];
						}
					}
					$tables.=$com['foreignTable'].",";
					if($com['column_relation']){
						$whereCondition.=" and ".$com['foreignTable'].".".$com['column_relation']." = ".$form['source'].".".$name;
					} else {
						$whereCondition.=" and ".$com['foreignTable'].".".$name." = ".$form['source'].".".$name;
					}
					$weightArray[$n] = strlen($headerArray[$n])+2;
					$n++;
				} else {
					if($com['type']!='hidden'){
						if($com['class']=='static'){
							$weightArray[$n] = strlen($headerArray[$n])+2;
							#if($config->database->type=='postgresql'){
							#	$selectedFields.="CASE ";
							#}
							#if($config->database->type=='mysql'){
							for($i=0;$i<=count($com['items'])-2;$i++){
								$selectedFields.="IF(".$form['source'].".".$name."='".$com['items'][$i][0]."', '".$com['items'][$i][1]."', ";
								if($weightArray[$n]<strlen($com['items'][$i][1])) {
									$weightArray[$n] = strlen($com['items'][$i][1])+1;
								}
							}
							#}

							/*if($config->database->type=='postgresql'){
								for($i=0;$i<=count($com['items'])-1;$i++){
								$selectedFields.=" when ".$form['source'].".".$name."='".$com['items'][$i][0]."' THEN '".$com['items'][$i][1]."' ";
								if($weightArray[$n]<strlen($com['items'][$i][1])) {
								$weightArray[$n] = strlen($com['items'][$i][1])+1;
								}
								}
								}*/

							$n++;
							#if($config->database->type=='mysql'){
							$selectedFields.="'".$com['items'][$i][1]."')";
							for($j=0;$j<=$i-2;$j++) {
								$selectedFields.=")";
							}
							/*}
							 if($config->database->type=='postgresql'){
								$selectedFields.=" end ";
								}*/
							$selectedFields.=",";
						} else {
							$selectedFields.=$form['source'].".".$name.",";
							//Aqui seguro que no es foranea, entonces tenemos que poner la tabla principal                                                  //
							//antes para evitar repeticiones
							if($name==$OrderFields){
								$OrderFields = $form['source'].".".$OrderFields;
							}
							$weightArray[$n] = strlen($headerArray[$n])+2;
							$n++;
						}
					}

				}
			}
		}
		$tables.=$form['source'];
		$selectedFields = substr($selectedFields, 0, strlen($selectedFields)-1);
		if(isset($form['dataRequisite'])&&$form['dataRequisite']){
			$whereCondition.=" AND {$form['dataFilter']}";
		}
		if($OrderFields){
			$OrderCondition = "ORDER BY ".$OrderFields;
		} else {
			$OrderCondition = "";
		}
		$query = "select $selectedFields from $tables where 1 = 1 ".$whereCondition. " " .$OrderCondition;
		$q = $db->query($query);
		if(!is_bool($q)){
			if(!$db->numRows($q)){
				Flash::notice("No hay información para listar");
				return;
			}
		} else {
			Flash::error($db->error());
			return;
		}

		$result = array();
		$n = 0;
		$db->setFetchMode(dbBase::DB_NUM);
		while($row = $db->fetchArray($q)){
			$result[$n++] = $row;
		}
		$db->setFetchMode(dbBase::DB_BOTH);

		foreach($result as $row){
			for($i=0;$i<=count($row)-1;$i++){
				if($weightArray[$i]<strlen(trim($row[$i]))){
					$weightArray[$i] = strlen(trim($row[$i]));
				}
			}
		}

		for($i=0;$i<=count($weightArray)-1;$i++){
			$weightArray[$i]*= 1.8;
		}

		$sumArray = array_sum($weightArray);

		//echo $sumArray;

		if(!isset($_REQUEST['reportType'])||!$_REQUEST['reportType']){
			$_REQUEST['reportType'] = 'pdf';
		}

		if($_REQUEST['reportType']!='html'){
			$title = html_entity_decode($form['caption'], ENT_NOQUOTES, 'UTF-8');
		} else {
			$title = $form['caption'];
		}

		switch($_REQUEST['reportType']){
			case 'pdf':
				require_once "Library/Kumbia/Generator/GeneratorReport/Format/Pdf.php";
				pdf($result, $sumArray, $title, $weightArray, $headerArray);
				break;
			case 'xls':
				#error_reporting(0);
				require_once "Library/Kumbia/Generator/GeneratorReport/Format/Xls.php";
				xls($result, $sumArray, $title, $weightArray, $headerArray);
				break;
			case 'html':
				require_once "Library/Kumbia/Generator/GeneratorReport/Format/Htm.php";
				htm($result, $sumArray, $title, $weightArray, $headerArray);
				break;
			case 'doc':
				require_once "Library/Kumbia/Generator/GeneratorReport/Format/Doc.php";
				doc($result, $sumArray, $title, $weightArray, $headerArray);
				break;
			default:
				require_once "Library/Kumbia/Generator/GeneratorReport/Format/Pdf.php";
				pdf($result, $sumArray, $title, $weightArray, $headerArray);
				break;
		}

	}
}

