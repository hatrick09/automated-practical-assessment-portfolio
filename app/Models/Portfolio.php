<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Portfolios are compiled dynamically from Assessments + Evidence.
// This model only stores a generation timestamp and optional public share token.
class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'share_token', 'generated_date'];

    protected function casts(): array
    {
        return ['generated_date' => 'datetime'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
