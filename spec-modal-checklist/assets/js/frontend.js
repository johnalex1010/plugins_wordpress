(function () {
  'use strict';

  // Debe coincidir con el breakpoint de frontend.css: por debajo de 800px solo se muestran modales con versión móvil.
  var MOBILE_VIEWPORT_QUERY = '(max-width: 799.98px)';

  function getMobileViewportQuery() {
    return typeof window.matchMedia === 'function' ? window.matchMedia(MOBILE_VIEWPORT_QUERY) : null;
  }

  function isMobileViewport() {
    var mobileViewportQuery = getMobileViewportQuery();

    return Boolean(mobileViewportQuery && mobileViewportQuery.matches);
  }

  function hasMobileVersion(modal) {
    return modal.classList.contains('smp-overlay--has-mobile');
  }

  function canShowInCurrentViewport(modal) {
    return !isMobileViewport() || hasMobileVersion(modal);
  }

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

  function getModalImageUrls(modal) {
    var imageUrls = [];

    modal.querySelectorAll('video[data-smp-poster], img.smp-youtube-facade__image[data-smp-src]').forEach(function (element) {
      imageUrls.push(element.getAttribute(element.tagName === 'VIDEO' ? 'data-smp-poster' : 'data-smp-src'));
    });

    return imageUrls;
  }

  // Descarga una imagen y avisa al terminar (o fallar) para encadenar la carga del video después de la portada.
  function loadImage(imageUrl, onComplete) {
    var image = new Image();
    var handleComplete = function () {
      if (onComplete) {
        onComplete();
      }
    };

    if (!imageUrl) {
      handleComplete();
      return;
    }

    image.fetchPriority = 'low';
    image.onload = handleComplete;
    image.onerror = handleComplete;
    image.src = imageUrl;
  }

  // Precarga portada y miniatura durante el delay, con prioridad baja y después de la carga de la página, para no competir con el LCP.
  function preloadModalImages(modal) {
    var startPreload = function () {
      if (isMobileViewport()) {
        return;
      }

      getModalImageUrls(modal).forEach(function (imageUrl) {
        loadImage(imageUrl);
      });
    };

    if (document.readyState === 'complete') {
      startPreload();
    } else {
      window.addEventListener('load', startPreload, { once: true });
    }
  }

  function isModalMediaActive(modal) {
    return modal.classList.contains('is-visible') && !isMobileViewport();
  }

  function loadVideoMetadata(modal, video) {
    var dialog = modal.querySelector('.smp-modal');

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
  }

  // Orden de carga: primero la portada y, cuando ya se ve, la metadata del video.
  function loadModalVideo(modal, video) {
    var posterUrl = video.getAttribute('data-smp-poster');

    if (posterUrl && !video.getAttribute('poster')) {
      video.setAttribute('poster', posterUrl);
    }

    loadImage(posterUrl, function () {
      if (isModalMediaActive(modal)) {
        loadVideoMetadata(modal, video);
      }
    });
  }

  function loadModalMedia(modal) {
    modal.querySelectorAll('video.smp-modal__video').forEach(function (video) {
      loadModalVideo(modal, video);
    });

    // YouTube: solo se muestra la miniatura; el iframe se carga cuando el usuario pulsa reproducir.
    modal.querySelectorAll('img.smp-youtube-facade__image[data-smp-src]').forEach(function (thumbnail) {
      if (!thumbnail.getAttribute('src')) {
        thumbnail.setAttribute('src', thumbnail.getAttribute('data-smp-src'));
      }
    });
  }

  function playYouTubeVideo(facadeButton) {
    var media = facadeButton.closest('.smp-media');
    var iframe = media ? media.querySelector('iframe[data-smp-src]') : null;

    if (!iframe) {
      return;
    }

    iframe.setAttribute('src', iframe.getAttribute('data-smp-src'));
    iframe.hidden = false;
    facadeButton.hidden = true;
  }

  function stopModalMedia(modal) {
    modal.querySelectorAll('video.smp-modal__video').forEach(function (video) {
      video.pause();
    });

    // Quitar el src del iframe es la forma fiable de detener YouTube sin cargar su API; se vuelve a mostrar la miniatura.
    modal.querySelectorAll('iframe[data-smp-src]').forEach(function (iframe) {
      iframe.removeAttribute('src');
      iframe.hidden = true;
    });

    modal.querySelectorAll('.smp-youtube-facade').forEach(function (facadeButton) {
      facadeButton.hidden = false;
    });
  }

  function showModal(modal) {
    modal.classList.add('is-visible');

    // En mobile el video está oculto: no se descarga la portada, el video ni la miniatura de YouTube.
    if (!isMobileViewport()) {
      loadModalMedia(modal);
    }
  }

  // Si el viewport cruza el breakpoint con el modal abierto, carga o detiene el video según corresponda.
  function bindViewportChange() {
    var mobileViewportQuery = getMobileViewportQuery();

    if (!mobileViewportQuery) {
      return;
    }

    var handleViewportChange = function () {
      document.querySelectorAll('.smp-overlay.is-visible').forEach(function (modal) {
        if (isMobileViewport()) {
          stopModalMedia(modal);
        } else {
          loadModalMedia(modal);
        }
      });
    };

    if (typeof mobileViewportQuery.addEventListener === 'function') {
      mobileViewportQuery.addEventListener('change', handleViewportChange);
    } else if (typeof mobileViewportQuery.addListener === 'function') {
      mobileViewportQuery.addListener(handleViewportChange);
    }
  }

  function closeModal(modal) {
    modal.classList.remove('is-visible');
    stopModalMedia(modal);
  }

  document.addEventListener('DOMContentLoaded', function () {
    bindViewportChange();

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

      modal.querySelectorAll('.smp-youtube-facade').forEach(function (facadeButton) {
        facadeButton.addEventListener('click', function () {
          playYouTubeVideo(facadeButton);
        });
      });

      if (!shouldShow(modal)) {
        return;
      }

      preloadModalImages(modal);

      window.setTimeout(function () {
        // Se revisa al vencer el delay para no consumir la frecuencia de un modal que no se puede mostrar.
        if (!canShowInCurrentViewport(modal)) {
          return;
        }

        showModal(modal);
        markAsSeen(modal);
      }, delay);
    });
  });
})();
