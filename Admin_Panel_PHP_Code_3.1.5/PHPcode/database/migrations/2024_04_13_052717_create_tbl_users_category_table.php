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
        if (Schema::hasTable('tbl_users_category')) {
            Schema::table('tbl_users_category', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_users_category', 'created_at')) {
                    $table->timestamps();
                }
                if (Schema::hasColumn('tbl_users_category', 'category_id')) {
                    $table->text('category_id')->change();
                }
            });
        } else {
            Schema::create('tbl_users_category', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->index('user_id');
                $table->text('category_id');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_users_category');
    }
};
