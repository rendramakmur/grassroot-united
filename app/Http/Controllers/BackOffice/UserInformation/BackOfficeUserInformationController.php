<?php

namespace App\Http\Controllers\BackOffice\UserInformation;

use App\Http\Constant\ApiCode;
use App\Http\Controllers\Controller;
use App\Http\Helper\NumberGenerator;
use App\Http\Requests\BackOffice\UserInformation\CreateUserRequest;
use App\Http\Requests\BackOffice\UserInformation\UpdateUserRequest;
use App\Http\Response\General\BasicResponse;
use App\Http\Service\BackOffice\UserInformation\UserInformationBuilder;
use App\Mail\UserConfirmationEmail;
use App\Models\UserInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class BackOfficeUserInformationController extends Controller
{
    use BasicResponse;

    public function index(Request $request) {
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);
        $userNumber = $request->query('userNumber');
        $email = $request->query('email');

        $userQuery = UserInformation::query();

        if ($userNumber) {
            $userQuery->where('ui_user_number', $userNumber);
        }
        if ($email) {
            $userQuery->where('ui_email', 'like', '%' . $email . '%');
        }

        $paginateData = $userQuery->paginate($limit, ['*'], 'page', $page);
        $items = $paginateData->items();
        $response = collect($items)->map(function ($user) {
            return [
                'id' => $user->ui_id,
                'userNumber' => $user->ui_user_number,
                'email' => $user->ui_email,
                'firstName' => $user->ui_first_name,
                'lastName' => $user->ui_last_name,
                'emailStatus' => $user->ui_email_status
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

    public function detail(Request $request, $userNumber) {
        $user = UserInformation::where('ui_user_number', $userNumber)->first();
        if (!$user) {
            $this->buildErrorResponse('User not found', ApiCode::NOT_FOUND);
        }
        $res = UserInformationBuilder::build($user);

        return $this->buildSuccessResponse($res);
    }

    public function create(CreateUserRequest $request) 
    {
        $data = $request->validated();

        if (UserInformation::where('ui_email', $request['email'])->exists()) {
            $this->buildErrorResponse('Email already exists', ApiCode::BAD_REQUEST);
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
            $activationUrl = url('/api/backoffice/user/activate/' . $user->ui_user_number . '/' . $user->ui_activation_code);
            Mail::to($user->ui_email)->queue(new UserConfirmationEmail($user, $activationUrl));

            return $this->buildSuccessResponse($response);
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->buildErrorResponse($e, ApiCode::SERVER_ERROR);
        }
    }

    public function update(UpdateUserRequest $request, $userNumber) {
        $data = $request->validated();
        $user = UserInformation::where('ui_user_number', $userNumber)->first();
        if (!$user) {
            $this->buildErrorResponse('User not found', ApiCode::NOT_FOUND);
        }

        if (UserInformation::where('ui_email', $request['email'])->where('ui_id', '!=', $user->ui_id)->exists()) {
            $this->buildErrorResponse('Email already exists', ApiCode::BAD_REQUEST);
        }

        DB::beginTransaction();

        try {
            $user->ui_user_type = $data['userType']['id'];
            $user->ui_first_name = $data['firstName'];
            $user->ui_last_name = $data['lastName'];
            $user->ui_email = $data['email'];
            $user->ui_mobile_prefix = $data['mobilePrefix']['id'];
            $user->ui_mobile_number = $data['mobileNumber'];
            $user->ui_occupation = $data['occupation']['id'];
            $user->ui_date_of_birth = $data['dateOfBirth'];
            $user->ui_gender = $data['gender']['id'];
            $user->ui_photo_profile = $data['photoProfile'];
            $user->ui_address = $data['address'];
            $user->ui_city = $data['city']['id'];
            $user->ui_body_size = $data['bodySize']['id'];
            $user->ui_email_status = $data['emailStatus'];
            $user->ui_verified_at = $data['verifiedAt'];
            $user->ui_updated_by = $request->input('tokenPayload')['userId'];

            $user->save();

            DB::commit();

            $response = UserInformationBuilder::build($user);

            return $this->buildSuccessResponse($response);
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->buildErrorResponse($e, ApiCode::SERVER_ERROR);
        }
    }

    public function delete(Request $request, $userNumber) {
        $user = UserInformation::where('ui_user_number', $userNumber)->first();
        if (!$user) {
            $this->buildErrorResponse('User not found', ApiCode::NOT_FOUND);
        }

        try {
            $user->delete();

            $this->buildSuccessResponse("User deleted successfully");
        } catch (\Exception $e) {
            $this->buildErrorResponse($e, ApiCode::SERVER_ERROR);
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

        if (!($user->ui_activation_code == $activationCode)) {
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
