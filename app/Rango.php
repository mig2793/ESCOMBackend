<?php

namespace escom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Rango extends Model
{
public $timestamps = false;

    protected $table = 'rangos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_rango','rango',
    ];

    protected function getRange($range){
    	$selectrange = DB::select( DB::raw("
                SELECT      r.id_rango,
                            r.rango
                FROM rangos r
                WHERE r.rango LIKE '%".$range."%'"));  

    	return $selectrange;
    }

    protected function getAllRanges(){
    	$selectAllR = DB::select(DB::raw("
                SELECT      r.id_rango,
                            r.rango
                FROM rangos r"));

    	return $selectAllR;
    }

    protected function insertRange($request){
    	$insertrange = DB::insert(DB::raw("
    			INSERT INTO rangos(rango)
	    		VALUES 		('".$request["range"]."');
    		"));

    	return $insertrange;
    }

    protected function updateRange($request,$id){
    	$updaterange = DB::update(DB::raw("
    			UPDATE 	rangos
    			SET 	rango		=  	'".$request["range"]."'
			    WHERE 	id_rango 	=	".$id."
    		"));

    	return $updaterange;
    }

    protected function deleteRange($range){
    	$deleterange = DB::delete(DB::raw("
    			DELETE FROM rangos
    			WHERE id_rango = ".$range.""));

    	return $deleterange;
    }
}
