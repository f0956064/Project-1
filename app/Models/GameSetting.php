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

    public function banners()
    {
        return $this->hasMany(File::class, 'entity_id', 'id')->where('entity_type', File::$fileType['home_banner']['type']);
    }
}
