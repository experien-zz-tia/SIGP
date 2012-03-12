<?php
include_once 'utilities/PDF.php';
include_once 'utilities/constantes.php';
include_once 'utilities/templatePower/class.TemplatePower.inc.php';
require_once('correo.php');
class ReporteController extends ApplicationController {

	protected   $auth;

	protected function initialize(){
		$this->setTemplateAfter("menu");
		$this->auth=Auth::getActiveIdentity();
	}

	public function reporteCalificacionesAction(){

	}

	public function cartaPostulacionAction(){}

	public function solCartaPostulacionAction(){
		
		$categoria=$this->auth['categoriaUsuario_id'];
		$decanatoId= DECANATO_CIENCIAS;
		
		echo 'decanato: '.$decanatoId;
		
		if($categoria==CAT_USUARIO_PASANTE){
			echo ' categoria'.$categoria;
			$conf= new Configuracion();
			if ($conf->getConsultaCalificacionesbyDecanato($decanatoId)=='S'){
				echo ' if';
				$id=$this->auth['idUsuario'];
				$this->setResponse('view');
				$this->setParamToView('documento', $this->crearConstanciaNotas($id));
			}else{
				echo ' else';
//				$this->routeTo('controller: pasante','action: consultaSinHabilitar');
			}
		}
	}

	public function mostrarCalificacionesAction(){
		$this->setResponse('view');
		$categoria=$this->auth['categoriaUsuario_id'];
		$encabezados= $this->crearEncabezadosNotas($categoria);
		$pasante = new Pasante();
		$carrera=$this->getParametro('pCarrera', 'numerico', '*');
		$resultado = $pasante->getNotasPorTutor($categoria,'','*','*','*',$carrera);
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
				$firma='Coordinador de Pasantías';
				break;
			case CAT_USUARIO_TUTOR_ACAD:
				$nombreReporte='reporteCalificacionesTA.pdf';
				$textoItem='Tutor Academico';
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
		$doc = $pdf->Output('', 'S');
		$this->setParamToView('documento', $doc);
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
		array("titulo"=>'Cédula',
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
		$this->setResponse('view');
		$encabezados= array(array("titulo"=>'Pasantía',
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
		$pdf->imprimirTitulo('Historial de asesorías - Tutor Académico');
		$pdf->SetFont('Arial','',12);
		$pasantia = new Pasantia();
		$idTutor=$this->auth['idUsuario'];
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
		$pdf->imprimirFirma('Coordinador de Pasantías');
		$doc = $pdf->Output('', 'S');
		$this->setParamToView('documento', $doc);

		//	$this->enviarConstantiaTutorAcademico($tutor['correo'], $doc);

	}

	protected function enviarConstantiaTutorAcademico($correo,$pdf) {
		$mailer = new Correo();
		$body ="Estimado usuario,<BR> A continuación se adjunta el historial de asesorías. En caso de presentar algún inconveniente dirigase a la coordinación de pasantías.";
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
		$encabezados= array(array("titulo"=>'Ítem',
								"ancho"=>140,
								"tipo"=>"STRING",
								"alineacion"=>"L"),
		array("titulo"=>'Calificación',
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
		$pdf->imprimirItemTextoBasico(utf8_encode('Cédula'), $datosPasante['cedula']);
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

	public function mostrarPasantesAction(){
		$datos = array();
		$pasante= new Pasante();
		$carrera=$this->getParametro('pCarrera', 'numerico', '');
		$datos=$pasante->consultaPasantias($carrera,'','*','*');

		$datos= $datos['resultado'];
		$this->setResponse('view');
		$encabezadosPrincipal= array(array("titulo"=>'Cédula',
											"ancho"=>20,
											"tipo"=>"string",
											"alineacion"=>"R"),
		array("titulo"=>'Apellidos',
											"ancho"=>45,
											"tipo"=>"string",
											"alineacion"=>"L"),
		array("titulo"=>'Nombres',
											"ancho"=>45,
											"tipo"=>"string",
											"alineacion"=>"L"),
		array("titulo"=>'Empresa',
											"ancho"=>50,
											"tipo"=>"string",
											"alineacion"=>"L"),
		array("titulo"=>'Estatus',
											"ancho"=>30,
											"tipo"=>"string",
											"alineacion"=>"L")

		);
		$datos = $this->procesarDatosListadoPasante($datos);
		$pdf = new ReportPDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->imprimirTitulo('Reporte de Pasantes');
		$pdf->tablaBasica($encabezadosPrincipal, $datos);
		$doc = $pdf->Output('', 'S');
		$this->setParamToView('documento', $doc);
	}

	private function procesarDatosListadoPasante($datos) {
		$i=0;
		$aux = array();
		foreach ($datos as $dato){
			$aux[$i][0]=$dato['cedulaPasante'];
			$aux[$i][1]=$dato['apellidoPasante'];
			$aux[$i][2]=$dato['nombrePasante'];
			$aux[$i][3]=$dato['razonSocial'];
			$aux[$i][4]=$dato['estatusPasantia'];
			$i++;
		}
		return $aux;
	}

	public function reporteMaestrosAction(){

	}

	public function mostrarOfertasAction(){
		$datos = array();
		$oferta= new Oferta();
		$inicio=$this->getParametro('pInicio', 'STRING', '');
		$fin=$this->getParametro('pFin', 'STRING', '');
		$datos=$oferta->getOfertasReporte($inicio, $fin);
		$this->setResponse('view');
		$encabezadosPrincipal= array(array("titulo"=>'Fecha',
											"ancho"=>20,
											"tipo"=>"string",
											"alineacion"=>"R"),
		array("titulo"=>'Empresa',
											"ancho"=>45,
											"tipo"=>"string",
											"alineacion"=>"L"),
		array("titulo"=>'Titulo',
											"ancho"=>85,
											"tipo"=>"string",
											"alineacion"=>"L"),
		array("titulo"=>'Vacantes',
											"ancho"=>20,
											"tipo"=>"string",
											"alineacion"=>"R"),
		array("titulo"=>'Tipo',
											"ancho"=>20,
											"tipo"=>"string",
											"alineacion"=>"L")

		);
		$pdf = new ReportPDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->imprimirTitulo('Reporte de Ofertas');
		if ($inicio!='' && $fin!=''){
			$pdf->imprimirItemTextoBasico("Fechas", "Desde el ".$inicio." hasta el ".$fin);
		}
		$pdf->tablaBasica($encabezadosPrincipal, $datos);
		$doc = $pdf->Output('', 'S');
		$this->setParamToView('documento', $doc);
	}



	public function mostrarTutoresAction(){
		$datos = array();
		$this->setResponse('view');
		$tipo=$this->getParametro('pTipo', 'STRING', '');

		if ($tipo=='E'){
			$tutor= new TutorEmpresarial();
			$datos=$tutor->getTutoresEmpresarialesReporte();
			$encabezadosPrincipal= array(array("titulo"=>'Cédula',
											"ancho"=>20,
											"tipo"=>"string",
											"alineacion"=>"L"),
			array("titulo"=>'Nombre',
											"ancho"=>45,
											"tipo"=>"string",
											"alineacion"=>"L"),
			array("titulo"=>'Apellido',
											"ancho"=>45,
											"tipo"=>"string",
											"alineacion"=>"L"),
			array("titulo"=>'Empresa',
											"ancho"=>45,
											"tipo"=>"string",
											"alineacion"=>"L"),
			array("titulo"=>'Cargo',
											"ancho"=>35,
											"tipo"=>"string",
											"alineacion"=>"L")

			);
		}
		else{
			$tutor= new TutorAcademico();
			$datos=$tutor->getTutoresAcademicosReporte();
			$encabezadosPrincipal= array(array("titulo"=>'Cédula',
											"ancho"=>20,
											"tipo"=>"string",
											"alineacion"=>"L"),
			array("titulo"=>'Nombre',
											"ancho"=>35,
											"tipo"=>"string",
											"alineacion"=>"L"),
			array("titulo"=>'Apellido',
											"ancho"=>35,
											"tipo"=>"string",
											"alineacion"=>"L"),
			array("titulo"=>'Departamento',
											"ancho"=>85,
											"tipo"=>"string",
											"alineacion"=>"L"),
			array("titulo"=>'Cargo',
											"ancho"=>20,
											"tipo"=>"string",
											"alineacion"=>"L")

			);
		}

		$pdf = new ReportPDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->imprimirTitulo('Reporte de Tutores '.($tipo=='A'?'Académicos':'Empresariales'));
		$pdf->tablaBasica($encabezadosPrincipal, $datos);
		$doc = $pdf->Output('', 'S');
		$this->setParamToView('documento', $doc);
	}


	public function mostrarEmpresasAction(){
		$datos = array();
		$empresa= new Empresa();
		$ciudad=$this->getParametro('pCiudad', 'STRING', '');
		$datos=$empresa->getEmpresasReporte($ciudad);
		$this->setResponse('view');
		$encabezadosPrincipal= array(array("titulo"=>'RIF',
											"ancho"=>25,
											"tipo"=>"string",
											"alineacion"=>"L"),
		array("titulo"=>'Empresa',
											"ancho"=>55,
											"tipo"=>"string",
											"alineacion"=>"L"),
		array("titulo"=>'Contacto',
											"ancho"=>40,
											"tipo"=>"string",
											"alineacion"=>"L"),
		array("titulo"=>'Teléfono',
											"ancho"=>25,
											"tipo"=>"string",
											"alineacion"=>"R"),
		array("titulo"=>'E-mail',
											"ancho"=>45,
											"tipo"=>"string",
											"alineacion"=>"L")

		);
		$pdf = new ReportPDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->imprimirTitulo('Reporte de Empresas');

		$pdf->tablaBasica($encabezadosPrincipal, $datos);
		$doc = $pdf->Output('', 'S');
		$this->setParamToView('documento', $doc);
	}


}