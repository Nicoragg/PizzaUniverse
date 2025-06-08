class SweetAlertConfirm {
  static defaultConfig = {
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#c82333',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Sim, excluir!',
    cancelButtonText: 'Cancelar',
    reverseButtons: true
  };

  static showDeleteConfirmation({ title, text, onConfirm, customConfig = {} }) {
    const config = {
      ...this.defaultConfig,
      title,
      text,
      ...customConfig
    };

    Swal.fire(config).then((result) => {
      if (result.isConfirmed && onConfirm) {
        onConfirm();
      }
    });
  }

  static confirmPizzaDeletion(onConfirm) {
    this.showDeleteConfirmation({
      title: 'Confirmar Exclusão',
      text: 'Tem certeza que deseja excluir esta pizza? Esta ação não pode ser desfeita.',
      onConfirm
    });
  }

  static confirmUserDeletion(onConfirm) {
    this.showDeleteConfirmation({
      title: 'Confirmar Exclusão',
      text: 'Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.',
      onConfirm
    });
  }

  static confirmCustomerDeletion(onConfirm) {
    this.showDeleteConfirmation({
      title: 'Confirmar Exclusão',
      text: 'Tem certeza que deseja excluir este cliente? Esta ação não pode ser desfeita.',
      onConfirm
    });
  }

  static confirmCustomerDeactivation(onConfirm) {
    Swal.fire({
      title: 'Desativar Cliente',
      text: 'Tem certeza que deseja desativar este cliente?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ffc107',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sim, desativar!',
      cancelButtonText: 'Cancelar',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed && onConfirm) {
        onConfirm();
      }
    });
  }

  static confirmCustomerActivation(onConfirm) {
    Swal.fire({
      title: 'Ativar Cliente',
      text: 'Tem certeza que deseja ativar este cliente?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sim, ativar!',
      cancelButtonText: 'Cancelar',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed && onConfirm) {
        onConfirm();
      }
    });
  }

  static showSuccess(title = 'Sucesso!', text = 'Operação realizada com sucesso.') {
    Swal.fire({
      title,
      text,
      icon: 'success',
      confirmButtonColor: '#28a745'
    });
  }

  static showError(title = 'Erro!', text = 'Ocorreu um erro durante a operação.') {
    Swal.fire({
      title,
      text,
      icon: 'error',
      confirmButtonColor: '#dc3545'
    });
  }
}