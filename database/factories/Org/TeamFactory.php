<?php

namespace Database\Factories\Org;

use App\Models\Org\Organization;
use App\Models\Org\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'organization_id' => Organization::factory(),
            'team_leader_id' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the team belongs to a specific organization.
     */
    public function forOrganization(Organization $organization): static
    {
        return $this->state(fn (array $attributes) => [
            'organization_id' => $organization->id,
        ]);
    }

    /**
     * Indicate that the team has a specific leader.
     */
    public function withLeader(User $leader): static
    {
        return $this->state(fn (array $attributes) => [
            'team_leader_id' => $leader->id,
        ]);
    }
} 