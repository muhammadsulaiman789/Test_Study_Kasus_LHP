const modal = document.getElementById('transactionModal');
const openModalBtn = document.getElementById('newTransactionBtn');
const closeModalBtn = document.getElementById('closeModalBtn');
const typeSelect = document.getElementById('type');
const productInput = document.getElementById('product');
const fromTankInput = document.getElementById('fromTank');
const toTankInput = document.getElementById('toTank');
const fromSelect = document.getElementById('from');
const toSelect = document.getElementById('to');

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

  if (type == 'SELL') {
    if (from == 'T04') {
      productInput.value = 'Olein';
    } else if (from == 'T05') {
      productInput.value = 'Stearin';
    } else {
      productInput.value = '';
    }
  }

  if (type == 'PROCESS OUT') {
    if (from == 'T01' || from == 'T02') {
      productInput.value = 'CPO';
    } else if (from == 'T03') {
      productInput.value = 'RBDPO';
    } else {
      productInput.value = '';
    }
  }
});

toSelect.addEventListener('change', () => {
  const to = toSelect.value;
  const type = typeSelect.value;

  if (type == 'PROCESS IN') {
    if (to == 'T03') {
      productInput.value = 'RBDPO';
    } else if (to == 'T04') {
      productInput.value = 'Olein';
    } else if (to == 'T05') {
      productInput.value = 'Stearin';
    } else {
      productInput.value = '';
    }
  }
});

async function loadTankSourceDestination(type) {
  try {
    fromSelect.innerHTML = '<option value="">Select From</option>';
    toSelect.innerHTML = '<option value="">Select To</option>';
    let product = '';

    fromTankInput.classList.remove('hidden');
    toTankInput.classList.remove('hidden');

    switch (type) {
      case 'RECEIVE':
        product = 'CPO';
        fromTankInput.classList.add('hidden');
        break;
      case 'TRANSFER':
        product = 'RBDPO';
        break;
      case 'SELL':
        toTankInput.classList.add('hidden');
        break;
      case 'PROCESS IN':
        fromTankInput.classList.add('hidden');
        break;
      case 'PROCESS OUT':
        toTankInput.classList.add('hidden');
        break;
      default:
        product = '';
    }

    productInput.value = product;

    const response = await fetch(`/tank/${type}`);
    const data = await response.json();

    data['from'].forEach((f) => {
      const opt = document.createElement('option');
      opt.value = f;
      opt.textContent = f;
      fromSelect.appendChild(opt);
    });

    data['to'].forEach((t) => {
      const opt = document.createElement('option');
      opt.value = t;
      opt.textContent = t;
      toSelect.appendChild(opt);
    });
  } catch (error) {
    console.error('Error fetching options:', error);
  }
}
