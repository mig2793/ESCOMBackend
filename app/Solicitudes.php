<?php

namespace escom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Solicitudes extends Model
{
    public $timestamps = false;

    protected $table = 'solicitudes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Id_solicitud','Id_maquina','id_persona','Fecha','Hora_inicio','Hora_final','Detalles_Actividad','id_equipos','id_insumo','Id_estadomaquina','Id_solicitud','estado_solicitud','img_entregado','img_devuelto','documento','nombre','apellido',
    ];

    protected function getrequest($idsolicitud){
    	$selectRequest = DB::select( DB::raw("
                SELECT      s.Id_solicitud,
                            s.Id_maquina,
                            e.Equipo,
                            s.id_persona,
                            u.nombre,
                            u.apellido,
                            s.Fecha,
                            s.Hora_inicio,
                            s.Hora_final,
                            s.Detalles_Actividad
                FROM solicitudes s 
                INNER JOIN usuarios u ON u.documento = s.id_persona
                INNER JOIN equipos e ON e.Serial = s.Id_maquina
                WHERE s.Id_solicitud = '".$idsolicitud."'"));

        $selectsupplies = DB::select( DB::raw("
                SELECT  sei.id_insumo,
                		i.insumo
                FROM solicitudxequipoxinsumo sei
                INNER JOIN insumos i ON i.Id_insumo = sei.id_insumo 
                WHERE sei.Id_solicitud = '".$idsolicitud."'"));

        if($selectRequest){
            $selectRequest["insumos"] = $selectsupplies;
        }

    	return $selectRequest;
    }

    protected function getRequestDate($date){
        $selectRequest = DB::select( DB::raw("
                SELECT  count(*)
                FROM solicitudes s 
                WHERE s.Fecha = '".$date[0]."' 
                AND s.Hora_inicio >= '".$date[1]."' 
                AND s.Hora_final <= '".$date[2]."'")); 

        return $selectRequest;       
    }

    protected function getAllrequest(){
    	$selectAllM = DB::select(DB::raw("
    			SELECT  e.id_Equipos,
                        e.Equipo,
                        e.Serial,
                        e.Imagen,
                        e.Estado
                FROM equipos e"));

    	return $selectAllM;
    }

    protected function insertrequest($request){  	
    	$insertsolicitud = DB::insert(DB::raw("
    			INSERT INTO solicitudes(Id_maquina,
						    			id_persona,
						    			Fecha,
						    			Hora_inicio,
						    			Hora_final,
						    			Detalles_Actividad)
	    		VALUES 		('".$request["idequipo"]."',
	    					'".$request["idpersona"]."',
	    					'".$request["fecha"]."',
	    					'".$request["horaInicio"]."',
	    					'".$request["horaFin"]."',
	    					'".$request["detalleActividad"]."');
    		"));

    	return $insertsolicitud;
    }

    protected function insertMxS($idSolicitud,$idEquipo,$supplies){

        $arraybad = array();
        $countSupplies = count($supplies);
        for ($i = 0; $i < $countSupplies; $i++) { 
    		$insertMxS = DB::insert(DB::raw("
    			INSERT INTO solicitudxequipoxinsumo(Id_solicitud,id_equipos,id_insumo)
	    		VALUES 		('".$idSolicitud."',
	    					'".$idEquipo."',
	    					'".$supplies."');
    		"));
            if($insertMxS <= 0)
                array_push($arraybad,$supplies[$i]);          
        }

        return $arraybad;
    }

    protected function insertstate($idSolicitud,$state){
		$insertstate = DB::insert(DB::raw("
			INSERT INTO estadomaquina(Id_solicitud,estado_solicitud)
    		VALUES 		('".$idSolicitud."',
    					'".$state."');
		"));

        return $insertstate;
    }

    protected function updaterequest($request,$solicitud){
    	$updaterquest = DB::update(DB::raw("
    			UPDATE 	solicitudes
    			SET 	Id_maquina			=  	'".$request["machine"]."',
    					Fecha 				=  	'".$request["date"]."',
    					Hora_inicio 		=  	'".$request["houri"]."',
    					Hora_final			=	'".$request["hourf"]."',
			    		Detalles_Actividad	=	".$request["datailsAct"]."
			    WHERE 	Serial 				=	'".$solicitud."'
    		"));

    	return $updaterquest;
    }

    protected function updatestate($request,$state){
		$insertstate = DB::insert(DB::raw("
			UPDATE 	estadomaquina
			SET 	estado_solicitud = '".$state."',
                    img_entregado = '".$request["img_entregada"]."',
                    img_devuelto = '".$request["img_devuelta"]."'
    		WHERE 	Id_solicitud = '".$request["idSolicitud"]."'
		"));

        return $insertstate;
    }


    protected function deleteresquestMxS($idEquipo,$idsupplies,$solicitud){
        $deleteMxS = DB::delete(DB::raw("
                DELETE FROM solicitudxequipoxinsumo
                WHERE Id_solicitud = '".$solicitud."' AND id_equipos = '".$idEquipo."' AND id_insumo = '".$idsupplies."'
            "));

        return $deleteMxS;
    }   

}
