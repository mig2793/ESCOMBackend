<?php

namespace escom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Equipos extends Model
{
    public $timestamps = false;

    protected $table = 'equipos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_Equipos','Equipo','Serial','Modelo','id_Marca','Estado','TiempoMantenimiento','TiempoUsoActual','Imagen','id_equipo','id_insumo',
    ];

    protected function getMachine($serial){
    	$selectMachine = DB::select( DB::raw("
                SELECT      e.id_Equipos,
                            e.Equipo,
                            e.Serial,
                            e.Modelo,
                            e.id_Marca,
                            e.Estado,
                            e.TiempoMantenimiento,
                            e.TiempoUsoActual,
                            e.Imagen,
                            m.marca
                FROM equipos e 
                INNER JOIN marcas m ON e.id_Marca = m.id_marca
                WHERE e.Serial = '".$serial."'"));

        $selectsupplies = DB::select( DB::raw("
                SELECT  ei.id_insumo,
                        i.insumo
                FROM equiposxinsumo ei
                INNER JOIN insumos i ON i.Id_insumo = ei.id_insumo 
                WHERE ei.id_equipo = '".$serial."'"));

        if($selectMachine){
            $selectMachine["insumos"] = $selectsupplies;
        }

    	return $selectMachine;
    }

    protected function getAllMachine(){
    	$selectAllM = DB::select(DB::raw("
    			SELECT  e.id_Equipos,
                        e.Equipo,
                        e.Serial,
                        e.Imagen,
                        e.Estado
                FROM equipos e 
                WHERE e.Estado = 'Disponible'"));

    	return $selectAllM;
    }

    protected function insertMachine($request){
    	
    	
    	$insertMachine = DB::insert(DB::raw("
    			INSERT INTO equipos(Equipo,Serial,Modelo,id_Marca,Estado,TiempoMantenimiento,TiempoUsoActual,Imagen)
	    		VALUES 		('".$request["machine"]."',
	    					'".$request["serial"]."',
	    					'".$request["model"]."',
	    					'".$request["id_mark"]."',
	    					'".$request["state"]."',
	    					".$request["timeMaintenance"].",
	    					".$request["timeUseCurrent"].",
	    					'".$request["image"]."');
    		"));

    	return $insertMachine;
    }

    protected function updateMachine($request,$serial){
    	$updateMachine = DB::update(DB::raw("
    			UPDATE 	equipos
    			SET 	Equipo 				=  	'".$request["machine"]."',
    					Modelo 				=  	'".$request["model"]."',
    					id_Marca 			=  	'".$request["id_mark"]."',
    					Estado				=	'".$request["state"]."',
			    		TiempoMantenimiento	=	".$request["timeMaintenance"].",
			    		TiempoUsoActual		=  	".$request["timeUseCurrent"].",
			    		Imagen				=  	'".$request["image"]."'
			    WHERE 	Serial 				=	'".$serial."'
    		"));

    	return $updateMachine;
    }

    protected function insertMxS($idEquipo,$supplies){
        $arraybad = array();
        $countSupplies = count($supplies);
        for ($i = 0; $i < $countSupplies; $i++) { 
            $insertMxS = DB::insert(DB::raw("
                    INSERT INTO equiposxinsumo(id_equipo,id_insumo) 
                    VALUES ('".$idEquipo."','".$supplies[$i]."')
                "));
            if($insertMxS <= 0)
                array_push($arraybad,$supplies[$i]);
        }

        return $arraybad;
    }

    protected function deleteMxS($idEquipo,$idsupplies){
        $deleteMxS = DB::delete(DB::raw("
                DELETE FROM equiposxinsumo
                WHERE id_equipo = '".$idEquipo."' AND id_insumo = '".$idsupplies."'
            "));

        return $deleteMxS;
    }   

}
