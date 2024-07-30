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
        if (Schema::hasTable('tbl_survey_question')) {
            Schema::table('tbl_survey_question', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_survey_question', 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create('tbl_survey_question', function (Blueprint $table) {
                $table->id();
                $table->integer('language_id')->index('language_id');
                $table->text('question');
                $table->integer('status');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_survey_question');
    }
};