<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->enum('type', ['regular', 'shared'])->default('regular')->after('name');
            $table->string('location_type', 50)->nullable()->after('type');
            $table->boolean('is_shared')->default(false)->after('location_type');
        });
    }

    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropColumn(['type', 'location_type', 'is_shared']);
        });
    }
};