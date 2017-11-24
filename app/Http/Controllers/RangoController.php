<?php

namespace escom\Http\Controllers;

use Illuminate\Http\Request;
use escom\Rango;

class RangoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function index()
    {
        $getall = Rango::getAllRanges();

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
        $validateRange = Rango::getRange($request["range"]);

        if(!$validateRange){
            $validateInsert = Rango::insertRange($request);
            
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
                    "response" => "El registro ya se encuentra en la base de datos"
                ]);        
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($range)
    {
        $getRange =  Rango::getRange($range);

        if(isset($getRange[0]->id_rango)){
            return response()->json([
                "response" => $getRange
            ]);
        }else
            return "El registro no se encuentra en la base de datos";  
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
        $validateUpdate = Rango::updateRange($request,$id);

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
        $deleteitemRange = Rango::deleteRange($id);

        if($deleteitemRange>0){
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
