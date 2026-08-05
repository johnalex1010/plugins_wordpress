(function () {
  'use strict';

  function isHomeOrFrontPage() {
    return document.body.classList.contains('home') || document.body.classList.contains('front-page');
  }

  function findHeaderTarget() {
    var headerSelectors = [
      'body > header',
      'header.site-header',
      '#masthead',
      '#header',
      '.site-header',
      '.main-header'
    ];
    var headerTarget = null;

    headerSelectors.some(function (selector) {
      headerTarget = document.querySelector(selector);
      return Boolean(headerTarget);
    });

    return headerTarget;
  }

  function findBreadcrumbTarget() {
    var breadcrumbContainerSelectors = [
      '.breadcrumbs-header',
      '.breadcrumbs_header',
      '.breadcrumb-header'
    ];
    var breadcrumbSelectors = [
      '.yoast-breadcrumb',
      '#breadcrumbs',
      '.breadcrumbs',
      '.breadcrumb',
      '.rank-math-breadcrumb',
      '.bcn-breadcrumb-trail',
      'nav[aria-label="breadcrumb"]',
      'nav[aria-label="Breadcrumb"]'
    ];
    var breadcrumbTarget = null;

    breadcrumbContainerSelectors.some(function (selector) {
      breadcrumbTarget = document.querySelector(selector);
      return Boolean(breadcrumbTarget);
    });

    if (breadcrumbTarget) {
      return breadcrumbTarget;
    }

    breadcrumbSelectors.some(function (selector) {
      var breadcrumbElement = document.querySelector(selector);

      if (!breadcrumbElement) {
        return false;
      }

      breadcrumbTarget = breadcrumbElement.closest(breadcrumbContainerSelectors.join(', ')) || breadcrumbElement;
      return true;
    });

    return breadcrumbTarget;
  }

  function findPlacementTarget() {
    if (isHomeOrFrontPage()) {
      return findHeaderTarget();
    }

    return findBreadcrumbTarget() || findHeaderTarget();
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
