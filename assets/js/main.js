/* Vivera / EVE-N — shared JavaScript */

document.addEventListener('DOMContentLoaded', () => {
  /* ---- Mobile Menu Toggle ---- */
  const hamburger = document.querySelector('.hamburger');
  const mobileMenu = document.querySelector('.mobile-menu');
  if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', () => {
      const open = mobileMenu.classList.toggle('open');
      hamburger.classList.toggle('open', open);
    });
  }

  /* ---- Accordion Toggle ---- */
  document.querySelectorAll('.accordion__item').forEach((item) => {
    const header = item.querySelector('.accordion__header');
    if (header) {
      header.addEventListener('click', () => {
        const isOpen = item.classList.contains('open');
        // Close all in same accordion group
        const group = item.closest('.accordion');
        if (group) {
          group.querySelectorAll('.accordion__item').forEach((sibling) => {
            sibling.classList.remove('open');
          });
        }
        if (!isOpen) {
          item.classList.add('open');
        }
      });
    }
  });

  /* ---- Projecten card toggle (mobile: same accordion behavior) ---- */
  document.querySelectorAll('.card__icon--plus').forEach((icon) => {
    icon.addEventListener('click', (e) => {
      e.stopPropagation();
      const card = icon.closest('.card');
      if (card) {
        const isOpen = card.classList.contains('open');
        document.querySelectorAll('.card.open').forEach((c) => c.classList.remove('open'));
        if (!isOpen) {
          card.classList.add('open');
        }
      }
    });
  });
});
