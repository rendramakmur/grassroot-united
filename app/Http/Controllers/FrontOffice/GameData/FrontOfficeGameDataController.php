<?php

namespace App\Http\Controllers\FrontOffice\GameData;

use App\Http\Constant\ApiCode;
use App\Http\Constant\GlobalParamSlug;
use App\Http\Controllers\Controller;
use App\Http\Helper\NumberGenerator;
use App\Http\Response\General\BasicResponse;
use App\Http\Service\FrontOffice\GameData\GameDataBuilder;
use App\Http\Service\General\GlobalParamService;
use App\Http\Service\General\ValidatePlayerQuota;
use App\Models\GameData;
use App\Models\GameRegisteredPlayer;
use App\Models\GameRegistration;
use App\Models\UserInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontOfficeGameDataController extends Controller
{
    use BasicResponse;

    public function index(Request $request) {
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);
        $gameNumber = $request->query('gameNumber');

        $gameQuery = GameData::query();

        if ($gameNumber) {
            $gameQuery->where('gd_game_number', $gameQuery);
        }

        $paginateData = $gameQuery->paginate($limit, ['*'], 'page', $page);
        $items = $paginateData->items();

        $response = collect($items)->map(function ($game) {
            $registeredPlayer = GameRegisteredPlayer::where('grp_gd_id', $game->gd_id);
            $outfieldCount = $registeredPlayer->where('grp_is_outfield', true)->count();
            $goalkeeperCount = $registeredPlayer->where('grp_is_outfield', false)->count();

            return [
                'id' => $game->gd_id,
                'gameNumber' => $game->gd_game_number,
                'venueName' => $game->gd_venue_name,
                'gameDate' => $game->gd_game_date,
                'goalkeeperQuota' => $game->gd_goalkeeper_quota,
                'filledGoalkeeper' => $goalkeeperCount,
                'outfieldQuota' => $game->gd_outfield_quota,
                'filledOutfield' => $outfieldCount,
                'gameStatus' => GlobalParamService::getDefaultDataBySlug($game->gd_status, GlobalParamSlug::GAME_STATUS)->toArray(),
            ];
        });

        $responseData = [
            'page' => $paginateData->currentPage(),
            'limit' => $paginateData->perPage(),
            'totalPage' => $paginateData->lastPage(),
            'totalElements' => $paginateData->total(),
            'data' => $response->toArray()
        ];
        
        return $this->buildSuccessResponse($responseData);
    }

    public function detail(Request $request, $gameNumber) {
        $game = GameData::where('gd_game_number', $gameNumber)->first();
        if (!$game) {
            $this->buildErrorResponse('Game not found', ApiCode::NOT_FOUND);
        }
        $res = GameDataBuilder::build($game);

        return $this->buildSuccessResponse($res);
    }

    public function register(Request $request, $gameNumber) {
        $isOutfield = $request->query('isOutfield' === true, true);
        $userId = $request->attributes->get('tokenPayload')['userId'];
        if (!$userId) {
            $this->buildErrorResponse('Please logged in first', ApiCode::NOT_FOUND);
        }

        $game = GameData::where('gd_game_number', $gameNumber)->first();
        if (!$game) {
            $this->buildErrorResponse('Game not found', ApiCode::NOT_FOUND);
        }

        $registeredPlayer = GameRegistration::where('gr_gd_id', $game->gd_id)
        ->where('gr_ui_id', $userId)
        ->first();

        if ($registeredPlayer) {
            $this->buildErrorResponse("Player already registered", ApiCode::BAD_REQUEST);
        }

        ValidatePlayerQuota::validate($game);
        
        DB::beginTransaction();

        try {
            if ($isOutfield) {
                $price = $game->gd_goalkeeper_price;
            } else {
                $price = $game->gd_outfield_price;
            }

            $registerPlayer = new GameRegistration();
            $registerPlayer->gr_gd_id = $game->gd_id;
            $registerPlayer->gr_ui_id = $userId;
            $registerPlayer->gr_is_outfield = $isOutfield;
            $registerPlayer->gr_amount = $price;
            $registerPlayer->gr_transaction_number = NumberGenerator::generate(15, 'TRX', 'gr_transaction_number', $registerPlayer);
            $registerPlayer->gr_created_by = $request->attributes->get('tokenPayload')['userId'];

            $registerPlayer->save();

            $response = GameDataBuilder::build($game);

            DB::commit();
            
            return $this->buildSuccessResponse($response);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->buildErrorResponse($e, ApiCode::SERVER_ERROR);
        }
    }

    public function paid(Request $request, $transactionNumber) {
        $registrationData = GameRegistration::where('gr_transaction_number', $transactionNumber)->first();
        if (!$registrationData) {
            $this->buildErrorResponse("Registered player not found", ApiCode::NOT_FOUND);
        }

        $game = GameData::where('gd_id', $registrationData->gr_gd_id)->first();
        if (!$game) {
            $this->buildErrorResponse('Game not found', ApiCode::NOT_FOUND);
        }
        $user = UserInformation::where('ui_id', $registrationData->gr_ui_id)->first();
        if (!$user) {
            $this->buildErrorResponse('User not found', ApiCode::NOT_FOUND);
        }

        $userCheck = GameRegisteredPlayer::where('grp_gd_id', $game->gd_id)
        ->where('grp_ui_id', $user->ui_id)
        ->exists();
        if ($userCheck) {
            $this->buildErrorResponse("Player already registered/paid", ApiCode::BAD_REQUEST);
        }

        ValidatePlayerQuota::validate($game);

        try {
            $paidPlayer = new GameRegisteredPlayer();
            $paidPlayer->grp_gd_id = $game->gd_id;
            $paidPlayer->grp_ui_id = $user->ui_id;
            $paidPlayer->grp_is_outfield = $registrationData->gr_is_outfield;
            $paidPlayer->grp_amount_paid = $registrationData->gr_amount;
            // Get date time of user payment here
            $paidPlayer->grp_paid_at = date('Y-m-d H:i:s');
            $paidPlayer->grp_transaction_number = $registrationData->gr_transaction_number;

            $paidPlayer->save();

            // Also save to transaction table

            $response = GameDataBuilder::build($game);

            DB::commit();
            
            return $this->buildSuccessResponse($response);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->buildErrorResponse($e, ApiCode::SERVER_ERROR);
        }
    }
}
