<?php

namespace Database\Seeders;

use App\Models\Org\Organization;
use App\Models\Org\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organization = Organization::where('slug', 'dutch-navy-seals')->first();
        $teamLeader = User::where('email', 'team-leader@example.com')->first();

        $teams = [
            [
                'name' => 'Alpha Team',
                'description' => 'Primary assault team specializing in maritime operations.',
                'organization_id' => $organization->id,
                'team_leader_id' => $teamLeader->id,
            ],
            [
                'name' => 'Bravo Team',
                'description' => 'Support team specializing in reconnaissance and intelligence gathering.',
                'organization_id' => $organization->id,
                'team_leader_id' => null,
            ],
            [
                'name' => 'Charlie Team',
                'description' => 'Specialized team focusing on underwater operations and demolitions.',
                'organization_id' => $organization->id,
                'team_leader_id' => null,
            ],
        ];

        foreach ($teams as $team) {
            Team::create($team);
        }

        // Update the team leader's organization and current team
        $teamLeader->update([
            'organization_id' => $organization->id,
            'current_team_id' => Team::where('name', 'Alpha Team')->first()->id,
        ]);
    }
}
