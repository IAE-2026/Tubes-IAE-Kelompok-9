<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nilais', function (Blueprint $table) {
            $table->string('receipt_number')->nullable()->after('tahun_ajaran');
            $table->string('recorded_by')->nullable()->after('receipt_number');
        });
    }

    public function down(): void
    {
        Schema::table('nilais', function (Blueprint $table) {
            $table->dropColumn(['receipt_number', 'recorded_by']);
        });
    }
};
