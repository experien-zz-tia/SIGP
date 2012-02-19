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
 * @subpackage 	ReportAdapter
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license 	New BSD License
 */

/**
 * ReportAdapter
 *
 * Abstrae los principales métodos para adaptadores de Report
 *
 * @category 	Kumbia
 * @package 	Report
 * @subpackage 	ReportAdapter
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license 	New BSD License
 */
class ReportAdapter extends Object {

	/**
	 * Titulo General del Reporte
	 *
	 * @var string
	 */
	private $_title;

	/**
	 * Codificacion del documento
	 *
	 * @var string
	 */
	private $_encoding = "UTF-8";

	/**
	 * Numero de pagina actual
	 *
	 * @var integer
	 */
	private $_pageNumber = 1;

	/**
	 * Establece el numero de filas por pagina
	 *
	 * @var int
	 */
	private $_rowsPerPage = -1;

	/**
	 * Nombre del documento
	 *
	 * @var string
	 */
	private $_name = 'document';

	/**
	 * Items del encabezado del documento
	 *
	 * @var array
	 */
	private $_headerItems = array();

	/**
	 * Items del footer del documento
	 *
	 * @var array
	 */
	private $_footerItems = array();

	/**
	 * Encabezados de las columnas del reporte
	 *
	 * @var array
	 */
	private $_columnHeaders = array();

	/**
	 * Estilo de las celdas encabezado
	 *
	 * @var ReportStyle
	 */
	private $_cellHeaderStyle = null;

	/**
	 * Estilos de las columnas del reporte
	 *
	 * @var array
	 */
	private $_columnStyles = array();

	/**
	 * Columnas que deben ser totalizadas
	 *
	 * @var array
	 */
	protected $_totalizeColumns = array();

	/**
	 * Datos del Reporte
	 *
	 * @var array
	 */
	private $_data = array();

	/**
	 * Activa/Desactiva la paginacion automatica
	 *
	 * @var boolean
	 */
	private $_pagination = true;

	/**
	 * Modo de visualizacion del reporte
	 *
	 * @var string
	 */
	private $_displayMode = 'normal';

	/**
	 * Establece el titulo del Reporte
	 *
	 * @param string $reportTitle
	 */
	public function setDocumentTitle($reportTitle){
		$this->_title = $reportTitle;
	}

	/**
	 * Genera la salida al explorador
	 *
	 */
	public function outputToBrowser(){
		echo $this->getOutput();
	}

	/**
	 * Establece la codificacion del documento
	 *
	 * @param string $encoding
	 */
	public function setEncoding($encoding){
		$this->_encoding = $encoding;
	}

	/**
	 * Devuelve la codificación del documento
	 *
	 * @return string
	 */
	public function getEncoding(){
		return $this->_encoding;
	}

	/**
	 * Devuelve el titulo general del documento
	 *
	 * @return string
	 */
	public function getDocumentTitle(){
		return $this->_title;
	}

	/**
	 * Establece el nombre del documento
	 *
	 * @param string $name
	 */
	public function setDocumentName($name){
		$this->_name = $name;
	}

	/**
	 * Devuelve el nombre interno del documento
	 *
	 * @return string
	 */
	public function getDocumentName(){
		return $this->_name;
	}

	/**
	 * Establece el encabezado
	 *
	 * @param array $items
	 */
	public function setHeader($items){
		$this->_headerItems = $items;
	}

	/**
	 * Devuelve el encabezado
	 *
	 * @return array
	 */
	public function getHeader(){
		return $this->_headerItems;
	}

	/**
	 * Establece el footer
	 *
	 * @param array $items
	 */
	public function setFooter($items){
		$this->_footerItems = $items;
	}

	/**
	 * Devuelve el footer
	 *
	 * @return array
	 */
	public function getFooter(){
		return $this->_footerItems;
	}

	/**
	 * Renderiza el encabezado del documento
	 *
	 */
	protected function _renderHeader(){
		$header = $this->getHeader();
		if(is_array($header)){
			foreach($header as $item){
				$this->_renderItem($item);
			}
		} else {
			$this->_renderItem($header);
		}
	}

	/**
	 * Renderiza el footer del documento
	 *
	 */
	protected function _renderFooter(){
		$footer = $this->getFooter();
		if(is_array($footer)){
			foreach($footer as $item){
				$this->_renderItem($item);
			}
		} else {
			$this->_renderItem($header);
		}
	}

	/**
	 * Establece el estilo de una columna
	 *
	 * @param integer $numberColumn
	 * @param ReportStyle $style
	 */
	public function setColumnStyle($numberColumn, ReportStyle $style){
		$this->_columnStyles[(int)$numberColumn] = $style;
	}

	/**
	 * Devuelve los estilos de las columnas
	 *
	 * @return array
	 */
	public function getColumnStyles(){
		return $this->_columnStyles;
	}

	/**
	 * Establece el estilo de las celdas encabezado
	 *
	 * @param ReportStyle $style
	 */
	public function setCellHeaderStyle(ReportStyle $style){
		$this->_cellHeaderStyle = $style;
	}

	/**
	 * Devuelve el estilo de las celdas encabezado
	 *
	 * @return ReportStyle
	 */
	public function getCellHeaderStyle(){
		return $this->_cellHeaderStyle;
	}

	/**
	 * Establece los encabezados del reporte
	 *
	 * @param array $columnHeaders
	 */
	public function setColumnHeaders($columnHeaders){
		$this->_columnHeaders = $columnHeaders;
	}

	/**
	 * Agrega un encabezado
	 *
	 * @param string $columnHeader
	 */
	public function addColumnHeader($columnHeader){
		$this->_columnHeaders[] = $columnHeader;
	}

	/**
	 * Devuelve los encabezados del reporte
	 *
	 * @return array
	 */
	public function getColumnHeaders(){
		return $this->_columnHeaders;
	}

	/**
	 * Agrega una fila al reporte
	 *
	 * @param array $row
	 */
	public function addRow($row){
		$this->_data[] = $row;
	}

	/**
	 * Devuelve las filas del reporte
	 *
	 * @return array
	 */
	public function getRows(){
		return $this->_data;
	}

	/**
	 * Agrega una columna de total
	 *
	 * @param int $column
	 */
	public function addTotalizeColumn($column){
		if(!in_array($column, $this->_totalizeColumns)){
			$this->_totalizeColumns[] = $column;
		}
	}

	/**
	 * Establece las columnas a totalizar
	 *
	 * @param array $columns
	 */
	public function setTotalizeColumns($columns){
		$this->_totalizeColumns = $columns;
	}

	/**
	 * Remplaza constantes en el texto
	 *
	 * @param string $text
	 */
	protected function _prepareText($text){
		$text = str_replace("%pageNumber%", $this->_getPageNumber(), $text);
		return $text;
	}

	/**
	 * Establece el numero de pagina actual
	 *
	 * @param integer $number
	 */
	protected function _setPageNumber($number){
		$this->_pageNumber = $number;
	}

	/**
	 * Devuelve el numero de pagina actual
	 *
	 * @return integer
	 */
	protected function _getPageNumber(){
		return $this->_pageNumber;
	}

	/**
	 * Establece si se debe paginar automáticamente ó por defecto
	 *
	 * @param boolean $pagination
	 */
	public function setPagination($pagination){
		$this->_pagination = $pagination;
	}

	/**
	 * Devuelve si se debe paginar automáticamente ó por defecto
	 *
	 */
	public function getPagination(){
		return $this->_pagination;
	}

	/**
	 * Establece el numero de registros por pagina
	 *
	 * @param int $number
	 */
	public function setRowsPerPage($number){
		$this->_rowsPerPage = (int) $number;
	}

	/**
	 * Devuelve el numero de registros por pagina
	 *
	 * @return devuelve
	 */
	public function getRowsPerPage(){
		return $this->_rowsPerPage;
	}

	/**
	 * Establece el modo de visualizacion del reporte
	 *
	 * @param string $displayMode
	 */
	public function setDisplayMode($displayMode){
		$this->_displayMode = $displayMode;
	}

	/**
	 * Devuelve el modo de visualizacion
	 *
	 */
	public function getDisplayMode(){
		return $this->_displayMode;
	}

	/**
	 * Genera la salida a un archivo
	 *
	 * @param string $fileName
	 */
	public function outputToFile($fileName){
		file_put_contents($fileName.'.'.$this->getFileExtension(), $this->getOutput());
	}

}