/**
 * @file
 * Default JavaScript file for Modal Page.
 */

(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.modalPage = {
    attach: function (context, settings) {

      var modalPage = $('#js-modal-page-show-modal', context);

      if (!modalPage.length) {
        return false;
      }

      var checkbox_please_do_not_show_again = $('.modal-page-please-do-not-show-again', context);
      var id_modal = checkbox_please_do_not_show_again.val();
      var cookie_please_do_not_show_again = $.cookie('please_do_not_show_again_modal_id_' + id_modal);

      // Check don't show again option.
      if (cookie_please_do_not_show_again) {
        return false;
      }

      // Check auto-open.
      var auto_open = true;
      if (typeof settings.modal_page != 'undefined' && settings.modal_page.auto_open) {
        auto_open = settings.modal_page.auto_open;
      }

      // Open Modal on Auto Open.
      if (auto_open == true) {

        var delay = $('#js-modal-page-show-modal #delay_display', context).val() * 1000;

        setTimeout(function () {
          modalPage.modal();
        }, delay);
      }

      // Open Modal Page clicking on "open-modal-page" class.
      $('.open-modal-page', context).on('click', function () {
        modalPage.modal();
      });

      // Open Modal Page clicking on user custom element.
      if (typeof settings.modal_page != 'undefined' && settings.modal_page.open_modal_on_element_click) {
        var link_open_modal = $(settings.modal_page.open_modal_on_element_click, context);
        link_open_modal.on('click', function () {
          modalPage.modal();
        });
      }

      var ok_buttom = $('.js-modal-page-ok-buttom', context);

      ok_buttom.on('click', function () {

        if (checkbox_please_do_not_show_again.is(':checked')) {

          $.cookie('please_do_not_show_again_modal_id_' + id_modal, true, {expires: 365 * 20 });

        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
