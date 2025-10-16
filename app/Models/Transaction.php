<?php

namespace App\Models;

use Illuminate\Http\Request;

class Transaction
{
  public string $type;
  public ?string $from_tank_id;
  public ?string $to_tank_id;
  public ?string $product;
  public float $quantity_ton;
  public ?string $notes;

  public function __construct(
    string $type,
    ?string $from_tank_id,
    ?string $to_tank_id,
    ?string $product,
    float $quantity_ton,
    ?string $notes,
  ) {
    $this->type = $type;
    $this->from_tank_id = $from_tank_id;
    $this->to_tank_id = $to_tank_id;
    $this->product = $product;
    $this->quantity_ton = $quantity_ton;
    $this->notes = $notes;
  }

  public static function fromArray(array $data): self
  {
    return new self(
      $data['type'],
      $data['from_tank_id'] ?? null,
      $data['to_tank_id'] ?? null,
      $data['product'] ?? null,
      (float) $data['quantity_ton'],
      $data['notes'] ?? null,
    );
  }

  public static function fromRequest(Request $request): self
  {
    return new self($request->type, $request->from, $request->to, $request->product, $request->volume, $request->note);
  }

  public function toArray(): array
  {
    return [
      'type' => $this->type,
      'from_tank_id' => $this->from_tank_id,
      'to_tank_id' => $this->to_tank_id,
      'product' => $this->product,
      'quantity_ton' => $this->quantity_ton,
      'notes' => $this->notes,
    ];
  }
}
