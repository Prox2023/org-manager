<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Org\AuditLog;
use App\Models\Org\Organization;
use App\Models\Org\RankHistory;
use App\Models\Org\Team;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'organization_id',
        'current_team_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the organization that the user belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the current team that the user belongs to.
     */
    public function currentTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    /**
     * Get the rank histories for the user.
     */
    public function rankHistories(): HasMany
    {
        return $this->hasMany(RankHistory::class);
    }

    /**
     * Get the teams where the user is a team leader.
     */
    public function leadingTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'team_leader_id');
    }

    /**
     * Check if the user is a team leader of any team.
     */
    public function isTeamLeader(): bool
    {
        return $this->leadingTeams()->exists();
    }

    /**
     * Check if the user is a team leader of a specific team.
     */
    public function isTeamLeaderOf(Team $team): bool
    {
        return $this->id === $team->team_leader_id;
    }

    /**
     * Get the audit logs for the user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get the current rank history for the user.
     */
    public function currentRankHistory()
    {
        return $this->hasOne(RankHistory::class)->whereNull('end_date');
    }

    /**
     * Get all rank histories including soft deleted ones.
     */
    public function allRankHistories(): HasMany
    {
        return $this->rankHistories()->withTrashed();
    }

    /**
     * Get only soft deleted rank histories.
     */
    public function deletedRankHistories(): HasMany
    {
        return $this->rankHistories()->onlyTrashed();
    }

    /**
     * Get all teams led by the user, including soft deleted ones.
     */
    public function allLeadingTeams(): HasMany
    {
        return $this->leadingTeams()->withTrashed();
    }

    /**
     * Get only soft deleted teams led by the user.
     */
    public function deletedLeadingTeams(): HasMany
    {
        return $this->leadingTeams()->onlyTrashed();
    }

    /**
     * Get the organization even if soft deleted.
     */
    public function organizationWithTrashed(): BelongsTo
    {
        return $this->organization()->withTrashed();
    }

    /**
     * Get the current team even if soft deleted.
     */
    public function currentTeamWithTrashed(): BelongsTo
    {
        return $this->currentTeam()->withTrashed();
    }
}
