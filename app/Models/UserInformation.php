<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInformation extends Model
{
    use HasFactory;

    protected $table = "user_information";
    protected $primaryKey = "ui_id";
    protected $keyType = "int";
    public $timestamp = true;
    const CREATED_AT = 'ui_created_at';
    const UPDATED_AT = 'ui_updated_at';
    public $incrementing = true;

    protected $fillable = [
        'ui_user_type',
        'ui_first_name',
        'ui_last_name',
        'ui_email',
        'ui_password',
        'ui_mobile_prefix',
        'ui_mobile_number',
        'ui_occupation',
        'ui_date_of_birth',
        'ui_gender',
        'ui_photo_profile',
        'ui_address',
        'ui_city',
        'ui_body_size',
        'ui_activation_code',
        'ui_email_status',
        'ui_verified_at',
        'ui_created_by',
        'ui_updated_by'
    ];
}
