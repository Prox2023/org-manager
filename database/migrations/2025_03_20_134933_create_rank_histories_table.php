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
        Schema::create('rank_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_leader_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->string('role'); // The role they had during this period (e.g., 'team_leader', 'member', etc.)
            $table->string('rank_type')->nullable(); // The type of rank (e.g., 'captain', 'lieutenant', etc.)
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rank_histories');
    }
};
