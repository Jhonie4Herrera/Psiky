<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['user_id', 'asunto', 'descripcion', 'priority', 'status', 'type'];
}