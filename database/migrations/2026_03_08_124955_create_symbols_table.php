<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('symbols', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->uuid('id')->primary();
            $table->string('symbol', 32)->unique(); // e.g. {R}, {2/W}, {W/P}
            $table->string('svg_uri', 128)->nullable(); // scryfall CDN URL, used for re-downloading
            $table->string('loose_variant', 16)->nullable(); // e.g. "R"
            $table->string('english', 128); // e.g. "one red mana"
            $table->boolean('represents_mana')->default(false);
            $table->boolean('appears_in_mana_costs')->default(false);
            $table->boolean('transposable')->default(false);
            $table->boolean('hybrid')->default(false);
            $table->boolean('phyrexian')->default(false);
            $table->boolean('funny')->default(false);
            $table->unsignedInteger('cmc')->nullable();
            $table->string('colors', 6);  // concatenated color chars, e.g. "R" or "WU"
            $table->string('path', 32);   // local filename, e.g. "W.svg" or "2-W.svg"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('symbols');
    }
};
