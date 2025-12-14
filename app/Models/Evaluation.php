<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    /** @use HasFactory<\Database\Factories\EvaluationFactory> */
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'score',
        'classification',
        'reasoning',
        'insights',
        'recommendations',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'insights' => 'array',
            'recommendations' => 'array',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
