<?php

namespace App\Models;

class DailyReport
{
  public string $report_date;
  public array $initial_stock;
  public array $final_physical_stock;

  public function __construct(string $report_date, array $initial_stock, array $final_physical_stock)
  {
    $this->report_date = $report_date;
    $this->initial_stock = $initial_stock;
    $this->final_physical_stock = $final_physical_stock;
  }

  public static function fromArray(array $data): self
  {
    return new self(
      $data['report_date'],
      array_map(fn($item) => TankStock::fromArray($item), $data['initial_stock']),
      array_map(fn($item) => TankStock::fromArray($item), $data['final_physical_stock']),
    );
  }
}

class TankStock
{
  public string $tank_id;
  public string $product;
  public float $mass_ton;

  public function __construct(string $tank_id, string $product, float $mass_ton)
  {
    $this->tank_id = $tank_id;
    $this->product = $product;
    $this->mass_ton = $mass_ton;
  }

  public static function fromArray(array $data): self
  {
    return new self($data['tank_id'], $data['product'], (float) $data['mass_ton']);
  }
}
