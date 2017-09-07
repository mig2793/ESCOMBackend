<?php

namespace escom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Reports extends Model
{
    protected function getReport($idReport){
    	if($idReport == "insumos"){
    		$getReport = DB::select( DB::raw("
    			SELECT 		i.codigo,
                            i.insumo,
                            i.cantidad
                FROM insumos i
                ORDER BY i.insumo desc"));  
    	}else if($idReport == "novedades"){
    		$getReport = DB::select( DB::raw("
    			SELECT eq.Equipo,
					no.Fecha,
					no.Novedad,
					no.Estado,
					pr.prioridad
					FROM novedades no
					INNER JOIN equipos eq ON eq.id_Equipos = no.Id_equipos
					INNER JOIN prioridad pr ON pr.Id_prioridad = no.Id_prioridad
				"));
    	else{
    		$getReport = "No se encontró el reporte solicitado";
    	}
    	return $getReport;
    }
}
