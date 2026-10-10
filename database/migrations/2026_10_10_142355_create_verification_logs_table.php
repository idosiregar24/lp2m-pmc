<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Append-only history of verification status changes (PRD §4.11) — no updated_at.
     */
    public function up(): void
    {
        Schema::create('verification_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('verifiable');
            $table->string('from_status', 20)->nullable();
            $table->string('to_status', 20);
            $table->text('note')->nullable();
            $table->foreignId('acted_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_logs');
    }
};
