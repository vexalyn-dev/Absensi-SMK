<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            if (!Schema::hasColumn('classrooms', 'is_shared')) {
                $table->boolean('is_shared')->default(false)->after('location_type');
            }
            if (!Schema::hasColumn('classrooms', 'major')) {
                $table->string('major', 50)->nullable()->after('code');
            }
            if (!Schema::hasColumn('classrooms', 'level')) {
                $table->string('level', 10)->nullable()->after('major');
            }
            if (!Schema::hasColumn('classrooms', 'capacity')) {
                $table->integer('capacity')->nullable()->after('level');
            }
            if (!Schema::hasColumn('classrooms', 'description')) {
                $table->string('description', 500)->nullable()->after('capacity');
            }
            if (!Schema::hasColumn('classrooms', 'qr_code')) {
                $table->string('qr_code')->nullable()->after('qr_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $cols = ['is_shared', 'major', 'level', 'capacity', 'description', 'qr_code'];
            $drop = array_filter($cols, fn($c) => Schema::hasColumn('classrooms', $c));
            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};
