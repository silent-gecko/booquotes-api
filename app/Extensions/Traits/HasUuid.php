<?php

namespace App\Extensions\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUuid
{
    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

    protected static function bootHasUuid() {
        static::creating(function (Model $model) {
            if (!$model->getKey() || !Str::isUuid($model->getKey())) {
                $model->setAttribute($model->getKeyName(), Str::uuid());
            }
        });
    }
}
