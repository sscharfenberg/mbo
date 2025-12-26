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
        Schema::create('sets', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->uuid('id')->primary();
            $table->string('code', length: 6)->unique();
            $table->string('name', length: 255)->unique();
            $table->string('block_code', length: 6)->nullable();
            $table->string('block', length: 255)->nullable();
            $table->string('parent_set_code', length: 6)->nullable();
            $table->unsignedSmallInteger('card_count')->default(0);
            $table->unsignedSmallInteger('printed_size')->default(0);
            $table->enum('set_type', config('binder.scryfall.set_types'))->nullable();
            $table->boolean('digital')->default(false);
            $table->string('scryfall_uri', length: 64);
            $table->string('icon', length: 64);
            $table->date('released_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sets');
    }
};
