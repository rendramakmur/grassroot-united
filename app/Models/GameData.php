<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameData extends Model
{
    use HasFactory;

    protected $table = "game_data";
    protected $primaryKey = "gd_id";
    protected $keyType = "int";
    public $timestamp = true;
    const CREATED_AT = 'gd_created_at';
    const UPDATED_AT = 'gd_updated_at';
    public $incrementing = true;

    protected $fillable = [
        'gd_game_number',
        'gd_venue_name',
        'gd_venue_address',
        'gd_map_url',
        'gd_game_date',
        'gd_duration',
        'gd_goalkeeper_quota',
        'gd_outfield_quota',
        'gd_goalkeeper_price',
        'gd_outfield_price',
        'gd_notes',
        'gd_status',
        'gd_created_by',
        'gd_updated_by'
    ];
}
