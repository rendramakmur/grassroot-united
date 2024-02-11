<?php

namespace App\Http\Response\General;

class DefaultData 
{
  public $id;
  public $name;

  public function __construct($id = null, $name = null)
  {
    $this->id = $id;
    $this->name = $name;
  }

  public function toArray(): array 
  {
    return [
      'id' => $this->id,
      'name' => $this->name
    ];
  }
}