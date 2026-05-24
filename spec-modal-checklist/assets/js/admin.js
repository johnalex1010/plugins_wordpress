(function ($) {
  'use strict';

  $(function () {
    var mediaFrame;
    var $pageSearch = $('#smp_page_search');
    var $pageItems = $('.smp-page-selector__item');
    var $pageEmpty = $('.smp-page-selector__empty');

    $('#smp_upload_image_button').on('click', function (event) {
      event.preventDefault();

      if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
        return;
      }

      if (mediaFrame) {
        mediaFrame.open();
        return;
      }

      mediaFrame = wp.media({
        title: window.SMP_ADMIN_I18N && window.SMP_ADMIN_I18N.mediaTitle ? window.SMP_ADMIN_I18N.mediaTitle : 'Seleccionar Imagen',
        button: {
          text: window.SMP_ADMIN_I18N && window.SMP_ADMIN_I18N.mediaButton ? window.SMP_ADMIN_I18N.mediaButton : 'Usar Imagen'
        },
        multiple: false
      });

      mediaFrame.on('select', function () {
        var attachment = mediaFrame.state().get('selection').first().toJSON();
        var image = document.createElement('img');

        image.src = attachment.url;
        image.alt = attachment.alt || '';

        $('#smp_image_id').val(attachment.id);
        $('#smp_image_preview').empty().append(image);
      });

      mediaFrame.open();
    });

    $pageSearch.on('input', function () {
      var query = String($(this).val() || '').toLowerCase().trim();
      var visibleCount = 0;

      $pageItems.each(function () {
        var $item = $(this);
        var title = String($item.data('smp-page-title') || '');
        var isVisible = !query || title.indexOf(query) !== -1;

        $item.toggleClass('is-hidden', !isVisible);

        if (isVisible) {
          visibleCount += 1;
        }
      });

      $pageEmpty.toggleClass('is-visible', visibleCount === 0);
    });
  });
})(jQuery);
