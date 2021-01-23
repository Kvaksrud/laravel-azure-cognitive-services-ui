<?php

namespace Kvaksrud\AzureCognitiveServices\Ui\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LargePersonGroup extends Model
{
    protected $fillable = [
        'largePersonGroupId',
        'name',
        'userData'
    ];

    protected $casts = [
        'userData' => 'array'
    ];

    protected $with = [
        'trainingStatus'
    ];

    public function people(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LargePersonGroupPerson::class);
    }

    public function isTeachable(): bool
    {
        $peopleIds = LargePersonGroupPerson::where('large_person_group_id','=',$this->id)->pluck('id');
        if(LargePersonGroupPerson::whereHas('persistedFaceIds', function ($q) use ($peopleIds) {
            return $q->whereIn('large_person_group_person_id',$peopleIds)->where('trained','=',false);
        })->count() > 0)
            return true;
        return false;
    }

    public function trainingStatus(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(LargePersonGroupTrainingStatus::class);
    }
}
