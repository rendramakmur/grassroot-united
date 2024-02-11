<?php

namespace App\Http\Response\General;

use App\Http\Constant\ApiCode;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

trait BasicResponse{
  protected static function buildSuccessResponse($data): JsonResponse 
  {
    return response()->json([
      'status' => "Success",
      'data' => $data,
      'code' => ApiCode::SUCCESS,
      'error' => null
    ]);
  }

  protected static function buildErrorResponse($error, $code) 
  {
    throw new HttpResponseException(response([
      'status' => "Error",
      'data' => null,
      'code' => $code,
      'error' => $error
      ]
    ));
  }
}