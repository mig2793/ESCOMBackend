<?php

namespace escom\Http\Controllers;

use Illuminate\Http\Request;
use escom\Marcas;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $getall = Marcas::getAllmarks();

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
        $validateMark = Marcas::getMark($request["mark"]);

        if(!$validateMark){
            $validateInsert = Marcas::insertmark($request);
            
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
                    "response" => "La marca ya se encuentra registrado en la base de datos"
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
        $getMark =  Marcas::getMark($id);

        if(isset($getMark[0]->id_marca)){
            return response()->json([
                "response" => $getMark
            ]);
        }else
            return "La marca no se encuentra registrada en la base de datos"; 
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
        $validateUpdate = Marcas::updateMark($request,$id);

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
        $deleteitemMark = Marcas::deleteMark($id);

        if($deleteitemMark>0){
            return response()->json([
                    "response" => "Eliminado satisfactoriamente"
                ]);
        }else{
            return response()->json([
                    "response" => "Eror al eliminar la marca"
                ]);
        }
    }
}
