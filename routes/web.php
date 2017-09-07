<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Rutas para los webservices de usuario
Route::resource('users','UsersController');
Route::post('/users/login','UsersController@login');
Route::put('/users/','UsersController@restorePassword');

//Rutas para los webservices de equipos
Route::resource('maquinas','EquiposController');
Route::post('/maquinas/insertMxSCon','EquiposController@insertMxSCon');
Route::get('/maquinas/suppliexm/{id}','EquiposController@getSupplieMachine');
Route::get('/maquinas/mm/MachineMainte','EquiposController@MachineMainte');
Route::put('/maquinas/stateMachine/{id}','EquiposController@updateState');
//Rutas para los webservices de insumos
Route::resource('insumos','suppliesController');

//Rutas para los webservices de marcas
Route::resource('marcas','MarcaController');

//Rutas para los webservices de marcas
Route::resource('rango','RangoController');

//Rutas para los webservices de Novedades
Route::resource('novedades','NoveltiesController');
Route::post('novedades/fechas','NoveltiesController@getNoveltiesDates');

//Rutas para los webservices de equiposXInsumos
Route::resource('maquinasInsumos','EquiXInsuController');

//Rutas para los webservices de solicitudes
Route::resource('solicitudes','SolicitudesController');
Route::get('/solicitudes/getrequestAll/{id}','SolicitudesController@getrequestAll');
Route::post('/solicitudes/getsolicitudesDate/','SolicitudesController@getrequestDateAll');
//Rutas reportes
Route::get('/report/{id}','ReportsController@getReport');

Route::get('/', function () {
    return view('welcome');
});
