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
 * @package 	Report
 * @subpackage 	Adapters
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license 	New BSD License
 * @version 	$Id: Pdf.php 82 2009-09-13 21:06:31Z gutierrezandresfelipe $
 */

/**
 * PdfReport
 *
 * Adaptador que permite generar reportes en PDF
 *
 * @category 	Kumbia
 * @package 	Report
 * @subpackage 	Adapters
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license 	New BSD License
 */
class PdfReport extends ReportAdapter implements ReportInterface {

	/**
	 * Documento FPDF
	 *
	 * @var FPDF
	 */
	private $_pdf;

	/**
	 * Tama&ntilde;os de las columnas
	 *
	 * @var array
	 */
	private $_columnSizes = array();

	/**
	 * Constructor de la clase PDFReport
	 *
	 */
	public function __construct(){
		if(class_exists('FPDF')==false){
			Core::importFromLibrary('Fpdf', 'Fpdf.php');
		}
		$this->_pdf = new FPDF('P', 'mm', 'letter');
		$this->_pdf->SetTitle($this->getDocumentTitle());
		$this->_pdf->SetDisplayMode('fullpage');
		$this->_pdf->Open();
		$this->_pdf->AddPage();
	}

	/**
	 * Genera la salida del reporte
	 *
	 * @return string
	 */
	public function getOutput(){
		$this->_pdf->SetLineWidth(.2);
		$this->_renderHeader();
		$this->_pdf->Ln();
		$this->_renderColumnHeaders();
		$this->_renderRows();
		return $this->_pdf->Output('', 'S');
	}

	/**
	 * Genera la salida del reporte al Explorador
	 *
	 * @return string
	 */
	public function outputToBrowser(){
		$this->getOutput();
		echo $this->_pdf->Output($this->getDocumentName(), 'I');
	}

	/**
	 * Establece los encabezados del reporte
	 *
	 * @param array $columnHeaders
	 */
	public function setColumnHeaders($columnHeaders){
		$i = 0;
		foreach($columnHeaders as $columnHeader){
			$columnHeader = html_entity_decode($columnHeader);
			if(isset($this->_columnSizes[$i])==false){
				$this->_columnSizes[$i] = strlen($columnHeader);
			} else {
				$length = strlen($columnHeader);
				if($length>$this->_columnSizes[$i]){
					$this->_columnSizes[$i] = $length;
				}
			}
			++$i;
		}
		parent::setColumnHeaders($columnHeaders);
	}

	/**
	 * Agrega una fila al reporte
	 *
	 * @param array $row
	 */
	public function addRow($row){
		$i = 0;
		foreach($row as $columnValue){
			if(isset($this->_columnSizes[$i])==false){
				$this->_columnSizes[$i] = strlen($columnValue);
			} else {
				$length = strlen($columnValue);
				if($length>$this->_columnSizes[$i]){
					$this->_columnSizes[$i] = $length;
				}
			}
			++$i;
		}
		parent::addRow($row);
	}

	/**
	 * Renderiza un item
	 *
	 * @param mixed $item
	 */
	protected function _renderItem($item){
		if(is_string($item)){
			$this->_output.= $item;
			return;
		}
		if(is_object($item)){
			if(get_class($item)=='ReportText'){
				$attributes = $this->_prepareStyle($item->getAttributes());
				$color = $attributes['color'];
				$this->_applyStyle($attributes);
				$height = ceil($attributes['fontSize']*6/16);
				$text = $this->_prepareText($item->getText());
				$this->_pdf->Cell(200, $height, $text, $attributes['border'], 0, $attributes['textAlign']);
				$this->_pdf->Ln();
				return;
			}
		}
	}

	private function _applyStyle(array $attributes){
		$color = $attributes['color'];
		$this->_pdf->SetTextColor($color['R'], $color['G'], $color['B']);
		$this->_pdf->SetFont($attributes['fontFamily'], $attributes['fontWeight'], $attributes['fontSize']);
		if(isset($attributes['borderColor'])){
			$borderColor = $attributes['borderColor'];
			$this->_pdf->SetDrawColor($borderColor['R'], $borderColor['G'], $borderColor['B']);
		}
		$backgroundColor = $attributes['backgroundColor'];
		$this->_pdf->SetFillColor($backgroundColor['R'], $backgroundColor['G'], $backgroundColor['B']);
	}

	private function _prepareStyle(array $attributes){
		$style = array();
		$style['fontFamily'] = 'helvetica';
		$style['fontWeight'] = '';
		$style['fontSize'] = 10;
		$style['color'] = array('R' => 0x00, 'G' => 0x00, 'B' => 0x00);
		$style['backgroundColor'] = array('R' => 0xFF, 'G' => 0xFF, 'B' => 0xFF);
		$style['border'] = 0;
		$style['textAlign'] = 'L';
		foreach($attributes as $attributeName => $value){
			if($attributeName=='fontSize'){
				$style['fontSize'] = $value;
				continue;
			}
			if($attributeName=='fontWeight'){
				if($value=='bold'){
					$value = 'B';
				}
				$style['fontWeight'] = $value;
				continue;
			}
			if($attributeName=='color'){
				$style['color'] = $this->_buildRGBArrayColor($value);
				continue;
			}
			if($attributeName=='borderColor'){
				$style['borderColor'] = $this->_buildRGBArrayColor($value);
				$style['border'] = 1;
				continue;
			}
			if($attributeName=='textAlign'){
				$style['textAlign'] = strtoupper(substr($value, 0, 1));
				continue;
			}
			if($attributeName=='backgroundColor'){
				$style['backgroundColor'] = $this->_buildRGBArrayColor($value);
				continue;
			}
		}
		return $style;
	}

	private function _getWidthPage(){

	}

	private function _adjustToCenter($mult=1){
		$sumArray = array_sum($this->_columnSizes);
		if($sumArray>250){
			$widthPage = 355;
		} else {
			$widthPage = 140;
			if($mult!=1) $widthPage = 215;
		}
		$sumArray *= $mult;
		$pos = floor(($widthPage/2)-($sumArray/2));
		if($pos<0) $pos = 0;
		$this->_pdf->SetX($pos);
	}

	private function _renderColumnHeaders(){
		$style = $this->getCellHeaderStyle();
		$attributes = $style->getStyles();
		if(isset($attributes['fontWeight'])==false){
			$attributes['fontWeight'] = "bold";
		}
		$preparedStyle = $this->_prepareStyle($attributes);
		$this->_applyStyle($preparedStyle);
		$this->_adjustToCenter(3);
		$height = ceil($preparedStyle['fontSize']*6/16)+1;
		$i = 0;
		foreach($this->getColumnHeaders() as $header){
			$header = html_entity_decode($header);
			$this->_pdf->Cell($this->_columnSizes[$i]*3, $height, $header, 1, 0, 'C', 1);
			++$i;
		}
		$this->_pdf->Ln();
	}

	private function _renderRows(){
		$data = $this->getRows();
		$styles = $this->getColumnStyles();
		$height = null;
		foreach($data as $row){
			$this->_adjustToCenter(3);
			$i = 0;
			foreach($row as $value){
				if(isset($styles[$i])){
					$preparedStyle = $this->_prepareStyle($styles[$i]->getStyles());
					$this->_applyStyle($preparedStyle);
					if($height==null){
						$height = ceil($preparedStyle['fontSize']*6/16)+1;
					}
				}
				$this->_pdf->Cell($this->_columnSizes[$i]*3, $height, $value, 1, 0, 'C', 1);
				++$i;
			}
			$this->_pdf->Ln();
		}
	}

	/**
	 * Creo un color RGB
	 *
	 * @param string $value
	 * @return array
	 */
	private function _buildRGBArrayColor($value){
		return array(
			'R' => hexdec(substr($value, 1, 2)),
			'G' => hexdec(substr($value, 3, 2)),
			'B' => hexdec(substr($value, 5, 2))
		);
	}

	/**
	 * Devuelve la extension del archivo recomendada
	 *
	 * @return string
	 */
	protected function getFileExtension(){
		return 'pdf';
	}

}
