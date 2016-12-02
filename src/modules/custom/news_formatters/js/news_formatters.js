/**
 * @file
 * Contains js for the change time zone.
 */

(function ($, Drupal) {
  'use strict';
  Drupal.behaviors.news_formatters = {
    attach: function (context, settings) {
      var dateSS = $('.news-date');
      if (dateSS && dateSS.length) {
          var days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
              months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
          if (dateSS && dateSS.length > 0) {
              $(dateSS).once('js--time-toggle').each(function () {
                  var created = $(this).data('time') * 1000;
                  var interval = Math.round((jQuery.now() - created)/1000);
                  if (interval > 86400) {
                      var date_format = (drupalSettings.news_formatters.date_formatter || 'Y-m-d'),
                          date = new Date(created),
                          replacements = {
                              "dd": date.getDate(), // day of the week 2 num
                              "d": date.getDate(), // day of the week 2 num
                              "D": days[date.getDay()], // day of the week name
                              "M": months[date.getMonth()], // month name
                              "m": date.getMonth() + 1, // month 1 num
                              "mm": date.getMonth() + 1, // month 2 num
                              "Y": date.getFullYear(), // year 4 num
                              "yy": date.getFullYear(),  // year 4 num
                              "HH": date.getHours(),  // year 2 num, 24h format
                              "nn": date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()
                          }, date_transform = date_format;
                      date_transform = date_transform.replace(/\w+/g, function (all) {
                          return replacements[all] || all;
                      });
                      $(this).html(date_transform);
                  }
                  else if (interval > 3600) {
                      $(this).html(Math.floor(interval / 3600) + Drupal.t(' h ago'));
                  }
                  else if (interval > 60) {
                      $(this).html(Math.floor(interval / 60) + Drupal.t(' min ago'));
                  }
                  else {
                      $(this).html(interval + Drupal.t(' sec ago'));
                  }
              });
          }
      }
    }
  }

})(jQuery, Drupal);
