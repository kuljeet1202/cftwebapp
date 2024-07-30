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
        if (Schema::hasTable('tbl_comment_notification')) {
            Schema::table('tbl_comment_notification', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_comment_notification', 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create('tbl_comment_notification', function (Blueprint $table) {
                $table->id();
                $table->integer('master_id')->index('master_id');
                $table->integer('user_id')->index('user_id');
                $table->integer('sender_id')->index('sender_id');
                $table->string('type', 255);
                $table->text('message');
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
        Schema::dropIfExists('tbl_comment_notification');
    }
};
