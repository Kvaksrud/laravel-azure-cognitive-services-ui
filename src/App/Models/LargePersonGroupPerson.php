<?php

namespace Kvaksrud\AzureCognitiveServices\Ui\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LargePersonGroupPerson extends Model
{
    protected $fillable = [
        'id',
        'personId',
        'large_person_group_id',
        'persistedFaceIds',
        'name',
        'userData'
    ];

    protected $casts = [
        'persistedFaceIds' => 'array',
        'userData' => 'array',
    ];

    protected $with = [
        'largePersonGroup',
        'persistedFaceIds'
    ];

    public function largePersonGroup(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LargePersonGroup::class);
    }

    public function persistedFaceIds(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PersistedFaceId::class);
    }
}

