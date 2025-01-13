<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Laravel\Jetstream\Jetstream;

class Member extends Pivot
{
    // todo: rename to ProjectMembership

    public const ROLE_OWNER = 10;

    public const ROLE_MAINTAINER = 15;

    public const ROLE_COLLABORATOR = 20;

    public const ROLE_GUEST = 30;

    public const ROLE_OBSERVER = 40;

    protected static $jetstreamRoleMap = [
        'admin' => self::ROLE_MAINTAINER,
        'collaborator' => self::ROLE_COLLABORATOR,
        'guest' => self::ROLE_GUEST,
        'observer' => self::ROLE_OBSERVER,
    ];

    protected static $jetstreamInvertedRoleMap = [
        self::ROLE_OWNER => 'owner',
        self::ROLE_MAINTAINER => 'admin',
        self::ROLE_COLLABORATOR => 'collaborator',
        self::ROLE_GUEST => 'guest',
        self::ROLE_OBSERVER => 'observer',
    ];

    /**
     * Autoincrement primary key configured
     *
     * @var bool
     */
    public $incrementing = true;

    protected $table = 'members';

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['user'];

    protected function roleLabel(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            $name = self::$jetstreamInvertedRoleMap[$this->role] ?? null;
            if (! $name) {
                return null;
            }

            return Jetstream::findRole($name)->name ?? $name;
        });
    }

    protected function source(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            return __('Project');
        });
    }

    public function isTeamMember()
    {
        return false;
    }

    public function isProjectMember()
    {
        return true;
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

    public static function convertJetstreamRole($role)
    {
        return self::$jetstreamRoleMap[$role] ?? self::ROLE_GUEST;
    }
}
