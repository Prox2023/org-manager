<?php

namespace App\Models\Org;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Organization extends Model
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
        'slug',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Organization $organization) {
            if (empty($organization->slug)) {
                $organization->slug = Str::slug($organization->name);
            }
        });
    }

    /**
     * Get the users that belong to the organization.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the teams that belong to the organization.
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Get the audit logs for the organization.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get all teams including soft deleted ones.
     */
    public function allTeams(): HasMany
    {
        return $this->teams()->withTrashed();
    }

    /**
     * Get only soft deleted teams.
     */
    public function deletedTeams(): HasMany
    {
        return $this->teams()->onlyTrashed();
    }

    /**
     * Get all users including soft deleted ones.
     */
    public function allUsers(): HasMany
    {
        return $this->users()->withTrashed();
    }

    /**
     * Get only soft deleted users.
     */
    public function deletedUsers(): HasMany
    {
        return $this->users()->onlyTrashed();
    }
}
