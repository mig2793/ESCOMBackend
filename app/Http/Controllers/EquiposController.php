<?php

namespace escom\Http\Controllers;

use Illuminate\Http\Request;
use escom\Equipos;

class EquiposController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $getall = Equipos::getAllMachine();

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

            $filename = $request["image"];
            return $filename;

        /*$validateMachine = Equipos::getMachine($request["serial"]);

        if(!$validateMachine){
            $validateInsert = Equipos::insertMachine($request);
            
            if($validateInsert > 0){
                $ValidateInsertMxS = Equipos::insertMxS($request["serial"],$request["supplies"]);
                if(!$ValidateInsertMxS){
                    return response()->json([
                            "response" => "Guardado satisfacoriamente",
                            "state" => 1
                        ]);                      
                }else{
                    return response()->json([
                            "response" => "Hubo un error al guardar la información",
                            "state" => 0
                        ]);                      
                }         
            }else{
                return response()->json([
                        "response" => "Error al guardar",
                        "state" => 0
                    ]);               
            }
        }else{
            return response()->json([
                    "response" => "El equipo ya se encuentra registrado en la base de datos",
                    "state" => 0
                ]);        
        }*/
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
                "response" => $getMachine,
                "status" => 1
            ]);
        }else
            return response()->json([
                "response" => "La máquina o equipo no se encuentra en la base de datos",
                "status" => 0
            ]);
    }

    public function MachineMainte()
    {
        $getMachineMainte =  Equipos::MachineMaintenance();

        if($getMachineMainte){
            return response()->json([
                "response" => $getMachineMainte,
                "status" => 1
            ]);
        }else
            return response()->json([
                "response" => "No hay datos",
                "status" => 0
            ]);
    }

    public function getSupplieMachine($id)
    {
        $gesupplieXMachi =  Equipos::getSupplieXMachine($id);

        if(isset($gesupplieXMachi[0]->Id_insumo)){
            return response()->json([
                "response" => $gesupplieXMachi
            ]);
        }else{
            return response()->json([
                "response" => "No hay insumos disponible para esta máquina",
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
    public function update(Request $request, $id)
    {
        $validateUpdate = Equipos::updateMachine($request,$id);

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

    public function updateState(Request $request, $id)
    {
        $validateUpdate = Equipos::updateMachineState($request,$id);

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

    public function insertMxSCon(Request $request)
    {
        $validateInsert = Equipos::insertMxS($request["serial"],$request["supplies"]);

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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $elemetsDelete = preg_split('~,~',$id);
        $validateDelete = Equipos::deleteMxS($elemetsDelete[0],$elemetsDelete[1]);

        if($validateDelete > 0){
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
