<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
        $table->id();
        $table->string('ten_du_an');
        $table->date('ngay_bat_dau');
        $table->date('ngay_ket_thuc')->nullable();
        $table->integer('tien_do')->default(0);
        $table->string('trang_thai')->default('Đang thực hiện');
        // Lưu ý: Tên bảng nhân viên của bạn là 'nhanvien' (không có chữ s)
        $table->foreignId('nhanvien_id')->constrained('nhanvien')->onDelete('cascade');
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
