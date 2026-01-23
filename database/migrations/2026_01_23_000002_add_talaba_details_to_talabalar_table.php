<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Talabalar jadvaliga qo'shimcha ma'lumotlar qo'shish
     */
    public function up(): void
    {
        Schema::table('talabalar', function (Blueprint $table) {
            $table->string('jshshir', 14)->nullable()->after('fish'); // JSHSHIR - 14 raqamli
            $table->string('pasport', 15)->nullable()->after('jshshir'); // Pasport seriya va raqami (null bo'lishi mumkin)
            $table->date('tugilgan_sana')->nullable()->after('pasport'); // Tug'ilgan sanasi
            $table->enum('jinsi', ['Erkak', 'Ayol'])->nullable()->after('tugilgan_sana'); // Jinsi
            $table->enum('qabul_turi', ['Grant', 'Kontrakt'])->nullable()->after('jinsi'); // Qabul turi
            $table->string('talim_shakli', 50)->nullable()->after('qabul_turi'); // Ta'lim shakli
            $table->string('oquv_yili', 20)->nullable()->after('talim_shakli'); // O'quv yili (2025-2027)
            $table->string('tuman', 100)->nullable()->after('oquv_yili'); // Tuman
            $table->text('manzil')->nullable()->after('tuman'); // Manzil

            // Indekslar
            $table->index('jshshir');
            $table->index('jinsi');
            $table->index('qabul_turi');
            $table->index('tuman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talabalar', function (Blueprint $table) {
            $table->dropIndex(['jshshir']);
            $table->dropIndex(['jinsi']);
            $table->dropIndex(['qabul_turi']);
            $table->dropIndex(['tuman']);

            $table->dropColumn([
                'jshshir',
                'pasport',
                'tugilgan_sana',
                'jinsi',
                'qabul_turi',
                'talim_shakli',
                'oquv_yili',
                'tuman',
                'manzil',
            ]);
        });
    }
};
