<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameRegistration extends Model
{
    use HasFactory;

    protected $table = "game_registration";
    protected $primaryKey = "gr_id";
    protected $keyType = "int";
    public $timestamp = true;
    const CREATED_AT = 'gr_created_at';
    const UPDATED_AT = 'gr_updated_at';
    public $incrementing = true;

    protected $fillable = [
        'gr_gd_id',
        'gr_ui_id',
        'gr_is_outfield',
        'gr_amount',
        'gr_transaction_number',
        'gr_created_by',
        'gr_updated_by'
    ];
}
