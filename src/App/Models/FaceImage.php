<?php

namespace Kvaksrud\AzureCognitiveServices\Ui\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaceImage extends Model
{
    protected $fillable = [
        'detection_id',
        'url',
        'original_path',
        'detection_path'
    ];

    public function detection(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Detection::class);
    }
}
