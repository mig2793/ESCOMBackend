<?php

namespace escom\Http\Controllers;

use Illuminate\Http\Request;
use escom\Solicitudes;

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
        $dateCurrent = "$mydate[mon]/$mydate[mday]/$mydate[year]";
        if($request["fecha"]<$dateCurrent){
            $validadate = Solicitudes::getRequestDate($request);
            if($validadate[0]->count <= 0){
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
                return response()->json([
                        "response" => "Ya se encuentra una solicitud para usar la máquina para esta hora. Por favor elige otra."
                    ]);        
            }
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
