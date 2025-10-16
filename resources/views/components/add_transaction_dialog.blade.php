{{-- Modal Dialog --}}
<div id="transactionModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50">
  <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg">
    <h3 class="text-lg font-semibold mb-4">New Transaction</h3>
    <form method="POST" action="{{ route('transaction.add') }}" id="transactionForm" class="space-y-3">
      @csrf

      {{-- Type --}}
      <div>
        <label class="block text-sm font-medium">Type</label>
        <select id="type" name="type" class="w-full border border-gray-300 rounded px-2 py-1 mt-1" onchange="loadTankSourceDestination(this.value)">
          <option value="">Select Type</option>
          <option value="RECEIVE">RECEIVE</option>
          <option value="PROCESS IN">PROCESS IN</option>
          <option value="PROCESS OUT">PROCESS OUT</option>
          <option value="TRANSFER">TRANSFER</option>
          <option value="SELL">SELL</option>
        </select>
      </div>

      {{-- From / To --}}
      <div class="grid grid-cols-2 gap-2">
        <div id="fromTank">
          <label class="block text-sm font-medium">From</label>
          <select id="from" name="from" class="w-full border border-gray-300 rounded px-2 py-1 mt-1">
            <option value="">Select Tank</option>
          </select>
        </div>
        <div id="toTank">
          <label class="block text-sm font-medium">To</label>
          <select id="to" name="to" class="w-full border border-gray-300 rounded px-2 py-1 mt-1">
            <option value="">Select Tank</option>
          </select>
        </div>
      </div>

      {{-- Product --}}
      <div>
        <label class="block text-sm font-medium">Product</label>
        <input type="text" id="product" readonly name="product"
          class="w-full border border-gray-300 rounded px-2 py-1 mt-1 bg-gray-100 text-orange-600 font-medium">
      </div>

      {{-- Volume --}}
      <div>
        <label class="block text-sm font-medium">Volume</label>
        <input type="number" name="volume" id="volume" placeholder="Enter volume"
          class="w-full border border-gray-300 rounded px-2 py-1 mt-1 text-orange-600 font-medium">
      </div>

      {{-- Note --}}
      <div>
        <label class="block text-sm font-medium">Note</label>
        <textarea id="notes" name="note" rows="3"
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

<script src="{{ asset('js/modal_transaction.js') }}"></script>

<!-- <script>
  const modal = document.getElementById('transactionModal');
  const openModalBtn = document.getElementById('newTransactionBtn');
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
    "Process Out": {
      from: ["Tank 01", "Tank 02", "Tank 03"],
      to: []
    },
    "Process In": {
      from: [],
      to: ["Tank 03", "Tank 04", "Tank 05"]
    }
  };

  openModalBtn.addEventListener('click', () => {
    modal.classList.remove('hidden');
  });

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

    if (type == "Sell") {
      if (from == "Tank 04") {
        productInput.value = "Olein";
      } else if (from == "Tank 05") {
        productInput.value = "Stearin";
      } else {
        productInput.value = "";
      }
    }

    if (type == "Process Out") {
      if (from == "Tank 01" || from == "Tank 02") {
        productInput.value = "CPO";
      } else if (from == "Tank 03") {
        productInput.value = "RBDPO"
      } else {
        productInput.value = "";
      }
    }
  });

  toSelect.addEventListener('change', () => {
    const to = toSelect.value;
    const type = typeSelect.value;

    if (type == "Process In") {
      if (to == "Tank 03") {
        productInput.value = "RBDPO";
      } else if (to == "Tank 04") {
        productInput.value = "Olein"
      } else if (to == "Tank 05") {
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
      case 'Process In':
        fromTankInput.classList.add('hidden');
        break;
      case 'Process Out':
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

      tankOptions[type].to.forEach(t => {
        const opt = document.createElement('option');
        opt.value = t;
        opt.textContent = t;
        toSelect.appendChild(opt);
      });
    }
    productInput.value = product;
  });
</script> -->
