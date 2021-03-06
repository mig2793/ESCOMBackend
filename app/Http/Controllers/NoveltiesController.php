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
                    "response" => $getall,
                    "status" => 1
                ]);
        }else{
            return response()->json([
                "response" => "No se encontraron datos",
                "status" => 0
            ]);
        }        
    }

    public function getNoveltiesDates(Request $request){

        $getall = Novelties::getNovelDates($request);

        if($getall){
            return response()->json([
                    "response" => $getall,
                    "status" => 1
                ]);
        }else{
            return response()->json([
                "response" => "No se encontraron datos",
                "status" => 0
            ]);
        }         
    }

    public function storeImage(Request $request){

        $file = Input::file('file');
        if ($file!=null) {

            $ext = $file->getClientOriginalExtension();
            $image_name = $file->getClientOriginalName();
        }

        if (!file_exists(public_path().'/uploads/images')) {
            mkdir(public_path().'/uploads/images',0755, true);
        }
        
        Image::make(Input::file('file'))->save(public_path().'/uploads/images/'.$image_name);

        $updateImage = Novelties::updateImage($image_name,$request["serial"]);

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
                    "response" => "Guardado satisfactoriamente",
                    "state" => 1
                ]);            
        }else{
            return response()->json([
                    "response" => "Error al guardar",
                    "state" => 0
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
                    "response" => "Datos actualizados satisfactoriamente",
                    "state" => 1
                ]);            
        }else{
            return response()->json([
                    "response" => "Error al actualizar",
                    "state" => 0
                ]);               
        }
    }
}
