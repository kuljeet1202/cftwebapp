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
        if (Schema::hasTable('tbl_survey_option')) {
            Schema::table('tbl_survey_option', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_survey_option', 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create('tbl_survey_option', function (Blueprint $table) {
                $table->id();
                $table->integer('question_id')->index('question_id');
                $table->text('options');
                $table->integer('counter')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_survey_option');
    }
};