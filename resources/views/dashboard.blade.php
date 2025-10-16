<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tank Daily Report</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-800 p-6">

  <div class="max-w-6xl mx-auto p-6 bg-white shadow rounded-lg mt-8">
    <!-- Header -->
    <div class="text-center mb-6">
      <h2 class="text-lg font-semibold">Tank Daily Report</h2>
      <p class="text-sm text-gray-600">{{ now()->format('d-M-y') }}</p>
    </div>

    <!-- Tank Information -->
    <div class="max-w-5xl mx-auto mb-8">
      <h3 class="text-sm font-semibold mb-2">Tank Information</h3>
      <p>Report Date : {{ $reportDate }}</p>
      <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full border border-gray-300 text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="border border-gray-300 py-2 px-4 text-center">Tank</th>
              <th class="border border-gray-300 py-2 px-4 text-center">Product</th>
              <th class="border border-gray-300 py-2 px-4 text-center">Initial Ton</th>
              <th class="border border-gray-300 py-2 px-4 text-center">Final Physical Ton</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($tanks as $tank)
              <tr class="hover:bg-gray-50">
                <td class="border border-gray-300 py-2 px-4 text-center">{{ $tank->tank_name }}</td>
                <td class="border border-gray-300 py-2 px-4 text-center">{{ $tank->product }}</td>
                <td class="border border-gray-300 py-2 px-4 text-center">{{ $tank->initial_ton }}</td>
                <td class="border border-gray-300 py-2 px-4 text-center">{{ $tank->final_phys_ton }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="flex justify-center gap-8 mt-4">
        <button id="newTransactionBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded shadow">
          New Transaction
        </button>
        <a href="{{ route('summary') }}">
          <button id="viewSummaryBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded shadow">
          View Summary
        </button>
        </a>
      </div>
    </div>

    <!-- Transaction History -->
    <div class="max-w-5xl mx-auto">
      <h3 class="text-sm font-semibold mb-2">Today Transaction</h3>
      <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full border border-gray-300 text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="border border-gray-300 py-2 px-4 text-center">Type</th>
              <th class="border border-gray-300 py-2 px-4 text-center">From Tank</th>
              <th class="border border-gray-300 py-2 px-4 text-center">To Tank</th>
              <th class="border border-gray-300 py-2 px-4 text-center">Product</th>
              <th class="border border-gray-300 py-2 px-4 text-center">Quantity</th>
              <th class="border border-gray-300 py-2 px-4 text-center">Notes</th>
            </tr>
          </thead>
          @if (!empty($transactions) && count($transactions) > 0)
            @foreach ($transactions as $tx)
              <tr class="hover:bg-gray-50">
                <td class="border border-gray-300 py-2 px-4 text-center">{{ $tx->type }}</td>
                <td class="border border-gray-300 py-2 px-4 text-center">{{ $tx->from_tank_id }}</td>
                <td class="border border-gray-300 py-2 px-4 text-center">{{ $tx->to_tank_id }}</td>
                <td class="border border-gray-300 py-2 px-4 text-center">{{ $tx->product }}</td>
                <td class="border border-gray-300 py-2 px-4 text-center">{{ $tx->quantity_ton }}</td>
                <td class="border border-gray-300 py-2 px-4 text-center">{{ $tx->notes }}</td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="6" class="text-center py-4 text-gray-500 bg-gray-50">
                There are no transaction today
              </td>
            </tr>
          @endif
        </table>
      </div>
    </div>
  </div>

  @include('components.add_transaction_dialog', ['tanks' => $tanks])

</body>

</html>
