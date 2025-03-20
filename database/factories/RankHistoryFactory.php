<?php

namespace Database\Factories;

use App\Models\RankHistory;
use App\Models\User;
use App\Models\Team;
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
            'start_date' => now(),
            'end_date' => null,
        ];
    }
} 