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
        if (Schema::hasTable('tbl_comment_flag')) {
            Schema::table('tbl_comment_flag', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_comment_flag', 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create('tbl_comment_flag', function (Blueprint $table) {
                $table->id();
                $table->integer('comment_id')->index('comment_id');
                $table->integer('user_id')->index('user_id');
                $table->integer('news_id')->index('news_id');
                $table->text('message');
                $table->tinyInteger('status')->comment('0-deactive, 1-active');
                $table->dateTime('date');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_comment_flag');
    }
};