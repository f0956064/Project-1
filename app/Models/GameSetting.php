<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSetting extends Model
{
    protected $table = 'game_settings';

    protected $fillable = [
        'show_games',
        'deposit',
        'withdrawal',
    ];

    protected $hidden = [
        'updated_at',
    ];
}
