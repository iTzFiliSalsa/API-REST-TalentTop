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

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', 'AlumnoController@login');
Route::post('/register', 'AlumnoController@register');
Route::get('/horarios', 'CursoController@horarios');
Route::get('/cursos/{dia}/{semestre}', 'CursoController@cursos');
Route::get('/comprobar/{id}/{dia}', 'AlumnoController@vCurso');
Route::post('/cursos', 'CursoController@aCurso');
