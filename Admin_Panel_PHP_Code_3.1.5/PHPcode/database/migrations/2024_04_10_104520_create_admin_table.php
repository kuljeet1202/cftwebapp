<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('admin')) {
            Schema::table('admin', function (Blueprint $table) {
                if (!Schema::hasColumn('admin', 'created_at')) {
                    $table->timestamps();
                }
                if (!Schema::hasColumn('admin', 'image')) {
                    $table->string('image')->nullable();
                }
            });
        } else {
            Schema::create('admin', function (Blueprint $table) {
                $table->id();
                $table->string('username');
                $table->text('password')->nullable();
                $table->string('email')->nullable();
                $table->text('forgot_unique_code')->nullable();
                $table->string('forgot_at')->nullable();
                $table->string('image')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
