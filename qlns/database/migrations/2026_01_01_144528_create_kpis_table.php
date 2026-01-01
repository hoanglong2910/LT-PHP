<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKpisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpis', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('nhanvien_id'); // Liên kết với bảng nhanviens
        $table->integer('thang'); 
        $table->integer('nam');
        $table->float('chi_so_kpi'); // Lưu giá trị % (ví dụ: 85.5)
        $table->text('ghi_chu')->nullable();
        $table->timestamps();

        // Khóa ngoại liên kết với bảng nhân viên
        $table->foreign('nhanvien_id')->references('id')->on('nhanviens')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kpis');
    }
}
