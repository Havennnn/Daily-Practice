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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lease_to_id')->nullable()->constrined('users')->nullOnDelete();
            $table->string('title', 255);
            $table->text('description');
            $table->enum('status', ['open','in_progress','on_hold','closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('low');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
