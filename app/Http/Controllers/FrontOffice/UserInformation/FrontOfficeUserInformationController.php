<?php

namespace App\Http\Controllers\FrontOffice\UserInformation;

use App\Http\Constant\ApiCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\FrontOffice\UserInformation\FrontOfficeUserUpdateRequest;
use App\Http\Response\General\BasicResponse;
use App\Http\Service\FrontOffice\UserInformation\UserInformationBuilder;
use App\Models\UserInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function update(FrontOfficeUserUpdateRequest $request, $userNumber) {
        $data = $request->validated();
        $user = UserInformation::where('ui_user_number', $userNumber)->first();
        if (!$user) {
            $this->buildErrorResponse('User not found', ApiCode::NOT_FOUND);
        }

        DB::beginTransaction();

        try {
            $user->ui_first_name = $data['firstName'];
            $user->ui_last_name = $data['lastName'];
            $user->ui_mobile_prefix = $data['mobilePrefix']['id'];
            $user->ui_mobile_number = $data['mobileNumber'];
            $user->ui_occupation = $data['occupation']['id'];
            $user->ui_date_of_birth = $data['dateOfBirth'];
            $user->ui_gender = $data['gender']['id'];
            $user->ui_photo_profile = $data['photoProfile'];
            $user->ui_address = $data['address'];
            $user->ui_city = $data['city']['id'];
            $user->ui_body_size = $data['bodySize']['id'];
            $user->ui_updated_by = $request->attributes->get('tokenPayload')['userId'];

            $user->save();

            $response = UserInformationBuilder::build($user);

            DB::commit();

            return $this->buildSuccessResponse($response);
        } catch (\Exception $e) {
            error_log($e);
            DB::rollBack();
            
            $this->buildErrorResponse($e, ApiCode::SERVER_ERROR);
        }
    }
}
