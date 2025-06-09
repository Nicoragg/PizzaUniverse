class OrderManager {
  constructor() {
    this.pizzaPrices = {};
    this.pizzaNames = {};
    this.tomSelectInstance = null;
    this.elements = {};

    this.init();
  }

  init() {
    this.cacheElements();
    this.bindEvents();
    this.initializeComponents();
  }

  cacheElements() {
    this.elements = {
      customerSelect: document.getElementById('customer_id'),
      customerInfo: document.getElementById('customer-info'),
      orderSummary: document.getElementById('order-summary'),
      submitBtn: document.getElementById('submit-order'),
      quantityInputs: document.querySelectorAll('.orders-quantity-input'),
      pizzaCards: document.querySelectorAll('.orders-pizza-card')
    };
  }

  bindEvents() {
    document.addEventListener('DOMContentLoaded', () => this.onDOMContentLoaded());
    document.addEventListener('keydown', (e) => this.handleKeyboardEvents(e));

    this.elements.quantityInputs.forEach(input => {
      input.addEventListener('input', () => this.updateOrderSummary());
    });
  }

  onDOMContentLoaded() {
    this.initializeCustomerSelect();
    this.initializePizzaCards();

    if (this.elements.customerSelect?.value) {
      this.handleCustomerSelection(this.elements.customerSelect.value);
    }
  }

  initializePizzaData(prices, names) {
    this.pizzaPrices = prices;
    this.pizzaNames = names;
  }

  initializeComponents() {
    this.customerHandler = new CustomerHandler(this);
    this.pizzaHandler = new PizzaHandler(this);
    this.orderSummaryHandler = new OrderSummaryHandler(this);
  }

  initializeCustomerSelect() {
    if (!this.elements.customerSelect) return;

    if (typeof TomSelect === 'undefined') {
      console.warn('TomSelect não está disponível. Carregando dinamicamente...');
      this.loadTomSelect();
      return;
    }

    this.createTomSelectInstance();
  }

  loadTomSelect() {
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js';
    script.onload = () => {
      this.createTomSelectInstance();
    };
    script.onerror = () => {
      console.error('Erro ao carregar TomSelect');
    };
    document.head.appendChild(script);
  }

  createTomSelectInstance() {
    this.tomSelectInstance = new TomSelect('#customer_id', {
      placeholder: 'Selecione ou busque um cliente...',
      searchField: ['text'],
      create: false,
      maxOptions: null,
      loadThrottle: 300,
      closeAfterSelect: true,
      render: {
        option: (data, escape) => {
          const option = document.querySelector(`option[value="${data.value}"]`);
          if (!option) return `<div class="tom-select-option">${escape(data.text)}</div>`;

          const phone = option.getAttribute('data-phone') || '';
          const city = option.getAttribute('data-city') || '';
          const state = option.getAttribute('data-state') || '';

          return `
            <div class="tom-select-option">
              <strong>${escape(data.text)}</strong>
              <div class="customer-details">
                ${phone ? `📞 ${new DataFormatter().formatPhone(phone)}` : ''}
                ${city && state ? ` • 📍 ${escape(city)} - ${escape(state)}` : ''}
              </div>
            </div>
          `;
        },
        item: (data, escape) => `
          <div class="tom-select-item" style="line-height: 1.4; padding: 8px 12px;">
            <i class="bi bi-person-check" style="margin-right: 6px;"></i>
            <span>${escape(data.text)}</span>
          </div>
        `,
        no_results: () => `
          <div class="no-results">
            <i class="bi bi-search"></i>
            <span>Nenhum cliente encontrado</span>
          </div>
        `
      },
      onChange: (value) => this.handleCustomerSelection(value),
      onFocus: () => this.onTomSelectFocus(),
      onBlur: () => this.onTomSelectBlur()
    });

    const wrapper = this.tomSelectInstance.wrapper;
    wrapper.classList.add('orders-tom-select');

    const control = this.tomSelectInstance.control;
    control.style.cursor = 'pointer';

    this.tomSelectInstance.control_input.style.cursor = 'pointer';
    this.tomSelectInstance.control_input.readOnly = true;

    this.tomSelectInstance.on('change', () => {
      this.addSelectionAnimation();
      setTimeout(() => {
        this.tomSelectInstance.blur();
        this.tomSelectInstance.control_input.blur();
      }, 100);
    });
  }

  onTomSelectFocus() {
    const wrapper = this.tomSelectInstance.wrapper;
    wrapper.classList.add('focus');
  }

  onTomSelectBlur() {
    const wrapper = this.tomSelectInstance.wrapper;
    wrapper.classList.remove('focus');
  }

  addSelectionAnimation() {
    const control = this.tomSelectInstance.control;
    control.style.transform = 'scale(0.98)';
    setTimeout(() => {
      control.style.transform = 'scale(1)';
    }, 150);
  }

  handleCustomerSelection(customerId) {
    this.customerHandler.handleSelection(customerId);
  }

  initializePizzaCards() {
    this.pizzaHandler.initializeCards();
  }


  increaseQuantity(pizzaId) {
    this.pizzaHandler.increaseQuantity(pizzaId);
  }

  decreaseQuantity(pizzaId) {
    this.pizzaHandler.decreaseQuantity(pizzaId);
  }

  updateOrderSummary() {
    this.orderSummaryHandler.update();
  }

  handleKeyboardEvents(e) {
    const activeElement = document.activeElement;

    if (activeElement?.classList.contains('orders-quantity-input')) {
      const pizzaId = activeElement.id.replace('qty_', '');

      if (e.key === 'ArrowUp' || e.key === '+') {
        e.preventDefault();
        this.increaseQuantity(pizzaId);
      } else if (e.key === 'ArrowDown' || e.key === '-') {
        e.preventDefault();
        this.decreaseQuantity(pizzaId);
      }
    }
  }

  scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
      section.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
  }
}
class CustomerHandler {
  constructor(orderManager) {
    this.orderManager = orderManager;
    this.formatter = new DataFormatter();
  }

  handleSelection(customerId) {
    const customerInfo = this.orderManager.elements.customerInfo;

    if (!customerId) {
      this.hideCustomerInfo();
      return;
    }

    const customerData = this.getCustomerData(customerId);
    if (customerData) {
      this.displayCustomerInfo(customerData);
      this.showCustomerInfo();
    } else {
      this.hideCustomerInfo();
    }
  }

  getCustomerData(customerId) {
    const option = document.querySelector(`#customer_id option[value="${customerId}"]`);
    if (!option) return null;

    return {
      name: option.getAttribute('data-name') || '-',
      phone: option.getAttribute('data-phone') || '-',
      cpf: option.getAttribute('data-cpf') || '-',
      street: option.getAttribute('data-street') || '-',
      neighborhood: option.getAttribute('data-neighborhood') || '-',
      city: option.getAttribute('data-city') || '-',
      state: option.getAttribute('data-state') || '-',
      zipcode: option.getAttribute('data-zipcode') || '-'
    };
  }

  showCustomerInfo() {
    const customerInfo = this.orderManager.elements.customerInfo;
    customerInfo.style.display = 'block';
    customerInfo.style.opacity = '0';
    customerInfo.style.transform = 'translateY(20px)';

    requestAnimationFrame(() => {
      customerInfo.style.transition = 'all 0.4s ease';
      customerInfo.style.opacity = '1';
      customerInfo.style.transform = 'translateY(0)';
    });
  }

  displayCustomerInfo(customerData) {
    const addressParts = [
      customerData.street,
      customerData.neighborhood,
      customerData.city + ' - ' + customerData.state,
      'CEP: ' + this.formatter.formatZipcode(customerData.zipcode)
    ].filter(part => part && part !== ' - ' && part !== 'CEP: -');

    const fullAddress = addressParts.join(', ');

    const fields = {
      'customer-name': customerData.name,
      'customer-phone': this.formatter.formatPhone(customerData.phone),
      'customer-cpf': this.formatter.formatCPF(customerData.cpf)
    };

    Object.entries(fields).forEach(([id, value]) => {
      const element = document.getElementById(id);
      if (element) {
        element.textContent = value;
        this.addTypingAnimation(element, value);
      }
    });

    const addressContainer = document.querySelector('.orders-address-details');
    if (addressContainer) {
      addressContainer.innerHTML = fullAddress;
      this.addTypingAnimation(addressContainer, fullAddress);
    }
  }

  addTypingAnimation(element, text) {
    element.style.overflow = 'hidden';
    element.style.whiteSpace = 'nowrap';
    element.style.borderRight = '2px solid var(--orders-primary)';
    element.style.animation = 'ordersTyping 0.8s steps(40, end), ordersBlink 0.8s step-end infinite';

    setTimeout(() => {
      element.style.animation = '';
      element.style.borderRight = '';
      element.style.overflow = '';
      element.style.whiteSpace = '';
    }, 1000);
  }

  hideCustomerInfo() {
    const customerInfo = this.orderManager.elements.customerInfo;
    customerInfo.style.transition = 'all 0.3s ease';
    customerInfo.style.opacity = '0';
    customerInfo.style.transform = 'translateY(-20px)';

    setTimeout(() => {
      customerInfo.style.display = 'none';
    }, 300);
  }
}

class PizzaHandler {
  constructor(orderManager) {
    this.orderManager = orderManager;
  }

  initializeCards() {
    this.orderManager.elements.pizzaCards.forEach(card => {
      const pizzaId = card.getAttribute('data-pizza-id');
      const quantityInput = document.getElementById(`qty_${pizzaId}`);

      if (quantityInput) {
        this.updatePizzaCardState(pizzaId, parseInt(quantityInput.value));
      }

      this.attachCardEventListeners(card, pizzaId);
    });
  }

  attachCardEventListeners(card, pizzaId) {
    const increaseBtn = card.querySelector('.orders-btn-increase');
    const decreaseBtn = card.querySelector('.orders-btn-decrease');
    const quantityInput = card.querySelector('.orders-quantity-input');

    if (increaseBtn) {
      increaseBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.increaseQuantity(pizzaId);
        this.addButtonPressAnimation(increaseBtn);
      });
    }

    if (decreaseBtn) {
      decreaseBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.decreaseQuantity(pizzaId);
        this.addButtonPressAnimation(decreaseBtn);
      });
    }

    if (quantityInput) {
      quantityInput.addEventListener('wheel', (e) => {
        e.preventDefault();
        if (e.deltaY < 0) {
          this.increaseQuantity(pizzaId);
        } else {
          this.decreaseQuantity(pizzaId);
        }
      });
    }
  }

  addButtonPressAnimation(button) {
    button.style.transform = 'scale(0.9)';
    setTimeout(() => {
      button.style.transform = '';
    }, 100);
  }

  increaseQuantity(pizzaId) {
    const input = document.getElementById(`qty_${pizzaId}`);
    if (!input) return;

    const currentValue = parseInt(input.value);
    const newValue = Math.min(currentValue + 1, 99);

    this.updateQuantity(pizzaId, newValue);
  }

  decreaseQuantity(pizzaId) {
    const input = document.getElementById(`qty_${pizzaId}`);
    if (!input) return;

    const currentValue = parseInt(input.value);
    const newValue = Math.max(currentValue - 1, 0);

    this.updateQuantity(pizzaId, newValue);
  }

  updateQuantity(pizzaId, newValue) {
    const input = document.getElementById(`qty_${pizzaId}`);
    if (!input) return;

    const oldValue = parseInt(input.value);
    input.value = newValue;

    this.addChangeAnimation(input);
    this.updatePizzaCardState(pizzaId, newValue);

    this.addQuantityFeedback(input, newValue, oldValue);

    this.orderManager.updateOrderSummary();
  }

  addQuantityFeedback(input, newValue, oldValue) {
    const card = input.closest('.orders-pizza-card');
    if (!card) return;

    const feedback = document.createElement('div');
    feedback.className = 'quantity-feedback';
    feedback.style.cssText = `
      position: absolute;
      top: -30px;
      left: 50%;
      transform: translateX(-50%);
      background: var(--orders-primary);
      color: var(--orders-secondary);
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.8rem;
      font-weight: 700;
      z-index: 1000;
      pointer-events: none;
      opacity: 0;
      transition: all 0.3s ease;
    `;

    const diff = newValue - oldValue;
    feedback.textContent = diff > 0 ? `+${diff}` : diff.toString();

    input.parentElement.style.position = 'relative';
    input.parentElement.appendChild(feedback);

    requestAnimationFrame(() => {
      feedback.style.opacity = '1';
      feedback.style.transform = 'translateX(-50%) translateY(-10px)';
    });

    setTimeout(() => {
      feedback.style.opacity = '0';
      feedback.style.transform = 'translateX(-50%) translateY(-20px)';
      setTimeout(() => feedback.remove(), 300);
    }, 1000);
  }

  addChangeAnimation(input) {
    input.classList.add('changed');
    setTimeout(() => input.classList.remove('changed'), 300);
  }

  updatePizzaCardState(pizzaId, quantity) {
    const card = document.querySelector(`[data-pizza-id="${pizzaId}"]`);
    if (!card) return;

    card.setAttribute('data-quantity', quantity);
    card.classList.toggle('selected', quantity > 0);

    if (quantity > 0) {
      this.addSelectionAnimation(card);
    }
  }

  addSelectionAnimation(card) {
    card.style.transform = 'scale(1.02)';
    setTimeout(() => {
      card.style.transform = '';
    }, 200);
  }
}

class OrderSummaryHandler {
  constructor(orderManager) {
    this.orderManager = orderManager;
  }

  update() {
    const items = this.getOrderItems();
    const total = this.calculateTotal(items);

    this.renderSummary(items, total);
    this.updateSubmitButton(items.length > 0);
    this.addSmoothAnimation();
  }

  getOrderItems() {
    const items = [];

    this.orderManager.elements.quantityInputs.forEach(input => {
      const pizzaId = input.id.replace('qty_', '');
      const quantity = parseInt(input.value);

      if (quantity > 0) {
        const price = this.orderManager.pizzaPrices[pizzaId];
        const name = this.orderManager.pizzaNames[pizzaId];

        items.push({
          name,
          quantity,
          price,
          subtotal: price * quantity
        });
      }
    });

    return items;
  }

  calculateTotal(items) {
    return items.reduce((total, item) => total + item.subtotal, 0);
  }

  renderSummary(items, total) {
    const summaryDiv = this.orderManager.elements.orderSummary;

    if (items.length === 0) {
      summaryDiv.innerHTML = this.getEmptySummaryHTML();
    } else {
      summaryDiv.innerHTML = this.getItemsSummaryHTML(items, total);
    }
  }

  getEmptySummaryHTML() {
    return `
      <div class="orders-summary-item">
        <span><i class="bi bi-cart-x"></i> Nenhuma pizza selecionada</span>
        <span>R$ 0,00</span>
      </div>
    `;
  }

  getItemsSummaryHTML(items, total) {
    const formatter = new DataFormatter();
    const itemsHTML = items.map(item => `
      <div class="orders-summary-item">
        <span>
          <strong>${item.quantity}x</strong> ${item.name}
          <small style="display: block; color: var(--orders-text-muted); font-size: 0.85rem;">
            R$ ${formatter.formatPrice(item.price)} cada
          </small>
        </span>
        <span class="item-subtotal" style="color: var(--orders-success); font-weight: 600;">
          R$ ${formatter.formatPrice(item.subtotal)}
        </span>
      </div>
    `).join('');

    const totalText = items.length === 1 ? 'tipo' : 'tipos';

    return `
      <div class="summary-items">${itemsHTML}</div>
      <div class="orders-summary-item orders-summary-total">
        <span><i class="bi bi-receipt"></i> <strong>Total (${items.length} ${totalText})</strong></span>
        <span><strong>R$ ${formatter.formatPrice(total)}</strong></span>
      </div>
    `;
  }

  updateSubmitButton(hasItems) {
    const submitBtn = this.orderManager.elements.submitBtn;
    const submitContainer = document.getElementById('submit-container');
    const submitText = document.getElementById('submit-text');

    if (!submitBtn) return;

    submitBtn.disabled = !hasItems;

    if (hasItems) {
      submitBtn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Finalizar Pedido';
      submitBtn.classList.remove('loading');
    } else {
      submitBtn.innerHTML = '<i class="bi bi-lock-fill"></i> Finalizar Pedido';
      submitBtn.classList.remove('loading');
    }

    if (submitContainer) {
      if (hasItems) {
        submitContainer.classList.remove('disabled');
        submitContainer.classList.add('ready');
      } else {
        submitContainer.classList.add('disabled');
        submitContainer.classList.remove('ready');
      }
    }

    if (submitText) {
      if (hasItems) {
        submitText.className = 'orders-submit-text ready';
        submitText.innerHTML = '<i class="bi bi-check-circle"></i> Tudo pronto! Clique para finalizar o pedido';
      } else {
        submitText.className = 'orders-submit-text disabled';
        submitText.innerHTML = '<i class="bi bi-info-circle"></i> Selecione pelo menos uma pizza para continuar';
      }
    }

    this.addSubmitButtonAnimation(submitBtn, hasItems);
  }

  addSubmitButtonAnimation(submitBtn, isEnabled) {
    submitBtn.style.transform = 'scale(0.95)';

    setTimeout(() => {
      submitBtn.style.transform = '';

      if (isEnabled) {
        submitBtn.classList.add('orders-pulse');
        setTimeout(() => {
          submitBtn.classList.remove('orders-pulse');
        }, 1500);
      }
    }, 150);

    if (isEnabled) {
      this.addSubmitGlowEffect(submitBtn);
    }
  }

  addSubmitGlowEffect(submitBtn) {
    const glowElement = document.createElement('div');
    glowElement.style.cssText = `
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
      transition: left 0.6s ease;
      pointer-events: none;
      z-index: 1;
    `;

    submitBtn.style.position = 'relative';
    submitBtn.style.overflow = 'hidden';
    submitBtn.appendChild(glowElement);

    setTimeout(() => {
      glowElement.style.left = '100%';
    }, 100);

    setTimeout(() => {
      glowElement.remove();
    }, 700);
  }

  addSmoothAnimation() {
    const summaryDiv = this.orderManager.elements.orderSummary;
    summaryDiv.style.opacity = '0.7';
    summaryDiv.style.transform = 'scale(0.98)';

    setTimeout(() => {
      summaryDiv.style.opacity = '1';
      summaryDiv.style.transform = 'scale(1)';
    }, 100);
  }
}

class DataFormatter {
  formatPhone(phone) {
    if (!phone) return '-';
    const cleaned = phone.replace(/\D/g, '');
    if (cleaned.length === 11) {
      return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2, 7)}-${cleaned.slice(7)}`;
    } else if (cleaned.length === 10) {
      return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2, 6)}-${cleaned.slice(6)}`;
    }
    return phone;
  }

  formatCPF(cpf) {
    if (!cpf) return '-';
    const cleaned = cpf.replace(/\D/g, '');
    if (cleaned.length === 11) {
      return `${cleaned.slice(0, 3)}.${cleaned.slice(3, 6)}.${cleaned.slice(6, 9)}-${cleaned.slice(9)}`;
    }
    return cpf;
  }

  formatZipcode(zipcode) {
    if (!zipcode) return '-';
    const cleaned = zipcode.replace(/\D/g, '');
    if (cleaned.length === 8) {
      return `${cleaned.slice(0, 5)}-${cleaned.slice(5)}`;
    }
    return zipcode;
  }

  formatPrice(price) {
    if (typeof price !== 'number') return 'R$ 0,00';
    return `R$ ${price.toFixed(2).replace('.', ',')}`;
  }

  formatDate(date) {
    if (!date) return '-';
    if (typeof date === 'string') {
      date = new Date(date);
    }
    return date.toLocaleDateString('pt-BR');
  }

  formatDateTime(date) {
    if (!date) return '-';
    if (typeof date === 'string') {
      date = new Date(date);
    }
    return `${date.toLocaleDateString('pt-BR')} às ${date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}`;
  }
}

const orderManager = new OrderManager();

window.orderManager = orderManager;
window.initializePizzaData = (prices, names) => orderManager.initializePizzaData(prices, names);
window.increaseQuantity = (pizzaId) => orderManager.increaseQuantity(pizzaId);
window.decreaseQuantity = (pizzaId) => orderManager.decreaseQuantity(pizzaId);
window.scrollToSection = (sectionId) => orderManager.scrollToSection(sectionId);