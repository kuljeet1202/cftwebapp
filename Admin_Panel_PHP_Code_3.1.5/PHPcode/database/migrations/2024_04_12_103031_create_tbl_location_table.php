<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('tbl_location')) {
            Schema::table('tbl_location', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_location', 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create('tbl_location', function (Blueprint $table) {
                $table->id();
                $table->string('location_name');
                $table->string('latitude');
                $table->string('longitude');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_location');
    }
};
