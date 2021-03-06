window = (function (window, document, $, undefined) {
  'use strict';

  var app = {};

  app.cache = function () {
    // The form
    app.$metabox = $('#field_group');
    app.$colors = app.$metabox.find('select');
  };

  app.init = function () {
    app.cache();
    $('body').on('change', '#blokk_velger', app.change_color)
  };

  app.change_color = function (evt) {
    var that = $(this),
      new_val = that.val();

    app.disable_form();

    // ajaxurl is already defined in wp-admin, so no special stuff needed.
    $.post(ajaxurl, {
      action: 'get_options',
      value: new_val
    }, app.handle_response, 'json');
  };

  app.handle_response = function (resp) {
    console.log(resp);
    if (!resp.success || !resp.data) {
      return false;
    }

    // Clear out the <option> tags
    app.$colors.empty();
    app.$colors.append(resp.data);

    app.enable_form();
  };

  app.disable_form = function () {
    app.$metabox.find('select').prop('disabled', true);
  };

  app.enable_form = function () {
    app.$metabox.find('select').prop('disabled', false);
  };

  $(document).ready(app.init);

  return app;

})(window, document, jQuery);