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
        if (Schema::hasTable('tbl_news')) {
            // Add missing columns if not present
            Schema::table('tbl_news', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_news', 'slug')) {
                    $table->string('slug');
                }
                if (!Schema::hasColumn('tbl_news', 'meta_title')) {
                    $table->text('meta_title')->nullable();
                }
                if (!Schema::hasColumn('tbl_news', 'meta_description')) {
                    $table->text('meta_description')->nullable();
                }
                if (!Schema::hasColumn('tbl_news', 'meta_keyword')) {
                    $table->text('meta_keyword')->nullable();
                }
                if (!Schema::hasColumn('tbl_news', 'schema_markup')) {
                    $table->text('schema_markup')->nullable();
                }
                if (!Schema::hasColumn('tbl_news', 'created_at')) {
                    $table->timestamps();
                }
            });
            // Update existing records
            DB::table('tbl_news')
                ->whereNull('slug')
                ->orwhere('slug', '')
                ->update([
                    'slug' => DB::raw('REPLACE(LOWER(title), " ", "-")'),
                ]);
            DB::table('tbl_news')
                ->whereNull('meta_keyword')
                ->update([
                    'meta_keyword' => DB::raw('REPLACE(LOWER(title), " ", "-")'),
                ]);
            DB::table('tbl_news')
                ->whereNull('meta_description')
                ->update([
                    'meta_description' => DB::raw('REPLACE(LOWER(title), " ", "-")'),
                ]);
            DB::table('tbl_news')
                ->whereNull('meta_title')
                ->update([
                    'meta_title' => DB::raw('REPLACE(LOWER(title), " ", "-")'),
                ]);
        } else {
            Schema::create('tbl_news', function (Blueprint $table) {
                $table->id();
                $table->integer('language_id')->index('language_id');
                $table->integer('category_id')->index('category_id');
                $table->integer('subcategory_id')->default(0)->index('subcategory_id');
                $table->text('tag_id');
                $table->integer('location_id')->default(0)->index('location_id');
                $table->text('title');
                $table->string('slug')->index('slug');
                $table->string('image')->nullable();
                $table->dateTime('date')->nullable();
                $table->string('content_type', 50);
                $table->text('content_value')->nullable();
                $table->longText('description')->nullable();
                $table->integer('user_id');
                $table->integer('admin_id');
                $table->date('show_till')->nullable();
                $table->tinyInteger('status')->default(0)->comment('1-active, 0-deactive'); // 1=Active, 0=Deactive
                $table->integer('is_clone')->default(0);
                $table->integer('counter')->default(0);
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
        Schema::dropIfExists('tbl_news');
    }
};
