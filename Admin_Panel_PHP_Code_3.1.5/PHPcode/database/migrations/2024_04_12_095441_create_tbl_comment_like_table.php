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
        if (Schema::hasTable('tbl_comment_like')) {
            Schema::table('tbl_comment_like', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_comment_like', 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create('tbl_comment_like', function (Blueprint $table) {
                $table->id();
                $table->integer('comment_id')->index('comment_id');
                $table->integer('user_id')->index('user_id');
                $table->tinyInteger('status')->index('status')->comment('1-like, 2-dislike');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_comment_like');
    }
};
