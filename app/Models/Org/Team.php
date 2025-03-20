<?php

namespace App\Models\Org;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'organization_id',
        'team_leader_id',
    ];

    /**
     * Get the organization that owns the team.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the team leader of the team.
     */
    public function teamLeader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'team_leader_id');
    }

    /**
     * Get the users that belong to the team through rank histories.
     */
    public function rankHistories(): HasMany
    {
        return $this->hasMany(RankHistory::class);
    }

    /**
     * Get the current users in the team.
     */
    public function currentMembers()
    {
        return $this->rankHistories()->whereNull('end_date')->get()->pluck('user');
    }

    /**
     * Get the audit logs for the team.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
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
     * Get all members including those with deleted rank histories.
     */
    public function allMembers()
    {
        return $this->allRankHistories()->get()->pluck('user')->unique();
    }

    /**
     * Get the team leader even if soft deleted.
     */
    public function teamLeaderWithTrashed(): BelongsTo
    {
        return $this->teamLeader()->withTrashed();
    }
}
