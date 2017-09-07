<?php

namespace escom\Http\Controllers;

use Illuminate\Http\Request;
use escom\Reports;

class ReportsController extends Controller
{
	public function getReport($idReport){
		$getReportM = Reports::getReport($idReport);
        if($getReportM){
            return response()->json([
                    "response" => $getReportM
                ]);
        }
	}    
}
