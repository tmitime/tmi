<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;

class Project extends Model
{
    use HasFactory, GeneratesUuid;

    protected $fillable = [
        'name', 'description', 'start_at', 'end_at', 'working_days', 'team_id',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
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
     * Members of the team that has access because of team membership
     */
    public function teamMembers()
    {
        return $this->team()->first()->users();
    }

    /**
     * All members that has access to this project, via direct membership or team membership
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
     * @param \App\Models\User $user
     * @param int $role
     * @return bool
     */
    public function hasMember(User $user, $role = null)
    {
        return $this->members()
            ->wherePivot('user_id', $user->getKey())
            ->when($role, function($query, $r) use($role){
                if(is_array($role)){
                    return $query->whereIn('members.role', $role);
                }
                return $query->where('members.role', $role);
            })
            ->exists();
    }

    /**
     * Filter projects that has a specific user as member
     * @param mixed $query
     * @param \App\Models\User $user
     * @param array|int $role
     */
    public function scopeWithMember($query, User $user, $role = null)
    {
        return $query->whereHas('members', function ($builder) use($user, $role){
           $builder 
            ->where('members.user_id', $user->getKey())
            ->when($role, function($query, $r) use($role){
                if(is_array($role)){
                    return $query->whereIn('members.role', $role);
                }
                return $query->where('members.role', $role);
            });
        });
    }

    public function owner()
    {
        return $this->hasOne(Member::class)->ofMany()->where('members.role', Member::ROLE_OWNER);
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
    
    public function latestTasks()
    {
        return $this->tasks()->orderBy('created_at', 'desc')->limit(10);
    }

    public function getIsOngoingAttribute()
    {
        $today = today();
        return $this->start_at->lessThanOrEqualTo($today) &&
         (is_null($this->end_at) 
         || (!is_null($this->end_at) && $this->end_at->greaterThan($today)));
    }

}
