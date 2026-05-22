(function () {
  'use strict';

  document.addEventListener('click', function (event) {
    var closeButton = event.target.closest('.sfb-floating-banner__close');

    if (!closeButton) {
      return;
    }

    var bannerId = closeButton.getAttribute('data-sfb-banner-id');
    var banner = document.getElementById('sfb-banner-' + bannerId);

    if (banner) {
      banner.style.display = 'none';
    }
  });
})();
