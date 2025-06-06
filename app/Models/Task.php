<?php

namespace App\Models;

use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use GeneratesUuid, HasFactory;

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

    public function scopeSummaryByDay($query)
    {
        return $query
            ->selectRaw('date_format(created_at, "%w") as step, date_format(created_at, "%Y-%m-%d") as day, COUNT(*) AS tasks, SUM(duration) as time')
            ->groupBy(['step', 'day'])
            ->orderBy('day', 'asc');
    }

    public function scopeSummaryByMonth($query)
    {
        return $query
            ->selectRaw('date_format(created_at, "%Y-%m") as month, COUNT(*) AS tasks, SUM(duration) as time')
            ->groupBy('month')
            ->orderBy('month', 'asc');
    }

    /**
     * Check if task was updated after creation
     */
    protected function isEdited(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            return $this->updated_at->greaterThan($this->created_at);
        });
    }

    /**
     * Check if task is of type meeting
     */
    protected function isMeeting(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            return $this->type === 'tmi:Meeting';
        });
    }

    public function toCsv()
    {
        $atoms = [
            $this->created_at->toDateTimeString(),
            'm',
            $this->duration,
            $this->description,
            $this->type,
        ];

        return implode(';', $atoms);
    }
}
