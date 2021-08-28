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
    
}
