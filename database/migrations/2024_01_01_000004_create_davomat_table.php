<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Davomat jadvali - kunlik davomat ma'lumotlari
     */
    public function up(): void
    {
        Schema::create('davomat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('talaba_id')->constrained('talabalar')->onDelete('cascade');
            $table->foreignId('guruh_id')->constrained('guruhlar')->onDelete('restrict');
            $table->date('sana');
            $table->enum('para_1', ['bor', 'yoq'])->nullable(); // 1-para davomati
            $table->enum('para_2', ['bor', 'yoq'])->nullable(); // 2-para davomati
            $table->enum('para_3', ['bor', 'yoq'])->nullable(); // 3-para davomati
            $table->foreignId('xodim_id')->constrained('users')->onDelete('restrict'); // Davomat olgan xodim
            $table->text('izoh')->nullable();
            $table->timestamps();
            
            // Unique constraint - bitta talaba bitta kunda faqat bitta davomat
            $table->unique(['talaba_id', 'sana']);
            
            // Indekslar - tez qidirish uchun
            $table->index('sana');
            $table->index('guruh_id');
            $table->index('xodim_id');
            $table->index(['guruh_id', 'sana']);
            $table->index(['sana', 'para_1', 'para_2', 'para_3']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('davomat');
    }
};
