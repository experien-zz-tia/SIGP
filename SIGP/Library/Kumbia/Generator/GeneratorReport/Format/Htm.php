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
 * @package		GeneratorReport
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2008 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license 	New BSD License
 */

/**
 * HtmGenerator
 *
 * Generador de Reportes
 *
 * @category	Kumbia
 * @package		Report
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright	Copyright (c) 2005-2008 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license		New BSD License
 */
function htm($result, $sumArray, $title, $weightArray, $headerArray){

	$config = CoreConfig::readAppConfig();
	$active_app = Router::getApplication();
	$file = md5(uniqid());

	$content = "
<html>
 <head>
   <title>REPORTE DE ".strtoupper($title)."</title>
 </head>
 <body bgcolor='white'>
 <div style='font-size:20px;font-family:Verdana;color:#000000'>".strtoupper($config->application->name)."</div>\n
 <div style='font-size:18px;font-family:Verdana;color:#000000'>REPORTE DE ".strtoupper($title)."</div>\n
 <div style='font-size:18px;font-family:Verdana;color:#000000'>".date("Y-m-d")."</div>\n
 <br/>
 <table cellspacing='0' border=1 style='border:1px solid #969696'>
 ";
	$content.= "<tr bgcolor='#F2F2F2'>\n";
	for($i=0;$i<=count($headerArray)-1;++$i){
		$content.= "<th style='font-family:Verdana;font-size:12px'>".$headerArray[$i]."</th>\n";
	}
	$content.= "</tr>\n";

	$l = 5;
	foreach($result as $row){
		$content.= "<tr bgcolor='white'>\n";
		$numberColumns = count($row);
		for($i=0;$i<$numberColumns;++$i){
			if(is_numeric($row[$i])){
				$content.= "<td style='font-family:Verdana;font-size:12px' align='center'>{$row[$i]}</td>";
			} else {
				$content.= "<td style='font-family:Verdana;font-size:12px'>{$row[$i]}&nbsp;</td>";
			}
		}
		$content.= "</tr>\n";
		++$l;
	}

	file_put_contents("public/temp/$file.html", $content);

	if(isset($raw_output)){
		echo "<script type='text/javascript'> window.open('".Core::getInstancePath()."temp/".$file.".html', null);  </script>";
	} else {
		Generator::formsPrint("<script type='text/javascript'> window.open('".Core::getInstancePath()."temp/".$file.".html', null);  </script>");
	}

}
