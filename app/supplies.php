<?php

namespace escom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class supplies extends Model
{
    public $timestamps = false;

    protected $table = 'insumos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Id_insumo','insumo','cantidad',
    ];

    protected function getSupplie($supplie){
    	$selectsupplie = DB::select( DB::raw("
                SELECT      i.Id_insumo,
                            i.codigo,
                            i.insumo,
                            i.cantidad
                FROM insumos i
                WHERE i.codigo = '".$supplie."'
                ORDER BY i.insumo desc"));  

    	return $selectsupplie;
    }

    protected function getAllsupplies(){
    	$selectAllS = DB::select(DB::raw("
                SELECT      i.Id_insumo,
                            i.codigo,
                            i.insumo,
                            i.cantidad
                FROM insumos i
                ORDER BY i.insumo asc"));

    	return $selectAllS;
    }

    protected function insertSupplie($request){
    	$insertsupplie = DB::insert(DB::raw("
    			INSERT INTO insumos(insumo,cantidad,codigo)
	    		VALUES 		('".$request["supplie"]."',
	    					'".$request["quantity"]."',
                            '".$request["code"]."');
    		"));

    	return $insertsupplie;
    }

    protected function updateSuplie($request,$supplie){
    	$updatesupplie = DB::update(DB::raw("
    			UPDATE 	insumos
    			SET 	insumo				=  	'".$request["supplie"]."',
			    		cantidad			=  	".$request["quantity"]."
			    WHERE 	Id_insumo 			=	'".$supplie."'
    		"));

    	return $updatesupplie;
    }

    protected function updateQuantitySupp($insumo){
        $updatesupplie = DB::update(DB::raw("
                UPDATE  insumos
                SET     cantidad            =   cantidad - 1
                WHERE   Id_insumo           =   '".$insumo->id_insumo."'
            "));

        return $updatesupplie;
    }

    protected function deleteSupplie($supplie){
    	$deleteSup = DB::delete(DB::raw("
    			DELETE FROM insumos
    			WHERE Id_insumo = ".$supplie.""));

    	return $deleteSup;
    }
}
