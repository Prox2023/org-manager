<?php

namespace Database\Seeders;

use App\Models\Org\RankHistory;
use App\Models\Org\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RankHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alphaTeam = Team::where('name', 'Alpha Team')->first();
        $teamLeader = User::where('email', 'team-leader@example.com')->first();
        $member = User::where('email', 'member@example.com')->first();

        // Create team leader rank history
        RankHistory::create([
            'user_id' => $teamLeader->id,
            'team_id' => $alphaTeam->id,
            'team_leader_id' => null, // No one assigned them as they're the first leader
            'start_date' => now(),
            'end_date' => null,
            'role' => 'team_leader',
            'rank_type' => 'captain',
            'notes' => 'Initial team leader assignment',
        ]);

        // Create member rank history
        RankHistory::create([
            'user_id' => $member->id,
            'team_id' => $alphaTeam->id,
            'team_leader_id' => $teamLeader->id,
            'start_date' => now(),
            'end_date' => null,
            'role' => 'member',
            'rank_type' => 'operator',
            'notes' => 'Initial team member assignment',
        ]);

        // Update the member's organization and current team
        $member->update([
            'organization_id' => $alphaTeam->organization_id,
            'current_team_id' => $alphaTeam->id,
        ]);
    }
}
