<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameRegisteredPlayer extends Model
{
    use HasFactory;

    protected $table = "game_registered_player";
    protected $primaryKey = "grp_id";
    protected $keyType = "int";
    public $timestamp = true;
    const CREATED_AT = 'grp_created_at';
    const UPDATED_AT = 'grp_updated_at';
    public $incrementing = true;

    protected $fillable = [
        'grp_gd_id',
        'grp_ui_id',
        'grp_is_outfield',
        'grp_amount_paid',
        'grp_paid_at',
        'grp_transaction_number',
        'grp_created_by',
        'grp_updated_by'
    ];
}
