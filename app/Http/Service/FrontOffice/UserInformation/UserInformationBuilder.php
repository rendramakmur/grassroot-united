<?php

namespace App\Http\Service\FrontOffice\UserInformation;

use App\Http\Constant\GlobalParamSlug;
use App\Http\Service\General\CityService;
use App\Http\Service\General\GlobalParamService;
use App\Models\UserInformation;

class UserInformationBuilder
{
  public static function build(UserInformation $userInformation) 
  {
    return [
      'id' => $userInformation->ui_id,
      'userNumber' => $userInformation->ui_user_number,
      'firstName' => $userInformation->ui_first_name,
      'lastName' => $userInformation->ui_last_name,
      'email' => $userInformation->ui_email,
      'mobilePrefix' => GlobalParamService::getDefaultDataBySlug($userInformation->ui_mobile_prefix, GlobalParamSlug::MOBILE_PREFIX)->toArray(),
      'mobileNumber' => $userInformation->ui_mobile_number,
      'occupation' => GlobalParamService::getDefaultDataBySlug($userInformation->ui_occupation, GlobalParamSlug::OCCUPATION)->toArray(),
      'dateOfBirth' => $userInformation->ui_date_of_birth,
      'gender' => GlobalParamService::getDefaultDataBySlug($userInformation->ui_gender, GlobalParamSlug::GENDER)->toArray(),
      'photoProfile' => $userInformation->ui_photo_profile,
      'address' => $userInformation->ui_address,
      'city' => CityService::getDefaultDataById($userInformation->ui_city)->toArray(),
      'bodySize' => GlobalParamService::getDefaultDataBySlug($userInformation->ui_body_size, GlobalParamSlug::BODY_SIZE),
      'emailStatus' => $userInformation->ui_email_status
    ];
  }
}