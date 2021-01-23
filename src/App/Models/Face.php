<?php

namespace Kvaksrud\AzureCognitiveServices\Ui\App\Models;

use Illuminate\Database\Eloquent\Model;
use Kvaksrud\AzureCognitiveServices\Api\Objects\FaceObject;

class Face extends Model
{
    protected $fillable = [
        'detection_id',
        'faceId',
        'persistedFaceId',
        'face_rectangle',
        'url',
        'color',
    ];

    protected $casts = [
        'face_rectangle' => 'array',
    ];

    public function detection(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Detection::class);
    }

    public function asFaceObject(): FaceObject
    {
        $return['faceId'] = $this->faceId;
        $return['persistedFaceId'] = $this->persistedFaceId;
        $return['faceRectangle'] = (object)$this->face_rectangle;
        return New FaceObject((object)$return);
    }

    public function persistedFace(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PersistedFaceId::class,'persistedFaceId','persistedFaceId');
    }

    public function candidate(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FaceCandidate::class);
    }
}
