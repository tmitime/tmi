<?php

namespace App\Models;

use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\Jetstream;

class Project extends Model
{
    use GeneratesUuid, HasFactory;

    protected $fillable = [
        'name', 'description', 'start_at', 'end_at', 'working_days', 'team_id',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['team'];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public static function findUsingRouteKey($value)
    {
        $instance = new self;

        return $instance->where($instance->getRouteKeyName(), $value)->first();
    }

    /**
     * Direct members of this project explicitly added
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'members')
            ->using(Member::class)
            ->as('membership')
            ->withTimestamps()
            ->withPivot('role');
    }

    /**
     * Inherited members from the team
     *
     * Note: owner of the team is excluded
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teamMembers()
    {
        return $this->belongsToMany(
            Jetstream::userModel(),
            Jetstream::membershipModel(), 'team_id', null, 'team_id')
            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    /**
     * All members that has access to this project,
     * via direct membership or team membership
     *
     * @return \Illuminate\Support\Collection|\App\Models\User[]
     */
    public function allMembers()
    {
        return $this->members->merge($this->teamMembers);
    }

    /**
     * Check if a user is a member of the project
     *
     * If the role is specified, the check ensure that the user has the given role
     *
     * @param  int  $role
     * @return bool
     */
    public function hasMember(User $user, $role = null)
    {
        return $this->members()
            ->wherePivot('user_id', $user->getKey())
            ->when($role, function ($query, $r) use ($role) {
                if (is_array($role)) {
                    return $query->whereIn('members.role', $role);
                }

                return $query->where('members.role', $role);
            })
            ->exists();
    }

    public function hasMemberWithEmail($email)
    {
        return $this->allMembers()->contains(function ($user) use ($email) {
            return $user->email === $email;
        });
    }

    /**
     * Remove the given user from the project.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function removeUser($user)
    {
        $this->members()->detach($user);
    }

    /**
     * Filter projects that has a specific user as member
     *
     * @param  mixed  $query
     * @param  array|int  $role
     */
    public function scopeWithMember($query, User $user, $role = null)
    {
        return $query->whereHas('members', function ($builder) use ($user, $role) {
            $builder
                ->where('members.user_id', $user->getKey())
                ->when($role, function ($query, $r) use ($role) {
                    if (is_array($role)) {
                        return $query->whereIn('members.role', $role);
                    }

                    return $query->where('members.role', $role);
                });
        });
    }

    /**
     * The owner of this project.
     *
     * The user that was first added with Member::ROLE_OWNER
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function owner()
    {
        return $this->hasOne(Member::class)->ofMany([
            'created_at' => 'min',
        ], function ($query) {
            $query->where('members.role', Member::ROLE_OWNER);
        });
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function scopeOfTeam($query, Team $team)
    {
        return $query->where('team_id', $team->getKey());
    }

    /**
     * Filter projects showing only shared with the specified user that are not
     * part of their teams
     */
    public function scopeSharedTo($query, User $user)
    {
        $teams = $user->allTeams()->pluck('id');

        return $query->withMember($user)
            ->whereNotIn('team_id', $teams->toArray());
    }

    public function latestTasks()
    {
        return $this->tasks()->orderBy('created_at', 'desc')->limit(10);
    }

    protected function isOngoing(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            $today = today();

            return $this->start_at->lessThanOrEqualTo($today) &&
             (is_null($this->end_at)
             || (! is_null($this->end_at) && $this->end_at->greaterThan($today)));
        });
    }

    /**
     * Purge all of the project's resources.
     *
     * @return void
     */
    public function purge()
    {
        DB::transaction(function () {
            $this->tasks()->delete();

            $this->members()->detach();

            $this->delete();
        });
    }

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }
}
