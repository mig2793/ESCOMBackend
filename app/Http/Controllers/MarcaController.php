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
                        "response" => "Guardado satisfactoriamente",
                        "status" => 1
                    ]);            
            }else{
                return response()->json([
                        "response" => "Error al guardar",
                        "status" => 0
                    ]);               
            }
        }else{
            return response()->json([
                    "response" => "La marca ya se encuentra registrada",
                    "status" => 0
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
                "response" => $getMark,
                "state" => 1
            ]);
        }else
            return response()->json([
                "response" => "Esta marca no se encuentra registrada",
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
