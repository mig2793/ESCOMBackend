<?php

namespace escom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use escom\Solicitudes;
use escom\supplies;
use Carbon\Carbon;

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
        $date = Carbon::today();
        $date =  preg_split('~ ~',$date);
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
                            s.Detalles_Actividad,
                            em.Id_solicitud,
                            em.estado_solicitud,
                            em.img_entregado,
                            em.img_devuelto,
                            em.comentario_ent,
                            em.comentario_dev,
                            em.usuario_entrega,
                            em.usuario_recibe,
                            datediff(s.Fecha, '".$date[0]."') as days
                FROM solicitudes s 
                INNER JOIN usuarios u ON u.documento = s.id_persona
                INNER JOIN equipos e ON e.Serial = s.Id_maquina
                INNER JOIN estadomaquina em ON em.Id_solicitud = s.Id_solicitud
                WHERE s.Id_solicitud = '".$idsolicitud."'
                ORDER BY s.Fecha asc"));

        $selectsupplies = DB::select( DB::raw("
                SELECT  sei.Id_solicitud,
                        sei.id_insumo,
                		i.insumo
                FROM solicitudxequipoxinsumo sei
                INNER JOIN insumos i ON i.Id_insumo = sei.id_insumo 
                WHERE sei.Id_solicitud = '".$idsolicitud."'"));

        if($selectRequest){
            $selectRequest["insumos"] = $selectsupplies;
        }

    	return $selectRequest;
    }

    protected function getrequestDateAll($date){
        $selectRequest = DB::select( DB::raw("
                SELECT      s.Id_solicitud,
                            e.Equipo,
                            u.nombre,
                            u.apellido,
                            s.Fecha,
                            s.Hora_inicio,
                            s.Hora_final,
                            s.Detalles_Actividad,
                            em.estado_solicitud,
                            em.img_entregado,
                            em.img_devuelto,
                            em.comentario_ent,
                            em.comentario_dev,
                            em.usuario_entrega,
                            em.usuario_recibe
                FROM solicitudes s 
                INNER JOIN usuarios u ON u.documento = s.id_persona
                INNER JOIN equipos e ON e.Serial = s.Id_maquina
                INNER JOIN estadomaquina em ON em.Id_solicitud = s.Id_solicitud
                WHERE  s.Fecha >= '".$date["datei"]."' and s.Fecha <= '".$date["datef"]."'
                ORDER BY s.Fecha desc"));

        $selectsupplies = DB::select( DB::raw("
                SELECT  sei.Id_solicitud,
                        sei.id_insumo,
                        i.insumo
                FROM solicitudxequipoxinsumo sei
                INNER JOIN insumos i ON i.Id_insumo = sei.id_insumo
                INNER JOIN solicitudes s ON s.Id_solicitud =  sei.Id_solicitud"));

        if($selectRequest){
            for($i=0;$i<count($selectRequest);$i++){
                $selectRequest[$i]->insumos = [];
                for($j=0;$j<count($selectsupplies);$j++){
                    if($selectRequest[$i]->Id_solicitud == $selectsupplies[$j]->Id_solicitud){
                        array_push($selectRequest[$i]->insumos,$selectsupplies[$j]);
                    }
                }
            }   
        }
        return $selectRequest;  
    }

    protected function getrequestAll($idPersona){
        $date = Carbon::today();
        $date =  preg_split('~ ~',$date);
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
                            s.Detalles_Actividad,
                            em.Id_estadomaquina,
                            em.estado_solicitud,
                            em.img_entregado,
                            em.img_devuelto,
                            em.usuario_entrega,
                            em.usuario_recibe,
                            '' as insumos,
                            datediff(s.Fecha, '".$date[0]."') as days
                FROM solicitudes s 
                INNER JOIN usuarios u ON u.documento = s.id_persona
                INNER JOIN equipos e ON e.Serial = s.Id_maquina
                LEFT JOIN estadomaquina em ON em.Id_solicitud = s.Id_solicitud
                WHERE s.id_persona = '".$idPersona."'
                ORDER BY s.Fecha asc"));

        $selectsupplies = DB::select( DB::raw("
                SELECT  sei.Id_solicitud,
                        sei.id_insumo,
                        i.insumo
                FROM solicitudxequipoxinsumo sei
                INNER JOIN insumos i ON i.Id_insumo = sei.id_insumo
                INNER JOIN solicitudes s ON s.Id_solicitud =  sei.Id_solicitud
                WHERE s.id_persona = '".$idPersona."'"));

        if($selectRequest){
            for($i=0;$i<count($selectRequest);$i++){
                $selectRequest[$i]->insumos = [];
                for($j=0;$j<count($selectsupplies);$j++){
                    if($selectRequest[$i]->Id_solicitud == $selectsupplies[$j]->Id_solicitud){
                        array_push($selectRequest[$i]->insumos,$selectsupplies[$j]);
                    }
                }
            }   
        }
        return $selectRequest;
    }

    protected function getrequestAlladmin(){
        $date = Carbon::today();
        $date =  preg_split('~ ~',$date);
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
                            s.Detalles_Actividad,
                            em.Id_estadomaquina,
                            em.estado_solicitud,
                            em.img_entregado,
                            em.img_devuelto,
                            em.usuario_entrega,
                            em.usuario_recibe,
                            '' as insumos,
                            datediff(s.Fecha, '".$date[0]."') as days
                FROM solicitudes s 
                INNER JOIN usuarios u ON u.documento = s.id_persona
                INNER JOIN equipos e ON e.Serial = s.Id_maquina
                LEFT JOIN estadomaquina em ON em.Id_solicitud = s.Id_solicitud
                ORDER BY s.Fecha asc"));

        $selectsupplies = DB::select( DB::raw("
                SELECT  sei.Id_solicitud,
                        sei.id_insumo,
                        i.insumo
                FROM solicitudxequipoxinsumo sei
                INNER JOIN insumos i ON i.Id_insumo = sei.id_insumo
                INNER JOIN solicitudes s ON s.Id_solicitud =  sei.Id_solicitud"));

        if($selectRequest){
            for($i=0;$i<count($selectRequest);$i++){
                $selectRequest[$i]->insumos = [];
                for($j=0;$j<count($selectsupplies);$j++){
                    if($selectRequest[$i]->Id_solicitud == $selectsupplies[$j]->Id_solicitud){
                        array_push($selectRequest[$i]->insumos,$selectsupplies[$j]);
                    }
                }
            }   
        }
        return $selectRequest;
    }

    protected function getLastId($request){

        $getlastId = DB::select(DB::raw("
            SELECT s.Id_solicitud
            FROM solicitudes s 
            WHERE s.Id_maquina = '".$request["idequipo"]."'
            AND s.id_persona = '".$request["idpersona"]."'
            AND s.Fecha = '".$request["fecha"]."'
            ORDER BY s.Id_solicitud desc
            LIMIT 1"));

        return $getlastId;
    }

    protected function getRequestDate($date){
        $selectRequest = DB::select( DB::raw("
                SELECT s.Hora_inicio,s.Hora_final,
                (select count(*) from solicitudes) count
                FROM solicitudes s 
                WHERE s.Fecha = '".$date["fecha"]."'
                AND s.Id_maquina = '".$date["idequipo"]."'
                AND (s.Hora_inicio BETWEEN '".$date["horaInicio"]."' AND '".$date["horaFin"]."'
                OR s.Hora_final BETWEEN '".$date["horaInicio"]."' AND '".$date["horaFin"]."')")); 

        return $selectRequest;       
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

    protected function insertStateMachi($request){ 
        $insertsolistate = DB::insert(DB::raw("
                INSERT INTO estadomaquina
                            (Id_solicitud,
                            estado_solicitud,
                            img_entregado,
                            img_devuelto)
                VALUES      ('".$request["Id_solicitud"]."',
                            '".$request["estado_solicitud"]."',
                            '".$request["img_entregado"]."',
                            '".$request["img_devuelto"]."');
            "));

        return $insertsolistate;
    }

    protected function insertMxS($idSolicitud,$idEquipo,$supplies){

        $arraybad = array();
        $countSupplies = count($supplies);
        for ($i = 0; $i < $countSupplies; $i++) { 
    		$insertMxS = DB::insert(DB::raw("
    			INSERT INTO solicitudxequipoxinsumo(Id_solicitud,id_equipos,id_insumo)
	    		VALUES 		('".$idSolicitud."',
	    					'".$idEquipo."',
	    					'".$supplies[$i]."');
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

    protected function updatestate($request,$idSolicitud){

		$insertstate = DB::insert(DB::raw("
			UPDATE 	estadomaquina
			SET 	estado_solicitud = '".$request["state"]."',
                    comentario_ent = '".$request["coment_ent"]."',
                    comentario_dev = '".$request["coment_dev"]."',
                    usuario_entrega = '".$request["usuario_entrega"]."',
                    usuario_recibe = '".$request["usuario_recibe"]."'
    		WHERE 	Id_solicitud = '".$idSolicitud."'
		"));
        
        $getHourDifference = Solicitudes::getHourSol($idSolicitud);

        if($request["state"] == "4"){    

            $updateHour = DB::insert(DB::raw("
                UPDATE  equipos
                SET     TiempoAcumulado = ".floatval($getHourDifference[0]->horasnum)." + TiempoAcumulado,
                        TiempoUsoActual = ".floatval($getHourDifference[0]->horasnum)." + TiempoUsoActual
                WHERE   Serial = '".$getHourDifference[0]->Id_maquina."'
            "));

            $getInsumos = Solicitudes::getrequest($idSolicitud);

            if($getInsumos){
                for($i=0;$i<count($getInsumos["insumos"]);$i++){
                    $UpdateCantInsum = supplies::updateQuantitySupp($getInsumos["insumos"][$i]); 
                }             
            }

        }

        $selectTActual = DB::select(DB::raw("
                SELECT  TiempoUsoActual,TiempoMantenimiento
                FROM equipos
                WHERE Serial = '".$getHourDifference[0]->Id_maquina."'"));

        if(floatval($selectTActual[0]->TiempoUsoActual)>=floatval($selectTActual[0]->TiempoMantenimiento)){
            $updateState = DB::update(DB::raw("
                UPDATE  equipos
                SET     Estado              =   'Inhabilitado'
                WHERE   Serial              =   '".$getHourDifference[0]->Id_maquina."'
            "));            
        }

        return $insertstate;
    }

    protected function getHourSol($idSolicitud){
        $selectRequest = DB::select( DB::raw("
                SELECT TIMESTAMPDIFF(MINUTE,CONCAT(s.Fecha,' ',s.Hora_inicio),CONCAT(s.Fecha,' ',s.Hora_final))/60 as horasnum,Id_maquina 
                    FROM solicitudes s
                    WHERE s.Id_solicitud = '".$idSolicitud."'
                "));
        return $selectRequest;
    }


    protected function deleteresquestMxS($idEquipo,$idsupplies,$solicitud){
        $deleteMxS = DB::delete(DB::raw("
                DELETE FROM solicitudxequipoxinsumo
                WHERE Id_solicitud = '".$solicitud."' AND id_equipos = '".$idEquipo."' AND id_insumo = '".$idsupplies."'
            "));

        return $deleteMxS;
    } 

    protected function updateImage($img,$request){

        if($request["accion"] == 1){
            $updateImageS = DB::update(DB::raw("
                UPDATE  estadomaquina
                SET     img_entregado       =   '".$img."'
                WHERE   Id_solicitud        =   '".$request["id_solicitud"]."'
            "));
        }else{
            $updateImageS = DB::update(DB::raw("
                UPDATE  estadomaquina
                SET     img_devuelto       =   '".$img."'
                WHERE   Id_solicitud        =   '".$request["id_solicitud"]."'
            "));           
        }

        return $updateImageS;
    }

    protected function ValidateHour($id){

        $mydate=getdate(date("U"));
		$hour = count("$mydate[hours]")>1 ? "$mydate[hours]" : "0"."$mydate[hours]";
		$minute = count("$mydate[minutes]")>1 ? "$mydate[minutes]" : "0"."$mydate[minutes]";
        $dateCurrent = $hour.":".$minute.":"."$mydate[seconds]";
        $date = "$mydate[year]/$mydate[mon]/$mydate[mday]";
        $validateHour =  DB::select( DB::raw("
                SELECT count(*) as count
                    FROM solicitudes s
                    WHERE s.Id_solicitud = '".$id."'
                    and '".$dateCurrent."' between s.Hora_inicio
                    and s.Hora_final 
                    and s.Fecha = '".$date."' 
                "));
        return $validateHour;       
    }

    protected function deleteRequest($id){
        $deleteReqSxE = DB::delete(DB::raw("
                DELETE FROM solicitudxequipoxinsumo
                WHERE Id_solicitud = '".$id."';
            "));

        $deleteStateReq = DB::delete(DB::raw("
                DELETE FROM estadomaquina
                WHERE Id_solicitud = '".$id."'
            ")); 
        $deleteReq = DB::delete(DB::raw("
                DELETE FROM solicitudes
                WHERE Id_solicitud = '".$id."'
            ")); 

        return $deleteReq;
    }

}
