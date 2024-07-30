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
        if (Schema::hasTable('tbl_subcategory')) {
            Schema::table('tbl_subcategory', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_subcategory', 'slug')) {
                    $table->string('slug');
                }
                if (!Schema::hasColumn('tbl_subcategory', 'row_order')) {
                    $table->integer('row_order')->default(0);
                }
                if (!Schema::hasColumn('tbl_subcategory', 'created_at')) {
                    $table->timestamps();
                }
            });
            // Update existing records
            DB::table('tbl_subcategory')
                ->whereNull('slug')
                ->orwhere('slug', '')
                ->update([
                    'slug' => DB::raw('REPLACE(LOWER(subcategory_name), " ", "-")'),
                ]);
        } else {
            Schema::create('tbl_subcategory', function (Blueprint $table) {
                $table->id();
                $table->integer('language_id')->index('language_id'); // Nullable foreign key
                $table->integer('category_id')->index('category_id'); // Using integer instead of int(11) for category_id
                $table->string('subcategory_name');
                $table->string('slug')->index('slug');
                $table->integer('row_order')->default(0);
                $table->string('image');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_subcategory');
    }
};
