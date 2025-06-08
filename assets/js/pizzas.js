document.addEventListener('DOMContentLoaded', () => {
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

document.addEventListener('DOMContentLoaded', () => {
  const pizzaForm = document.querySelector('.pizza-form');

  if (pizzaForm) {
    pizzaForm.addEventListener('submit', (e) => {
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

document.addEventListener('DOMContentLoaded', () => {
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