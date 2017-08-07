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
        $getall = Solicitudes::getrequestAll();

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
                    $countSupplies = count($request["supplies"]);
                    if($countSupplies>0){
                        $getLastId = Solicitudes::getLastId($request);
                        $ValidateInsertMxS = Solicitudes::insertMxS($getLastId[0]->Id_solicitud,$request["idequipo"],$request["supplies"]);
                        $request["Id_solicitud"] = $getLastId[0]->Id_solicitud;
                        $InsertState = Solicitudes::insertStateMachi($request);
                        if(!$ValidateInsertMxS){
                            return response()->json([
                                    "response" => "Guardado satisfacoriamente"
                                ]);                      
                        }else{
                            return response()->json([
                                    "response" => "Hubo un error al guardar la información"
                                ]);                      
                        }   
                    }else{
                        return response()->json([
                                "response" => "Guardado satisfacoriamente"
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
        $getMachine =  Equipos::getMachine($id);

        if(isset($getMachine[0]->Serial)){
            return response()->json([
                "response" => $getMachine
            ]);
        }else
            return "La máquina o equipo no se encuentra en la base de datos";
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
    public function update(Request $request, $id)
    {
        $validateUpdate = Equipos::updateMachine($request,$id);

        if($validateUpdate > 0){
            return response()->json([
                    "response" => "Datos actualizados satisfactoriamente"
                ]);            
        }else{
            return response()->json([
                    "response" => "Error al actualizar"
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
