<?php

namespace App\Http\Controllers\BackOffice\Credential;

use App\Http\Constant\ApiCode;
use App\Http\Controllers\Controller;
use App\Http\Helper\JwtHelper;
use App\Http\Requests\BackOffice\Credential\BackOfficeLoginRequest;
use App\Http\Response\General\BasicResponse;
use App\Http\Service\BackOffice\UserInformation\UserInformationBuilder;
use App\Models\UserInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BackOfficeCredentialController extends Controller
{
    use BasicResponse;

    public function login(BackOfficeLoginRequest $request) {
        $data = $request->validated();

        $user = UserInformation::where('ui_email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->ui_password)) {
            $this->buildErrorResponse("Email/Password is invalid", ApiCode::UNAUTHORIZED);
        }

        $tokenPayload = [
            'userId' => $user->ui_id,
            'userType' => $user->ui_user_type,
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
}
