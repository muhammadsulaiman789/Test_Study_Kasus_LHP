<?php

namespace App\Models;

use Config\Datahelper;

class TankInformation
{
  public string $id;
  public string $tank_name;
  public string $product;
  public float $initial_ton;
  public float $final_phys_ton;

  public function __construct(string $id, string $tank_name, string $product, float $initial_ton, float $final_phys_ton)
  {
    $this->id = $id;
    $this->tank_name = Datahelper::convertIdToName($tank_name);
    $this->product = $product;
    $this->initial_ton = $initial_ton;
    $this->final_phys_ton = $final_phys_ton;
  }

  public static function fromStock(TankStock $tank, $final_ton): self
  {
    return new self($tank->tank_id, $tank->tank_id, $tank->product, $tank->mass_ton, $final_ton);
  }
}
