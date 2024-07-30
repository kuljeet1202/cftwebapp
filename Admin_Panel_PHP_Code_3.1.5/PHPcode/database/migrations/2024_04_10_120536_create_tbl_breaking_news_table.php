<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('tbl_breaking_news')) {
            Schema::table('tbl_breaking_news', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_breaking_news', 'slug')) {
                    $table->string('slug');
                }
                if (!Schema::hasColumn('tbl_breaking_news', 'meta_title')) {
                    $table->text('meta_title')->nullable();
                }
                if (!Schema::hasColumn('tbl_breaking_news', 'meta_description')) {
                    $table->text('meta_description')->nullable();
                }
                if (!Schema::hasColumn('tbl_breaking_news', 'meta_keyword')) {
                    $table->text('meta_keyword')->nullable();
                }
                if (!Schema::hasColumn('tbl_breaking_news', 'schema_markup')) {
                    $table->text('schema_markup')->nullable();
                }
                if (!Schema::hasColumn('tbl_breaking_news', 'created_at')) {
                    $table->timestamps();
                }
            });

            // Update existing records
            DB::table('tbl_breaking_news')
                ->whereNull('slug')
                ->orwhere('slug', '')
                ->update([
                    'slug' => DB::raw('REPLACE(LOWER(title), " ", "-")'),
                ]);

            DB::table('tbl_breaking_news')
                ->whereNull('meta_keyword')
                ->update([
                    'meta_keyword' => DB::raw('REPLACE(LOWER(title), " ", "-")'),
                ]);

            DB::table('tbl_breaking_news')
                ->whereNull('meta_description')
                ->update([
                    'meta_description' => DB::raw('REPLACE(LOWER(title), " ", "-")'),
                ]);

            DB::table('tbl_breaking_news')
                ->whereNull('meta_title')
                ->update([
                    'meta_title' => DB::raw('REPLACE(LOWER(title), " ", "-")'),
                ]);
        } else {
            Schema::create('tbl_breaking_news', function (Blueprint $table) {
                $table->id();
                $table->integer('language_id')->index('language_id'); // Nullable foreign key
                $table->text('title');
                $table->string('slug')->index('slug');
                $table->string('image')->nullable();
                $table->string('content_type', 50)->nullable();
                $table->text('content_value')->nullable();
                $table->text('description')->nullable();
                $table->text('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->text('meta_keyword')->nullable();
                $table->text('schema_markup')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_breaking_news');
    }
};
