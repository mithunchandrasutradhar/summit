<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwardNominationReview extends Model
{
    use HasFactory;

    public const DECISIONS = ['no_decision', 'shortlist', 'reject'];

    protected $fillable = [
        'nomination_id',
        'reviewer_id',
        'score',
        'comments',
        'decision',
    ];

    public function nomination()
    {
        return $this->belongsTo(AwardNomination::class, 'nomination_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
