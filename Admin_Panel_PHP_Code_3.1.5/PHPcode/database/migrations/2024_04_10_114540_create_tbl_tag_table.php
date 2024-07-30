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
        if (Schema::hasTable('tbl_tag')) {
            // Add missing columns if not present
            Schema::table('tbl_tag', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_tag', 'slug')) {
                    $table->string('slug');
                }
                if (!Schema::hasColumn('tbl_tag', 'meta_title')) {
                    $table->text('meta_title')->nullable();
                }
                if (!Schema::hasColumn('tbl_tag', 'meta_description')) {
                    $table->text('meta_description')->nullable();
                }
                if (!Schema::hasColumn('tbl_tag', 'meta_keyword')) {
                    $table->text('meta_keyword')->nullable();
                }
                if (!Schema::hasColumn('tbl_tag', 'schema_markup')) {
                    $table->text('schema_markup')->nullable();
                }
                if (!Schema::hasColumn('tbl_tag', 'og_image')) {
                    $table->text('og_image')->nullable();
                }
                if (!Schema::hasColumn('tbl_tag', 'created_at')) {
                    $table->timestamps();
                }
            });
            // Update existing records
            DB::table('tbl_tag')
                ->whereNull('slug')
                ->orwhere('slug', '')
                ->update([
                    'slug' => DB::raw('REPLACE(LOWER(tag_name), " ", "-")'),
                ]);
            DB::table('tbl_tag')
                ->whereNull('meta_keyword')
                ->update([
                    'meta_keyword' => DB::raw('REPLACE(LOWER(tag_name), " ", "-")'),
                ]);
            DB::table('tbl_tag')
                ->whereNull('meta_description')
                ->update([
                    'meta_description' => DB::raw('REPLACE(LOWER(tag_name), " ", "-")'),
                ]);
            DB::table('tbl_tag')
                ->whereNull('meta_title')
                ->update([
                    'meta_title' => DB::raw('REPLACE(LOWER(tag_name), " ", "-")'),
                ]);
        } else {
            Schema::create('tbl_tag', function (Blueprint $table) {
                $table->id();
                $table->integer('language_id')->index('language_id'); // Nullable foreign key
                $table->string('tag_name', 100)->nullable();
                $table->string('slug')->index('slug');
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
        Schema::dropIfExists('tbl_tag');
    }
};
