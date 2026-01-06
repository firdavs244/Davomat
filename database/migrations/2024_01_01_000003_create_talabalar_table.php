<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Talabalar jadvali - kollej talabalari uchun
     */
    public function up(): void
    {
        Schema::create('talabalar', function (Blueprint $table) {
            $table->id();
            $table->string('fish', 150); // Familiya Ism Sharif
            $table->foreignId('guruh_id')->constrained('guruhlar')->onDelete('restrict');
            $table->date('kirgan_sana'); // Kollej talabasi bo'lgan kun
            $table->date('ketgan_sana')->nullable(); // Talaba ketgan bo'lsa
            $table->enum('holati', ['aktiv', 'noaktiv', 'ketgan', 'akademik_tatil'])->default('aktiv');
            $table->text('izoh')->nullable(); // Qo'shimcha izoh
            $table->timestamps();
            
            // Indekslar - tez qidirish uchun
            $table->index('guruh_id');
            $table->index('holati');
            $table->index('kirgan_sana');
            $table->index('ketgan_sana');
            $table->index(['guruh_id', 'holati']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talabalar');
    }
};
