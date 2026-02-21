<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameWinner extends Model
{
    protected $table = 'game_winners';

    protected $fillable = [
        'user_id',
        'game_id',
        'slot_id',
        'game_mode_id',
        'bet_amount',
        'winning_amount',
        'guess_number',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
