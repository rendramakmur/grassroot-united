<?php

namespace App\Http\Controllers\BackOffice\UserInformation;

use App\Http\Constant\ApiCode;
use App\Http\Controllers\Controller;
use App\Http\Helper\NumberGenerator;
use App\Http\Requests\BackOffice\UserInformation\CreateUserRequest;
use App\Http\Response\General\BasicResponse;
use App\Http\Service\BackOffice\UserInformation\UserInformationBuilder;
use App\Models\UserInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class BackOfficeUserInformationController extends Controller
{
    use BasicResponse;

    public function create(CreateUserRequest $request) 
    {
        $data = $request->validated();

        if (UserInformation::where('ui_email', $request['email'])->exists())
        {
            $this->buildErrorResponse("Email already exists", ApiCode::BAD_REQUEST);
        }

        DB::beginTransaction();

        try {
            $user = new UserInformation();
            $user->ui_user_type = $data['userType']['id'];
            $user->ui_user_number = NumberGenerator::generate(6, 'GRU-', 'ui_user_number', $user);
            $user->ui_first_name = $data['firstName'];
            $user->ui_last_name = $data['lastName'];
            $user->ui_email = $data['email'];
            $user->ui_password = Hash::make(Str::uuid()->toString());
            $user->ui_mobile_prefix = $data['mobilePrefix']['id'];
            $user->ui_mobile_number = $data['mobileNumber'];
            $user->ui_occupation = $data['occupation']['id'];
            $user->ui_date_of_birth = $data['dateOfBirth'];
            $user->ui_gender = $data['gender']['id'];
            $user->ui_photo_profile = $data['photoProfile'];
            $user->ui_address = $data['address'];
            $user->ui_city = $data['city']['id'];
            $user->ui_body_size = $data['bodySize']['id'];
            $user->ui_activation_code = Str::uuid()->toString();
            $user->ui_email_status = $data['emailStatus'];
            $user->ui_verified_at = $data['verifiedAt'];
            $user->ui_created_by = $request->input('tokenPayload')['userId'];

            $user->save();

            DB::commit();

            $response = UserInformationBuilder::build($user);

            return $this->buildSuccessResponse($response);
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->buildErrorResponse($e, ApiCode::SERVER_ERROR);
        }
    }

    
}
