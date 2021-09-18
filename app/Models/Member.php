<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Member extends Pivot
{
    public const ROLE_OWNER = 10;
    
    public const ROLE_DEVELOPER = 20;
    
    public const ROLE_GUEST = 30;

    /**
     * Autoincrement primary key configured
     *
     * @var bool
     */
    public $incrementing = true;

    protected $table = "members";

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['user'];


    public function getRoleLabelAttribute($value)
    {
        switch ($this->role) {
            case self::ROLE_OWNER:
                return 'owner';
                break;
            
            case self::ROLE_DEVELOPER:
                return 'developer';
                break;
            
            default:
                return 'guest';
                break;
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function personalTeam()
    {
        return $this->user->personalTeam();
    }
    
}
