<?php
namespace escom\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use escom\User;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $getU = User::getRestorePass();

        if($getU){
            return response()->json([
                "response" => $getU,
                "status"     => 1
            ]);
        }else
            return response()->json([
                "response"  => "No hay datos",
                "status"     => 0
            ]);        
    }

    public function getAnswerRespond($id)
    {
        $getU = User::getAnswerRespond($id);

        if($getU){
            return response()->json([
                "response" => $getU,
                "status"     => 1
            ]);
        }else
            return response()->json([
                "response"  => "No existe el usuario con este numero de documento",
                "status"     => 0
            ]);        
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //DB::select( DB::raw("SELECT COUNT(*) FROM usuarios WHERE cedula = ".$request['nit'].""));
    public function store(Request $request)
    {
        $countValidate = User::validateUser($request['nit']);
        if($countValidate > 0){
            return response()->json([
                    "message" => "El usuario ya existe en la base de datos",
                    "state"     => 0
                ]);
        }else{
            $validatesave = User::create([
                'documento' => $request['nit'],
                'nombre' => $request['name'],
                'apellido' => $request['lastname'],
                'contrasena' => Hash::make($request['password']),
                'id_rol' => $request['rol'], 
                'id_rango' => $request['range'],
                'id_testudio' => $request['tipoestudio'],
                'id_pregunta' => $request['id_pregunta'],
                'respuesta' => $request['response'],
                'estado' => $request['estado'],
            ]);


            $validatesave = json_decode($validatesave,true);

            if(strlen($validatesave["documento"])>0){
                $datauser = User::getUser($request['nit']);
                 return response()->json([
                    "message"   => "Usuario creado satisfactoriamente",
                    "data"      => $datauser,
                    "state"     => 1
                ]);     
            }else{
                 return response()->json([
                    "message" => "Error al crear el usuario",
                    "state"     => 0
                ]);     
            }
      
        }

    }

    public function login(Request $request){

        $data = User::login($request["nit"],$request["password"]);
        //$data = json_decode($data,true);
        return $data;
    }

    public function restorePass($password,$id){
        $restPass = User::restorePassword($password,$id);

        if($restPass>0){
            return response()->json([
                'message' => "Cambio de constraseña exitoso"
            ]);
        }else{
            return response()->json([
                'message' => "Error al cambiar la contraseña"
            ]);   
        }
    }

    public function restorePasswordb(Request $request, $id){
        $restPass = User::restorePassword($request["password"],$id);

        if($restPass>0){
            return response()->json([
                'message' => "Cambio de constraseña exitoso",
                'state' => 1
            ]);
        }else{
            return response()->json([
                'message' => "Error al cambiar la contraseña",
                'state' => 0
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
        $getU = User::getUser($id);

        if(isset($getU[0]->documento)){
            return response()->json([
                "response" => $getU
            ]);
        }else
            return response()->json([
                "response"  => "No hay ningun usuario registrado con este numero de documento",
                "status"     => 0
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

        if($request['password'] != ""){
            $changePassword = User::restorePassword($request['password'],$id);
        }

        $updateU = User::updateUser($request, $id);

        if($updateU > 0 || $changePassword > 0){
            return response()->json([
                    "response" => "Datos actualizados satisfactoriamente"
                ]);            
        }else{
            return response()->json([
                    "response" => "Error al actualizar"
                ]);               
        }

    }

    protected function updateStateUser(Request $request){
        $validateDelete = User::updateStateUser($request["id"]);

        if($validateDelete > 0){
            return response()->json([
                    "response" => "Datos actualizados satisfactoriamente",
                    "state" => 1
                ]);
        }else{
            return response()->json([
                    "response" => "Error al actualizar el elemento",
                    "state" => 0
                ]);            
        }        
    }

    protected function updateStateUserReject(Request $request){
        $validateDelete = User::rejectUser($request["id"]);

        if($validateDelete > 0){
            return response()->json([
                    "response" => "Datos actualizados satisfactoriamente",
                    "state" => 1
                ]);
        }else{
            return response()->json([
                    "response" => "Error al actualizar el elemento",
                    "state" => 0
                ]);            
        }        
    }

    protected function getUsersApp(){
        $getU = User::getUsersApp();

        if(isset($getU[0]->documento)){
            return response()->json([
                "response" => $getU
            ]);
        }else
            return response()->json([
                "response"  => "No hay datos",
                "status"     => 0
            ]);         
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $validateDelete = User::deleteUser($id);

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
