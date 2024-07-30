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
        if (Schema::hasTable('tbl_pages')) {
            Schema::table('tbl_pages', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_pages', 'schema_markup')) {
                    $table->text('schema_markup')->nullable();
                }
                if (!Schema::hasColumn('tbl_pages', 'meta_title')) {
                    $table->text('meta_title')->nullable();
                }
                if (!Schema::hasColumn('tbl_pages', 'og_image')) {
                    $table->text('og_image')->nullable();
                }
                if (!Schema::hasColumn('tbl_pages', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });

            DB::table('tbl_pages')
                ->whereNull('meta_title')
                ->update([
                    'meta_title' => DB::raw('REPLACE(LOWER(title), " ", "-")'),
                ]);

            DB::table('tbl_pages')
                ->where('slug', 'privacy-policy')
                ->update([
                    'page_type' => 'privacy-policy',
                ]);
            DB::table('tbl_pages')
                ->where('slug', 'terms-condition')
                ->update([
                    'page_type' => 'terms-condition',
                ]);
            DB::table('tbl_pages')
                ->where('slug', 'about-us')
                ->update([
                    'page_type' => 'about-us',
                ]);
            DB::table('tbl_pages')
                ->where('slug', 'contact-us')
                ->update([
                    'page_type' => 'contact-us',
                ]);

            DB::table('tbl_pages')
                ->whereNotIn('slug', ['privacy-policy', 'terms-condition', 'about-us', 'contact-us'])
                ->update(['page_type' => 'custom']);
        } else {
            Schema::create('tbl_pages', function (Blueprint $table) {
                $table->id();
                $table->integer('language_id')->index('language_id');
                $table->string('title', 500);
                $table->string('page_type', 50);
                $table->string('slug', 500);
                $table->mediumText('page_content')->nullable();
                $table->string('page_icon')->nullable();
                $table->string('og_image')->nullable();
                $table->text('schema_markup')->nullable();
                $table->text('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->text('meta_keywords')->nullable();
                $table->tinyInteger('is_custom')->default(1)->comment('0-default, 1-custom');
                $table->tinyInteger('is_termspolicy')->default(0);
                $table->tinyInteger('is_privacypolicy')->default(0);
                $table->tinyInteger('status')->default(1)->comment('0-deactive, 1-active');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_pages');
    }
};
