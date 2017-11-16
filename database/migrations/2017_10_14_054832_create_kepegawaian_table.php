<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKepegawaianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instansi',function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->string('nama_instansi',191);
            $table->string('kelas',191);
            $table->string('alamat',191);
            $table->string('kode_pos',15);
            $table->string('telp',25);
            $table->string('fax',25);
            $table->string('website',65);
            $table->string('email',65);
            $table->timestamps();
        });

        Schema::create('golongans', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('nama_golongan',191)->unique();
            $table->timestamps();
        });

        Schema::create('pangkats',function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->string('nama_pangkat',65);
            $table->integer('golongan_id')->nullable()->unsigned();
            $table->string('ruang',1);
            $table->timestamps();

            $table->foreign('golongan_id')
                ->references('id')
                ->on('golongans')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        Schema::create('status',function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->string('nama_status',191);
            $table->timestamps();
        });

        Schema::create('sasaran_kerja',function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->string('nama_sasaran',191);
            $table->date('start_periode');
            $table->date('end_periode');
            $table->timestamps();
        });

        Schema::create('jabatan',function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->string('nama_jabatan',191);
            $table->timestamps();
        });

        Schema::create('tugas_jabatan',function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('sasaran_kerja_id')->unsigned()->nullable();
            $table->integer('jabatan_id')->unsigned()->nullable();
            $table->string('nama_tugas',191);
            $table->timestamps();

            $table->foreign('sasaran_kerja_id')
                ->references('id')
                ->on('sasaran_kerja')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            $table->foreign('jabatan_id')
                ->references('id')
                ->on('jabatan')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        Schema::create('tugas_jabatan_target',function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('tugas_jabatan_id')->unsigned()->nullable();
            $table->string('kuant',191)->nullable();
            $table->string('output',65)->nullable();
            $table->string('kual',65)->nullable();
            $table->string('waktu',65)->nullable();
            $table->string('periode_waktu',65)->nullable();
            $table->float('biaya')->nullable();
            $table->float('perhitungan')->nullable();
            $table->float('nilai_pencapaian')->nullable();
            $table->enum('type',['target','realisasi']);
            $table->timestamps();

            $table->foreign('tugas_jabatan_id')
                ->references('id')
                ->on('tugas_jabatan')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        Schema::create('pegawai',function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->string('nip',25)->nullable()->unique();
            $table->string('tmk',25)->nullable()->unique();
            $table->integer('status_id')->unsigned();
            $table->string('nama_lengkap',191)->nullable();
            $table->string('tempat_lahir',191)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama',15)->nullable();
            $table->string('alamat',191)->nullable();
            $table->enum('active',['Y','N'])->nullable();
            $table->string('foto',191);
            $table->timestamps();

            $table->foreign('status_id')
                ->references('id')
                ->on('status')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        Schema::create('pangkat_pegawai',function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('pegawai_id')->unsigned();
            $table->integer('pangkat_id')->unsigned();
            $table->date('tmt')->nullable();
            $table->enum('active',['Y','N']);
            $table->timestamps();

            $table->foreign('pegawai_id')
                ->references('id')
                ->on('pegawai')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('pangkat_id')
                ->references('id')
                ->on('pangkats')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        Schema::create('jabatan_pegawai',function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('pegawai_id')->unsigned();
            $table->integer('jabatan_id')->unsigned();
            $table->date('tmt')->nullable();
            $table->enum('active',['Y','N']);
            $table->timestamps();

            $table->foreign('pegawai_id')
                ->references('id')
                ->on('pegawai')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('jabatan_id')
                ->references('id')
                ->on('jabatan')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        Schema::create('user_pegawai',function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('pegawai_id')->unsigned();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('pegawai_id')
                ->references('id')
                ->on('pegawai')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        Schema::create('perilaku_kerja',function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->string('nama_perilaku',65);
            $table->string('deskripsi',191);
            $table->timestamps();
        });

        Schema::create('nilai_skp',function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('sasaran_kerja_id')->unsigned();
            $table->date('tgl_penilaian')->nullable();
            $table->integer('pegawai_id')->unsigned();
            $table->integer('pejabat_penilai')->unsigned();
            $table->integer('atasan_pejabat_penilai')->unsigned();
            $table->float('nilai_pencapaian')->nullable();
            $table->string('keberatan_dari_pegawai',191)->nullable();
            $table->string('tanggapan_dari_penilai',191)->nullable();
            $table->string('keputusan_atas_keberatan',191)->nullable();
            $table->date('tgl_diterima_pegawai')->nullable();
            $table->date('tgl_diterima_penilai')->nullable();
            $table->timestamps();

            $table->foreign('sasaran_kerja_id')
                ->references('id')
                ->on('sasaran_kerja')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('pegawai_id')
                ->references('id')
                ->on('pegawai')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('pejabat_penilai')
                ->references('id')
                ->on('pegawai')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('atasan_pejabat_penilai')
                ->references('id')
                ->on('pegawai')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        Schema::create('nilai_tugas_tambahan',function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('nilai_skp_id')->unsigned()->nullable();
            $table->enum('type',['tugas','kreativitas'])->nullable();
            $table->string('nama',191);
            $table->float('nilai')->nullable();
            $table->timestamps();

            $table->foreign('nilai_skp_id')
                ->references('id')
                ->on('nilai_skp')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        Schema::create('nilai_prestasi_kerja',function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('nilai_skp_id')->unsigned()->nullable();
            $table->integer('perilaku_kerja_id')->unsigned()->nullable();
            $table->string('nilai',10)->nullable();
            $table->String('uraian',191)->nullable();
            $table->timestamps();
            
            $table->foreign('perilaku_kerja_id')
                ->references('id')
                ->on('perilaku_kerja')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        Schema::create('nilai_kegiatan_harian',function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('pegawai_id')->unsigned();
            $table->date('tanggal')->nullable();
            $table->time('jam')->nullable();
            $table->string('kegiatan',191)->nullable();
            $table->string('hasil',191)->nullable();
            $table->string('keterangan',191)->nullable();
            $table->timestamps();

            $table->foreign('pegawai_id')
                ->references('id')
                ->on('pegawai')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kepegawaian');
    }
}
