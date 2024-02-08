<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $table = "mr_city";
    protected $primaryKey = "mc_id";
    protected $keyType = "int";
    public $incrementing = true;
}
