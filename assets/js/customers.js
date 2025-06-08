document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const currentPage = urlParams.get('page');

  if (currentPage !== 'customers') return;

  const deleteButtons = document.querySelectorAll('a[href*="confirm="]');

  deleteButtons.forEach(button => {
    button.addEventListener('click', (e) => {
      e.preventDefault();

      const href = button.getAttribute('href');
      const customerId = new URLSearchParams(href.split('?')[1]).get('confirm');

      SweetAlertConfirm.confirmCustomerDeletion(() => {
        window.location.href = `?page=customers&action=delete&id=${customerId}`;
      });
    });
  });

  const cpfInput = document.getElementById('cpf');
  if (cpfInput) {
    cpfInput.addEventListener('input', (e) => {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length <= 11) {
        value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        e.target.value = value;
      }
    });
  }

  const phoneInput = document.getElementById('phone');
  if (phoneInput) {
    phoneInput.addEventListener('input', (e) => {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length <= 11) {
        if (value.length <= 10) {
          value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
        } else {
          value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        }
        e.target.value = value;
      }
    });
  }

  const zipcodeInput = document.getElementById('zipcode');
  if (zipcodeInput) {
    zipcodeInput.addEventListener('input', (e) => {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length <= 8) {
        value = value.replace(/(\d{5})(\d{3})/, '$1-$2');
        e.target.value = value;
      }
    });

    zipcodeInput.addEventListener('blur', async (e) => {
      const cep = e.target.value.replace(/\D/g, '');
      if (cep.length === 8) {
        try {
          const originalPlaceholder = e.target.placeholder;
          e.target.placeholder = 'Buscando endereço...';
          e.target.style.background = 'linear-gradient(90deg, #f8f9fa 25%, #e9ecef 50%, #f8f9fa 75%)';
          e.target.style.backgroundSize = '200% 100%';
          e.target.style.animation = 'loading 1s infinite';

          const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
          const data = await response.json();

          e.target.placeholder = originalPlaceholder;
          e.target.style.background = '';
          e.target.style.animation = '';

          if (!data.erro) {
            const neighborhoodInput = document.getElementById('neighborhood');
            const streetInput = document.getElementById('street');
            const cityInput = document.getElementById('city');
            const stateInput = document.getElementById('state');

            if (neighborhoodInput && data.bairro) {
              neighborhoodInput.value = data.bairro;
              neighborhoodInput.classList.add('api-filled');
            }
            if (streetInput && data.logradouro) {
              streetInput.value = data.logradouro;
              streetInput.classList.add('api-filled');
            }
            if (cityInput && data.localidade) {
              cityInput.value = data.localidade;
              cityInput.classList.add('api-filled');
            }
            if (stateInput && data.estado) {
              stateInput.value = data.estado;
              stateInput.classList.add('api-filled');
            }

            e.target.style.borderColor = '#28a745';
            e.target.style.backgroundColor = '#f8fff9';

            showTemporaryMessage('✅ Endereço encontrado e preenchido automaticamente!', 'success');
          } else {
            e.target.style.borderColor = '#ffc107';
            e.target.style.backgroundColor = '#fffdf7';
            showTemporaryMessage('⚠️ CEP não encontrado. Preencha o endereço manualmente.', 'warning');
          }
        } catch (error) {
          console.log('Erro ao buscar CEP:', error);
          e.target.style.borderColor = '#dc3545';
          e.target.style.backgroundColor = '#fff5f5';
          showTemporaryMessage('❌ Erro ao buscar CEP. Verifique sua conexão.', 'error');
        }
      }
    });
  }

  const addressFields = ['neighborhood', 'street', 'city', 'state'];
  addressFields.forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
      field.addEventListener('input', () => {
        field.classList.remove('api-filled');
      });
    }
  });

  function showTemporaryMessage(message, type = 'info') {
    const existingMessage = document.querySelector('.temp-message');
    if (existingMessage) {
      existingMessage.remove();
    }

    const messageEl = document.createElement('div');
    messageEl.className = `temp-message temp-message-${type}`;
    messageEl.textContent = message;
    messageEl.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 12px 20px;
      border-radius: 8px;
      font-weight: 600;
      z-index: 1000;
      max-width: 350px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      transform: translateX(400px);
      transition: transform 0.3s ease;
    `;

    switch (type) {
      case 'success':
        messageEl.style.background = 'linear-gradient(135deg, #d4edda, #c3e6cb)';
        messageEl.style.color = '#155724';
        messageEl.style.border = '1px solid #c3e6cb';
        break;
      case 'warning':
        messageEl.style.background = 'linear-gradient(135deg, #fff3cd, #ffeaa7)';
        messageEl.style.color = '#856404';
        messageEl.style.border = '1px solid #ffeaa7';
        break;
      case 'error':
        messageEl.style.background = 'linear-gradient(135deg, #f8d7da, #f5c6cb)';
        messageEl.style.color = '#721c24';
        messageEl.style.border = '1px solid #f5c6cb';
        break;
    }

    document.body.appendChild(messageEl);

    setTimeout(() => {
      messageEl.style.transform = 'translateX(0)';
    }, 100);

    setTimeout(() => {
      messageEl.style.transform = 'translateX(400px)';
      setTimeout(() => {
        if (messageEl.parentNode) {
          messageEl.remove();
        }
      }, 300);
    }, 4000);
  }
});