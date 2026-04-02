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
        Schema::create('legalities', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->foreignUuid('oracle_card_id')
                ->references('id')
                ->on('oracle_cards')
                ->cascadeOnDelete();
            $table->string('format', 32);
            $table->string('legality', 16);

            $table->index(['format', 'legality']);
            $table->index('oracle_card_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legalities');
    }
};
