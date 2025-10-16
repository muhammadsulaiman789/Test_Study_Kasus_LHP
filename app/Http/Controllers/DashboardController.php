<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Config\Datahelper;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  public function index()
  {
    $dailyReport = Datahelper::loadDailyReport();

    $tanks = $dailyReport['tanks'];
    $transactions = Datahelper::loadTransaction();

    return view('dashboard', [
      'reportDate' => date('d-M-y', strtotime($dailyReport['report_date'])),
      'tanks' => $tanks,
      'transactions' => $transactions,
    ]);
  }

  public function addTransaction(Request $request)
  {
    $request->validate([
      'type' => 'required|string',
      'product' => 'required|string',
      'from' => 'nullable|string',
      'to' => 'nullable|string',
      'volume' => 'required|numeric',
      'notes' => 'nullable|string',
    ]);

    $transactions = Datahelper::loadTransaction();
    $transactions[] = Transaction::fromRequest($request);
    session(['transactions' => $transactions]);

    return redirect()->route('dashboard')->with('success', 'Transaction added successfully.');
  }

  public function sourceDestinationTank($type)
  {
    switch ($type) {
      case 'RECEIVE':
        return [
          'from' => [],
          'to' => ['T01', 'T02'],
        ];
      case 'SELL':
        return [
          'from' => ['T04', 'T05'],
          'to' => [],
        ];
      case 'TRANSFER':
        return [
          'from' => ['T03'],
          'to' => ['T01', 'T02'],
        ];
      case 'PROCESS OUT':
        return [
          'from' => ['T01', 'T02', 'T03'],
          'to' => [],
        ];
      case 'PROCESS IN':
        return [
          'from' => [],
          'to' => ['T03', 'T04', 'T05'],
        ];
      default:
        return [];
    }
  }
}
