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
	Route::get('tugas-pegawai/{id}','PegawaiController@tugas');
	Route::get('jabatan/{id}/tugas','JabatanController@tugas_jabatan');
	Route::get('tugas/{id}','JabatanController@tugas');
	Route::put('tugas-jabatan/{id}','JabatanController@add_tugas');
	Route::get('nilai-skp','NilaiController@nilai_skp');
	Route::get('nilai-skp/{id}/report','NilaiController@nilai_skp_report');

	Route::group(['prefix'=>'data'],function(){
		Route::post('save-session-sasaran','HomeController@save_session_sasaran');
		Route::resource('status','StatusController');
		Route::resource('golongan','GolonganController');
		Route::get('list-golongan','GolonganController@list_golongan');
		Route::resource('pangkat','PangkatController');
		Route::resource('instansi','InstansiController');
		Route::get('target/{id}/jabatan','JabatanController@target');
		Route::get('tugas/{id}','JabatanController@tugas_by_id');
		Route::put('tugas/{id}','JabatanController@update_tugas');
		Route::delete('tugas/{id}','JabatanController@delete_tugas');
		Route::post('target','JabatanController@target_store');
		Route::get('target/{id}','JabatanController@target_by_id');
		Route::get('cek-target/{id}','JabatanController@cek_target');
		Route::delete('target/{id}','JabatanController@hapus_target');
		Route::put('target/{id}','JabatanController@update_target');
		Route::get('nilai-skp','NilaiController@nilai_skp_data');

		Route::get('list-pegawai','PegawaiController@list_pegawai');
		Route::post('nilai-skp','NilaiController@nilai_skp_store');
		Route::get('nilai-skp/{id}','NilaiController@nilai_skp_detail');
		Route::put('nilai-skp/{id}','NilaiController@nilai_skp_update');
		Route::put('update-nilai-skp/{id}','NilaiController@update_skp_nilai');
		Route::delete('nilai-skp/{id}','NilaiController@nilai_skp_delete');

		Route::get('form-skp/{id}','NilaiController@form_skp');
		Route::get('form-skp-realisasi/{id}','NilaiController@form_skp_realisasi');
		Route::post('tugastambahan','NilaiController@tugas_tambahan_store');
		Route::delete('tugastambahan/{id}','NilaiController@tugas_tambahan_delete');
	});
});
