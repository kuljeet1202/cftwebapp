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
        if (Schema::hasTable('tbl_featured_sections')) {
            Schema::table('tbl_featured_sections', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_featured_sections', 'slug')) {
                    $table->string('slug', 500)->nullable();
                }
                if (!Schema::hasColumn('tbl_featured_sections', 'meta_keyword')) {
                    $table->text('meta_keyword')->nullable();
                }
                if (!Schema::hasColumn('tbl_featured_sections', 'schema_markup')) {
                    $table->text('schema_markup')->nullable();
                }
                if (!Schema::hasColumn('tbl_featured_sections', 'meta_description')) {
                    $table->text('meta_description')->nullable();
                }
                if (!Schema::hasColumn('tbl_featured_sections', 'meta_title')) {
                    $table->text('meta_title')->nullable();
                }
                if (!Schema::hasColumn('tbl_featured_sections', 'og_image')) {
                    $table->text('og_image')->nullable();
                }
                if (!Schema::hasColumn('tbl_featured_sections', 'created_at')) {
                    $table->timestamps();
                }
                if (!Schema::hasColumn('tbl_featured_sections', 'updated_at')) {
                    $table->date('updated_at')->nullable();
                }
            });

            // Update existing records
            DB::table('tbl_featured_sections')
                ->whereNull('slug')
                ->orwhere('slug', '')
                ->update([
                    'slug' => DB::raw('REPLACE(LOWER(title), " ", "-")'),
                ]);

            DB::table('tbl_featured_sections')
                ->whereNull('meta_keyword')
                ->update([
                    'meta_keyword' => DB::raw('REPLACE(LOWER(title), " ", "-")'),
                ]);

            DB::table('tbl_featured_sections')
                ->whereNull('meta_description')
                ->update([
                    'meta_description' => DB::raw('REPLACE(LOWER(title), " ", "-")'),
                ]);

            DB::table('tbl_featured_sections')
                ->whereNull('meta_title')
                ->update([
                    'meta_title' => DB::raw('REPLACE(LOWER(title), " ", "-")'),
                ]);
        } else {
            Schema::create('tbl_featured_sections', function (Blueprint $table) {
                $table->id();
                $table->integer('language_id')->index('language_id');
                $table->string('title', 500)->nullable();
                $table->string('slug', 500)->nullable();
                $table->string('short_description', 500)->nullable();
                $table->string('news_type', 500)->nullable();
                $table->string('videos_type', 100)->nullable();
                $table->string('filter_type', 500)->nullable();
                $table->string('category_ids');
                $table->string('subcategory_ids');
                $table->string('news_ids');
                $table->string('style_app', 100)->nullable();
                $table->string('style_web', 100)->nullable();
                $table->integer('row_order')->default(0);
                $table->tinyInteger('status')->default(1)->comment('0-deactive, 1-active');
                $table->tinyInteger('is_based_on_user_choice')->comment('0-filter_section, 1-news from users category');
                $table->text('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->text('meta_keyword')->nullable();
                $table->text('schema_markup')->nullable();
                $table->text('og_image')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_featured_sections');
    }
};
