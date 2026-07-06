<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            if (Schema::hasColumn('classrooms', 'building')) {
                $table->dropColumn('building');
            }
            if (Schema::hasColumn('classrooms', 'floor')) {
                $table->dropColumn('floor');
            }
        });
    }

    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->string('building')->nullable()->after('code');
            $table->integer('floor')->nullable()->after('building');
        });
    }
};