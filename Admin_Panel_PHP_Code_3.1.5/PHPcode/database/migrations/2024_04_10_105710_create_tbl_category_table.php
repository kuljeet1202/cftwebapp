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
        if (Schema::hasTable('tbl_category')) {
            // Add missing columns if not present
            Schema::table('tbl_category', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_category', 'slug')) {
                    $table->string('slug');
                }
                if (!Schema::hasColumn('tbl_category', 'row_order')) {
                    $table->integer('row_order')->default(0);
                }
                if (!Schema::hasColumn('tbl_category', 'meta_title')) {
                    $table->text('meta_title')->nullable();
                }
                if (!Schema::hasColumn('tbl_category', 'meta_description')) {
                    $table->text('meta_description')->nullable();
                }
                if (!Schema::hasColumn('tbl_category', 'meta_keyword')) {
                    $table->text('meta_keyword')->nullable();
                }
                if (!Schema::hasColumn('tbl_category', 'schema_markup')) {
                    $table->text('schema_markup')->nullable();
                }
                if (!Schema::hasColumn('tbl_category', 'created_at')) {
                    $table->timestamps();
                }
            });
            // Update existing records
            DB::table('tbl_category')
                ->whereNull('slug')
                ->orwhere('slug', '')
                ->update([
                    'slug' => DB::raw('REPLACE(LOWER(category_name), " ", "-")'),
                ]);
            DB::table('tbl_category')
                ->whereNull('meta_keyword')
                ->update([
                    'meta_keyword' => DB::raw('REPLACE(LOWER(category_name), " ", "-")'),
                ]);
            DB::table('tbl_category')
                ->whereNull('meta_description')
                ->update([
                    'meta_description' => DB::raw('REPLACE(LOWER(category_name), " ", "-")'),
                ]);
            DB::table('tbl_category')
                ->whereNull('meta_title')
                ->update([
                    'meta_title' => DB::raw('REPLACE(LOWER(category_name), " ", "-")'),
                ]);
        } else {
            Schema::create('tbl_category', function (Blueprint $table) {
                $table->id();
                $table->integer('language_id')->index('language_id'); // Nullable foreign key
                $table->string('category_name');
                $table->string('slug')->index('slug');
                $table->integer('row_order')->default(0);
                $table->string('image')->nullable();
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
        Schema::dropIfExists('tbl_category');
    }
};
