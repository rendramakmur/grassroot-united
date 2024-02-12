<?php

namespace App\Http\Service\General;

use App\Http\Response\General\DefaultData;
use App\Models\City;

class CityService
{
  public static function getDefaultDataById($id)
  {
    if (!$id) {
      return new DefaultData(null, null);
    }
    $data = City::where('mc_id', $id)->first();

    return new DefaultData($data->mc_id, $data->mc_name);
  }
}