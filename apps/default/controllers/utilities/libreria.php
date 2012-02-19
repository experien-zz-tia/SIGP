<?php
class Libreria {

	static function compararFechas($primera, $segunda)  {
		$valoresPrimera = explode ("/", $primera);
		$valoresSegunda = explode ("/", $segunda);
		$diaPrimera    = $valoresPrimera[0];
		$mesPrimera  = $valoresPrimera[1];
		$anyoPrimera   = $valoresPrimera[2];
		$diaSegunda   = $valoresSegunda[0];
		$mesSegunda = $valoresSegunda[1];
		$anyoSegunda  = $valoresSegunda[2];
		$diasPrimeraJuliano = gregoriantojd($mesPrimera, $diaPrimera, $anyoPrimera);
		$diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anyoSegunda);
		if(!checkdate($mesPrimera, $diaPrimera, $anyoPrimera)){
			return 0;
		}elseif(!checkdate($mesSegunda, $diaSegunda, $anyoSegunda)){
			return 0;
		}else{
			return   $diasSegundaJuliano-$diasPrimeraJuliano;
		}
	}
	
	static function obtenerAnio($fecha)  {
		$valores = explode ("/", $fecha);
		return $valores[2];
}
}

?>