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
    return view('auth.login');
});

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');
Route::group(['prefix'=>'home'],function(){
	Route::get('/','HomeController@index')->name('home');
	Route::get('change-password','HomeController@change_password');
	Route::get('profile','HomeController@profile');
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
	Route::resource('users','UserController');
	Route::get('report','HomeController@report');
	Route::get('{id}/preview','NilaiController@preview_skp');
	Route::get('{id}/export-xls','NilaiController@export_xls');
	Route::resource('nilai-harian','NilaiharianController');
	Route::get('{id}/report-harian-belum-konfirmasi','HomeController@report_harian_belum_konfirmasi');

	Route::group(['prefix'=>'report'],function(){
		Route::get('kegiatan-harian','NilaiharianController@report_kegiatan_harian');
		Route::get('kegiatan-harian-preview','NilaiharianController@report_kegiatan_harian_preview');
		Route::get('nilai-skp','NilaiharianController@report_nilai_skp');
	});

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
		Route::post('target-realisasi','JabatanController@realisasi_store');
		Route::get('target/{id}','JabatanController@target_by_id');
		Route::get('cek-target/{id}','JabatanController@cek_target');
		Route::delete('target/{id}','JabatanController@hapus_target');
		Route::put('target/{id}','JabatanController@update_target');
		Route::put('realisasi/{id}','JabatanController@update_realisasi');
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
		Route::get('perilaku-kerja-by-id-skp/{id}','NilaiController@perilaku_kerja_by_id_skp');
		Route::get('list-perilaku-by-skp/{id}','NilaiController@list_perilaku_by_skp');
		Route::post('list-perilaku-by-skp','NilaiController@list_perilaku_by_skp_store');
		Route::delete('list-perilaku-by-skp/{id}','NilaiController@list_perilaku_by_skp_delete');

		Route::post('kegiatan-harian-preview','NilaiharianController@preview_kegiatan_harian');
		Route::get('export-kegiatan-harian','NilaiharianController@export_kegiatan_harian');
		Route::post('report-skp-preview','NilaiharianController@report_skp_preview');

		Route::post('reset-password','UserController@reset_password');
		Route::post('change-password','UserController@change_password');
		Route::post('approve-kegiatan','HomeController@approve_kegiatan');
	});
});

Route::get('tes-rumus',function(){
	$target_kuant=607;
	$target_kual=100;
	$target_waktu=12;
	$target_biaya=0;

	$realisasi_kuant=607;
	$realisasi_kual=89;
	$realisasi_waktu=12;
	$realisasi_biaya=0;

	if($target_kuant>0){
		$a=1;
	}else{
		$a=0;
	}

	if($realisasi_waktu!=0){
		$persen_waktu=100-($realisasi_waktu/$target_waktu*100);
	}else{
		$persen_waktu=0;
	}

	if($realisasi_biaya!=0){
		$persen_biaya=100-($realisasi_biaya/$target_biaya*100);
	}else{
		$persen_biaya=0;
	}

	$kuantitas=$realisasi_kuant/$target_kuant*100;
	$kualitas=$realisasi_kual/$target_kual*100;

	if($persen_waktu>24){
		$waktu=76-((((1.76*$target_waktu-$realisasi_waktu)/$target_waktu)*100)-100);
	}else{
		$waktu=((1.76*$target_waktu-$realisasi_waktu)/$target_waktu)*100;
	}

	if($persen_biaya!=0){
		if($persen_biaya>24){
			$biaya=76-((((1.76*$target_biaya-$realisasi_biaya)/$target_biaya)*100)-100);
		}else{
			$biaya=((1.76*$target_biaya-$realisasi_biaya)/$target_biaya)*100;
		}
	}else{
		$biaya=0;
	}

	$penghitungan=$kuantitas+$kualitas+$waktu+$biaya;

	if($realisasi_biaya==""){
		$nilai_capaian_skp=$penghitungan/3;
	}else{
		$nilai_capaian_skp=$penghitungan/4;
	}

	return array(
		'target'=>array(
			'kuant'=>$target_kuant,
			'kual'=>$target_kual,
			'waktu'=>$target_waktu,
			'biaya'=>$target_biaya
		),
		'realisasi'=>array(
			'kuant'=>$realisasi_kuant,
			'kual'=>$realisasi_kual,
			'waktu'=>$realisasi_waktu,
			'biaya'=>$realisasi_biaya
		),
		'persen_waktu'=>$persen_waktu,
		'persen_biaya'=>$persen_biaya,
		'kualitas'=>$kualitas,
		'kuantitas'=>$kuantitas,
		'waktu'=>$waktu,
		'biaya'=>$biaya,
		'penghitungan'=>$penghitungan,
		'nilai_capaian_skp'=>$nilai_capaian_skp
	);
});
