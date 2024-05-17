<?php

namespace App\Http\Helper;

use App\Http\Constant\ApiCode;
use App\Http\Response\General\BasicResponse;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
  use BasicResponse;

  public static function generateToken(array $payload, int $expiry = (60 * 60 * 24)): string 
  {
    $issuedAt = time();
    $expiresAt = $issuedAt + $expiry;

    $token = JWT::encode(
      array_merge($payload, [
        'issuedAt' => date('Y-m-d H:i:s', $issuedAt),
        'expiredAt' => date('Y-m-d H:i:s', $expiresAt)
      ]),
      env('JWT_SECRET'), 
      'HS256'
    );

    return $token;
  }

  public static function decodedToken(string $token)
  {
      try {
          $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
          return $decoded;
      } catch (\Exception $e) {
          self::buildErrorResponse($e->getMessage(), ApiCode::UNAUTHORIZED);
      }
  }
}