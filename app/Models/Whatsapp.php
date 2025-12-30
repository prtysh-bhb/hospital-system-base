<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\WhatsappTemplating;
class Whatsapp extends Model
{
    protected $casts = [
        'template' => WhatsappTemplating::class,
    ];
}
