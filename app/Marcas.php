<?php

namespace escom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Marcas extends Model
{
	public $timestamps = false;

    protected $table = 'marcas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_marca','marca',
    ];

    protected function getMark($mark){
    	$selectmark = DB::select( DB::raw("
                SELECT      m.id_marca,
                            m.marca
                FROM marcas m
                WHERE m.marca = '".$mark."'
                ORDER BY m.marca asc"));  

    	return $selectmark;
    }

    protected function getAllmarks(){
    	$selectAllM = DB::select(DB::raw("
                SELECT      m.id_marca,
                            m.marca
                FROM marcas m
                ORDER BY m.marca asc"));

    	return $selectAllM;
    }

    protected function insertmark($request){
    	$insertmark = DB::insert(DB::raw("
    			INSERT INTO marcas(marca)
	    		VALUES 		('".$request["mark"]."');
    		"));

    	return $insertmark;
    }

    protected function updateMark($request,$id){
    	$updatemark = DB::update(DB::raw("
    			UPDATE 	marcas
    			SET 	marca       = '".$request["mark"]."'
			    WHERE 	id_marca    = '".$id."'
    		"));

    	return $updatemark;
    }

    protected function deleteMark($id){
    	$deleteMark = DB::delete(DB::raw("
    			DELETE FROM marcas
    			WHERE id_marca = ".$id.""));

    	return $deleteMark;
    }
}
