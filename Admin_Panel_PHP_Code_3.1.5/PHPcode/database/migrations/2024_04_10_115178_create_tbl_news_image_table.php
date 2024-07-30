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
        if (Schema::hasTable('tbl_news_image')) {
            Schema::table('tbl_news_image', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_news_image', 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create('tbl_news_image', function (Blueprint $table) {
                $table->id();
                $table->integer('news_id')->index('news_id');
                $table->string('other_image');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_news_image');
    }
};
