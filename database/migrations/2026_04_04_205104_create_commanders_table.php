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
        Schema::create('commanders', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->foreignUuid('deck_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignUuid('default_card_id')
                ->constrained('default_cards')
                ->cascadeOnDelete();
            $table->boolean('is_partner')->default(false);

            $table->primary(['deck_id', 'default_card_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commanders');
    }
};
