<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameGallery extends Model
{
    use HasFactory;

    protected $table = "game_gallery";
    protected $primaryKey = "gg_id";
    protected $keyType = "int";
    public $timestamp = true;
    const CREATED_AT = 'gg_created_at';
    const UPDATED_AT = 'gg_updated_at';
    public $incrementing = true;

    protected $fillable = [
        'gg_gd_id',
        'gg_image_url',
        'gg_alt_image',
        'gg_created_by',
        'gg_updated_by'
    ];
}
