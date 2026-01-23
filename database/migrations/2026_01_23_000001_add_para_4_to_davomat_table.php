<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 4-para ustunini davomat jadvaliga qo'shish
     */
    public function up(): void
    {
        Schema::table('davomat', function (Blueprint $table) {
            $table->enum('para_4', ['bor', 'yoq'])->nullable()->after('para_3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('davomat', function (Blueprint $table) {
            $table->dropColumn('para_4');
        });
    }
};
