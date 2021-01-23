<?php

namespace Kvaksrud\AzureCognitiveServices\Ui\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaceCandidate extends Model
{
    protected $fillable = [
        'face_id',
        'personId',
        'confidence',
    ];

    public function face(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Face::class);
    }

    public function person(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LargePersonGroupPerson::class,'personId','personId');
    }
}
