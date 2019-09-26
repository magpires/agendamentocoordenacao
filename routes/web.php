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

Auth::routes();

Route::prefix('/home')->group(function(){
    Route::get('/', 'HomeController@index')->name('home');

    //Rotas para reuniões
    Route::post('/criadisponibilidade', 'ReuniaoController@criaDisponibilidade')->name('criardisponibilidade');
    Route::post('/agendareuniao', 'ReuniaoController@agendaReuniao')->name('agendareuniao');
    Route::post('/excluireuniao', 'ReuniaoController@delete')->name('excluireuniao');
});

Route::post('/infosolicitante/{rm}', 'ReuniaoController@infoSolicitante')->name('infosolicitante');

Route::get('/calendariocoordenadores', 'ReuniaoController@calendarioCoordenadores')->name('calendariocoordenadores');
Route::get('/calendariocoordenador/{id}', 'ReuniaoController@calendarioCoordenador')->name('calendariocoordenador');

//Rota para histórico de reuniões
Route::get('/historicoreunioes', 'ReuniaoController@historicoReunioes')->name('historicoreunioes');
Route::post('/inforeuniao/{id}', 'ReuniaoController@infoReuniao')->name('inforeuniao');

// //Rota para histórico de reuniões
// Route::get('/historicoreunioes', 'EventoController@historicoReunioes')->name('historicoreunioes');
// Route::post('/inforeuniao/{id}', 'EventoController@infoReuniao')->name('inforeuniao');

//Aqui teremos as rotas de atualização do perfil dos usuários
Route::get('/meuperfil', 'PerfilController@index')->name('meuperfil');
Route::post('/atualizaperfil', 'PerfilController@atualizaPerfil')->name('atualizaperfil');
Route::get('/mudasenha', 'PerfilController@indexMudaSenha')->name('mudasenha');
Route::post('/mudasenha', 'PerfilController@mudaSenha')->name('mudasenha.submit');

//Rotas para gerenciar usuários
Route::get('/gerenciarusuarios', 'AdminController@index')->name('gerenciarusuarios');
Route::post('/cadastrarusuario', 'AdminController@cadastraUsuario')->name('cadastrarusuario');
Route::post('/infousuario/{id}', 'AdminController@infoUsuario')->name('infousuario');
Route::post('/atualizausuario', 'AdminController@atualizaUsuario')->name('atualizausuario');
Route::post('/excluiusuario', 'AdminController@delete')->name('excluiusuario');

//Caso o usuário não passe um ID para o coordenador, mandamos ele de volta para a Home
Route::get('/calendariocoordenador', function () {
    return redirect('home');
});
