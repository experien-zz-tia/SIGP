<?php
include_once($_SERVER['DOCUMENT_ROOT'].'SIGP/Library/fpdf17/fpdf.php');
include_once('constantes.php');
/**
 * Clase para al elaboracion de documentos en PDF
 * @author Robert A
 *
 */


function hex2dec($couleur = "#000000"){
	$R = substr($couleur, 1, 2);
	$rouge = hexdec($R);
	$V = substr($couleur, 3, 2);
	$vert = hexdec($V);
	$B = substr($couleur, 5, 2);
	$bleu = hexdec($B);
	$tbl_couleur = array();
	$tbl_couleur['R']=$rouge;
	$tbl_couleur['G']=$vert;
	$tbl_couleur['B']=$bleu;
	return $tbl_couleur;
}

//conversion pixel -> millimeter in 72 dpi
function px2mm($px){
	return $px*25.4/72;
}

function txtentities($html){
	$trans = get_html_translation_table(HTML_ENTITIES);
	$trans = array_flip($trans);
	return strtr($html, $trans);
}

class ReportPDF extends FPDFv17{


	/**
	 * Columna de referencia para alinear los items
	 * @var int
	 */
	const COLUMNA_ITEM = 30;
	
	const COLUMNA_ESPACIO = 70;

	private function crearTituloEncabezado($titulo,$alineacion='C') {
		// Calculamos ancho y posición del título.
		$ancho = $this->GetStringWidth($titulo)+6;
		$this->SetX((210-$ancho)/2);
		$this->Cell($ancho,6,$titulo,0,1,"'$alineacion'");
	}

	function Header(){
		// Arial bold 12
		$this->SetFont('Arial','B',10);
		$this->Image(DIRECTORIO_IMAGENES.'/logoUcla.jpg',20,10);
		$this->Image(DIRECTORIO_IMAGENES.'/logoDecanato.jpg',165,12);
		$titulo='Universidad Centroccidental Lisandro Alvarado';
		$titulo2='Decanato de Ciencias y Tecnología';
		$titulo3='Coordinación de Pasantías';
		$this->crearTituloEncabezado($titulo);
		$this->crearTituloEncabezado($titulo2);
		$this->crearTituloEncabezado($titulo3);
		//Salto de línea
		$this->Ln(6);
	}

	function imprimirTitulo($titulo)	{
		// Arial 12
		$this->SetFont('Arial','B',12);
		// Color de fondo
		$this->SetFillColor(200,220,255);
		// Título
		$this->Cell(0,6,$titulo,0,1,'C',true);
		// Salto de línea
		$this->Ln(4);
	}

	function Footer(){
		// Posición a 2 cm del final
		$this->SetY(-20);
		$this->Cell(190,0,'','T');
		// Arial itálica 8
		$this->SetFont('Arial','I',8);
		// Color del texto en gris
		$this->SetTextColor(128);
		// Número de página
		$fecha = date('d/m/Y h:i:s A');
		$this->Cell(-200,5,"Sistema Experientia - Página {$this->PageNo()}/{nb} - $fecha",0,0,'C');
	  
	  
	}

	/**
	 * La suma total del ancho no debe pasar de 190 ( en pagina A4 Vertical)
	 *
	 * @param array $encabezados
	 * @param array $datos
	 */
	function tablaBasica($encabezados, $datos){
		// Colores, ancho de línea y fuente en negrita
		$this->SetFillColor(179,217,255); // Azul Claro
		$this->SetLineWidth(.3);
		$this->SetFont('','B',10);
		$totalAncho=0;
		for($i=0;$i<count($encabezados);$i++){
			$this->Cell($encabezados[$i]['ancho'],7,$encabezados[$i]['titulo'],1,0,'C',true);
			$totalAncho += $encabezados[$i]['ancho'];
		}
		$this->Ln();
		// Restauración de colores y fuentes
		$this->SetFillColor(224,235,255);
		$this->SetTextColor(0);
		$this->SetFont('','',10);
		// Datos
		$fill = false;
		foreach($datos as $fila) {
			for($i=0;$i<count($encabezados);$i++){
				$tipo= strtoupper($encabezados[$i]['tipo']);
				if( $tipo=='STRING'){
					$this->Cell($encabezados[$i]['ancho'],6,utf8_decode($fila[$i]),'LR',0,$encabezados[$i]['alineacion'],$fill);
				}elseif ($tipo=='NUMBER'){
					$this->Cell($encabezados[$i]['ancho'],6,number_format($fila[$i]),'LR',0,'R',$fill);
				}elseif ($tipo=='DECIMAL'){
					$this->Cell($encabezados[$i]['ancho'],6,number_format($fila[$i],2),'LR',0,'R',$fill);
				}
			}
			$this->Ln();
			$fill = !$fill;
		}
		// Línea de cierre
		$this->Cell($totalAncho,0,'','T');
		$this->Ln();
	}

	function imprimirItemTextoBasico($titulo, $descripcion) {
		
		
		$this->SetFont('','B',10);
		$this->Cell(6+10,10,utf8_decode($titulo),0,0);
		//$w=(ReportPDF::COLUMNA_ITEM- $this->GetStringWidth(utf8_decode($titulo)));
		$this->Cell(ReportPDF::COLUMNA_ITEM,10,' :',0,0,'R');
		//$this->Cell($w,10,' :',0,0,'R');
		$this->Cell(6,10,utf8_decode($descripcion),0,1);//1
		
	}

	function imprimirFirmas($nombreUno, $nombreDos) {
		$this->Ln(15);
		$this->Cell(30);
		$this->Cell(50,10,$nombreUno,'T',0,'C');
		$this->Cell(30);
		$this->Cell(50,10,$nombreDos,'T',1,'C');
	}


	function imprimirFirma($nombre) {
		$this->Ln(15);
		$this->Cell(75);
		$this->Cell(50,10,$nombre,'T',1,'C');

	}

	function tablaBasicaHistorica($encabezados, $datos){
		// Colores, ancho de línea y fuente en negrita
		$this->SetFillColor(179,217,255); // Azul Claro
		$this->SetLineWidth(.3);
		$this->SetFont('','B',10);
		$totalAncho=0;
		for($i=0;$i<count($encabezados);$i++){
			$this->Cell($encabezados[$i]['ancho'],7,$encabezados[$i]['titulo'],1,0,'C',true);
			$totalAncho += $encabezados[$i]['ancho'];
		}
		$this->Ln();
		// Restauración de colores y fuentes
		$this->SetFillColor(224,235,255);
		$this->SetTextColor(0);
		$this->SetFont('','',10);
		// Datos
		foreach($datos as $subGrupo) {
			$this->Cell($totalAncho,6,utf8_decode($subGrupo['subTitulo']),1,0,'C',true);
			$this->Ln();
			$fill = false;
			foreach($subGrupo['datos'] as $fila) {
				for($i=0;$i<count($encabezados);$i++){
					$tipo= strtoupper($encabezados[$i]['tipo']);
					if( $tipo=='STRING'){
						$this->Cell($encabezados[$i]['ancho'],6,utf8_decode($fila[$i]),'LR',0,"'{$encabezados[$i]['alineacion']}'",$fill);
					}elseif ($tipo=='NUMBER'){
						$this->Cell($encabezados[$i]['ancho'],6,number_format($fila[$i]),'LR',0,'R',$fill);
					}elseif ($tipo=='DECIMAL'){
						$this->Cell($encabezados[$i]['ancho'],6,number_format($fila[$i],2),'LR',0,'R',$fill);
					}
				}
				$this->Ln();
				//  $fill = !$fill;
			}
		}
		// Línea de cierre
		$this->Cell($totalAncho,0,'','T');
	}


	var $B;
	var $I;
	var $U;
	var $HREF;
	var $fontList;
	var $issetfont;
	var $issetcolor;
	var $tableborder;
	var $tdbegin;
	var $tdwidth;
	var $tdheight;
	var $tdalign;
	var $tdbgcolor;

	function PDF($orientation='P', $unit='mm', $format='A4')
	{
		//Call parent constructor
		$this->FPDFv17($orientation,$unit,$format);
		//Initialization
		$this->B=0;
		$this->I=0;
		$this->U=0;
		$this->HREF='';

		$this->tableborder=0;
		$this->tdbegin=false;
		$this->tdwidth=0;
		$this->tdheight=0;
		$this->tdalign="L";
		$this->tdbgcolor=false;

		$this->oldx=0;
		$this->oldy=0;

		$this->fontlist=array("arial","times","courier","helvetica","symbol");
		$this->issetfont=false;
		$this->issetcolor=false;
	}

	//////////////////////////////////////
	//html parser

	function WriteHTML($html)
	{
		$html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote><hr><td><tr><table><sup>"); //remove all unsupported tags
		$html=str_replace("\n",'',$html); //replace carriage returns by spaces
		$html=str_replace("\t",'',$html); //replace carriage returns by spaces
		$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //explodes the string
		foreach($a as $i=>$e)
		{
			if($i%2==0)
			{
				//Text
				if($this->HREF)
				$this->PutLink($this->HREF,$e);
				elseif($this->tdbegin) {
					if(trim($e)!='' && $e!="&nbsp;") {
						$this->Cell($this->tdwidth,$this->tdheight,$e,$this->tableborder,'',$this->tdalign,$this->tdbgcolor);
					}
					elseif($e=="&nbsp;") {
						$this->Cell($this->tdwidth,$this->tdheight,'',$this->tableborder,'',$this->tdalign,$this->tdbgcolor);
					}
				}
				else
				$this->Write(5,stripslashes(txtentities($e)));
			}
			else
			{
				//Tag
				if($e[0]=='/')
				$this->CloseTag(strtoupper(substr($e,1)));
				else
				{
					//Extract attributes
					$a2=explode(' ',$e);
					$tag=strtoupper(array_shift($a2));
					$attr=array();
					foreach($a2 as $v)
					{
						if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
						$attr[strtoupper($a3[1])]=$a3[2];
					}
					$this->OpenTag($tag,$attr);
				}
			}
		}
	}

	function OpenTag($tag, $attr)
	{
		//Opening tag
		switch($tag){

			case 'SUP':
				if( !empty($attr['SUP']) ) {
					//Set current font to 6pt
					$this->SetFont('','',6);
					//Start 125cm plus width of cell to the right of left margin
					//Superscript "1"
					$this->Cell(2,2,$attr['SUP'],0,0,'L');
				}
				break;

			case 'TABLE': // TABLE-BEGIN
				if( !empty($attr['BORDER']) ) $this->tableborder=$attr['BORDER'];
				else $this->tableborder=0;
				break;
			case 'TR': //TR-BEGIN
				break;
			case 'TD': // TD-BEGIN
				if( !empty($attr['WIDTH']) ) $this->tdwidth=($attr['WIDTH']/4);
				else $this->tdwidth=40; // Set to your own width if you need bigger fixed cells
				if( !empty($attr['HEIGHT']) ) $this->tdheight=($attr['HEIGHT']/6);
				else $this->tdheight=6; // Set to your own height if you need bigger fixed cells
				if( !empty($attr['ALIGN']) ) {
					$align=$attr['ALIGN'];
					if($align=='LEFT') $this->tdalign='L';
					if($align=='CENTER') $this->tdalign='C';
					if($align=='RIGHT') $this->tdalign='R';
				}
				else $this->tdalign='L'; // Set to your own
				if( !empty($attr['BGCOLOR']) ) {
					$coul=hex2dec($attr['BGCOLOR']);
					$this->SetFillColor($coul['R'],$coul['G'],$coul['B']);
					$this->tdbgcolor=true;
				}
				$this->tdbegin=true;
				break;

			case 'HR':
				if( !empty($attr['WIDTH']) )
				$Width = $attr['WIDTH'];
				else
				$Width = $this->w - $this->lMargin-$this->rMargin;
				$x = $this->GetX();
				$y = $this->GetY();
				$this->SetLineWidth(0.2);
				$this->Line($x,$y,$x+$Width,$y);
				$this->SetLineWidth(0.2);
				$this->Ln(1);
				break;
			case 'STRONG':
				$this->SetStyle('B',true);
				break;
			case 'EM':
				$this->SetStyle('I',true);
				break;
			case 'B':
			case 'I':
			case 'U':
				$this->SetStyle($tag,true);
				break;
			case 'A':
				$this->HREF=$attr['HREF'];
				break;
			case 'IMG':
				if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
					if(!isset($attr['WIDTH']))
					$attr['WIDTH'] = 0;
					if(!isset($attr['HEIGHT']))
					$attr['HEIGHT'] = 0;
					$this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
				}
				break;
			case 'BLOCKQUOTE':
			case 'BR':
				$this->Ln(5);
				break;
			case 'P':
				$this->Ln(10);
				break;
			case 'FONT':
				if (isset($attr['COLOR']) && $attr['COLOR']!='') {
					$coul=hex2dec($attr['COLOR']);
					$this->SetTextColor($coul['R'],$coul['G'],$coul['B']);
					$this->issetcolor=true;
				}
				if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
					$this->SetFont(strtolower($attr['FACE']));
					$this->issetfont=true;
				}
				if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist) && isset($attr['SIZE']) && $attr['SIZE']!='') {
					$this->SetFont(strtolower($attr['FACE']),'',$attr['SIZE']);
					$this->issetfont=true;
				}
				break;
		}
	}

	function CloseTag($tag)
	{
		//Closing tag
		if($tag=='SUP') {
		}

		if($tag=='TD') { // TD-END
			$this->tdbegin=false;
			$this->tdwidth=0;
			$this->tdheight=0;
			$this->tdalign="L";
			$this->tdbgcolor=false;
		}
		if($tag=='TR') { // TR-END
			$this->Ln();
		}
		if($tag=='TABLE') { // TABLE-END
			//$this->Ln();
			$this->tableborder=0;
		}

		if($tag=='STRONG')
		$tag='B';
		if($tag=='EM')
		$tag='I';
		if($tag=='B' || $tag=='I' || $tag=='U')
		$this->SetStyle($tag,false);
		if($tag=='A')
		$this->HREF='';
		if($tag=='FONT'){
			if ($this->issetcolor==true) {
				$this->SetTextColor(0);
			}
			if ($this->issetfont) {
				$this->SetFont('arial');
				$this->issetfont=false;
			}
		}
	}

	function SetStyle($tag, $enable)
	{
		//Modify style and select corresponding font
		$this->$tag+=($enable ? 1 : -1);
		$style='';
		foreach(array('B','I','U') as $s) {
			if($this->$s>0)
			$style.=$s;
		}
		$this->SetFont('',$style);
	}

	function PutLink($URL, $txt)
	{
		//Put a hyperlink
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(5,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}


}