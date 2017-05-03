<?php

namespace escom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Crypt;

class User extends Model
{

    public $timestamps = false;

    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'documento','nombre','apellido','contrasena','cedula','id_rol','id_rango','id_testudio',
    ];

    protected function validateUser($request){
        $userValidate = DB::table('usuarios')
                        ->select(DB::raw('count(*) as count'))
                        ->where('documento', '=', $request)
                        ->get();

        $countValidate = json_decode($userValidate,true);
        $countValidate = (int) $countValidate[0]["count"];

        return $countValidate;
    }

    protected function login($nit,$password){

        $passwordhash = DB::select( DB::raw("SELECT contrasena FROM usuarios WHERE documento = '".$nit."'"));

        if(isset($passwordhash[0]->contrasena)){

            $passwordDecrypt = Hash::check($password,$passwordhash[0]->contrasena);

            if($passwordDecrypt){
                $login = DB::select( DB::raw("
                        SELECT      u.documento,
                                    u.nombre,
                                    u.apellido,
                                    ro.id_rol,
                                    r.id_rango,
                                    ro.rol,
                                    r.rango,
                                    te.id,
                                    te.tipoEstudio
                        FROM usuarios u 
                        INNER JOIN rango r ON r.id_rango = u.id_rango
                        INNER JOIN rol ro ON ro.id_rol = u.id_rol
                        INNER JOIN tipoestudio te ON te.id = u.id_testudio
                        WHERE documento = '".$nit."'"));

                return $login;
            }else
                return "Contraseña incorrecta";

        }else
            return 'El usuario no se encuentra registrado';
        
    }

    protected function getUser($nit){

        $getU = DB::select( DB::raw("
                SELECT      u.documento,
                            u.nombre,
                            u.apellido,
                            ro.id_rol,
                            r.id_rango,
                            ro.rol,
                            r.rango,
                            te.id,
                            te.tipoEstudio
                FROM usuarios u 
                INNER JOIN rango r ON r.id_rango = u.id_rango
                INNER JOIN rol ro ON ro.id_rol = u.id_rol
                INNER JOIN tipoestudio te ON te.id = u.id_testudio
                WHERE documento = '".$nit."'"));  

        return $getU;     
    }

    protected function updateUser($request,$id){

        $UsertUpd = DB::update( DB::raw("
                    UPDATE  usuarios
                    SET     nombre      =   '".$request['name']."',
                            apellido    =   '".$request['lastname']."',
                            id_rol      =   '".$request['rol']."',
                            id_rango    =   '".$request['range']."',
                            id_testudio =   '".$request['tipoestudio']."'
                    WHERE documento = '".$id."'"));  

        return $UsertUpd;   
    }

    protected restorePassword($password,$id){

        $updatePassword = DB::update(DB::raw(" 
                        UPDATE usuarios
                        SET contrasena = '".Hash::make($password)."'
                        WHERE documento = '".$id."'"));

        return $updatePassword;
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
