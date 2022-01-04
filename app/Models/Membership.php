<?php

namespace App\Models;

use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Membership as JetstreamMembership;

class Membership extends JetstreamMembership
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    public function getSourceAttribute($value)
    {
        return __('Team');
    }
    
    public function getRoleLabelAttribute($value)
    {
        return optional(Jetstream::findRole($this->role))->name ?? null;
    }

    
    public function isTeamMember()
    {
        return true;
    }
    
    public function isProjectMember()
    {
        return false;
    }
}
