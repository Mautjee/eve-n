/* Vivera / EVE-N — shared JavaScript */

document.addEventListener('DOMContentLoaded', () => {
  /* ---- Mobile Menu Toggle ---- */
  const hamburger = document.querySelector('.hamburger');
  const mobileMenu = document.querySelector('.mobile-menu');
  if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', () => {
      const open = mobileMenu.classList.toggle('open');
      hamburger.classList.toggle('open', open);
      hamburger.setAttribute('aria-expanded', String(open));
      hamburger.setAttribute('aria-label', open ? 'Menu sluiten' : 'Menu openen');
    });

    /* Close on Escape so the overlay is not a keyboard trap. */
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && mobileMenu.classList.contains('open')) {
        mobileMenu.classList.remove('open');
        hamburger.classList.remove('open');
        hamburger.setAttribute('aria-expanded', 'false');
        hamburger.setAttribute('aria-label', 'Menu openen');
        hamburger.focus();
      }
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

  /* ---- Accordion: aria-expanded + "+"/"−" toggle text ----
     A second, additive listener (rather than editing the block above) so the
     existing open/close logic stays untouched. Runs after it, since it is
     registered after it, so it can read the class state the first listener
     just set. */
  document.querySelectorAll('.accordion__header').forEach((header) => {
    header.addEventListener('click', () => {
      const group = header.closest('.accordion');
      const headers = group ? group.querySelectorAll('.accordion__header') : [header];
      headers.forEach((h) => {
        const isOpen = h.closest('.accordion__item').classList.contains('open');
        h.setAttribute('aria-expanded', String(isOpen));
        const toggle = h.querySelector('.accordion__toggle');
        if (toggle) toggle.textContent = isOpen ? '−' : '+';
      });
    });
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
        document.querySelectorAll('.card__icon--plus').forEach((other) => {
          const parent = other.closest('.card');
          other.setAttribute('aria-expanded', String(!!parent && parent.classList.contains('open')));
        });
      }
    });
  });
});
