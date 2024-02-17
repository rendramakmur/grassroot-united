<?php

namespace App\Http\Controllers\FrontOffice\UserInformation;

use App\Http\Constant\ApiCode;
use App\Http\Controllers\Controller;
use App\Http\Response\General\BasicResponse;
use App\Http\Service\FrontOffice\UserInformation\UserInformationBuilder;
use App\Models\UserInformation;
use Illuminate\Http\Request;

class FrontOfficeUserInformationController extends Controller
{
    use BasicResponse;

    public function detail(Request $request, $userNumber) {
        $user = UserInformation::where('ui_user_number', $userNumber)->first();
        if (!$user) {
            $this->buildErrorResponse('User not found', ApiCode::NOT_FOUND);
        }
        $res = UserInformationBuilder::build($user);

        return $this->buildSuccessResponse($res);
    }
}
