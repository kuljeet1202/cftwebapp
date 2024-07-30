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
        if (Schema::hasTable('tbl_live_streaming')) {
            Schema::table('tbl_live_streaming', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_live_streaming', 'meta_title')) {
                    $table->text('meta_title')->nullable();
                }
                if (!Schema::hasColumn('tbl_live_streaming', 'meta_description')) {
                    $table->text('meta_description')->nullable();
                }
                if (!Schema::hasColumn('tbl_live_streaming', 'meta_keyword')) {
                    $table->text('meta_keyword')->nullable();
                }
                if (!Schema::hasColumn('tbl_live_streaming', 'schema_markup')) {
                    $table->text('schema_markup')->nullable();
                }
                if (!Schema::hasColumn('tbl_live_streaming', 'created_at')) {
                    $table->timestamps();
                }
            });

            DB::table('tbl_live_streaming')
                ->whereNull('meta_keyword')
                ->update([
                    'meta_keyword' => DB::raw('REPLACE(LOWER(title), " ", "-")'),
                ]);

            DB::table('tbl_live_streaming')
                ->whereNull('meta_description')
                ->update([
                    'meta_description' => DB::raw('REPLACE(LOWER(title), " ", "-")'),
                ]);

            DB::table('tbl_live_streaming')
                ->whereNull('meta_title')
                ->update([
                    'meta_title' => DB::raw('REPLACE(LOWER(title), " ", "-")'),
                ]);
        } else {
            Schema::create('tbl_live_streaming', function (Blueprint $table) {
                $table->id();
                $table->integer('language_id')->index('language_id'); // Nullable foreign key
                $table->text('title');
                $table->string('image')->nullable();
                $table->string('type', 20)->nullable();
                $table->text('url')->nullable();
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
        Schema::dropIfExists('tbl_live_streaming');
    }
};
