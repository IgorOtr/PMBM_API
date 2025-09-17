<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chamado extends Model
{
    protected $table = 'chamados';

    protected $fillable = [
        'rand_id',
        'user_name',
        'user_cpf',
        'user_phone',
        'user_email',
        'user_address',
        'ticket_title',
        'ticket_subject',
        'ticket_address',
        'ticket_latitude',
        'ticket_longitude',
        'ticket_description',
        'ticket_file_name',
        'ticket_status',
        'ticket_service'
    ];

}
