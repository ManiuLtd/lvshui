<?php

namespace App\Models;

use App\Models\Model;

class FanTicket extends Model
{
    protected $table = 'fan_tickets';
    protected $guarded=[];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class,'ticket_id','id');
    }
}
