<?php

namespace App\Models;

use Carbon\Carbon;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory, GeneratesUuid;

    protected $fillable = [
        'duration',
        'description',
        'user_id',
        'project_id',
        'type',
        'created_at',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'tmi:Task',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeMeeting($query)
    {
        // TODO: get tasks that are = to tmi:Meeting or subclass of it
        return $query->whereIn('type', ['tmi:Meeting']);
    }
    
    public function scopeNotMeeting($query)
    {
        // TODO: get tasks that are = to tmi:Meeting or subclass of it
        return $query->whereNotIn('type', ['tmi:Meeting']);
    }

    public function scopePeriod($query, $start, $end)
    {
        return $query
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end);
    }


    /**
     * Check if task was updated after creation
     */
    public function getIsEditedAttribute()
    {
        return $this->updated_at->greaterThan($this->created_at);
    }
    
    /**
     * Check if task is of type meeting
     */
    public function getIsMeetingAttribute()
    {
        return $this->type === 'tmi:Meeting';
    }
}
