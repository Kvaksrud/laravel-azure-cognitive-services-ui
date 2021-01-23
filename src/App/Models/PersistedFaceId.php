<?php

namespace Kvaksrud\AzureCognitiveServices\Ui\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersistedFaceId extends Model
{
    protected $fillable = [
        'large_person_group_person_id',
        'persistedFaceId',
        'trained'
    ];

    protected $casts = [
        'trained' => 'bool'
    ];

    public function largePersonGroupPerson(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LargePersonGroupPerson::class);
    }
}
