<?php

use App\Enums\BinderType;
use App\Models\Container;
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
        Schema::create('containers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->uuid('id')->primary();
            $table->string('name', Container::NAME_MAX);
            $table->string('description', Container::DESCRIPTION_MAX)->nullable();
            $table->string('type', 64)
                ->default(BinderType::Binder->value);
            $table->string('custom_type', Container::CUSTOM_TYPE_MAX)->nullable();
            $table->unsignedSmallInteger('sort_order');
            $table->foreignUuid('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignUuid('default_card_id')
                ->nullable()
                ->constrained('default_cards')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
