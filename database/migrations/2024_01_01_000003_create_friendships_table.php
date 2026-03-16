<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Si ta table friendships existe déjà, ignore cette migration.
     * Structure attendue : friendships(sender_id, receiver_id, accepted_at)
     */
    public function up(): void
    {
        if (!Schema::hasTable('friendships')) {
            Schema::create('friendships', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
                $table->timestamp('accepted_at')->nullable(); // null = en attente
                $table->timestamps();

                $table->unique(['sender_id', 'receiver_id']);
                $table->index(['receiver_id', 'accepted_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('friendships');
    }
};
