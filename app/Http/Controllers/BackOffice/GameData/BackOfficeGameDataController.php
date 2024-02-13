<?php

namespace App\Http\Controllers\BackOffice\GameData;

use App\Http\Constant\ApiCode;
use App\Http\Constant\GlobalParamSlug;
use App\Http\Controllers\Controller;
use App\Http\Helper\NumberGenerator;
use App\Http\Requests\BackOffice\GameData\CreateGameDataRequest;
use App\Http\Requests\BackOffice\GameData\UpdateGameDataRequest;
use App\Http\Response\General\BasicResponse;
use App\Http\Service\BackOffice\GameData\GameDataBuilder;
use App\Http\Service\General\GlobalParamService;
use App\Models\GameData;
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
            return [
                'id' => $game->gd_id,
                'gameNumber' => $game->gd_game_number,
                'venueName' => $game->gd_venue_name,
                'gameDate' => $game->gd_game_date,
                'goalkeeperQuota' => $game->gd_goalkeeper_quota,
                'outfieldQuota' => $game->gd_outfield_quota,
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
            $game->gd_created_by = $request->input('tokenPayload')['userId'];

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
            $this->buildErrorResponse('User not found', ApiCode::NOT_FOUND);
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
            $game->gd_updated_by = $request->input('tokenPayload')['userId'];

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
            $this->buildErrorResponse('User not found', ApiCode::NOT_FOUND);
        }

        try {
            $game->delete();

            $this->buildSuccessResponse("Game deleted successfully");
        } catch (\Exception $e) {
            $this->buildErrorResponse($e, ApiCode::SERVER_ERROR);
        }
    }
}
