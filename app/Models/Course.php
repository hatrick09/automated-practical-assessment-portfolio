<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['trade_id', 'course_name', 'course_code', 'credit_hours'];

    public function trade(): BelongsTo
    {
        return $this->belongsTo(Trade::class);
    }

    public function rubrics(): HasMany
    {
        return $this->hasMany(Rubric::class);
    }

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'instructor_course', 'course_id', 'instructor_id')->withTimestamps();
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_student', 'course_id', 'student_id')
            ->withPivot(['attendance_percentage', 'overall_feedback'])
            ->withTimestamps();
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
