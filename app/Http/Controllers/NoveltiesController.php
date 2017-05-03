<?php

namespace escom\Http\Controllers;

use Illuminate\Http\Request;
use escom\Novelties;

class NoveltiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $getall = Novelties::getAllNovelties();

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateInsert = Novelties::insertNovelty($request);
        
        if($validateInsert > 0){
            return response()->json([
                    "response" => "Guardado satisfactoriamente"
                ]);            
        }else{
            return response()->json([
                    "response" => "Error al guardar"
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
        $getNovelt =  Novelties::getNovelties($id);

        if(isset($getNovelt[0]->Id_novedad)){
            return response()->json([
                "response" => $getNovelt
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
        $validateUpdate = Novelties::updateNovelty($request,$id);

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
}
