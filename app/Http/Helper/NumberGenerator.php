<?php

namespace App\Http\Helper;

use Illuminate\Database\Eloquent\Model;

class NumberGenerator
{
  public static function generate(int $length, string $prefix, string $columnName, Model $table)
  {
    $randomNumber = str_pad(mt_rand(1, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);

    $generatedNumber = $prefix . $randomNumber;
    $isExists = $table::where($columnName, $generatedNumber)->exists();

    if ($isExists)
    {
      self::generate($length, $prefix, $columnName, $table);
    }

    return $generatedNumber;
  }
}