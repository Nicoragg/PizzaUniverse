class PizzaManager {
  constructor() {
    this.elements = this.getElements();
    this.constants = {
      NEW_CATEGORY_VALUE: 'Nova Categoria',
      PIZZAS_PAGE: 'pizzas'
    };
    this.init();
  }

  getElements() {
    return {
      categorySelect: document.getElementById('category'),
      newCategoryField: document.getElementById('new-category-field'),
      newCategoryInput: document.getElementById('new_category'),
      pizzaForm: document.querySelector('.pizza-form'),
      priceInput: document.getElementById('price'),
      descriptionTextarea: document.getElementById('description'),
      deleteButtons: document.querySelectorAll('.btn-delete')
    };
  }

  init() {
    this.initCategoryToggle();
    this.initFormValidation();
    this.initTextareaAutoResize();
    this.initDeleteConfirmation();
  }

  initCategoryToggle() {
    const { categorySelect, newCategoryField, newCategoryInput } = this.elements;

    if (!categorySelect || !newCategoryField || !newCategoryInput) return;

    categorySelect.addEventListener('change', () => {
      this.toggleNewCategoryField();
    });

    this.initializeCategoryFieldState();
  }

  toggleNewCategoryField() {
    const { categorySelect, newCategoryField, newCategoryInput } = this.elements;
    const isNewCategory = categorySelect.value === this.constants.NEW_CATEGORY_VALUE;

    newCategoryField.style.display = isNewCategory ? 'block' : 'none';
    newCategoryInput.required = isNewCategory;

    if (!isNewCategory) {
      newCategoryInput.value = '';
    }
  }

  initializeCategoryFieldState() {
    const { categorySelect, newCategoryField, newCategoryInput } = this.elements;
    const selectedValue = categorySelect.value;

    const isValidOption = Array.from(categorySelect.options).some(option =>
      option.value === selectedValue && option.value !== this.constants.NEW_CATEGORY_VALUE
    );

    if (selectedValue && !isValidOption) {
      categorySelect.value = this.constants.NEW_CATEGORY_VALUE;
      newCategoryField.style.display = 'block';
      newCategoryInput.required = true;
    }
  }

  initFormValidation() {
    const { pizzaForm } = this.elements;

    if (!pizzaForm) return;

    pizzaForm.addEventListener('submit', (e) => {
      if (!this.validateForm()) {
        e.preventDefault();
      }
    });
  }

  validateForm() {
    return this.validatePrice() && this.validateNewCategory();
  }

  validatePrice() {
    const { priceInput } = this.elements;

    if (!priceInput?.value) return true;

    const price = parseFloat(priceInput.value);

    if (isNaN(price) || price <= 0) {
      this.showError('Por favor, insira um preço válido maior que zero.');
      priceInput.focus();
      return false;
    }

    return true;
  }

  validateNewCategory() {
    const { categorySelect, newCategoryInput } = this.elements;

    if (categorySelect?.value !== this.constants.NEW_CATEGORY_VALUE) return true;

    if (!newCategoryInput?.value?.trim()) {
      this.showError('Por favor, digite o nome da nova categoria.');
      newCategoryInput?.focus();
      return false;
    }

    return true;
  }

  showError(message) {
    alert(message);
  }

  initTextareaAutoResize() {
    const { descriptionTextarea } = this.elements;

    if (!descriptionTextarea) return;

    const autoResize = function () {
      this.style.height = 'auto';
      this.style.height = this.scrollHeight + 'px';
    };

    descriptionTextarea.addEventListener('input', autoResize);
    autoResize.call(descriptionTextarea);
  }

  initDeleteConfirmation() {
    if (!this.isOnPizzasPage()) return;

    const { deleteButtons } = this.elements;

    deleteButtons.forEach(button => {
      button.addEventListener('click', (e) => {
        e.preventDefault();
        this.handleDeleteClick(button);
      });
    });
  }

  isOnPizzasPage() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('page') === this.constants.PIZZAS_PAGE;
  }

  handleDeleteClick(button) {
    const href = button.getAttribute('href');

    if (!href) return;

    try {
      const pizzaId = this.extractPizzaIdFromHref(href);

      if (!pizzaId) {
        console.error('Could not extract pizza ID from href:', href);
        return;
      }

      this.confirmDeletion(pizzaId);
    } catch (error) {
      console.error('Error handling delete click:', error);
    }
  }

  extractPizzaIdFromHref(href) {
    const urlParts = href.split('?');
    if (urlParts.length < 2) return null;

    const params = new URLSearchParams(urlParts[1]);
    return params.get('confirm');
  }

  confirmDeletion(pizzaId) {
    if (typeof SweetAlertConfirm === 'undefined') {
      console.error('SweetAlertConfirm is not available');
      return;
    }

    SweetAlertConfirm.confirmPizzaDeletion(() => {
      window.location.href = `?page=${this.constants.PIZZAS_PAGE}&action=delete&id=${pizzaId}`;
    });
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new PizzaManager();
});