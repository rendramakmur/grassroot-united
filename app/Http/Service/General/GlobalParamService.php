<?php

namespace App\Http\Service\General;

use App\Http\Response\General\DefaultData;
use App\Models\GlobalParam;

class GlobalParamService
{
  public static function getDefaultDataBySlug($codeId, $slug)
  {
    if (!$codeId || !$slug) {
      return new DefaultData(null, null);
    }

    $data = GlobalParam::where('mgp_slug', $slug)
    ->where('mgp_code_id', $codeId)
    ->first();

    return new DefaultData($data->mgp_code_id, $data->mgp_description);
  }
}