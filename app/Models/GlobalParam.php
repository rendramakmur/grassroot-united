<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalParam extends Model
{
    use HasFactory;

    protected $table = "mr_global_param";
    protected $primaryKey = "mgp_id";
    protected $keyType = "int";
    public $incrementing = true;
}
