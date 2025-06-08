document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const currentPage = urlParams.get('page');

  if (currentPage !== 'users') return;

  const deleteButtons = document.querySelectorAll('a[href*="confirm="]');

  deleteButtons.forEach(button => {
    button.addEventListener('click', (e) => {
      e.preventDefault();

      const href = button.getAttribute('href');
      const userId = new URLSearchParams(href.split('?')[1]).get('confirm');

      SweetAlertConfirm.confirmUserDeletion(() => {
        window.location.href = `?page=users&action=delete&id=${userId}`;
      });
    });
  });
});