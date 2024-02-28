<?php

namespace App\Http\Controllers;

use App\Http\Response\General\BasicResponse;
use App\Http\Response\General\DefaultData;
use App\Models\City;
use App\Models\GlobalParam;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    use BasicResponse;

    public function getAll(Request $request, $slug) {
        $globalParamList = GlobalParam::where('mgp_slug', $slug)->get();
        $globalParamResponse = $globalParamList->map(function($globalParam) {
            return new DefaultData($globalParam->mgp_code_id, $globalParam->mgp_description);
        });

        return $this->buildSuccessResponse($globalParamResponse);
    }

    public function getAllCity(Request $request) {
        $cityList = City::all();
        $cityListResponse = $cityList->map(function($city) {
            return new DefaultData($city->mc_id, $city->mc_name);
        });

        return $this->buildSuccessResponse($cityListResponse);
    }
}
