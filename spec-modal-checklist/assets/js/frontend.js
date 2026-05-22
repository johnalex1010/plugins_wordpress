(function () {
  'use strict';

  function getStorageKey(modal) {
    return 'smp_modal_seen_v4_' + modal.id;
  }

  function shouldShow(modal) {
    var frequency = modal.getAttribute('data-smp-frequency') || 'session';
    var key = getStorageKey(modal);

    try {
      if (frequency === 'session') {
        return !window.sessionStorage.getItem(key);
      }

      if (frequency === 'persistent') {
        var stored = window.localStorage.getItem(key);

        if (!stored) {
          return true;
        }

        return Date.now() - parseInt(stored, 10) > 60 * 60 * 1000;
      }
    } catch (error) {
      return true;
    }

    return true;
  }

  function markAsSeen(modal) {
    var frequency = modal.getAttribute('data-smp-frequency') || 'session';
    var key = getStorageKey(modal);

    try {
      if (frequency === 'session') {
        window.sessionStorage.setItem(key, '1');
      } else if (frequency === 'persistent') {
        window.localStorage.setItem(key, String(Date.now()));
      }
    } catch (error) {}
  }

  function closeModal(modal) {
    modal.classList.remove('is-visible');
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.smp-overlay').forEach(function (modal) {
      var delay = parseInt(modal.getAttribute('data-smp-delay'), 10) || 2000;
      var closeButton = modal.querySelector('.smp-close');

      if (closeButton) {
        closeButton.addEventListener('click', function (event) {
          event.preventDefault();
          closeModal(modal);
        });
      }

      modal.addEventListener('click', function (event) {
        if (event.target === modal) {
          closeModal(modal);
        }
      });

      if (!shouldShow(modal)) {
        return;
      }

      window.setTimeout(function () {
        modal.classList.add('is-visible');
        markAsSeen(modal);
      }, delay);
    });
  });
})();
