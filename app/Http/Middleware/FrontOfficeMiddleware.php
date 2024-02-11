<?php

namespace App\Http\Middleware;

use App\Http\Constant\ApiCode;
use App\Http\Constant\GlobalParam;
use App\Http\Response\General\BasicResponse;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class FrontOfficeMiddleware
{
  use BasicResponse;
  
  public function handle($request, Closure $next) 
  {
    $jwtToken = $request->bearerToken();

    if ($jwtToken) {
      try {
        $decodedToken = JWT::decode($jwtToken, new Key (env('JWT_SECRET'), 'HS256'));
        
        if ($decodedToken->userType != GlobalParam::FRONT_OFFICE_USER) {
          $this->buildErrorResponse("Unauthorized", ApiCode::UNAUTHORIZED);
        }

        $request->tokenPayload = $decodedToken;
      } catch (\Exception $e) {
        $this->buildErrorResponse("Unauthorized", ApiCode::UNAUTHORIZED);
      }
    } else {
      $this->buildErrorResponse("Unauthorized", ApiCode::UNAUTHORIZED);
    }

    return $next($request);
  }
}