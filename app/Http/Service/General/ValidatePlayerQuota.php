<?php

namespace App\Http\Service\General;

use App\Http\Constant\ApiCode;
use App\Http\Response\General\BasicResponse;
use App\Models\GameData;
use App\Models\GameRegisteredPlayer;

class ValidatePlayerQuota
{
  use BasicResponse;

  public static function validate(GameData $game) {
    $registeredPlayer = GameRegisteredPlayer::where('grp_gd_id', $game->gd_id);
    $outfieldCount = $registeredPlayer->where('grp_is_outfield', true)->count();
    $goalkeeperCount = $registeredPlayer->where('grp_is_outfield', false)->count();

    if ($outfieldCount >= $game->gd_outfield_quota) {
        self::buildErrorResponse("Outfield player quota already full", ApiCode::BAD_REQUEST);
    }
    if ($goalkeeperCount >= $game->gd_goalkeeper_quota) {
        self::buildErrorResponse("Goalkeeper quota already full", ApiCode::BAD_REQUEST);
    }
  }
}