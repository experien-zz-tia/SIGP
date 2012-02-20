<?php
include_once 'utilities/PDF.php';
include_once 'utilities/constantes.php';
include_once 'utilities/templatePower/class.TemplatePower.inc.php';
require_once('correo.php');
class ReporteController extends ApplicationController {

	protected   $auth;

	protected function initialize(){
		$this->auth=Auth::getActiveIdentity();
	}
	public function reporteCalificacionesAction(){
	//	ob_end_clean();
		$this->setResponse('view');
		$categoria=$this->auth['categoriaUsuario_id'];
		$encabezados= $this->crearEncabezadosNotas($categoria);
		$pasante = new Pasante();
		$resultado = $pasante->getNotasPorTutor($categoria);
		$resultado = $resultado['resultado'];
		$datos = $this->procesarDatosNotas($resultado,$categoria);
		$pdf = new ReportPDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->imprimirTitulo('Reporte de Calificaciones');
		$nombreTutor=Session::getData('nombre');
		$textoItem='';
		$firma='';
		$nombreReporte='';
		switch ($categoria) {
			case CAT_USUARIO_COORDINADOR:
				$nombreReporte='reporteCalificaciones.pdf';
				$nombreTutor='TODOS';
				$textoItem='Tutores';
				$firma='Coordinador de Pasant�as';
				break;
			case CAT_USUARIO_TUTOR_ACAD:
				$nombreReporte='reporteCalificacionesTA.pdf';
				$textoItem='Tutor Acad�mico';
				$firma=$textoItem;
				break;
			case CAT_USUARIO_TUTOR_EMP:
				$nombreReporte='reporteCalificacionesTE.pdf';
				$textoItem='Tutor Empresarial';
				$firma=$textoItem;
				break;
		}
		$pdf->imprimirItemTextoBasico($textoItem, $nombreTutor);
		$pdf->tablaBasica($encabezados, $datos);
		$pdf->imprimirFirma($firma);
		//$pdf->Output(DIRECTORIO_CREACION_PDF.'/'.$nombreReporte,'F');
		$doc = $pdf->Output('', 'S');
		$this->setParamToView('documento', $doc);
//		$this->setParamToView('ruta', DIRECTORIO_PUBLICACION_PDF.'/'.$nombreReporte);

	}


	private function procesarDatosNotas($datos,$categoria) {
		$i=0;
		$aux = array();
		foreach ($datos as $dato){
			$aux[$i][0]=$i+1;
			$aux[$i][1]=$dato['cedula'];
			$aux[$i][2]=$aux[$i][2]=$dato['apellido'].', '.$dato['nombre'];
			switch ($categoria) {
				case CAT_USUARIO_COORDINADOR:
					$aux[$i][3]=$dato['notaInforme'];
					$aux[$i][4]=$dato['notaEmpresaTA'];
					$aux[$i][5]=$dato['notaEmpresaTE'];
					$aux[$i][6]=$dato['notaInforme'] + $dato['notaEmpresaTA'] + $dato['notaEmpresaTE'];
					break;
				case CAT_USUARIO_TUTOR_ACAD:
					$aux[$i][3]=$dato['notaInforme'];
					$aux[$i][4]=$dato['notaEmpresaTA'];
					break;
				case CAT_USUARIO_TUTOR_EMP:
					$aux[$i][3]=$dato['notaEmpresaTE'];
					break;
			}
			$i++;
		}
		return $aux;
	}

	private function crearEncabezadosNotas($categoria) {
		$aux= array(array("titulo"=>'Nro',
								"ancho"=>6,
								"tipo"=>"NUMBER",
								"alineacion"=>"R"),
		array("titulo"=>'C�dula',
								"ancho"=>20,
								"tipo"=>"string",
								"alineacion"=>"L"),
		array("titulo"=>'Apellidos y Nombres',
								"ancho"=>80,
								"tipo"=>"string",
								"alineacion"=>"L"));     	
		switch ($categoria) {
			case CAT_USUARIO_COORDINADOR:
				array_push($aux,
				array("titulo"=>'Informe',
								"ancho"=>20,
								"tipo"=>"decimal",
								"alineacion"=>"R"),
				array("titulo"=>'Empresa T.A',
								"ancho"=>22,
								"tipo"=>"decimal",
								"alineacion"=>"R"),
				array("titulo"=>'Empresa T.E.',
								"ancho"=>22,
								"tipo"=>"decimal",
								"alineacion"=>"R"),
				array("titulo"=>'Acumulado',
								"ancho"=>22,
								"tipo"=>"decimal",
								"alineacion"=>"R")
				);
				break;
			case CAT_USUARIO_TUTOR_ACAD:
				array_push($aux,
				array("titulo"=>'Informe',
								"ancho"=>20,
								"tipo"=>"decimal",
								"alineacion"=>"R"),
				array("titulo"=>'Empresa T.A',
								"ancho"=>22,
								"tipo"=>"decimal",
								"alineacion"=>"R")
				);
				break;
			case CAT_USUARIO_TUTOR_EMP:
				$aux[]=array("titulo"=>'Empresa T.E.',
								"ancho"=>22,
								"tipo"=>"decimal",
								"alineacion"=>"R");
				break;
		}
			
		return $aux;
	}



	protected function prepararPlantillaConstanciaTutorAcad($coordinador,$tutor,$pasantias) {
		$plantilla = new TemplatePower(DIRECTORIO_PLANTILLAS.'/constanciaTutorAcad.tpl');
		$plantilla->prepare();
		// Datos de la constancia
		$plantilla->assign('coordinador',$coordinador['nombre'].' '.$coordinador['apellido']);
		$plantilla->assign('cedula', $coordinador['cedula']);
		$plantilla->assign('nombreTutor', $tutor['nombre']);
		$plantilla->assign('apellidoTutor', $tutor['apellido']);
		$plantilla->assign('cedulaTutor', $tutor['cedula']);
		$contenido = $plantilla->getOutputContent();
		return $contenido;
	}

	public function mostrarConstanciaTutorAction() {
		$encabezados= array(array("titulo"=>'Pasant�a',
								"ancho"=>65,
								"tipo"=>"STRING",
								"alineacion"=>"L"),
		array("titulo"=>'Empresa',
								"ancho"=>60,
								"tipo"=>"string",
								"alineacion"=>"L"),
		array("titulo"=>'Pasante',
								"ancho"=>60,
								"tipo"=>"string",
								"alineacion"=>"L"));  
		$pdf=new ReportPDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->imprimirTitulo('Historial de asesor�as - Tutor Acad�mico');
		$pdf->SetFont('Arial','',12);
		$pasantia = new Pasantia();
		$idTutor=1;// ESTATICO
		$datos  = $pasantia->buscarPasantiasSupervizadas($idTutor);
		$coordinacion = new Coordinacion();
		$coord=$coordinacion->getDatosCoordinador(DECANATO_CIENCIAS);
		$tutorAcad= new TutorAcademico();
		$tutor=$tutorAcad->getTutorAcademicoById($idTutor);
		$html=$this->prepararPlantillaConstanciaTutorAcad($coord,$tutor,$datos);
		$pdf->WriteHTML($html);
		$datosRA=array();
		$i=0;
		$j=0;
		foreach ($datos as $dato){
			$datosRA[$i]['subTitulo']="{$dato['lapso']} ({$dato['fchInicio']} - {$dato['fchFin']})";
			foreach ($dato['datos'] as $elemento)   {
				$datosRA[$i]['datos'][$j][0]= $elemento['titulo'];
				$datosRA[$i]['datos'][$j][1]= $elemento['razonSocial'];
				$datosRA[$i]['datos'][$j][2]= $elemento['pasante'];
				$j++;
			}
			$i++;
		}
		$pdf->tablaBasicaHistorica($encabezados, $datosRA);
		$pdf->imprimirFirma('Coordinador de Pasant�as');
		$doc = $pdf->Output('', 'S');
		$this->enviarConstantiaTutorAcademico($tutor['correo'], $doc);

	}

	protected function enviarConstantiaTutorAcademico($correo,$pdf) {
		$mailer = new Correo();
		$body ="Estimado usuario,<BR> A continuaci�n se adjunta el historial de asesor�as. En caso de presentar alg�n inconveniente dirigase a la coordinaci�n de pasant�as.";
		$body .="<BR>Correo generado automaticamente por Experientia."; 
		$mailer->enviarCorreo($correo, 'Historial de asesorias', $body,$pdf,'constancia.pdf');
	}
	
	
	public function constanciaNotasPasanteAction(){
		$categoria=$this->auth['categoriaUsuario_id'];
		$decanatoId= DECANATO_CIENCIAS;
		if($categoria==CAT_USUARIO_PASANTE){
			$conf= new Configuracion();
			if ($conf->getConsultaCalificacionesbyDecanato($decanatoId)=='S'){
				$id=$this->auth['idUsuario'];
				$this->setResponse('view');
				$this->setParamToView('documento', $this->crearConstanciaNotas($id));
			}else{
				$this->routeTo('controller: pasante','action: consultaSinHabilitar');
			}
		}elseif($categoria==CAT_USUARIO_COORDINADOR OR $categoria==CAT_USUARIO_ANALISTA){
			$id= $this->getParametro('pPasanteId', 'numerico', -1);
			if ($id!=-1){
				$this->setResponse('view');
				$this->setParamToView('documento', $this->crearConstanciaNotas($id));
			}else{
				Router::routeToURI('/error/index/parametrosNoValidos');
			}
			
		}
	}
	
	protected function crearConstanciaNotas($idPasante) {
		$encabezados= array(array("titulo"=>'�tem',
								"ancho"=>140,
								"tipo"=>"STRING",
								"alineacion"=>"L"),
		array("titulo"=>'Calificaci�n',
								"ancho"=>45,
								"tipo"=>"NUMBER",
								"alineacion"=>"R"));
		$encabezadosPrincipal= array(array("titulo"=>'Nota Informe',
											"ancho"=>45,
											"tipo"=>"string",
											"alineacion"=>"R"),
									array("titulo"=>'Nota Empresa (TE)',
											"ancho"=>45,
											"tipo"=>"string",
											"alineacion"=>"R"),
									array("titulo"=>'Nota Empresa(TA)',
											"ancho"=>45,
											"tipo"=>"string",
											"alineacion"=>"R"),
									array("titulo"=>'Acumulado',
											"ancho"=>50,
											"tipo"=>"string",
											"alineacion"=>"R"));    
		$pdf=new ReportPDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->imprimirTitulo('Constancia de Calificaciones');
		$pdf->SetFont('Arial','',12);
		$pasante = new Pasante();
		$datosPasante  = $pasante->getPasantebyId($idPasante);
		$aux =$pasante->getNotasPorTutor('',$datosPasante['cedula']);
		$resultado= array();
		if ($aux){
			$aux=$aux['resultado'][0];
			$resultado[0][]=$aux['notaInforme'];
			$resultado[0][]=$aux['notaEmpresaTE'];
			$resultado[0][]=$aux['notaEmpresaTA'];
			$resultado[0][]=$aux['acumulado'];
		}
		$pdf->imprimirItemTextoBasico('Nombre y Apellido', "{$datosPasante['nombre']} {$datosPasante['apellido']}");
		$pdf->imprimirItemTextoBasico(utf8_encode('C�dula'), $datosPasante['cedula']);
		$pdf->imprimirItemTextoBasico('Programa', $datosPasante['carrera']);
		$pdf->tablaBasica($encabezadosPrincipal, $resultado);
		$pdf->Ln(1);
		$pdf->tablaBasica(array(array("titulo"=>'Detalle Calificaciones',
								"ancho"=>185,
								"tipo"=>"string",
								"alineacion"=>"C")), array());
	
		$evaluaciones =  new Pasanteevaluacion();
		$datosNotas=$evaluaciones->getDetalleNotas($idPasante,'*');
		$datosRA=array();
		$i=-1;
		$anterior=-1;
		foreach ($datosNotas as $dato){
			if ($dato['evaluacionId']!=$anterior){
					$j=0;
					$i++;
					$datosRA[$i]['subTitulo']=$dato['evalDescripcion'];
					$anterior= $dato['evaluacionId'];
			}
			$datosRA[$i]['datos'][$j][0]= $dato['item'];
			$datosRA[$i]['datos'][$j][1]= $dato['nota'];
			$j++;
		}
		$pdf->tablaBasicaHistorica($encabezados, $datosRA);
		$plantilla = new TemplatePower(DIRECTORIO_PLANTILLAS.'/notaConstanciaNotasPasante.tpl');
		$plantilla->prepare();
		$notaFinal = $plantilla->getOutputContent();
		$pdf->WriteHTML($notaFinal);
		$doc = $pdf->Output('', 'S');
		return $doc;
	}
	
	
}