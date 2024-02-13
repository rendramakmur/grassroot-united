<?php

namespace App\Http\Service\BackOffice\GameData;

use App\Http\Constant\GlobalParamSlug;
use App\Http\Service\General\GlobalParamService;
use App\Models\GameData;
use App\Models\GameGallery;
use App\Models\GameInformation;
use App\Models\GameRegisteredPlayer;
use App\Models\GameRegistration;
use App\Models\UserInformation;

class GameDataBuilder
{
  public static function build(GameData $gameData) {
    $gameDetail = [
      'id' => $gameData->gd_id,
      'gameNumber' => $gameData->gd_game_number,
      'venueName' => $gameData->gd_venue_name,
      'venueAddress' => $gameData->gd_venue_address,
      'mapUrl' => $gameData->gd_map_url,
      'gameDate' => $gameData->gd_game_date,
      'goalkeeperQuota' => $gameData->gd_goalkeeper_quota,
      'outfieldQuota' => $gameData->gd_outfield_quota,
      'goalkeeperPrice' => $gameData->gd_goalkeeper_price,
      'outfieldPrice' => $gameData->gd_outfield_price,
      'notes' => $gameData->gd_notes,
      'status' => GlobalParamService::getDefaultDataBySlug($gameData->gd_status, GlobalParamSlug::GAME_STATUS)->toArray()
    ];

    $gameInformation = GameInformation::where('gi_gd_id', $gameData->gd_id)->get();
    $gameInfoResponse = $gameInformation->map(function($info) {
      return [
        'id' => $info->gi_id,
        'type' => GlobalParamService::getDefaultDataBySlug($info->gi_info_type, GlobalParamSlug::GAME_INFO)->toArray(),
        'description' => $info->gi_description
      ];
    });

    $gameGallery = GameGallery::where('gg_gd_id', $gameData->gd_id)->get();
    $gameGalleryResponse = $gameGallery->map(function($gallery) {
      return [
        'id' => $gallery->gg_id,
        'url' => $gallery->gg_image_url,
        'altImage' => $gallery->gg_alt_image
      ];
    });

    $registrationList = GameRegistration::where('gr_gd_id', $gameData->gd_id)->get();
    $registrationResponse = $registrationList->map(function($registration) {
      $user = UserInformation::where('ui_id', $registration->gr_ui_id)->first();

      return [
        'id' => $registration->gr_id,
        'user' => [
          'id' => $user->ui_id ?? null,
          'userNumber' => $user->ui_user_number ?? null,
          'email' => $user->ui_email ?? null,
          'firstName' => $user->ui_first_name ?? null,
          'lastName' => $user->ui_last_name ?? null
        ],
        'isOutfield' => $registration->gr_is_outfield,
        'amount' => $registration->gr_amount,
        'transactionNumber' => $registration->gr_transaction_number,
        'registeredAt' => $registration->gr_created_at
      ];
    });

    $registeredList = GameRegisteredPlayer::where('grp_gd_id', $gameData->gd_id)->get();
    $registeredPlayerResponse = $registeredList->map(function($registered) {
      $user = UserInformation::where('ui_id', $registered->grp_ui_id)->first();

      return [
        'id' => $registered->grp_id,
        'user' => [
          'id' => $user->ui_id ?? null,
          'userNumber' => $user->ui_user_number ?? null,
          'email' => $user->ui_email ?? null,
          'firstName' => $user->ui_first_name ?? null,
          'lastName' => $user->ui_last_name ?? null
        ],
        'isOutfield' => $registered->grp_is_outfield,
        'amountPaid' => $registered->grp_amount_paid,
        'transactionNumber' => $registered->grp_transaction_number,
        'paidAt' => $registered->grp_paid_at
      ];
    });

    return [
      'detail' => $gameDetail,
      'information' => $gameInfoResponse,
      'gallery' => $gameGalleryResponse,
      'registration' =>$registrationResponse,
      'registered' => $registeredPlayerResponse
    ];
  }
}