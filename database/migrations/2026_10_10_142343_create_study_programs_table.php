<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained()->restrictOnDelete();
            $table->string('name', 150);
            $table->string('level', 10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['faculty_id', 'name', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_programs');
    }
};
