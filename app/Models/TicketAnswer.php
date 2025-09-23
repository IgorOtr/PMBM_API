<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAnswer extends Model
{
    protected $table = 'ticket_answer';

    protected $fillable = ['answer', 'secretaria', 'is_visible_to_user', 'responsible'];

    public function ticketAnswer()
    {
        return $this->morphTo();
    }
}
