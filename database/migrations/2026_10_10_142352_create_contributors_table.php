<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Peneliti / pengabdi / penulis / pemilik KI for every research-data module (PRD §6.1).
     * user_id set = internal lecturer; NULL = outside person or institution (name required).
     */
    public function up(): void
    {
        Schema::create('contributors', function (Blueprint $table) {
            $table->id();
            $table->morphs('contributable');
            $table->foreignId('user_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('name', 150)->nullable();
            $table->string('affiliation', 150)->nullable();
            $table->unsignedTinyInteger('sort_order');
            $table->timestamps();

            $table->unique(['contributable_type', 'contributable_id', 'sort_order'], 'contributors_order_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contributors');
    }
};
