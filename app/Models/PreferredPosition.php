<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreferredPosition extends Model
{
    use HasFactory;

    protected $table = "preferred_position";
    protected $primaryKey = "pp_id";
    protected $keyType = "int";
    public $timestamp = true;
    const CREATED_AT = 'pp_created_at';
    const UPDATED_AT = 'pp_updated_at';
    public $incrementing = true;

    protected $fillable = [
        'pp_ui_id',
        'pp_position',
        'pp_created_by',
        'pp_updated_by'
    ];
}
