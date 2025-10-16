<?php

namespace App\Http\Controllers;

use Config\Datahelper;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class SummaryController extends Controller
{
  public function index()
  {
    $summary = $this->generateReportSummary();
    return view('summary', $summary);
  }

  public function exportJson()
  {
    $summary = $this->generateReportSummary();

    $json = json_encode($summary, JSON_PRETTY_PRINT);

    return response($json, 200, [
      'Content-Type' => 'application/json',
      'Content-Disposition' => 'attachment; filename="laporan_output.json"',
    ]);
  }

  private function generateReportSummary()
  {
    /**
     * Calculate tank summaries based on transaction type
     *
     * Receive -> Add volume in tank 01 or 02
     * Sell -> Reduce volume in tank 04 or 05
     * Process_In ->  Add volume in tank 03, 04, or 05
     * Process_Out -> Reduce volume in tank 01, 02, 03
     * Transfer -> Reduce volume in source tank and add in target tank
     */

    /**
     * Calculate production summary based on tank summaries
     *
     * CPO Processed -> All process out from tank 01 & 02 to tank 03
     * Olein Processed -> All process in from tank 03 to tank 04
     * Stearin Processed -> All process in from tank 03 to tank 05
     * Total Production Produced -> Sum of process in from tank 04 and 05
     * Yield percentage -> Use this formula = (Total Production / CPO Processed) * 100%
     */

    /**
     * Calculate transaction summary based on transaction
     *
     * Receive -> Sum of tank 01 and 02 that receive material
     * Sold -> Sum of tank 04 and 05 that sell product
     */
    $tanks = Datahelper::loadDailyReport()['tanks'];
    $data = Datahelper::loadTransaction();

    $summaries = [];
    foreach ($tanks as $t) {
      $summaries[$t->id] = [
        'product' => $t->product,
        'opening_stock' => (float) $t->initial_ton,
        'total_in' => 0.0,
        'total_out' => 0.0,
        'physical_closing_stock' => (float) $t->final_phys_ton,
      ];
    }

    // transaction summaries
    $total_received = 0.0;
    $total_sold = 0.0;

    // production counters
    $cpo_processed = 0.0;
    $olein_prod = 0.0;
    $stearin_prod = 0.0;

    foreach ($data as $tx) {
      $type = strtoupper($tx->type);
      $qty = isset($tx->quantity_ton) ? (float) $tx->quantity_ton : 0.0;

      if ($type === 'RECEIVE') {
        $to = $tx->to_tank_id;
        if (!isset($summaries[$to])) {
          $summaries[$to] = [
            'product' => $tx->product ?? 'Unknown',
            'opening_stock' => 0.0,
            'total_in' => 0.0,
            'total_out' => 0.0,
          ];
        }
        $summaries[$to]['total_in'] += $qty;
        $total_received += $qty;
      } elseif ($type === 'PROCESS OUT') {
        $from = $tx->from_tank_id;
        if (!isset($summaries[$from])) {
          $summaries[$from] = [
            'product' => $tx->product ?? 'Unknown',
            'opening_stock' => 0.0,
            'total_in' => 0.0,
            'total_out' => 0.0,
          ];
        }
        $summaries[$from]['total_out'] += $qty;

        if (isset($tx->product) && strtoupper($tx->product) === 'CPO') {
          $cpo_processed += $qty;
        }
      } elseif ($type === 'PROCESS IN') {
        $to = $tx->to_tank_id;
        if (!isset($summaries[$to])) {
          $summaries[$to] = [
            'product' => $tx->product ?? 'Unknown',
            'opening_stock' => 0.0,
            'total_in' => 0.0,
            'total_out' => 0.0,
          ];
        }
        $summaries[$to]['total_in'] += $qty;

        if (isset($tx->product)) {
          $p = strtoupper($tx->product);
          if ($p === 'OLEIN') {
            $olein_prod += $qty;
          }
          if ($p === 'STEARYN' || $p === 'STEARIN') {
            $stearin_prod += $qty;
          } // tolerate typo
          if ($p === 'STEARIN') {
            $stearin_prod += 0;
          } // nothing
        }
      } elseif ($type === 'SELL') {
        $from = $tx->from_tank_id;
        if (!isset($summaries[$from])) {
          $summaries[$from] = [
            'product' => $tx->product ?? 'Unknown',
            'opening_stock' => 0.0,
            'total_in' => 0.0,
            'total_out' => 0.0,
          ];
        }
        $summaries[$from]['total_out'] += $qty;
        $total_sold += $qty;
      } elseif ($type === 'TRANSFER') {
        // transfer: treat as out from source tank and in to dest tank.
        $from = $tx->from_tank_id;
        $to = $tx->to_tank_id;
        if (!isset($summaries[$from])) {
          $summaries[$from] = [
            'product' => $tx->source_product ?? 'Unknown',
            'opening_stock' => 0.0,
            'total_in' => 0.0,
            'total_out' => 0.0,
          ];
        }
        if (!isset($summaries[$to])) {
          $summaries[$to] = [
            'product' => $tx->destination_product ?? 'Unknown',
            'opening_stock' => 0.0,
            'total_in' => 0.0,
            'total_out' => 0.0,
          ];
        }
        $summaries[$from]['total_out'] += $qty;
        $summaries[$to]['total_in'] += $qty;
      }
    }

    // compute tank summaries
    $tank_summaries = [];
    foreach ($summaries as $tank_id => $info) {
      $calc_closing = round($info['opening_stock'] + $info['total_in'] - $info['total_out'], 4);
      $phys = $info['physical_closing_stock'] ?? 0.0;
      $loss_gain = round($calc_closing - $phys, 4);

      $tank_summaries[] = [
        'tank_id' => $tank_id,
        'product' => $info['product'],
        'opening_stock' => (float) $info['opening_stock'],
        'total_in' => (float) round($info['total_in'], 4),
        'total_out' => (float) round($info['total_out'], 4),
        'calculated_closing_stock' => (float) $calc_closing,
        'physical_closing_stock' => (float) $phys,
        'loss_gain' => (float) $loss_gain,
      ];
    }

    // production summary
    $total_product = $olein_prod + $stearin_prod;
    $yield = $cpo_processed > 0 ? round(($total_product / $cpo_processed) * 100, 4) : 0.0;

    $report = [
      'report_date' => $data['report_date'] ?? now()->toDateString(),
      'tank_summaries' => $tank_summaries,
      'production_summary' => [
        'cpo_processed_ton' => (float) $cpo_processed,
        'olein_produced_ton' => (float) $olein_prod,
        'stearin_produced_ton' => (float) $stearin_prod,
        'total_product_produced_ton' => (float) $total_product,
        'yield_percentage' => (float) $yield,
      ],
      'transaction_summary' => [
        'total_received_ton' => (float) $total_received,
        'total_sold_ton' => (float) $total_sold,
      ],
    ];

    return $report;
  }
}
