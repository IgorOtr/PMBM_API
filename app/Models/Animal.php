<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Animal extends Model
{
    protected $table = 'animals';

    protected $fillable = [
        'user_input',
        'user_name',
        'user_cpf',
        'user_email',
        'user_phone',
        'user_address',
        'service',
        'secretaria',
        'animal_status',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'user_input' => 'array', // converte JSON <-> array automaticamente
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Se não tiver ID, gera automaticamente
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
