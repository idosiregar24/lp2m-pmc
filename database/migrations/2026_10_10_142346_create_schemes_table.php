<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schemes', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->string('name', 150);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['type', 'name']);
            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schemes');
    }
};
