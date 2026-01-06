<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Guruhlar jadvali - kollej guruhlari uchun
     */
    public function up(): void
    {
        Schema::create('guruhlar', function (Blueprint $table) {
            $table->id();
            $table->string('nomi', 50)->unique(); // Guruh nomi: IT-101, BH-201
            $table->tinyInteger('kurs')->unsigned(); // 1, 2, 3, 4 kurs
            $table->string('yunalish', 100); // Yo'nalish: Dasturlash, Buxgalteriya
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indekslar
            $table->index('kurs');
            $table->index('yunalish');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guruhlar');
    }
};
