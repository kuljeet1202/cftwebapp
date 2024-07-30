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
        if (Schema::hasTable('tbl_token')) {
            Schema::table('tbl_token', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_token', 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create('tbl_token', function (Blueprint $table) {
                $table->id();
                $table->text('token');
                $table->integer('language_id')->nullable();
                $table->string('latitude')->nullable();
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
        Schema::dropIfExists('tbl_token');
    }
};
