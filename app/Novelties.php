<?php

namespace escom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Novelties extends Model
{
public $timestamps = false;

    protected $table = 'equipos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Id_novedad','Id_equipos','Fecha','Novedad','Id_prioridad','prioridad','Equipo','Serial','Estado',
    ];

    protected function getNovelties($id){
    	$selectnovelty = DB::select( DB::raw("
                SELECT      n.Id_novedad,
                            n.Id_equipos,
                            n.Fecha,
                            n.Novedad,
                            n.Id_prioridad,
                            p.prioridad,
                            e.Equipo,
                            e.Serial
                FROM novedades n 
                INNER JOIN prioridad p ON p.Id_prioridad = n.Id_prioridad
                INNER JOIN equipos e ON e.id_Equipos = n.Id_equipos
                WHERE n.Id_novedad = ".$id."
                ORDER BY n.Fecha desc"));  

    	return $selectnovelty;
    }

    protected function getNovelDates($date){
        $selectnovelty = DB::select( DB::raw("
                SELECT      e.Serial,
                            n.Id_novedad,
                            e.Equipo,
                            n.Fecha,
                            n.Novedad,
                            p.prioridad,
                            n.solucion
                FROM novedades n 
                INNER JOIN prioridad p ON p.Id_prioridad = n.Id_prioridad
                INNER JOIN equipos e ON e.id_Equipos = n.Id_equipos
                WHERE n.Fecha >= '".$date["datei"]."' and n.Fecha <= '".$date["datef"]."'
                ORDER BY n.Fecha desc"));  

        return $selectnovelty;
    }

    protected function getAllNovelties(){
    	$selectAllN = DB::select(DB::raw("
                SELECT      n.Id_novedad,
                            n.Id_equipos,
                            n.Fecha,
                            n.Novedad,
                            n.Id_prioridad,
                            p.prioridad,
                            e.Equipo,
                            e.Serial
                FROM novedades n 
                INNER JOIN prioridad p ON p.Id_prioridad = n.Id_prioridad
                INNER JOIN equipos e ON e.id_Equipos = n.Id_equipos
                WHERE n.Estado = 'Enviado'"));

    	return $selectAllN;
    }

    protected function insertNovelty($request){
    	$mydate=getdate(date("U"));
        $dateCurrent = "$mydate[year]/$mydate[mon]/$mydate[mday] $mydate[hours]:$mydate[minutes]:$mydate[seconds]";
    	$insertNovelty = DB::insert(DB::raw("
    			INSERT INTO novedades(Id_equipos,Fecha,Novedad,Id_prioridad,Estado)
	    		VALUES 		(".$request["id_machine"].",
	    					'".$dateCurrent."',
	    					'".$request["novelty"]."',
	    					'".$request["id_priority"]."',
                            '".$request["state"]."')
    		"));

    	return $insertNovelty;
    }

    protected function updateNovelty($request,$id){
    	$updateNovelty = DB::update(DB::raw("
    			UPDATE 	novedades
    			SET 	Estado 				=  	'".$request["state"]."',
                        solucion            =   '".$request["solution"]."'
			    WHERE 	Id_novedad			=	'".$id."'
    		"));

    	return $updateNovelty;
    }
}
