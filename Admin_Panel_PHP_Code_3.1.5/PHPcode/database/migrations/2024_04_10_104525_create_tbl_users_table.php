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
        if (Schema::hasTable('tbl_users')) {
            Schema::table('tbl_users', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_users', 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create('tbl_users', function (Blueprint $table) {
                $table->id();
                $table->text('firebase_id');
                $table->string('name')->nullable();
                $table->string('type', 10)->nullable();
                $table->string('email')->nullable();
                $table->string('mobile', 20)->nullable();
                $table->text('profile')->nullable();
                $table->text('fcm_id');
                $table->tinyInteger('status')->default(0)->comment('1-active, 0-deactive'); // Assuming 0=inactive, 1=active
                $table->dateTime('date');
                $table->integer('role');
                $table->timestamps();
                // $table->foreign('role_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_users');
    }
};
