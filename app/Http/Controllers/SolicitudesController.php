<?php

namespace escom\Http\Controllers;

use Illuminate\Http\Request;
use escom\Solicitudes;
use Illuminate\Support\Facades\Input;
use Image;

class SolicitudesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $getall = Solicitudes::getrequestAlladmin();

        if($getall){
            return response()->json([
                    "response" => $getall
                ]);
        }else{
            return response()->json([
                "response" => "No se encontraron datos"
            ]);
        }
    }

    public function getrequestAll($id)
    {
        $getall = Solicitudes::getrequestAll($id);

        if($getall){
            return response()->json([
                    "response" => $getall
                ]);
        }else{
            return response()->json([
                "response" => "No se encontraron datos"
            ]);
        }
    }

    public function getrequestDateAll(Request $request){
        $getall = Solicitudes::getrequestDateAll($request);

        if($getall){
            return response()->json([
                    "response" => $getall
                ]);
        }else{
            return response()->json([
                "response" => "No se encontraron datos"
            ]);
        }        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $mydate=getdate(date("U"));
        $date2 = "$mydate[year]/$mydate[mon]/$mydate[mday]";
        $date1 = $request["fecha"];
        $daterequest = date("Y-m-d", strtotime($date1));    
        $dateCurrent = date("Y-m-d", strtotime($date2));
        $datediff = abs(strtotime($daterequest) - strtotime($dateCurrent));  
        $numberDays =  $datediff/86400;
        $numberDays = intval($numberDays);
        $timeinit1 = strtotime( "8:00" );
        $timeinit2 = strtotime( "12:00" );
        $timefin1 = strtotime( "14:00" );
        $timefin2 = strtotime( "18:00" );
        $timegetinit = strtotime($request["horaInicio"]);
        $timegetfin = strtotime($request["horaFin"]);
        $hourstxt = "Horas ocupadas: ";
        if($daterequest>=$dateCurrent){
            if($numberDays > 0 && $numberDays < 6){
                if((($timegetinit >= $timeinit1 && $timegetinit <= $timeinit2) || ($timegetinit >= $timefin1 && $timegetinit <= $timefin2))
                    && (($timegetfin >= $timeinit1 && $timegetfin <= $timeinit2) || ($timegetfin >= $timefin1 && $timegetfin <= $timefin2)))
                {
                    $validadate = Solicitudes::getRequestDate($request);
                    if(!$validadate){
                        $validateInsert = Solicitudes::insertrequest($request);
                        if($validateInsert > 0){
                            $getLastId = Solicitudes::getLastId($request);
                            $request["Id_solicitud"] = $getLastId[0]->Id_solicitud;
                            $InsertState = Solicitudes::insertStateMachi($request);
                            $countSupplies = count($request["supplies"]);
                            if($countSupplies>0){
                                $ValidateInsertMxS = Solicitudes::insertMxS($getLastId[0]->Id_solicitud,$request["idequipo"],$request["supplies"]);
                                if(!$ValidateInsertMxS){
                                    return response()->json([
                                            "response" => "Guardado satisfactoriamente"
                                        ]);                      
                                }else{
                                    return response()->json([
                                            "response" => "Hubo un error al guardar la información"
                                        ]);                      
                                }   
                            }else{
                                return response()->json([
                                        "response" => "Guardado satisfactoriamente"
                                    ]);                        
                            }
                  
                        }else{
                            return response()->json([
                                    "response" => "Error al guardar"
                                ]);               
                        }
                    }else{
                        for($i=0;$i<count($validadate);$i++){
                            $hourstxt .= $validadate[$i]->Hora_inicio."-".$validadate[$i]->Hora_final.", ";
                        }
                        return response()->json([
                                "response" => $hourstxt." Ya se encuentra una solicitud para usar la máquina para esta hora. Por favor elige otra."
                            ]);        
                    }                    
                }else{
                    return response()->json([
                            "response" => "Debes pedir la máquina entre las 8:00 y 12:00 o 14:00 y 18:00"
                        ]);                       
                }

            }else{
                return response()->json([
                    "response" => "Debes solicitar la máquina con 24 horas de antelación y maximo 5 días de le fecha actual."
                ]);                   
            }                
        }else{
            return response()->json([
                    "response" => "La fecha y hora no pueden ser menor a la actual."
                ]);              
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $getsolicitud =  Solicitudes::getrequest($id);

        if(isset($getsolicitud[0]->Id_solicitud)){
            return response()->json([
                "response" => $getsolicitud
            ]);
        }else
            return response()->json([
                "response" => "No se encontró la solicitud",
                "state" => 0
            ]);
    }

    public function storeImage(Request $request){

        $validateHour = Solicitudes::ValidateHour($request["id_solicitud"]);

        if($validateHour[0]->count > 0){
            $file = Input::file('file');
            if ($file!=null) {

                $ext = $file->getClientOriginalExtension();
                $image_name = $file->getClientOriginalName();
            }

            if (!file_exists(public_path().'/uploads/images/estados/')) {
                mkdir(public_path().'/uploads/images/estados/',0755, true);
            }

            if($request["accion"] == 1){
                $image_name = "ent-".$request["id_solicitud"]."-".$image_name;
            }else{
                $image_name = "dev-".$request["id_solicitud"]."-".$image_name;
            }
            
            
            Image::make(Input::file('file'))->save(public_path().'/uploads/images/'.$image_name);

            $updateImage = Solicitudes::updateImage($image_name,$request);

            if($updateImage){
                return response()->json([
                    "response" => "Guardado satisfactoriamente",
                    "state" => 0
                ]);              
            }else{
                return response()->json([
                    "response" => "Error al insertar la imagen",
                    "state" => 0
                ]);              
            }            
        }else{
            return response()->json([
                "response" => "Debes cambiar el estado de la solicitud en el rango de horas que se pidió la máquina.",
                "state" => 0
            ]);               
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){

        $validateHour = Solicitudes::ValidateHour($id);

        if($validateHour[0]->count > 0 || ($request["state"] == 2 || $request["state"] == 3 || $request["state"] == 5)){

            $validateUpdateState = Solicitudes::updatestate($request, $id);
            if($validateUpdateState > 0){
                return response()->json([
                        "response" => "Datos actualizados satisfactoriamente",
                        "status"   => "1"
                    ]);            
            }else{
                return response()->json([
                        "response" => "Error al actualizar",
                        "status"   => "0"
                    ]);               
            }
        }else{
            return response()->json([
                "response" => "Debes cambiar el estado de la solicitud en el rango de horas que se pidió la máquina.",
                "state" => 0
            ]);               
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleteReq = Solicitudes::deleteRequest($id);

        if($deleteReq > 0){
            return response()->json([
                    "response" => "Eliminado satisfactoriamente",
                    "state" => 1
                ]);
        }else{
            return response()->json([
                    "response" => "Error al eliminar el elemento",
                    "state" => 0
                ]);            
        }
    }
}
