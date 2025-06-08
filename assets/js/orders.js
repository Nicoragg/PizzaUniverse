/**
 * Sistema de Gerenciamento de Pedidos
 * Módulo responsável por gerenciar a criação de pedidos de pizza
 */

class OrderManager {
  constructor() {
    this.pizzaPrices = {};
    this.pizzaNames = {};
    this.tomSelectInstance = null;
    this.elements = {};

    this.init();
  }

  /**
   * Inicialização do sistema
   */
  init() {
    this.cacheElements();
    this.bindEvents();
    this.initializeComponents();
  }

  /**
   * Cache dos elementos DOM
   */
  cacheElements() {
    this.elements = {
      customerSelect: document.getElementById('customer_id'),
      customerInfo: document.getElementById('customer-info'),
      orderSummary: document.getElementById('order-summary'),
      submitBtn: document.getElementById('submit-order'),
      quantityInputs: document.querySelectorAll('.quantity-input'),
      pizzaCards: document.querySelectorAll('.pizza-card')
    };
  }

  /**
   * Vinculação de eventos
   */
  bindEvents() {
    document.addEventListener('DOMContentLoaded', () => this.onDOMContentLoaded());
    document.addEventListener('keydown', (e) => this.handleKeyboardEvents(e));

    // Event listeners para inputs de quantidade
    this.elements.quantityInputs.forEach(input => {
      input.addEventListener('input', () => this.updateOrderSummary());
    });
  }

  /**
   * Evento de carregamento do DOM
   */
  onDOMContentLoaded() {
    this.initializeCustomerSelect();
    this.initializePizzaCards();

    // Trigger customer selection if there's a pre-selected value
    if (this.elements.customerSelect?.value) {
      this.handleCustomerSelection(this.elements.customerSelect.value);
    }
  }

  /**
   * Inicializa os dados das pizzas
   */
  initializePizzaData(prices, names) {
    this.pizzaPrices = prices;
    this.pizzaNames = names;
  }

  /**
   * Inicializa os componentes
   */
  initializeComponents() {
    this.customerHandler = new CustomerHandler(this);
    this.pizzaHandler = new PizzaHandler(this);
    this.orderSummaryHandler = new OrderSummaryHandler(this);
  }

  /**
   * Inicializa o seletor de cliente com TomSelect
   */
  initializeCustomerSelect() {
    if (!this.elements.customerSelect) return;

    this.tomSelectInstance = new TomSelect('#customer_id', {
      placeholder: 'Selecione ou busque um cliente...',
      searchField: ['text'],
      create: false,
      maxOptions: null,
      render: {
        option: (data, escape) => `
          <div class="tom-select-option">
            <strong>${escape(data.text)}</strong>
          </div>
        `,
        item: (data, escape) => `
          <div class="tom-select-item">${escape(data.text)}</div>
        `
      },
      onChange: (value) => this.handleCustomerSelection(value)
    });
  }

  /**
   * Manipula a seleção de cliente
   */
  handleCustomerSelection(customerId) {
    this.customerHandler.handleSelection(customerId);
  }

  /**
   * Inicializa os cards de pizza
   */
  initializePizzaCards() {
    this.pizzaHandler.initializeCards();
  }

  /**
   * Aumenta a quantidade de uma pizza
   */
  increaseQuantity(pizzaId) {
    this.pizzaHandler.increaseQuantity(pizzaId);
  }

  /**
   * Diminui a quantidade de uma pizza
   */
  decreaseQuantity(pizzaId) {
    this.pizzaHandler.decreaseQuantity(pizzaId);
  }

  /**
   * Atualiza o resumo do pedido
   */
  updateOrderSummary() {
    this.orderSummaryHandler.update();
  }

  /**
   * Manipula eventos de teclado
   */
  handleKeyboardEvents(e) {
    const activeElement = document.activeElement;

    if (activeElement?.classList.contains('quantity-input')) {
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

  /**
   * Scroll suave para seção
   */
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

/**
 * Classe responsável por gerenciar clientes
 */
class CustomerHandler {
  constructor(orderManager) {
    this.orderManager = orderManager;
    this.formatter = new DataFormatter();
  }

  /**
   * Manipula a seleção de cliente
   */
  handleSelection(customerId) {
    const customerInfo = this.orderManager.elements.customerInfo;

    if (!customerId) {
      this.hideCustomerInfo();
      return;
    }

    const customerData = this.getCustomerData(customerId);
    if (customerData) {
      this.displayCustomerInfo(customerData);
      customerInfo.style.display = 'block';
    } else {
      this.hideCustomerInfo();
    }
  }

  /**
   * Obtém os dados do cliente
   */
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

  /**
   * Exibe as informações do cliente
   */
  displayCustomerInfo(customerData) {
    const fields = {
      'customer-name': customerData.name,
      'customer-phone': this.formatter.formatPhone(customerData.phone),
      'customer-cpf': this.formatter.formatCPF(customerData.cpf),
      'customer-street': customerData.street,
      'customer-neighborhood': customerData.neighborhood,
      'customer-city': customerData.city,
      'customer-state': customerData.state,
      'customer-zipcode': this.formatter.formatZipcode(customerData.zipcode)
    };

    Object.entries(fields).forEach(([id, value]) => {
      const element = document.getElementById(id);
      if (element) element.textContent = value;
    });
  }

  /**
   * Oculta as informações do cliente
   */
  hideCustomerInfo() {
    this.orderManager.elements.customerInfo.style.display = 'none';
  }
}

/**
 * Classe responsável por gerenciar pizzas
 */
class PizzaHandler {
  constructor(orderManager) {
    this.orderManager = orderManager;
  }

  /**
   * Inicializa os cards de pizza
   */
  initializeCards() {
    this.orderManager.elements.pizzaCards.forEach(card => {
      const pizzaId = card.getAttribute('data-pizza-id');
      const quantityInput = document.getElementById(`qty_${pizzaId}`);

      if (quantityInput) {
        this.updateCardAppearance(card, parseInt(quantityInput.value));
      }
    });
  }

  /**
   * Aumenta a quantidade de uma pizza
   */
  increaseQuantity(pizzaId) {
    const input = document.getElementById(`qty_${pizzaId}`);
    if (!input) return;

    const currentValue = parseInt(input.value);
    const newValue = Math.min(currentValue + 1, 99);

    this.updateQuantity(pizzaId, newValue);
  }

  /**
   * Diminui a quantidade de uma pizza
   */
  decreaseQuantity(pizzaId) {
    const input = document.getElementById(`qty_${pizzaId}`);
    if (!input) return;

    const currentValue = parseInt(input.value);
    const newValue = Math.max(currentValue - 1, 0);

    this.updateQuantity(pizzaId, newValue);
  }

  /**
   * Atualiza a quantidade de uma pizza
   */
  updateQuantity(pizzaId, newValue) {
    const input = document.getElementById(`qty_${pizzaId}`);
    if (!input) return;

    input.value = newValue;
    this.addChangeAnimation(input);
    this.updatePizzaCardState(pizzaId, newValue);
    this.orderManager.updateOrderSummary();
  }

  /**
   * Adiciona animação de mudança
   */
  addChangeAnimation(input) {
    input.classList.add('changed');
    setTimeout(() => input.classList.remove('changed'), 300);
  }

  /**
   * Atualiza o estado visual do card da pizza
   */
  updatePizzaCardState(pizzaId, quantity) {
    const card = document.querySelector(`[data-pizza-id="${pizzaId}"]`);
    if (!card) return;

    card.setAttribute('data-quantity', quantity);
    card.classList.toggle('selected', quantity > 0);
  }

  /**
   * Atualiza a aparência do card
   */
  updateCardAppearance(card, quantity) {
    card.setAttribute('data-quantity', quantity);
    card.classList.toggle('selected', quantity > 0);
  }
}

/**
 * Classe responsável por gerenciar o resumo do pedido
 */
class OrderSummaryHandler {
  constructor(orderManager) {
    this.orderManager = orderManager;
  }

  /**
   * Atualiza o resumo do pedido
   */
  update() {
    const items = this.getOrderItems();
    const total = this.calculateTotal(items);

    this.renderSummary(items, total);
    this.updateSubmitButton(items.length > 0);
    this.addSmoothAnimation();
  }

  /**
   * Obtém os itens do pedido
   */
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

  /**
   * Calcula o total do pedido
   */
  calculateTotal(items) {
    return items.reduce((total, item) => total + item.subtotal, 0);
  }

  /**
   * Renderiza o resumo do pedido
   */
  renderSummary(items, total) {
    const summaryDiv = this.orderManager.elements.orderSummary;

    if (items.length === 0) {
      summaryDiv.innerHTML = this.getEmptySummaryHTML();
    } else {
      summaryDiv.innerHTML = this.getItemsSummaryHTML(items, total);
    }
  }

  /**
   * HTML para resumo vazio
   */
  getEmptySummaryHTML() {
    return `
      <div class="summary-item">
        <span><i class="bi bi-cart-x"></i> Nenhuma pizza selecionada</span>
        <span>R$ 0,00</span>
      </div>
    `;
  }

  /**
   * HTML para resumo com itens
   */
  getItemsSummaryHTML(items, total) {
    const itemsHTML = items.map(item => `
      <div class="summary-item">
        <span>
          <strong>${item.quantity}x</strong> ${item.name}
          <small>R$ ${this.formatPrice(item.price)} cada</small>
        </span>
        <span class="item-subtotal">R$ ${this.formatPrice(item.subtotal)}</span>
      </div>
    `).join('');

    const totalText = items.length === 1 ? 'tipo' : 'tipos';

    return `
      <div class="summary-items">${itemsHTML}</div>
      <div class="summary-item total">
        <span><i class="bi bi-receipt"></i> <strong>Total (${items.length} ${totalText})</strong></span>
        <span><strong>R$ ${this.formatPrice(total)}</strong></span>
      </div>
    `;
  }

  /**
   * Atualiza o botão de submit
   */
  updateSubmitButton(hasItems) {
    const submitBtn = this.orderManager.elements.submitBtn;
    if (!submitBtn) return;

    submitBtn.disabled = !hasItems;
    submitBtn.innerHTML = hasItems
      ? '<i class="bi bi-check-circle"></i> Finalizar Pedido'
      : '<i class="bi bi-lock"></i> Selecione as pizzas';
  }

  /**
   * Formata preço para exibição
   */
  formatPrice(price) {
    return price.toFixed(2).replace('.', ',');
  }

  /**
   * Adiciona animação suave na atualização
   */
  addSmoothAnimation() {
    const summaryDiv = this.orderManager.elements.orderSummary;
    summaryDiv.style.opacity = '0.5';
    setTimeout(() => {
      summaryDiv.style.opacity = '1';
    }, 100);
  }
}

/**
 * Classe utilitária para formatação de dados
 */
class DataFormatter {
  /**
   * Formata número de telefone
   */
  formatPhone(phone) {
    if (!phone) return '';

    const cleaned = phone.replace(/\D/g, '');

    if (cleaned.length === 11) {
      return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2, 7)}-${cleaned.slice(7)}`;
    } else if (cleaned.length === 10) {
      return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2, 6)}-${cleaned.slice(6)}`;
    }

    return phone;
  }

  /**
   * Formata CPF
   */
  formatCPF(cpf) {
    if (!cpf) return '';

    const cleaned = cpf.replace(/\D/g, '');

    if (cleaned.length === 11) {
      return `${cleaned.slice(0, 3)}.${cleaned.slice(3, 6)}.${cleaned.slice(6, 9)}-${cleaned.slice(9)}`;
    }

    return cpf;
  }

  /**
   * Formata CEP
   */
  formatZipcode(zipcode) {
    if (!zipcode) return '';

    const cleaned = zipcode.replace(/\D/g, '');

    if (cleaned.length === 8) {
      return `${cleaned.slice(0, 5)}-${cleaned.slice(5)}`;
    }

    return zipcode;
  }
}

const orderManager = new OrderManager();

window.orderManager = orderManager;
window.initializePizzaData = (prices, names) => orderManager.initializePizzaData(prices, names);
window.increaseQuantity = (pizzaId) => orderManager.increaseQuantity(pizzaId);
window.decreaseQuantity = (pizzaId) => orderManager.decreaseQuantity(pizzaId);
window.scrollToSection = (sectionId) => orderManager.scrollToSection(sectionId);

function initializePizzaData(prices, names) {
  orderManager.initializePizzaData(prices, names);
}

function increaseQuantity(pizzaId) {
  orderManager.increaseQuantity(pizzaId);
}

function decreaseQuantity(pizzaId) {
  orderManager.decreaseQuantity(pizzaId);
}

function scrollToSection(sectionId) {
  orderManager.scrollToSection(sectionId);
}