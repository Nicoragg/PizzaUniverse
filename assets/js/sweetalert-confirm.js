/**
 * Classe utilitária para modais de confirmação usando SweetAlert2
 */
class SweetAlertConfirm {
  /**
   * Configuração padrão para modais de confirmação
   */
  static defaultConfig = {
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Sim, excluir!',
    cancelButtonText: 'Cancelar',
    reverseButtons: true
  };

  /**
   * Mostra um modal de confirmação de exclusão
   * @param {Object} options - Opções para o modal
   * @param {string} options.title - Título do modal
   * @param {string} options.text - Texto do modal
   * @param {Function} options.onConfirm - Callback executado quando confirmado
   * @param {Object} options.customConfig - Configurações customizadas do SweetAlert2
   */
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

  /**
   * Configuração específica para exclusão de pizzas
   * @param {Function} onConfirm - Callback executado quando confirmado
   */
  static confirmPizzaDeletion(onConfirm) {
    this.showDeleteConfirmation({
      title: 'Confirmar Exclusão',
      text: 'Tem certeza que deseja excluir esta pizza? Esta ação não pode ser desfeita.',
      onConfirm
    });
  }

  /**
   * Configuração específica para exclusão de usuários
   * @param {Function} onConfirm - Callback executado quando confirmado
   */
  static confirmUserDeletion(onConfirm) {
    this.showDeleteConfirmation({
      title: 'Confirmar Exclusão',
      text: 'Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.',
      onConfirm
    });
  }

  /**
   * Modal de sucesso
   * @param {string} title - Título do modal
   * @param {string} text - Texto do modal
   */
  static showSuccess(title = 'Sucesso!', text = 'Operação realizada com sucesso.') {
    Swal.fire({
      title,
      text,
      icon: 'success',
      confirmButtonColor: '#28a745'
    });
  }

  /**
   * Modal de erro
   * @param {string} title - Título do modal
   * @param {string} text - Texto do modal
   */
  static showError(title = 'Erro!', text = 'Ocorreu um erro durante a operação.') {
    Swal.fire({
      title,
      text,
      icon: 'error',
      confirmButtonColor: '#dc3545'
    });
  }
}