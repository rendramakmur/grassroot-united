<?php

namespace App\Http\Controllers\FrontOffice\Credential;

use App\Http\Constant\ApiCode;
use App\Http\Constant\GlobalParam;
use App\Http\Controllers\Controller;
use App\Http\Helper\JwtHelper;
use App\Http\Helper\NumberGenerator;
use App\Http\Requests\FrontOffice\Credential\FrontOfficeLoginRequest;
use App\Http\Requests\FrontOffice\Credential\FrontOfficeRegisterRequest;
use App\Http\Response\General\BasicResponse;
use App\Http\Service\FrontOffice\UserInformation\UserInformationBuilder;
use App\Mail\UserConfirmationEmail;
use App\Models\UserInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class FrontOfficeCredentialController extends Controller
{
    use BasicResponse;

    public function login(FrontOfficeLoginRequest $request) {
        $data = $request->validated();

        $user = UserInformation::where('ui_email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->ui_password)) {
            $this->buildErrorResponse("Email/Password is invalid", ApiCode::BAD_REQUEST);
        }

        if (!$user->ui_email_status) {
            $this->buildErrorResponse("User not yet verified, check your email to verify", ApiCode::BAD_REQUEST);
        }

        $tokenPayload = [
            'userId' => $user->ui_id,
            'userType' => $user->ui_user_type,
            'userNumber' => $user->ui_user_number,
            'email' => $user->ui_email,
            'firstName' => $user->ui_first_name,
            'lastName' => $user->ui_last_name,
            'emailStatus' => $user->ui_email_status
        ];

        $token = JwtHelper::generateToken($tokenPayload);

        return $this->buildSuccessResponse([
            'token' => $token,
            'payload' => JwtHelper::decodedToken($token)
        ]);
    }

    public function register(FrontOfficeRegisterRequest $request) {
        $data = $request->validated();

        if (UserInformation::where('ui_email', $request['email'])->exists()) {
            $this->buildErrorResponse('Email already exists', ApiCode::BAD_REQUEST);
        }

        DB::beginTransaction();

        try {
            $uuid = Str::uuid()->toString();

            $user = new UserInformation();
            $user->ui_user_type = GlobalParam::FRONT_OFFICE_USER;
            $user->ui_user_number = NumberGenerator::generate(6, 'GRU-', 'ui_user_number', $user);
            $user->ui_first_name = $data['firstName'];
            $user->ui_last_name = $data['lastName'];
            $user->ui_email = $data['email'];
            $user->ui_password = Hash::make($data['password']);
            $user->ui_mobile_prefix = $data['mobilePrefix']['id'];
            $user->ui_mobile_number = $data['mobileNumber'];
            $user->ui_occupation = $data['occupation']['id'];
            $user->ui_date_of_birth = $data['dateOfBirth'];
            $user->ui_gender = $data['gender']['id'];
            $user->ui_city = $data['city']['id'];
            $user->ui_activation_code = $uuid;

            $user->save();

            $response = UserInformationBuilder::build($user);

            DB::commit();

            // Should make a link from front office path, then on front office will hit this endpoint
            $activationUrl = url('/api/activate/' . $user->ui_user_number . '/' . $user->ui_activation_code);
            Mail::to($user->ui_email)->queue(new UserConfirmationEmail($user, $activationUrl));

            return $this->buildSuccessResponse($response);
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->buildErrorResponse($e->getMessage(), ApiCode::SERVER_ERROR);
        }
    }



    public function activate(Request $request, $userNumber, $activationCode) {
        $user = UserInformation::where('ui_user_number', $userNumber)->first();
        if (!$user) {
            $this->buildErrorResponse('User not found', ApiCode::NOT_FOUND);
        }

        if ($user->ui_email_status) {
            $this->buildErrorResponse('Account already active', ApiCode::BAD_REQUEST);
        }

        if ($user->ui_activation_code != $activationCode) {
            $this->buildErrorResponse('Wrong activation code', ApiCode::BAD_REQUEST);
        }

        try {
            $user->ui_email_status = true;
            $user->save();

            $this->buildSuccessResponse("Email activate successfully");
        } catch (\Exception $e) {
            $this->buildErrorResponse($e, ApiCode::SERVER_ERROR);
        }
    }
}
