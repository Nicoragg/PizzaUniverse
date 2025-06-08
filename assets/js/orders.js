let pizzaPrices = {};
let pizzaNames = {};
let tomSelectInstance = null;

function initializePizzaData(prices, names) {
  pizzaPrices = prices;
  pizzaNames = names;
}

document.addEventListener('DOMContentLoaded', () => {
  // Inicializar TomSelect para o campo de cliente
  const customerSelect = document.getElementById('customer_id');
  if (customerSelect) {
    tomSelectInstance = new TomSelect('#customer_id', {
      placeholder: 'Selecione ou busque um cliente...',
      searchField: ['text'],
      create: false,
      maxOptions: null,
      render: {
        option: (data, escape) => {
          return '<div class="tom-select-option">' +
            '<strong>' + escape(data.text) + '</strong>' +
            '</div>';
        },
        item: (data, escape) => {
          return '<div class="tom-select-item">' + escape(data.text) + '</div>';
        }
      },
      onChange: function (value) {
        handleCustomerSelection(value);
      }
    });
  }

  initializePizzaCards();

  document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('input', updateOrderSummary);
  });

  // Trigger customer selection if there's a pre-selected value
  if (customerSelect && customerSelect.value) {
    handleCustomerSelection(customerSelect.value);
  }
});

function handleCustomerSelection(customerId) {
  const customerInfo = document.getElementById('customer-info');

  if (!customerId) {
    customerInfo.style.display = 'none';
    return;
  }

  // Buscar o option selecionado
  const originalSelect = document.querySelector('#customer_id option[value="' + customerId + '"]');

  if (originalSelect) {
    // Atualizar os dados do cliente na visualização
    document.getElementById('customer-name').textContent = originalSelect.getAttribute('data-name') || '-';
    document.getElementById('customer-phone').textContent = formatPhone(originalSelect.getAttribute('data-phone')) || '-';
    document.getElementById('customer-cpf').textContent = formatCPF(originalSelect.getAttribute('data-cpf')) || '-';
    document.getElementById('customer-street').textContent = originalSelect.getAttribute('data-street') || '-';
    document.getElementById('customer-neighborhood').textContent = originalSelect.getAttribute('data-neighborhood') || '-';
    document.getElementById('customer-city').textContent = originalSelect.getAttribute('data-city') || '-';
    document.getElementById('customer-state').textContent = originalSelect.getAttribute('data-state') || '-';
    document.getElementById('customer-zipcode').textContent = formatZipcode(originalSelect.getAttribute('data-zipcode')) || '-';

    customerInfo.style.display = 'block';
  } else {
    customerInfo.style.display = 'none';
  }
}

document.getElementById('customer_id').addEventListener('change', () => {
  var selectedOption = this.options[this.selectedIndex];
  var customerInfo = document.getElementById('customer-info');

  if (this.value) {
    // Atualiza os dados do cliente na visualização
    document.getElementById('customer-name').innerText = selectedOption.getAttribute('data-name');
    document.getElementById('customer-phone').innerText = selectedOption.getAttribute('data-phone');
    document.getElementById('customer-cpf').innerText = selectedOption.getAttribute('data-cpf');
    document.getElementById('customer-street').innerText = selectedOption.getAttribute('data-street');
    document.getElementById('customer-neighborhood').innerText = selectedOption.getAttribute('data-neighborhood');
    document.getElementById('customer-city').innerText = selectedOption.getAttribute('data-city');
    document.getElementById('customer-state').innerText = selectedOption.getAttribute('data-state');
    document.getElementById('customer-zipcode').innerText = selectedOption.getAttribute('data-zipcode');

    customerInfo.style.display = 'block';
  } else {
    customerInfo.style.display = 'none';
  }
});

function formatPhone(phone) {
  if (!phone) return '';
  const cleaned = phone.replace(/\D/g, '');
  if (cleaned.length === 11) {
    return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2, 7)}-${cleaned.slice(7)}`;
  } else if (cleaned.length === 10) {
    return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2, 6)}-${cleaned.slice(6)}`;
  }
  return phone;
}

function formatCPF(cpf) {
  if (!cpf) return '';
  const cleaned = cpf.replace(/\D/g, '');
  if (cleaned.length === 11) {
    return `${cleaned.slice(0, 3)}.${cleaned.slice(3, 6)}.${cleaned.slice(6, 9)}-${cleaned.slice(9)}`;
  }
  return cpf;
}

function formatZipcode(zipcode) {
  if (!zipcode) return '';
  const cleaned = zipcode.replace(/\D/g, '');
  if (cleaned.length === 8) {
    return `${cleaned.slice(0, 5)}-${cleaned.slice(5)}`;
  }
  return zipcode;
}

function initializePizzaCards() {
  document.querySelectorAll('.pizza-card').forEach(card => {
    const pizzaId = card.getAttribute('data-pizza-id');
    const quantityInput = document.getElementById('qty_' + pizzaId);

    updateCardAppearance(card, parseInt(quantityInput.value));
  });
}

function increaseQuantity(pizzaId) {
  const input = document.getElementById('qty_' + pizzaId);
  const currentValue = parseInt(input.value);
  const newValue = Math.min(currentValue + 1, 99);

  input.value = newValue;
  input.classList.add('changed');
  setTimeout(() => input.classList.remove('changed'), 300);

  updatePizzaCardState(pizzaId, newValue);
  updateOrderSummary();
}

function decreaseQuantity(pizzaId) {
  const input = document.getElementById('qty_' + pizzaId);
  const currentValue = parseInt(input.value);
  const newValue = Math.max(currentValue - 1, 0);

  input.value = newValue;
  input.classList.add('changed');
  setTimeout(() => input.classList.remove('changed'), 300);

  updatePizzaCardState(pizzaId, newValue);
  updateOrderSummary();
}

function updatePizzaCardState(pizzaId, quantity) {
  const card = document.querySelector(`[data-pizza-id="${pizzaId}"]`);
  if (card) {
    card.setAttribute('data-quantity', quantity);
    if (quantity > 0) {
      card.classList.add('selected');
    } else {
      card.classList.remove('selected');
    }
  }
}

function updateOrderSummary() {
  const summaryDiv = document.getElementById('order-summary');
  const submitBtn = document.getElementById('submit-order');
  let total = 0;
  let items = [];

  document.querySelectorAll('.quantity-input').forEach(input => {
    const pizzaId = input.id.replace('qty_', '');
    const quantity = parseInt(input.value);

    if (quantity > 0) {
      const price = pizzaPrices[pizzaId];
      const name = pizzaNames[pizzaId];
      const subtotal = price * quantity;

      items.push({
        name: name,
        quantity: quantity,
        price: price,
        subtotal: subtotal
      });

      total += subtotal;
    }
  });

  if (items.length === 0) {
    summaryDiv.innerHTML = `
      <div class="summary-item">
        <span><i class="bi bi-cart-x"></i> Nenhuma pizza selecionada</span>
        <span>R$ 0,00</span>
      </div>`;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-lock"></i> Selecione as pizzas';
  } else {
    let html = '<div class="summary-items">';
    items.forEach(item => {
      html += `
        <div class="summary-item">
          <span>
            <strong>${item.quantity}x</strong> ${item.name}
            <small>R$ ${item.price.toFixed(2).replace('.', ',')} cada</small>
          </span>
          <span class="item-subtotal">R$ ${item.subtotal.toFixed(2).replace('.', ',')}</span>
        </div>`;
    });
    html += `</div>
      <div class="summary-item total">
        <span><i class="bi bi-receipt"></i> <strong>Total (${items.length} ${items.length === 1 ? 'tipo' : 'tipos'})</strong></span>
        <span><strong>R$ ${total.toFixed(2).replace('.', ',')}</strong></span>
      </div>`;

    summaryDiv.innerHTML = html;
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Finalizar Pedido';
  }

  // Add smooth animation to summary update
  summaryDiv.style.opacity = '0.5';
  setTimeout(() => {
    summaryDiv.style.opacity = '1';
  }, 100);
}

// Add keyboard support for quantity controls
document.addEventListener('keydown', (e) => {
  const activeElement = document.activeElement;

  if (activeElement && activeElement.classList.contains('quantity-input')) {
    const pizzaId = activeElement.id.replace('qty_', '');

    if (e.key === 'ArrowUp' || e.key === '+') {
      e.preventDefault();
      increaseQuantity(pizzaId);
    } else if (e.key === 'ArrowDown' || e.key === '-') {
      e.preventDefault();
      decreaseQuantity(pizzaId);
    }
  }
});

// Add smooth scrolling to form sections
function scrollToSection(sectionId) {
  const section = document.getElementById(sectionId);
  if (section) {
    section.scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    });
  }
}