(function () {
  'use strict';

  function findPlacementTarget() {
    var breadcrumbSelectors = [
      '.breadcrumbs_header',
      '.yoast-breadcrumb',
      '#breadcrumbs',
      '.breadcrumbs',
      '.breadcrumb',
      '.rank-math-breadcrumb',
      '.bcn-breadcrumb-trail',
      'nav[aria-label="breadcrumb"]',
      'nav[aria-label="Breadcrumb"]'
    ];
    var headerSelectors = [
      'body > header',
      'header.site-header',
      '#masthead',
      '#header',
      '.site-header',
      '.main-header'
    ];
    var target = null;

    breadcrumbSelectors.some(function (selector) {
      target = document.querySelector(selector);
      return Boolean(target);
    });

    if (target) {
      return target;
    }

    headerSelectors.some(function (selector) {
      target = document.querySelector(selector);
      return Boolean(target);
    });

    return target;
  }

  function placeBanners() {
    var banners = Array.prototype.slice.call(document.querySelectorAll('[data-shb-header-banner]'));
    var target = findPlacementTarget();

    if (!banners.length || !target || !target.parentNode) {
      return;
    }

    banners.forEach(function (banner) {
      target.parentNode.insertBefore(banner, target.nextSibling);
      target = banner;
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', placeBanners);
  } else {
    placeBanners();
  }
})();
