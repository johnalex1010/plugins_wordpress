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

  // Ajusta el ancho del modal a la proporción real del video para evitar franjas negras en videos verticales.
  function applyVideoRatio(modal, video) {
    var videoWidth = video.videoWidth;
    var videoHeight = video.videoHeight;

    if (!videoWidth || !videoHeight) {
      return;
    }

    var isPortrait = videoHeight > videoWidth;

    modal.style.setProperty('--smp-video-ratio', videoWidth + ' / ' + videoHeight);
    modal.classList.toggle('smp-modal--portrait', isPortrait);
    modal.classList.toggle('smp-modal--landscape', !isPortrait);
  }

  function loadModalMedia(modal) {
    var dialog = modal.querySelector('.smp-modal');

    modal.querySelectorAll('video.smp-modal__video').forEach(function (video) {
      if (dialog) {
        if (video.readyState >= 1) {
          applyVideoRatio(dialog, video);
        } else {
          video.addEventListener('loadedmetadata', function () {
            applyVideoRatio(dialog, video);
          }, { once: true });
        }
      }

      if (video.preload === 'none') {
        video.preload = 'metadata';
        video.load();
      }
    });

    modal.querySelectorAll('iframe[data-smp-src]').forEach(function (iframe) {
      if (!iframe.getAttribute('src')) {
        iframe.setAttribute('src', iframe.getAttribute('data-smp-src'));
      }
    });
  }

  function stopModalMedia(modal) {
    modal.querySelectorAll('video.smp-modal__video').forEach(function (video) {
      video.pause();
    });

    // Quitar el src del iframe es la forma fiable de detener YouTube sin cargar su API.
    modal.querySelectorAll('iframe[data-smp-src]').forEach(function (iframe) {
      iframe.removeAttribute('src');
    });
  }

  function showModal(modal) {
    loadModalMedia(modal);
    modal.classList.add('is-visible');
  }

  function closeModal(modal) {
    modal.classList.remove('is-visible');
    stopModalMedia(modal);
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
        showModal(modal);
        markAsSeen(modal);
      }, delay);
    });
  });
})();
