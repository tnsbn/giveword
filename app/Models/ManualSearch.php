<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ManualSearch extends Model
{
    use Notifiable;

    protected $table = 'manual_search';
    public $timestamps = false;
    protected $fillable = [
        'word_id', 'weight',
    ];
}
