<?php

namespace Kvaksrud\AzureCognitiveServices\Ui\App\Models;

use Illuminate\Database\Eloquent\Model;

class Detection extends Model
{
    protected $fillable = [

    ];

    protected $with = [
        'faces',
        'image'
    ];

    public function faces(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Face::class);
    }

    public function image(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FaceImage::class);
    }
}
