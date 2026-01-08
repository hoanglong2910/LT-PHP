<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nhanvien_id');
            $table->integer('thang');
            $table->integer('nam');
            $table->string('type', 30);     // PRAISE | REMINDER | INFO
            $table->string('title', 150);
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['nhanvien_id', 'thang', 'nam']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_notifications');
    }
};
