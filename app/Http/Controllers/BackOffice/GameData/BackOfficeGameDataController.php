<?php

namespace App\Http\Controllers\BackOffice\GameData;

use App\Http\Constant\ApiCode;
use App\Http\Constant\GlobalParamSlug;
use App\Http\Controllers\Controller;
use App\Http\Helper\NumberGenerator;
use App\Http\Requests\BackOffice\GameData\CreateGameDataRequest;
use App\Http\Requests\BackOffice\GameData\DeletePaidPlayerRequest;
use App\Http\Requests\BackOffice\GameData\PaidPlayerRequest;
use App\Http\Requests\BackOffice\GameData\RegisterPlayerRequest;
use App\Http\Requests\BackOffice\GameData\UpdateGameDataRequest;
use App\Http\Requests\BackOffice\GameData\UpdateGameGalleryRequest;
use App\Http\Requests\BackOffice\GameData\UpdateGameInformationRequest;
use App\Http\Response\General\BasicResponse;
use App\Http\Service\BackOffice\GameData\GameDataBuilder;
use App\Http\Service\General\GlobalParamService;
use App\Http\Service\General\ValidatePlayerQuota;
use App\Models\GameData;
use App\Models\GameGallery;
use App\Models\GameInformation;
use App\Models\GameRegisteredPlayer;
use App\Models\GameRegistration;
use App\Models\UserInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BackOfficeGameDataController extends Controller
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

    public function create(CreateGameDataRequest $request) {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $game = new GameData();
            $game->gd_game_number = NumberGenerator::generate(6, 'GAME-', 'gd_game_number', $game);
            $game->gd_venue_name = $data['venueName'];
            $game->gd_venue_address = $data['venueAddress'];
            $game->gd_map_url = $data['mapUrl'];
            $game->gd_game_date = $data['gameDate'];
            $game->gd_goalkeeper_quota = $data['goalkeeperQuota'];
            $game->gd_outfield_quota = $data['outfieldQuota'];
            $game->gd_goalkeeper_price = $data['goalkeeperPrice'];
            $game->gd_outfield_price = $data['outfieldPrice'];
            $game->gd_notes = $data['notes'];
            $game->gd_status = $data['status']['id'];
            $game->gd_created_by = $request->attributes->get('tokenPayload')['userId'];

            $game->save();

            $response = GameDataBuilder::build($game);

            DB::commit();

            return $this->buildSuccessResponse($response);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->buildErrorResponse($e, ApiCode::SERVER_ERROR);
        }
    }

    public function update(UpdateGameDataRequest $request, $gameNumber) {
        $data = $request->validated();
        $game = GameData::where('gd_game_number', $gameNumber)->first();
        if (!$game) {
            $this->buildErrorResponse('Game not found', ApiCode::NOT_FOUND);
        }

        DB::beginTransaction();

        try {
            $game->gd_venue_name = $data['venueName'];
            $game->gd_venue_address = $data['venueAddress'];
            $game->gd_map_url = $data['mapUrl'];
            $game->gd_game_date = $data['gameDate'];
            $game->gd_goalkeeper_quota = $data['goalkeeperQuota'];
            $game->gd_outfield_quota = $data['outfieldQuota'];
            $game->gd_goalkeeper_price = $data['goalkeeperPrice'];
            $game->gd_outfield_price = $data['goalkeeperPrice'];
            $game->gd_notes = $data['notes'];
            $game->gd_status = $data['status']['id'];
            $game->gd_updated_by = $request->attributes->get('tokenPayload')['userId'];

            $game->save();

            $response = GameDataBuilder::build($game);

            DB::commit();

            return $this->buildSuccessResponse($response);
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->buildErrorResponse($e, ApiCode::SERVER_ERROR);
        }
    }

    public function delete(Request $request, $gameNumber) {
        $game = GameData::where('gd_game_number', $gameNumber)->first();
        if (!$game) {
            $this->buildErrorResponse('Game not found', ApiCode::NOT_FOUND);
        }

        DB::beginTransaction();

        try {
            $game->delete();

            DB::commit();

            $this->buildSuccessResponse("Game deleted successfully");
        } catch (\Exception $e) {
            DB::rollBack();

            $this->buildErrorResponse($e, ApiCode::SERVER_ERROR);
        }
    }

    public function updateGameInfo(UpdateGameInformationRequest $request, $gameNumber) {
        $data = $request->validated();
        $game = GameData::where('gd_game_number', $gameNumber)->first();
        if (!$game) {
            $this->buildErrorResponse('Game not found', ApiCode::NOT_FOUND);
        }

        DB::beginTransaction();

        try {
            GameInformation::where('gi_gd_id', $game->gd_id)->delete();

            foreach ($data as $info) {
                $gameInfo = new GameInformation();
                $gameInfo->gi_gd_id = $game->gd_id;
                $gameInfo->gi_info_type = $info['type']['id'];
                $gameInfo->gi_description = $info['description'];
                $gameInfo->save();
            }

            $response = GameDataBuilder::build($game);

            DB::commit();

            return $this->buildSuccessResponse($response);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->buildErrorResponse($e, ApiCode::SERVER_ERROR);
        }
    }

    public function updateGameGallery(UpdateGameGalleryRequest $request, $gameNumber) {
        $data = $request->validated();
        $game = GameData::where('gd_game_number', $gameNumber)->first();
        if (!$game) {
            $this->buildErrorResponse('Game not found', ApiCode::NOT_FOUND);
        }

        DB::beginTransaction();

        try {
            GameGallery::where('gg_gd_id', $game->gd_id)->delete();

            foreach ($data as $gallery) {
                $gameGallery = new GameGallery();
                $gameGallery->gg_gd_id = $game->gd_id;
                $gameGallery->gg_image_url = $gallery['url'];
                $gameGallery->gg_alt_image = $gallery['altImage'];
                $gameGallery->save();
            }

            $response = GameDataBuilder::build($game);

            DB::commit();

            return $this->buildSuccessResponse($response);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->buildErrorResponse($e, ApiCode::SERVER_ERROR);
        }
    }

    public function playerRegister(RegisterPlayerRequest $request, $gameNumber) {
        $data = $request->validated();
        $userNumber = $data['userNumber'];
        $game = GameData::where('gd_game_number', $gameNumber)->first();
        if (!$game) {
            $this->buildErrorResponse('Game not found', ApiCode::NOT_FOUND);
        }
        $user = UserInformation::where('ui_user_number', $userNumber)->first();
        if (!$user) {
            $this->buildErrorResponse('User not found', ApiCode::NOT_FOUND);
        }

        $registeredPlayer = GameRegistration::where('gr_gd_id', $game->gd_id)
        ->where('gr_ui_id', $user->ui_id)
        ->first();

        if ($registeredPlayer) {
            $this->buildErrorResponse("Player already registered", ApiCode::BAD_REQUEST);
        }

        ValidatePlayerQuota::validate($game);
        
        DB::beginTransaction();

        try {
            if ($data['isOutfield']) {
                $price = $game->gd_goalkeeper_price;
            } else {
                $price = $game->gd_outfield_price;
            }

            $registerPlayer = new GameRegistration();
            $registerPlayer->gr_gd_id = $game->gd_id;
            $registerPlayer->gr_ui_id = $user->ui_id;
            $registerPlayer->gr_is_outfield = $data['isOutfield'];
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

    public function playerPaid(PaidPlayerRequest $request, $gameNumber) {
        $data = $request->validated();
        $userNumber = $data['userNumber'];
        $game = GameData::where('gd_game_number', $gameNumber)->first();
        if (!$game) {
            $this->buildErrorResponse('Game not found', ApiCode::NOT_FOUND);
        }
        $user = UserInformation::where('ui_user_number', $userNumber)->first();
        if (!$user) {
            $this->buildErrorResponse('User not found', ApiCode::NOT_FOUND);
        }

        $registered = GameRegistration::where('gr_gd_id', $game->gd_id)
        ->where('gr_ui_id', $user->ui_id)
        ->first();

        if (!$registered) {
            $this->buildErrorResponse("Player not yet register", ApiCode::BAD_REQUEST);
        }
        
        $userCheck = GameRegisteredPlayer::where('grp_gd_id', $game->gd_id)
        ->where('grp_ui_id', $user->ui_id)
        ->exists();
        if ($userCheck) {
            $this->buildErrorResponse("Player already registered/paid", ApiCode::BAD_REQUEST);
        }

        ValidatePlayerQuota::validate($game);

        DB::beginTransaction();

        try {
            $paidPlayer = new GameRegisteredPlayer();
            $paidPlayer->grp_gd_id = $game->gd_id;
            $paidPlayer->grp_ui_id = $user->ui_id;
            $paidPlayer->grp_is_outfield = $registered->gr_is_outfield;
            $paidPlayer->grp_amount_paid = $data['amountPaid'];
            $paidPlayer->grp_paid_at = $data['paidAt'];
            $paidPlayer->grp_transaction_number = $registered->gr_transaction_number;
            $paidPlayer->grp_created_by = $request->attributes->get('tokenPayload')['userId'];

            $paidPlayer->save();

            $response = GameDataBuilder::build($game);

            DB::commit();
            
            return $this->buildSuccessResponse($response);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->buildErrorResponse($e, ApiCode::SERVER_ERROR);
        }
    }

    public function deletePaidPlayer(DeletePaidPlayerRequest $request, $gameNumber) {
        $data = $request->validated();
        $userNumber = $data['userNumber'];
        $game = GameData::where('gd_game_number', $gameNumber)->first();
        if (!$game) {
            $this->buildErrorResponse('Game not found', ApiCode::NOT_FOUND);
        }
        $user = UserInformation::where('ui_user_number', $userNumber)->first();
        if (!$user) {
            $this->buildErrorResponse('User not found', ApiCode::NOT_FOUND);
        }
        $paidPlayer = GameRegisteredPlayer::where('grp_gd_id', $game->gd_id)
        ->where('grp_ui_id', $user->ui_id)
        ->first();

        if (!$paidPlayer) {
            $this->buildErrorResponse('Registered player not found', ApiCode::NOT_FOUND);
        }

        DB::beginTransaction();

        try {
            $paidPlayer->delete();

            DB::commit();

            $this->buildSuccessResponse("Registered player deleted successfully");
        } catch (\Exception $e) {
            DB::rollBack();

            $this->buildErrorResponse($e, ApiCode::SERVER_ERROR);
        }
    }
}
