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
                WHERE n.Id_novedad = ".$id.""));  

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
                INNER JOIN equipos e ON e.id_Equipos = n.Id_equipos"));

    	return $selectAllN;
    }

    protected function insertNovelty($request){
    	
    	$insertNovelty = DB::insert(DB::raw("
    			INSERT INTO novedades(Id_equipos,Fecha,Novedad,Id_prioridad,Estado)
	    		VALUES 		(".$request["id_machine"].",
	    					'".$request["date"]."',
	    					'".$request["novelty"]."',
	    					'".$request["id_priority"]."',
                            '".$request["state"]."')
    		"));

    	return $insertNovelty;
    }

    protected function updateNovelty($request,$id){
    	$updateNovelty = DB::update(DB::raw("
    			UPDATE 	novedades
    			SET 	Estado 				=  	'".$request["state"]."'
			    WHERE 	Id_novedad			=	'".$id."'
    		"));

    	return $updateNovelty;
    }
}
