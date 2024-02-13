<?php

namespace App\Http\Middleware;

use App\Http\Constant\ApiCode;
use App\Http\Constant\GlobalParam;
use App\Http\Response\General\BasicResponse;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class BackOfficeMiddleware
{
  use BasicResponse;

  public function handle($request, Closure $next) 
  {
    $jwtToken = $request->bearerToken();

    if ($jwtToken) {
      try {
        $decodedToken = JWT::decode($jwtToken, new Key (env('JWT_SECRET'), 'HS256'));
        
        if ($decodedToken->userType != GlobalParam::BACK_OFFICE_USER) {
          $this->buildErrorResponse("Unauthorized", ApiCode::UNAUTHORIZED);
        }

        $tokenPayloadArray = json_decode(json_encode($decodedToken), true);
        $request->attributes->set('tokenPayload', $tokenPayloadArray);
      } catch (\Exception $e) {
        $this->buildErrorResponse("Unauthorized", ApiCode::UNAUTHORIZED);
      }
    } else {
      $this->buildErrorResponse("Unauthorized", ApiCode::UNAUTHORIZED);
    }

    return $next($request);
  }
}