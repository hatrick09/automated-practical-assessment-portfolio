<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'course_id', 'rubric_id', 'title', 'file_path', 'description', 'status', 'submitted_at', 'type',
    ];

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function rubric(): BelongsTo
    {
        return $this->belongsTo(Rubric::class);
    }

    // Same dual-purpose convention as Evidence: file_path holds either a storage
    // path (type=file) or a raw URL (type=video_link/code_link).
    public function url(): string
    {
        return $this->type === 'file'
            ? Storage::disk('public')->url($this->file_path)
            : $this->file_path;
    }

    public function isLink(): bool
    {
        return $this->type !== 'file';
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'video_link' => 'Video',
            'code_link' => 'Code / GitHub',
            default => 'File',
        };
    }
}
