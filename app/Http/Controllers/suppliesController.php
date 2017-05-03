<?php

namespace escom\Http\Controllers;

use Illuminate\Http\Request;
use escom\supplies;

class suppliesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $getall = supplies::getAllsupplies();

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
        $validatesupplie = supplies::getSupplie($request["supplie"]);

        if(!$validatesupplie){
            $validateInsert = supplies::insertSupplie($request);
            
            if($validateInsert > 0){
                return response()->json([
                        "response" => "Guardado satisfactoriamente"
                    ]);            
            }else{
                return response()->json([
                        "response" => "Error al guardar"
                    ]);               
            }
        }else{
            return response()->json([
                    "response" => "El insumo ya se encuentra registrado en la base de datos"
                ]);        
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($supplie)
    {
        $getSupplie =  supplies::getSupplie($supplie);

        if(isset($getSupplie[0]->Id_insumo)){
            return response()->json([
                "response" => $getSupplie
            ]);
        }else
            return "El insumo no se encuentra en la base de datos";  
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
    public function update(Request $request, $supplie)
    {
        $validateUpdate = supplies::updateSuplie($request,$supplie);

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
    public function destroy($supplie)
    {
        $deleteitemSupplie = supplies::deleteSupplie($supplie);

        if($deleteitemSupplie>0){
            return response()->json([
                    "response" => "Eliminado satisfactoriamente"
                ]);
        }else{
            return response()->json([
                    "response" => "Eror al eliminar el insumo"
                ]);
        }
    }
}
