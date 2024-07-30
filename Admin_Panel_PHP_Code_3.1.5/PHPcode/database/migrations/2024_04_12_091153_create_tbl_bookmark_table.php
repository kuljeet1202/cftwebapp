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
        if (Schema::hasTable('tbl_bookmark')) {
            Schema::table('tbl_bookmark', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_bookmark', 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create('tbl_bookmark', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->index('user_id');
                $table->integer('news_id')->index('news_id');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_bookmark');
    }
};
