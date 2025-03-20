<?php

namespace Tests\Unit\Models;

use App\Models\Org\Organization;
use App\Models\Org\RankHistory;
use App\Models\Org\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_an_organization()
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $organization->id]);

        $this->assertInstanceOf(Organization::class, $user->organization);
        $this->assertEquals($organization->id, $user->organization->id);
    }

    #[Test]
    public function it_belongs_to_a_team()
    {
        $team = Team::factory()->create();
        $user = User::factory()->create(['current_team_id' => $team->id]);

        $this->assertInstanceOf(Team::class, $user->currentTeam);
        $this->assertEquals($team->id, $user->currentTeam->id);
    }

    #[Test]
    public function it_has_many_rank_histories()
    {
        $user = User::factory()->create();
        $rankHistories = RankHistory::factory()->count(3)->create([
            'user_id' => $user->id,
            'team_id' => Team::factory()->create()->id,
            'team_leader_id' => User::factory()->create()->id,
            'role' => 'member',
            'rank_type' => 'operator',
        ]);

        $this->assertCount(3, $user->rankHistories);
        $this->assertInstanceOf(RankHistory::class, $user->rankHistories->first());
        $this->assertTrue($user->rankHistories->contains($rankHistories->first()));
    }

    #[Test]
    public function it_has_many_leading_teams()
    {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create(['team_leader_id' => $user->id]);

        $this->assertCount(3, $user->leadingTeams);
        $this->assertInstanceOf(Team::class, $user->leadingTeams->first());
        $this->assertTrue($user->leadingTeams->contains($teams->first()));
    }

    #[Test]
    public function it_can_check_if_user_is_team_leader()
    {
        $user = User::factory()->create();
        $this->assertFalse($user->isTeamLeader());

        Team::factory()->create(['team_leader_id' => $user->id]);
        $this->assertTrue($user->isTeamLeader());
    }

    #[Test]
    public function it_can_check_if_user_is_team_leader_of_specific_team()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['team_leader_id' => $user->id]);
        $otherTeam = Team::factory()->create();

        $this->assertTrue($user->isTeamLeaderOf($team));
        $this->assertFalse($user->isTeamLeaderOf($otherTeam));
    }

    #[Test]
    public function it_can_get_current_rank_history()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $teamLeader = User::factory()->create();
        
        $oldHistory = RankHistory::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'team_leader_id' => $teamLeader->id,
            'role' => 'member',
            'rank_type' => 'operator',
            'end_date' => now()->subDay(),
        ]);
        
        $currentHistory = RankHistory::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'team_leader_id' => $teamLeader->id,
            'role' => 'member',
            'rank_type' => 'operator',
            'end_date' => null,
        ]);

        $this->assertEquals($currentHistory->id, $user->currentRankHistory->id);
        $this->assertNotEquals($oldHistory->id, $user->currentRankHistory->id);
        $this->assertNull($user->currentRankHistory->end_date);
    }

    #[Test]
    public function it_can_get_all_rank_histories_including_soft_deleted()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $teamLeader = User::factory()->create();
        
        $activeHistory = RankHistory::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'team_leader_id' => $teamLeader->id,
            'role' => 'member',
            'rank_type' => 'operator',
        ]);
        
        $deletedHistory = RankHistory::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'team_leader_id' => $teamLeader->id,
            'role' => 'member',
            'rank_type' => 'operator',
        ]);
        $deletedHistory->delete();

        $this->assertCount(2, $user->allRankHistories);
        $this->assertTrue($user->allRankHistories->contains($activeHistory));
        $this->assertTrue($user->allRankHistories->contains($deletedHistory));
    }

    #[Test]
    public function it_can_get_only_deleted_rank_histories()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $teamLeader = User::factory()->create();
        
        $activeHistory = RankHistory::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'team_leader_id' => $teamLeader->id,
            'role' => 'member',
            'rank_type' => 'operator',
        ]);
        
        $deletedHistory = RankHistory::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'team_leader_id' => $teamLeader->id,
            'role' => 'member',
            'rank_type' => 'operator',
        ]);
        $deletedHistory->delete();

        $this->assertCount(1, $user->deletedRankHistories);
        $this->assertTrue($user->deletedRankHistories->contains($deletedHistory));
        $this->assertFalse($user->deletedRankHistories->contains($activeHistory));
    }

    #[Test]
    public function it_can_get_organization_with_trashed()
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $organization->delete();

        $this->assertNotNull($user->organizationWithTrashed);
        $this->assertTrue($user->organizationWithTrashed->trashed());
    }

    #[Test]
    public function it_can_get_current_team_with_trashed()
    {
        $team = Team::factory()->create();
        $user = User::factory()->create(['current_team_id' => $team->id]);
        $team->delete();

        $this->assertNotNull($user->currentTeamWithTrashed);
        $this->assertTrue($user->currentTeamWithTrashed->trashed());
    }
} 