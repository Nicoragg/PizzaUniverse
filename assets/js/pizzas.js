document.addEventListener('DOMContentLoaded', function () {
  const categorySelect = document.getElementById('category');
  const newCategoryField = document.getElementById('new-category-field');
  const newCategoryInput = document.getElementById('new_category');

  if (categorySelect && newCategoryField && newCategoryInput) {
    function toggleNewCategoryField() {
      if (categorySelect.value === 'Nova Categoria') {
        newCategoryField.style.display = 'block';
        newCategoryInput.required = true;
      } else {
        newCategoryField.style.display = 'none';
        newCategoryInput.required = false;
        newCategoryInput.value = '';
      }
    }

    categorySelect.addEventListener('change', toggleNewCategoryField);

    const selectedValue = categorySelect.value;
    if (selectedValue && !Array.from(categorySelect.options).some(option =>
      option.value === selectedValue && option.value !== 'Nova Categoria')) {
      categorySelect.value = 'Nova Categoria';
      newCategoryField.style.display = 'block';
      newCategoryInput.required = true;
    }
  }
});

document.addEventListener('DOMContentLoaded', function () {
  const pizzaForm = document.querySelector('.pizza-form');

  if (pizzaForm) {
    pizzaForm.addEventListener('submit', function (e) {
      const priceInput = document.getElementById('price');
      const categorySelect = document.getElementById('category');
      const newCategoryInput = document.getElementById('new_category');

      if (priceInput && priceInput.value) {
        const price = parseFloat(priceInput.value);
        if (isNaN(price) || price <= 0) {
          alert('Por favor, insira um preço válido maior que zero.');
          priceInput.focus();
          e.preventDefault();
          return;
        }
      }

      if (categorySelect && categorySelect.value === 'Nova Categoria') {
        if (!newCategoryInput || !newCategoryInput.value.trim()) {
          alert('Por favor, digite o nome da nova categoria.');
          if (newCategoryInput) newCategoryInput.focus();
          e.preventDefault();
          return;
        }
      }
    });
  }
});

document.addEventListener('DOMContentLoaded', function () {
  const deleteLinks = document.querySelectorAll('a[href*="confirm"]');

  deleteLinks.forEach(link => {
    link.addEventListener('click', function (e) {
      const confirmed = confirm('Tem certeza que deseja excluir esta pizza?');
      if (!confirmed) {
        e.preventDefault();
      }
    });
  });
});

document.addEventListener('DOMContentLoaded', function () {
  const errorFields = document.querySelectorAll('.field-error');

  errorFields.forEach(field => {
    field.addEventListener('input', function () {
      this.classList.remove('field-error');
    });

    if (errorFields[0] === field) {
      field.focus();
    }
  });
});

document.addEventListener('DOMContentLoaded', function () {
  const priceInput = document.getElementById('price');

  if (priceInput) {
    priceInput.addEventListener('blur', function () {
      const value = parseFloat(this.value);
      if (!isNaN(value) && value > 0) {
        this.value = value.toFixed(2);
      }
    });
  }
});

document.addEventListener('DOMContentLoaded', function () {
  const descriptionTextarea = document.getElementById('description');

  if (descriptionTextarea) {
    function autoResize() {
      this.style.height = 'auto';
      this.style.height = this.scrollHeight + 'px';
    }

    descriptionTextarea.addEventListener('input', autoResize);
    autoResize.call(descriptionTextarea);
  }
});