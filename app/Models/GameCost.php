<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameCost extends Model
{
    use HasFactory;

    protected $table = "game_cost";
    protected $primaryKey = "gc_id";
    protected $keyType = "int";
    public $timestamp = true;
    const CREATED_AT = 'gc_created_at';
    const UPDATED_AT = 'gc_updated_at';
    public $incrementing = true;

    protected $fillable = [
        'gc_gd_id',
        'gc_description',
        'gc_amount',
        'gc_created_by',
        'gc_updated_by'
    ];
}
