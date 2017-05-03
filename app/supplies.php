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
                            i.insumo,
                            i.cantidad
                FROM insumos i
                WHERE i.insumo LIKE '%".$supplie."%'"));  

    	return $selectsupplie;
    }

    protected function getAllsupplies(){
    	$selectAllS = DB::select(DB::raw("
                SELECT      i.Id_insumo,
                            i.insumo,
                            i.cantidad
                FROM insumos i"));

    	return $selectAllS;
    }

    protected function insertSupplie($request){
    	$insertsupplie = DB::insert(DB::raw("
    			INSERT INTO insumos(insumo,cantidad)
	    		VALUES 		('".$request["supplie"]."',
	    					'".$request["quantity"]."');
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

    protected function deleteSupplie($supplie){
    	$deleteSup = DB::delete(DB::raw("
    			DELETE FROM insumos
    			WHERE Id_insumo = ".$supplie.""));

    	return $deleteSup;
    }
}
