<?php

namespace Kvaksrud\AzureCognitiveServices\Ui\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LargePersonGroupTrainingStatus extends Model
{
    protected $fillable = [
        'large_person_group_id',
        'status',
        'createdDateTime',
        'lastActionDateTime',
        'lastSuccessfulTrainingDateTime',
    ];

    public function largePersonGroup(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LargePersonGroup::class);
    }
}
