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
      quantityInputs: document.querySelectorAll('.orders-quantity-input'),
      pizzaCards: document.querySelectorAll('.orders-pizza-card')
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

    // Verificar se TomSelect está disponível
    if (typeof TomSelect === 'undefined') {
      console.warn('TomSelect não está disponível. Carregando dinamicamente...');
      this.loadTomSelect();
      return;
    }

    this.createTomSelectInstance();
  }

  /**
   * Carrega TomSelect dinamicamente
   */
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

  /**
   * Cria a instância do TomSelect
   */
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
                ${phone ? `📞 ${this.formatPhone(phone)}` : ''}
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

    // Adicionar classes personalizadas
    const wrapper = this.tomSelectInstance.wrapper;
    wrapper.classList.add('orders-tom-select');

    // Corrigir cursor para seleção única
    const control = this.tomSelectInstance.control;
    control.style.cursor = 'pointer';

    // Garantir que o input não seja editável após seleção
    this.tomSelectInstance.control_input.style.cursor = 'pointer';
    this.tomSelectInstance.control_input.readOnly = true;

    // Event listener para mudanças
    this.tomSelectInstance.on('change', () => {
      this.addSelectionAnimation();
      // Garantir que o cursor seja removido após seleção
      setTimeout(() => {
        this.tomSelectInstance.blur();
        this.tomSelectInstance.control_input.blur();
      }, 100);
    });
  }

  /**
   * Manipula o foco do TomSelect
   */
  onTomSelectFocus() {
    const wrapper = this.tomSelectInstance.wrapper;
    wrapper.classList.add('focus');
  }

  /**
   * Manipula a perda de foco do TomSelect
   */
  onTomSelectBlur() {
    const wrapper = this.tomSelectInstance.wrapper;
    wrapper.classList.remove('focus');
  }

  /**
   * Adiciona animação de seleção
   */
  addSelectionAnimation() {
    const control = this.tomSelectInstance.control;
    control.style.transform = 'scale(0.98)';
    setTimeout(() => {
      control.style.transform = 'scale(1)';
    }, 150);
  }

  /**
   * Formata telefone para exibição
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
      this.showCustomerInfo();
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
   * Exibe as informações do cliente com animação
   */
  showCustomerInfo() {
    const customerInfo = this.orderManager.elements.customerInfo;
    customerInfo.style.display = 'block';
    customerInfo.style.opacity = '0';
    customerInfo.style.transform = 'translateY(20px)';

    // Trigger animation
    requestAnimationFrame(() => {
      customerInfo.style.transition = 'all 0.4s ease';
      customerInfo.style.opacity = '1';
      customerInfo.style.transform = 'translateY(0)';
    });
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
      if (element) {
        element.textContent = value;
        // Adicionar animação de typing
        this.addTypingAnimation(element, value);
      }
    });
  }

  /**
   * Adiciona animação de digitação
   */
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

  /**
   * Oculta as informações do cliente
   */
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

      // Adicionar event listeners para os botões
      this.attachCardEventListeners(card, pizzaId);
    });
  }

  /**
   * Anexa event listeners aos cards
   */
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

  /**
   * Adiciona animação de pressão do botão
   */
  addButtonPressAnimation(button) {
    button.style.transform = 'scale(0.9)';
    setTimeout(() => {
      button.style.transform = '';
    }, 100);
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

    const oldValue = parseInt(input.value);
    input.value = newValue;

    this.addChangeAnimation(input);
    this.updatePizzaCardState(pizzaId, newValue);

    // Adicionar feedback visual
    this.addQuantityFeedback(input, newValue, oldValue);

    this.orderManager.updateOrderSummary();
  }

  /**
   * Adiciona feedback visual para mudança de quantidade
   */
  addQuantityFeedback(input, newValue, oldValue) {
    const card = input.closest('.orders-pizza-card');
    if (!card) return;

    // Criar elemento de feedback
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

    // Animar feedback
    requestAnimationFrame(() => {
      feedback.style.opacity = '1';
      feedback.style.transform = 'translateX(-50%) translateY(-10px)';
    });

    // Remover feedback
    setTimeout(() => {
      feedback.style.opacity = '0';
      feedback.style.transform = 'translateX(-50%) translateY(-20px)';
      setTimeout(() => feedback.remove(), 300);
    }, 1000);
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

    // Adicionar animação de seleção
    if (quantity > 0) {
      this.addSelectionAnimation(card);
    }
  }

  /**
   * Adiciona animação de seleção ao card
   */
  addSelectionAnimation(card) {
    card.style.transform = 'scale(1.02)';
    setTimeout(() => {
      card.style.transform = '';
    }, 200);
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
      <div class="orders-summary-item">
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
      <div class="orders-summary-item">
        <span>
          <strong>${item.quantity}x</strong> ${item.name}
          <small style="display: block; color: var(--orders-text-muted); font-size: 0.85rem;">
            R$ ${this.formatPrice(item.price)} cada
          </small>
        </span>
        <span class="item-subtotal" style="color: var(--orders-success); font-weight: 600;">
          R$ ${this.formatPrice(item.subtotal)}
        </span>
      </div>
    `).join('');

    const totalText = items.length === 1 ? 'tipo' : 'tipos';

    return `
      <div class="summary-items">${itemsHTML}</div>
      <div class="orders-summary-item orders-summary-total">
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
    const submitContainer = document.getElementById('submit-container');
    const submitText = document.getElementById('submit-text');

    if (!submitBtn) return;

    // Atualizar estado do botão
    submitBtn.disabled = !hasItems;

    // Atualizar ícone e texto do botão
    if (hasItems) {
      submitBtn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Finalizar Pedido';
      submitBtn.classList.remove('loading');
    } else {
      submitBtn.innerHTML = '<i class="bi bi-lock-fill"></i> Finalizar Pedido';
      submitBtn.classList.remove('loading');
    }

    // Atualizar container e texto de estado
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

    // Adicionar animação de estado
    this.addSubmitButtonAnimation(submitBtn, hasItems);
  }

  /**
   * Adiciona animação ao botão de submit
   */
  addSubmitButtonAnimation(submitBtn, isEnabled) {
    // Animação de escala
    submitBtn.style.transform = 'scale(0.95)';

    setTimeout(() => {
      submitBtn.style.transform = '';

      // Se habilitado, adicionar efeito de pulso
      if (isEnabled) {
        submitBtn.classList.add('orders-pulse');
        setTimeout(() => {
          submitBtn.classList.remove('orders-pulse');
        }, 1500);
      }
    }, 150);

    // Adicionar efeito de brilho quando habilitar
    if (isEnabled) {
      this.addSubmitGlowEffect(submitBtn);
    }
  }

  /**
   * Adiciona efeito de brilho ao botão
   */
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

    // Trigger glow animation
    setTimeout(() => {
      glowElement.style.left = '100%';
    }, 100);

    // Remove glow element
    setTimeout(() => {
      glowElement.remove();
    }, 700);
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
    summaryDiv.style.opacity = '0.7';
    summaryDiv.style.transform = 'scale(0.98)';

    setTimeout(() => {
      summaryDiv.style.opacity = '1';
      summaryDiv.style.transform = 'scale(1)';
    }, 100);
  }
}

/**
 * Classe utilitária para formatação de dados
 */
class DataFormatter {
  /**
   * Formata telefone para exibição
   */
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

  /**
   * Formata CPF para exibição
   */
  formatCPF(cpf) {
    if (!cpf) return '-';
    const cleaned = cpf.replace(/\D/g, '');
    if (cleaned.length === 11) {
      return `${cleaned.slice(0, 3)}.${cleaned.slice(3, 6)}.${cleaned.slice(6, 9)}-${cleaned.slice(9)}`;
    }
    return cpf;
  }

  /**
   * Formata CEP para exibição
   */
  formatZipcode(zipcode) {
    if (!zipcode) return '-';
    const cleaned = zipcode.replace(/\D/g, '');
    if (cleaned.length === 8) {
      return `${cleaned.slice(0, 5)}-${cleaned.slice(5)}`;
    }
    return zipcode;
  }

  /**
   * Formata preço para exibição
   */
  formatPrice(price) {
    if (typeof price !== 'number') return 'R$ 0,00';
    return `R$ ${price.toFixed(2).replace('.', ',')}`;
  }

  /**
   * Formata data para exibição
   */
  formatDate(date) {
    if (!date) return '-';
    if (typeof date === 'string') {
      date = new Date(date);
    }
    return date.toLocaleDateString('pt-BR');
  }

  /**
   * Formata data e hora para exibição
   */
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