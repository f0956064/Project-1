<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSlotResult extends Model
{
    protected $table = 'game_slot_results';

    protected $fillable = [
        'game_slot_id',
        'result_date',
        'result_value',
    ];

    protected $hidden = [
        'updated_at',
    ];

    public function slot()
    {
        return $this->belongsTo(GameSlot::class, 'game_slot_id');
    }
}
