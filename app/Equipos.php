<?php

namespace escom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use escom\Equipos;

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
                            e.TiempoAcumulado,
                            e.Imagen,
                            m.marca
                FROM equipos e 
                INNER JOIN marcas m ON e.id_Marca = m.id_marca
                WHERE e.Serial = '".$serial."'
                ORDER BY e.Equipo desc"));

        $selectsupplies = DB::select( DB::raw("
                SELECT  ei.Id_insumo,
                        i.insumo
                FROM equiposxinsumo ei
                INNER JOIN insumos i ON i.Id_insumo = ei.id_insumo 
                WHERE ei.id_equipo = '".$serial."'"));

        if($selectMachine){
            $selectMachine["insumos"] = $selectsupplies;
        }

    	return $selectMachine;
    }

    /*protected function getMachine($serial){
        $selectMachine = DB::select( DB::raw("
                SELECT      e.id_Equipos,
                            e.Equipo,
                            e.Serial,
                            e.Modelo,
                            e.id_Marca,
                            e.Estado,
                            e.TiempoMantenimiento,
                            e.TiempoUsoActual,
                            SUM(e.TiempoAcumulado),
                            e.Imagen,
                            m.marca
                FROM equipos e 
                INNER JOIN marcas m ON e.id_Marca = m.id_marca
                GROUP BY Serial"));

        return $selectMachine;
    }*/

    protected function getSupplieXMachine($serial){
        $selectsupplie = DB::select( DB::raw("
                SELECT      i.Id_insumo,
                            i.insumo,
                            i.cantidad
                FROM equipos e
                INNER JOIN equiposxinsumo ei ON ei.id_equipo = e.Serial
                INNER JOIN insumos i ON i.Id_insumo = ei.Id_insumo
                WHERE ei.id_equipo = '".$serial."'"));  

        return $selectsupplie;
    }

    protected function getAllMachine(){
    	$selectAllM = DB::select(DB::raw("
    			SELECT  e.id_Equipos,
                        e.Equipo,
                        e.Serial,
                        e.Imagen,
                        e.Estado
                FROM equipos e 
                WHERE e.Estado = 'Disponible'
                ORDER BY e.Equipo desc"));

    	return $selectAllM;
    }

    protected function getReportMaint($id){
        $selectAllM = DB::select(DB::raw("
                SELECT  *
                FROM mantenimientos
                WHERE serial_equipo = '".$id."'
                ORDER BY fecha desc"));

        return $selectAllM;
    }

    protected function MachineMaintenance(){
        $getMachineMaint = DB::select(DB::raw("
                    SELECT 
                    e.id_Equipos,
                    e.Equipo,
                    e.Serial,
                    e.TiempoMantenimiento,
                    e.TiempoUsoActual 
                    FROM equipos e
                    WHERE e.TiempoMantenimiento - e.TiempoUsoActual <= 5"));
        
        return $getMachineMaint;
    }

    protected function insertMachine($request){	
    	$insertMachine = DB::insert(DB::raw("
    			INSERT INTO equipos(Equipo,Serial,Modelo,id_Marca,Estado,TiempoMantenimiento,TiempoUsoActual,TiempoAcumulado)
	    		VALUES 		('".$request["machine"]."',
	    					'".$request["serial"]."',
	    					'".$request["model"]."',
	    					'".$request["id_mark"]."',
	    					'".$request["state"]."',
	    					".$request["timeMaintenance"].",
	    					".$request["timeUseCurrent"]."),
                            ".$request["timeUseCurrent"]."),;
    		"));

    	return $insertMachine;
    }

    protected function updateImage($img,$serial){
        $updateMachine = DB::update(DB::raw("
                UPDATE  equipos
                SET     Imagen              =   '".$img."'
                WHERE   Serial              =   '".$serial."'
            "));

        return $updateMachine;
    }

    protected function updateMachine($request,$serial){

        $selectTActual = DB::select(DB::raw("
                SELECT  TiempoUsoActual
                FROM equipos
                WHERE Serial = '".$serial."'"));

        $acumulate = floatval($request["timeUseCurrent"]) - floatval($selectTActual[0]->TiempoUsoActual);
        
        if($acumulate<0)
            $acumulate = 0;

    	$updateMachine = DB::update(DB::raw("
    			UPDATE 	equipos
    			SET 	Equipo 				=  	'".$request["machine"]."',
    					Modelo 				=  	'".$request["model"]."',
    					id_Marca 			=  	'".$request["id_mark"]."',
    					Estado				=	'".$request["state"]."',
			    		TiempoMantenimiento	=	".$request["timeMaintenance"].",
			    		TiempoUsoActual		=  	".$request["timeUseCurrent"].",
                        TiempoAcumulado     =   ".$acumulate." + TiempoAcumulado
			    WHERE 	Serial 				=	'".$serial."'
    		"));

        if(floatval($request["timeUseCurrent"])>=floatval($request["timeMaintenance"])){
            $updateState = DB::update(DB::raw("
                UPDATE  equipos
                SET     Estado              =   'Inhabilitado'
                WHERE   Serial              =   '".$serial."'
            "));            
        }

    	return $updateMachine;
    }

    protected function updateMachineState($request,$serial){

        if($request["state"] == "Disponible"){
            $machine = Equipos::getMachine($serial);
            Equipos::insertMantenimi($machine);
        }

        $updateMachine = DB::update(DB::raw("
                UPDATE  equipos
                SET     Estado              =   '".$request["state"]."',
                        TiempoUsoActual     =   ".$request["timeUseCurrent"]."
                WHERE   Serial              =   '".$serial."'
            "));

        return $updateMachine;
    }

    protected function insertMantenimi($request){
        $mydate=getdate(date("U"));
        $dateCurrent = "$mydate[year]/$mydate[mon]/$mydate[mday] $mydate[hours]:$mydate[minutes]:$mydate[seconds]";
        $insertManteni = DB::insert(DB::raw("
                INSERT INTO mantenimientos(serial_equipo,equipo,fecha,usototal,usoactual)
                VALUES      ('".$request[0]->Serial."',
                            '".$request[0]->Equipo."',
                            '".$dateCurrent."',
                            '".$request[0]->TiempoUsoActual."',
                            '".$request[0]->TiempoAcumulado."');
            "));       
    }

    protected function insertMxS($idEquipo,$supplies){
        $arraybad = array();
        $countSupplies = count($supplies);
        for ($i = 0; $i < $countSupplies; $i++) { 
            $insertMxS = DB::insert(DB::raw("
                    INSERT INTO equiposxinsumo(id_equipo,Id_insumo) 
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
                WHERE id_equipo = '".$idEquipo."' AND Id_insumo = '".$idsupplies."'
            "));

        return $deleteMxS;
    }   
	
	protected function deleteMachine($id){

        $deleteMxS = DB::delete(DB::raw("
            DELETE FROM equiposxinsumo
            WHERE id_equipo = '".$id."'
        "));


        $deleteMach = DB::delete(DB::raw("
                DELETE FROM equipos
                WHERE Serial = '".$id."'
            "));

        return $deleteMach;		
	}
}
