<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department_id',
        'is_hod',
        'level',
        'gender',
        'student_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_hod' => 'boolean',
        ];
    }

    // Role helpers. HOD is represented as role=instructor + is_hod=true (see migration notes),
    // so it behaves as a distinct role in the app without altering the underlying role column.
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isInstructor(): bool { return $this->role === 'instructor'; }
    public function isStudent(): bool { return $this->role === 'student'; }
    public function isHod(): bool { return $this->role === 'instructor' && (bool) $this->is_hod; }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // Assessments this user (as student) has received.
    public function assessmentsAsStudent(): HasMany
    {
        return $this->hasMany(Assessment::class, 'student_id');
    }

    // Assessments this user (as instructor) has scored.
    public function assessmentsAsInstructor(): HasMany
    {
        return $this->hasMany(Assessment::class, 'instructor_id');
    }

    // Courses this instructor is assigned to teach/score.
    public function assignedCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'instructor_course', 'instructor_id', 'course_id')->withTimestamps();
    }

    // Courses this student is enrolled in (with per-enrollment report-card fields).
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_student', 'student_id', 'course_id')
            ->withPivot(['attendance_percentage', 'overall_feedback'])
            ->withTimestamps();
    }

    public function portfolio(): HasMany
    {
        return $this->hasMany(Portfolio::class, 'student_id');
    }

    // Work this student has uploaded for instructors to review.
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'student_id');
    }
}
