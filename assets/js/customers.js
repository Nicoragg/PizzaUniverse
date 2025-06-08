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
          const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
          const data = await response.json();

          if (!data.erro) {
            const neighborhoodInput = document.getElementById('neighborhood');
            const streetInput = document.getElementById('street');
            const cityInput = document.getElementById('city');
            const stateSelect = document.getElementById('state');

            if (neighborhoodInput && data.bairro) {
              neighborhoodInput.value = data.bairro;
            }
            if (streetInput && data.logradouro) {
              streetInput.value = data.logradouro;
            }
            if (cityInput && data.localidade) {
              cityInput.value = data.localidade;
            }
            if (stateSelect && data.uf) {
              stateSelect.value = data.uf;
            }
          }
        } catch (error) {
          console.log('Erro ao buscar CEP:', error);
        }
      }
    });
  }
});