let pizzaPrices = {};
let pizzaNames = {};

function initializePizzaData(prices, names) {
  pizzaPrices = prices;
  pizzaNames = names;
}

document.addEventListener('DOMContentLoaded', () => {
  const customerSelect = document.getElementById('customer_id');
  if (customerSelect) {
    new TomSelect('#customer_id', {
      placeholder: 'Selecione ou busque um cliente...',
      searchField: ['text'],
      create: false,
      render: {
        option: function (data, escape) {
          return '<div>' +
            '<strong>' + escape(data.text.split(' - ')[0]) + '</strong><br>' +
            '<small>' + escape(data.text.split(' - ')[1] || '') + '</small>' +
            '</div>';
        },
        item: function (data, escape) {
          return '<div>' + escape(data.text) + '</div>';
        }
      }
    });
  }

  document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('input', updateOrderSummary);
  });
});

function increaseQuantity(pizzaId) {
  const input = document.getElementById('qty_' + pizzaId);
  const currentValue = parseInt(input.value);
  if (currentValue < 99) {
    input.value = currentValue + 1;
    updateOrderSummary();
  }
}

function decreaseQuantity(pizzaId) {
  const input = document.getElementById('qty_' + pizzaId);
  const currentValue = parseInt(input.value);
  if (currentValue > 0) {
    input.value = currentValue - 1;
    updateOrderSummary();
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
    summaryDiv.innerHTML = '<div class="summary-item"><span>Nenhuma pizza selecionada</span><span>R$ 0,00</span></div>';
    submitBtn.disabled = true;
  } else {
    let html = '';
    items.forEach(item => {
      html += `<div class="summary-item">
                <span>${item.quantity}x ${item.name}</span>
                <span>R$ ${item.subtotal.toFixed(2).replace('.', ',')}</span>
            </div>`;
    });
    html += `<div class="summary-item total">
            <span>Total</span>
            <span>R$ ${total.toFixed(2).replace('.', ',')}</span>
        </div>`;

    summaryDiv.innerHTML = html;
    submitBtn.disabled = false;
  }
}