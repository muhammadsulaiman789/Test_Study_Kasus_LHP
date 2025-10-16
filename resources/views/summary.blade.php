<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daily Tank Report</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-800">
  <div class="max-w-6xl mx-auto p-6 bg-white shadow rounded-lg mt-8">
    <h2 class="text-center text-2xl font-bold mb-6">Report Summary</h2>

    <div class="mb-4">
      <p class="text-sm">Date : {{ now()->format('d M Y') }}</p>
    </div>

    {{-- Tank Summaries --}}
    <h3 class="font-semibold mb-2">Tank Summaries</h3>
    <table class="w-full border border-gray-400 text-sm text-center mb-6">
      <thead class="bg-gray-100">
        <tr>
          <th class="border border-gray-400 p-2">Tank</th>
          <th class="border border-gray-400 p-2">Product</th>
          <th class="border border-gray-400 p-2" colspan="2">Process</th>
          <th class="border border-gray-400 p-2">Initial Stock</th>
          <th class="border border-gray-400 p-2">Physical Closing Stock</th>
          <th class="border border-gray-400 p-2">Calculated Closing Stock</th>
          <th class="border border-gray-400 p-2">Loss Gain</th>
        </tr>
        <tr class="bg-gray-50">
          <th colspan="2"></th>
          <th class="border border-gray-400 p-2">In</th>
          <th class="border border-gray-400 p-2">Out</th>
          <th colspan="4"></th>
        </tr>
      </thead>
      <tbody>
        @if (!empty($tank_summaries) && count($tank_summaries) > 0)
          @foreach ($tank_summaries as $tx)
            <tr class="hover:bg-gray-50">
              <td class="border border-gray-400 p-2">{{ $tx['tank_id'] }}</td>
              <td class="border border-gray-400 p-2">{{ $tx['product'] }}</td>
              <td class="border border-gray-400 p-2">{{ $tx['total_in'] }}</td>
              <td class="border border-gray-400 p-2">{{ $tx['total_out'] }}</td>
              <td class="border border-gray-400 p-2">{{ $tx['opening_stock'] }}</td>
              <td class="border border-gray-400 p-2">{{ $tx['physical_closing_stock'] }}</td>
              <td class="border border-gray-400 p-2">{{ $tx['calculated_closing_stock'] }}</td>
              <td class="border border-gray-400 p-2">{{ $tx['loss_gain'] }}</td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="6" class="text-center py-4 text-gray-500 bg-gray-50">
              There are no transaction today
            </td>
          </tr>
        @endif
      </tbody>
    </table>

    {{-- Production Summary --}}
    <h3 class="font-semibold mb-2">Production Summary</h3>
    <table class="w-full border border-gray-400 text-sm text-center mb-6">
      <thead class="bg-gray-100">
        <tr>
          <th class="border border-gray-400 p-2">CPO</th>
          <th class="border border-gray-400 p-2">Olein</th>
          <th class="border border-gray-400 p-2">Stearin</th>
          <th class="border border-gray-400 p-2">Produce</th>
          <th class="border border-gray-400 p-2">Yield</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="border border-gray-400 p-2">{{ $production_summary['cpo_processed_ton'] }}</td>
          <td class="border border-gray-400 p-2">{{ $production_summary['olein_produced_ton'] }}</td>
          <td class="border border-gray-400 p-2">{{ $production_summary['stearin_produced_ton'] }}</td>
          <td class="border border-gray-400 p-2">{{ $production_summary['total_product_produced_ton'] }}</td>
          <td class="border border-gray-400 p-2">{{ $production_summary['yield_percentage'] }}</td>
        </tr>
      </tbody>
    </table>

    {{-- Transaction Summary --}}
    <h3 class="font-semibold mb-2">Transaction Summary</h3>
    <table class="w-full border border-gray-400 text-sm text-center mb-8">
      <thead class="bg-gray-100">
        <tr>
          <th class="border border-gray-400 p-2">Received</th>
          <th class="border border-gray-400 p-2">Sold</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="border border-gray-400 p-2">{{ $transaction_summary['total_received_ton'] }}</td>
          <td class="border border-gray-400 p-2">{{ $transaction_summary['total_sold_ton'] }}</td>
        </tr>
      </tbody>
    </table>

    {{-- Action Buttons --}}
    <div class="flex justify-center gap-4">
      <a href="{{ route('export.json') }}">
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Export</button>
      </a>
    </div>
  </div>

  {{-- Modal Dialog --}}
  <div id="transactionModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg">
      <h3 class="text-lg font-semibold mb-4">New Transaction</h3>
      <form id="transactionForm" class="space-y-3">
        {{-- Type --}}
        <div>
          <label class="block text-sm font-medium">Type</label>
          <select id="type" class="w-full border border-gray-300 rounded px-2 py-1 mt-1">
            <option value="">Select Type</option>
            <option value="Receive">Receive</option>
            <option value="Process">Process</option>
            <option value="Transfer">Transfer</option>
            <option value="Sell">Sell</option>
          </select>
        </div>

        {{-- From / To --}}
        <div class="grid grid-cols-2 gap-2">
          <div id="fromTank">
            <label class="block text-sm font-medium">From</label>
            <select id="from" class="w-full border border-gray-300 rounded px-2 py-1 mt-1">
              <option value="">Select Tank</option>
            </select>
          </div>
          <div id="toTank">
            <label class="block text-sm font-medium">To</label>
            <select id="to" class="w-full border border-gray-300 rounded px-2 py-1 mt-1">
              <option value="">Select Tank</option>
            </select>
          </div>
        </div>

        {{-- Product --}}
        <div>
          <label class="block text-sm font-medium">Product</label>
          <input type="text" id="product" readonly
            class="w-full border border-gray-300 rounded px-2 py-1 mt-1 bg-gray-100 text-orange-600 font-medium">
        </div>

        {{-- Volume --}}
        <div>
          <label class="block text-sm font-medium">Volume</label>
          <input type="number" id="volume" placeholder="Enter volume"
            class="w-full border border-gray-300 rounded px-2 py-1 mt-1 text-orange-600 font-medium">
        </div>

        {{-- Note --}}
        <div>
          <label class="block text-sm font-medium">Note</label>
          <textarea id="note" rows="3"
            class="w-full border border-gray-300 rounded px-2 py-1 mt-1 text-orange-600 font-medium"></textarea>
        </div>

        {{-- Buttons --}}
        <div class="flex justify-end gap-3 mt-4">
          <button type="button" id="closeModalBtn"
            class="px-3 py-1 rounded bg-gray-300 hover:bg-gray-400">Cancel</button>
          <button type="submit" class="px-4 py-1 rounded bg-blue-600 text-white hover:bg-blue-700">Submit</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const modal = document.getElementById('transactionModal');
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const typeSelect = document.getElementById('type');
    const productInput = document.getElementById('product');
    const fromTankInput = document.getElementById('fromTank');
    const toTankInput = document.getElementById('toTank');
    const fromSelect = document.getElementById('from');
    const toSelect = document.getElementById('to');

    const tankOptions = {
      "Receive": {
        from: [],
        to: ["Tank 01", "Tank 02"]
      },
      "Sell": {
        from: ["Tank 04", "Tank 05"],
        to: []
      },
      "Transfer": {
        from: ["Tank 03"],
        to: ["Tank 01", "Tank 02"]
      },
      "Process": {
        from: ["Tank 01", "Tank 02", "Tank 03"],
        to: []
      }
    };

    // Close modal
    closeModalBtn.addEventListener('click', () => {
      modal.classList.add('hidden');
      fromTankInput.classList.remove('hidden');
      toTankInput.classList.remove('hidden');
      typeSelect.value = '';
      productInput.value = '';
    });

    fromSelect.addEventListener('change', () => {
      const from = fromSelect.value;
      const type = typeSelect.value;

      if (type == "Process") {
        toSelect.innerHTML = '<option value="">Select To</option>';

        if (from == "Tank 01" || from == "Tank 02") {
          const opt = document.createElement('option');
          opt.value = "Tank 03";
          opt.textContent = "Tank 03";
          toSelect.appendChild(opt);

          productInput.value = "CPO";
        }

        if (from == "Tank 03") {
          const tank04 = document.createElement('option');
          tank04.value = "Tank 04";
          tank04.textContent = "Tank 04";
          toSelect.appendChild(tank04);

          const tank05 = document.createElement('option');
          tank05.value = "Tank 05";
          tank05.textContent = "Tank 05";
          toSelect.appendChild(tank05);

          productInput.value = "RBDPO";
        }
      }

      if (type == "Sell") {
        if (from == "Tank 04") {
          productInput.value = "Olein";
        } else if (from == "Tank 05") {
          productInput.value = "Stearin";
        } else {
          productInput.value = "";
        }
      }
    });

    // Set Product based on Type
    typeSelect.addEventListener('change', () => {
      const type = typeSelect.value;
      let product = '';

      fromSelect.innerHTML = '<option value="">Select From</option>';
      toSelect.innerHTML = '<option value="">Select To</option>';

      fromTankInput.classList.remove('hidden');
      toTankInput.classList.remove('hidden');

      switch (type) {
        case 'Receive':
          product = 'CPO';
          fromTankInput.classList.add('hidden');
          break;
        case 'Transfer':
          product = 'RBDPO';
          break;
        case 'Sell':
          toTankInput.classList.add('hidden');
          break;
        default:
          product = '';
      }

      if (tankOptions[type]) {
        tankOptions[type].from.forEach(f => {
          const opt = document.createElement('option');
          opt.value = f;
          opt.textContent = f;
          fromSelect.appendChild(opt);
        });

        if (type != "Process") {
          tankOptions[type].to.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t;
            opt.textContent = t;
            toSelect.appendChild(opt);
          });
        }
      }
      productInput.value = product;
    });

    // Submit (demo only)
    document.getElementById('transactionForm').addEventListener('submit', (e) => {
      e.preventDefault();
      alert('Transaction submitted!');
      modal.classList.add('hidden');
    });
  </script>
</body>

</html>
