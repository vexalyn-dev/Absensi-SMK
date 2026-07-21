<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('class_attendances', function (Blueprint $table) {
            $table->foreignId('selected_classroom_id')->nullable()->after('classroom_id')->constrained('classrooms')->onDelete('set null');
            $table->foreignId('subject_id')->nullable()->after('selected_classroom_id')->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('class_attendances', function (Blueprint $table) {
            $table->dropForeign(['selected_classroom_id']);
            $table->dropForeign(['subject_id']);
            $table->dropColumn(['selected_classroom_id', 'subject_id']);
        });
    }
};