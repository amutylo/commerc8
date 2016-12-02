/**
 * @file
 * Machine name functionality.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  drupalSettings.news_core = drupalSettings.news_core || {};

  /**
   * Attach the machine-readable name form element behavior.
   */
  Drupal.behaviors.newsLineForm = {
    attach: function (context, settings) {
      function checkEuCookie() {
        return typeof drupalSettings.eu_cookie_compliance == 'undefined' || Drupal.eu_cookie_compliance.hasAgreed();
      }

      // Reload page after user agree with cookie.
      if (typeof drupalSettings.eu_cookie_compliance != 'undefined') {
        $('.agree-button').bind('click', function () {
          location.reload();
        });
      }

      $('body').once('js--dialog').each(function () {
        // Hide home feed btn.
        if (!checkEuCookie()) {
          $('#categories-settings').hide();
        }
        var feed_popup = $('#feed-popup-link');
        if (feed_popup.length && typeof drupalSettings.news_core != 'undefined' && checkEuCookie()) {
          if (drupalSettings.news_core.isAnonymous && !$.cookie(drupalSettings.news_core.cookiesName)) {
            feed_popup.click();
          }
        }
      });

      if (typeof drupalSettings.news_core.validateCount != 'undefined') {
        var feedButton = {
          items: drupalSettings.news_core.validateCount || 0,
          forms: $('.home-feed-forms'),
          init: function(){
            var self = this;
            self.forms.each(function() {
              var form = $(this);
              if (self.getCount(form) < self.items) {
                self.disableSubmit(form);
              }
              $(this).find('input:checkbox').change(function () {
                if (self.getCount(form) >= self.items) {
                  self.enableSubmit(form);
                }
                else {
                  self.disableSubmit(form);
                }
              });
            });
          },
          getCount: function(form){
            return form.find('input:checkbox:checked').length;
          },
          enableSubmit: function(form){
            form.find('input[type=submit]').removeAttr('disabled');
          },
          disableSubmit: function(form){
            form.find('input[type=submit]').attr('disabled', 'disabled');
          }
        };
        feedButton.init();
      }
    }
  };

  Drupal.behaviors.newsFormattersFeed = {
    attach: function (context, settings) {
      var btn = $('.add-category-btn');
      var form_feed = $('.home-feed-forms');
      if (form_feed.length && btn.length) {
        btn.on('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          var item = $(this).data('item');
          var checkbox = form_feed.find('input[name="category['+ item +']"]');
          if (checkbox.length) {
            $('#categories-settings').closest('.main-nav').addClass('settings-showed');
            $('a.menu-toggle-btn').click();
            checkbox.prop('checked', true).trigger('change');
            checkbox.parent().addClass('new-selected-item')
          }
        })
      }
    }
  };

})(jQuery, Drupal, drupalSettings);