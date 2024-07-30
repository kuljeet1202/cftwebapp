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
        if (Schema::hasTable('tbl_settings')) {
            Schema::table('tbl_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_settings', 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create('tbl_settings', function (Blueprint $table) {
                $table->id();
                $table->string('type');
                $table->text('message');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_settings');
    }
};
