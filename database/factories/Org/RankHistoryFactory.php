<?php

namespace Database\Factories\Org;

use App\Models\Org\RankHistory;
use App\Models\Org\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RankHistoryFactory extends Factory
{
    protected $model = RankHistory::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'team_id' => Team::factory(),
            'team_leader_id' => User::factory(),
            'start_date' => now()->subMonths(3),
            'end_date' => null,
            'role' => 'member',
            'rank_type' => 'operator',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the rank history is for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Indicate that the rank history is for a specific team.
     */
    public function forTeam(Team $team): static
    {
        return $this->state(fn (array $attributes) => [
            'team_id' => $team->id,
        ]);
    }

    /**
     * Indicate that the rank history is under a specific team leader.
     */
    public function underTeamLeader(User $teamLeader): static
    {
        return $this->state(fn (array $attributes) => [
            'team_leader_id' => $teamLeader->id,
        ]);
    }

    /**
     * Indicate that the rank history has ended.
     */
    public function ended(): static
    {
        return $this->state(fn (array $attributes) => [
            'end_date' => now(),
        ]);
    }
} 