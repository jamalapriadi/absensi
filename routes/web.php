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

//Route::get('/home', 'HomeController@index')->name('home');
Route::group(['prefix'=>'home'],function(){
	Route::get('/','HomeController@index')->name('home');
	Route::get('master','HomeController@master');
	Route::resource('perilaku-kerja','PerilakukerjaController');
	Route::resource('sasaran-kerja','SasarankerjaController');
	Route::resource('pegawai','PegawaiController');
	Route::resource('jabatan','JabatanController');
	Route::get('tugas/{id}','JabatanController@tugas');
	Route::put('tugas-jabatan/{id}','JabatanController@add_tugas');

	Route::group(['prefix'=>'data'],function(){
		Route::post('save-session-sasaran','HomeController@save_session_sasaran');
		Route::resource('status','StatusController');
		Route::resource('golongan','GolonganController');
		Route::get('list-golongan','GolonganController@list_golongan');
		Route::resource('pangkat','PangkatController');
		Route::resource('instansi','InstansiController');
	});
});
