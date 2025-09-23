<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rural extends Model
{
    protected $table = 'rurals';

    protected $fillable = [
        'rand_id',
        'user_name',
        'user_cpf',
        'user_phone',
        'user_email',
        'user_address',
        'ticket_secreataria',
        'ticket_service',
        'ticket_subject',
        'ticket_title',
        'ticket_address',
        'ticket_latitude',
        'ticket_longitude',
        'ticket_description',
        'ticket_status',
    ];

    public function respostas()
    {
        return $this->morphMany(TicketAnswer::class, 'ticket');
    }
}
