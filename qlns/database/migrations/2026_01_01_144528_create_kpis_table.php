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

            // Bảng nhân viên của bạn là: nhanvien
            // Cột id của nhanvien là: int(10) UNSIGNED
            $table->unsignedInteger('nhanvien_id');

            $table->integer('thang');
            $table->integer('nam');

            // Lưu % KPI (ví dụ: 85.5)
            $table->float('chi_so_kpi');

            $table->text('ghi_chu')->nullable();
            $table->timestamps();

            // Khóa ngoại liên kết với bảng nhanvien
            $table->foreign('nhanvien_id')
                  ->references('id')
                  ->on('nhanvien')
                  ->onDelete('cascade');
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
