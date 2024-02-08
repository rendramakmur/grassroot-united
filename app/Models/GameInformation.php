<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameInformation extends Model
{
    use HasFactory;

    protected $table = "game_information";
    protected $primaryKey = "gi_id";
    protected $keyType = "int";
    public $timestamp = true;
    const CREATED_AT = 'gi_created_at';
    const UPDATED_AT = 'gi_updated_at';
    public $incrementing = true;

    protected $fillable = [
        'gi_gd_id',
        'gi_info_type',
        'gi_description',
        'gi_created_by',
        'gi_updated_by'
    ];
}
