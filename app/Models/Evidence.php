<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Evidence extends Model
{
    use HasFactory;

    protected $fillable = ['assessment_id', 'file_path', 'description', 'type'];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    // For type=file, file_path holds a storage-relative path (resolve to a public URL).
    // For type=video_link/code_link, file_path holds the raw URL directly.
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
