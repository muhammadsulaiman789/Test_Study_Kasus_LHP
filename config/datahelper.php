<?php

namespace Config;

use App\Models\DailyReport;
use App\Models\TankInformation;
use Illuminate\Support\Facades\File;

class Datahelper
{
  public static function loadDailyReport()
  {
    $filePath = public_path('daily_report.json');
    $jsonData = File::json($filePath);

    $report = DailyReport::fromArray($jsonData);

    $tanks = [];

    for ($i = 0; $i < count($report->initial_stock); $i++) {
      $tanks[] = TankInformation::fromStock($report->initial_stock[$i], $report->final_physical_stock[$i]->mass_ton);
    }

    return [
      'tanks' => $tanks,
      'report_date' => $report->report_date,
    ];
  }

  public static function convertIdToName($id)
  {
    $number = substr($id, 1);
    return "TANK $number";
  }

  public static function loadTransaction()
  {
    $tr_history = session('transactions', []);
    $transactions = [];

    for ($i = 0; $i < count($tr_history); $i++) {
      $transactions[] = $tr_history[$i];
    }

    return $transactions;
  }

  public static function getTankInformation($tank_id)
  {
    $tanks = self::loadDailyReport()['tanks'];
    $column = array_column($tanks, 'id');
    $key = array_search($tank_id, $column);

    return $tanks[$key];
  }
}
