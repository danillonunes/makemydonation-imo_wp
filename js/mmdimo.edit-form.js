jQuery(function($) {

var $metabox = $('#mmdimo_meta_box');

$metabox.each(function() {
  var $create = $('#mmdimo-case-create', $metabox);
  var $data = $('.case-data', $metabox);

  if ($create.length) {
    $data.hide();

    $create.bind('change', function() {
      if ($(this).is(':checked')) {
        $data.slideDown();
      }
      else {
        $data.slideUp();
      }
    }).trigger('change');
  }
});

$metabox.each(function() {
  var $family = $('.mmdimo-field-family-emails input', $metabox);
  var $notify = $('.mmdimo-field-family-notify', $metabox);

  $family
    .selectize({
      'create': true,
      'createOnBlur': true,
      'highlight': false,
      'persist': false,
      'openOnFocus': false,
      'maxOptions': 0,
      'maxItems': null,
      'createFilter': /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/
    })
    .bind('change', function() {
      $notify.hide();
      if ($family.val() != '') {
        $notify.show();
      }
    })
    .trigger('change');

    $('<style type="text/css">.selectize-control.multi .selectize-input [data-value], .selectize-control.multi .selectize-input .active[data-value] { background-color: ' + _wpColorScheme.icons.base + '; } </style>').appendTo($family);
});

$('#mmdimo_meta_box').each(function() {
  var metabox = this;
  var $create = $('#mmdimo-case-create', metabox);
  var $data = $('.case-data', metabox);
  var $state = $('#mmdimo-charity-state', metabox);
  var $charity = $('#mmdimo-charity', metabox);
  var $select = $('#mmdimo-charity-select input', metabox);
  var $all = $('#mmdimo-charity-select-all', metabox);
  var $single = $('#mmdimo-charity-select-single', metabox);
  var $charitySelect = $('#mmdimo-charity-single-select', metabox);
  var $charityMeta = $('#mmdimo-charity-metadata', metabox);
  var $charityName = $charity.after('<input type="text" id="mmdimo-charity-name" size="40">').next('#mmdimo-charity-name');
  var $charityActive = $charity.after('<a id="mmdimo-charity-active" title="Select another charity"></a>').next('#mmdimo-charity-active').hide();
  var $charityClear = $charityActive.after('<a id="mmdimo-charity-clear" title="Clear selected charity">Remove</a>').next('#mmdimo-charity-clear').hide();
  var options, engine, dataset;

  if ($charity.length && $charityName.length) {
    $charity.hide();

    $charityName
      .attr('autocomplete', 'OFF')
      .attr('aria-autocomplete', 'list');

    $state.bind('change', function() {
      var st = $(this).val();
      var state = '';

      if (st) {
        state = $state.find('option[value="' + $(this).val() + '"]').text() + ' ';
      }

      $charityName.attr('placeholder', 'Search all ' + state + 'charities');
    }).trigger('change');

    engine = new Bloodhound({
      identify: function(o) { return o.id_str; },
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      datumTokenizer: Bloodhound.tokenizers.obj.whitespace(),
      dupDetector: function(a, b) { return a.id_str === b.id_str; },
      remote: {
        url: 'https://funerals.makemydonation.org/orghunter/charitysearch/autocomplete/%QUERY/typeahead?eligible=1',
        prepare: function(query, settings) {
          var state = $state.val();
          settings.url = settings.url.replace('%QUERY', encodeURIComponent(query));
          if (state) {
            settings.url = settings.url + '&state=' + state;
          }
          return settings;
        },
        transform: function(results) {
          var arr = [];

          for (var key in results) {
            if (results.hasOwnProperty(key)) {
              arr.push(results[key]);
            }
          }

          arr.sort(function(a, b) {
            if (a.weight && b.weight) {
              if (a.weight < b.weight) {
                return -1;
              }
              if (a.weight > b.weight) {
                return 1;
              }
            }
            return 0;
          });

          return arr;
        },
      },
    });
    options = {};
    dataset = {
      source: engine,
      display: function(suggestion) {
        return suggestion.name;
      },
      templates: {
        suggestion: function(suggestion) {
          return '<div><strong>' + suggestion.name + '</strong><br><span>EIN: ' + suggestion.ein + ' — ' + suggestion.city + ', ' + suggestion.state + '</span></div>';
        }
      },
    };

    $charityName
      .typeahead(options, dataset)
      .bind('typeahead:select', function(event, suggestion) {
        var metadata = {
          state: $state.val(),
          charity: suggestion,
        };

        $charityActive
          .html('<strong>' + suggestion.name + '</strong><br><span>EIN: ' + suggestion.ein + ' — ' + suggestion.city + ', ' + suggestion.state + '</span>')
          .show();
        $charityClear.show();
        $charity.val(suggestion.ein);

        $charityMeta.val(JSON.stringify(metadata));

        $('.twitter-typeahead', metabox).hide();
        $state.hide();
        $charityName.hide();
      });

    if ($charityMeta.val()) {
      var metadata = $.parseJSON($charityMeta.val());
      var suggestion = metadata.charity;

      $charityActive
        .html('<strong>' + suggestion.name + '</strong><br><span>EIN: ' + suggestion.ein + ' — ' + suggestion.city + ', ' + suggestion.state + '</span>')
        .show();
      $charityClear.show();

      $state.val(metadata.state);
      $charityName.val(suggestion.name);

      $('.twitter-typeahead', metabox).hide();
      $state.hide();
      $charityName.hide();
    }

    $charityActive.bind('click', function(event) {
      $charityActive.hide();
      $charityClear.hide();
      $('.twitter-typeahead', metabox).show();
      $state.show();
      $charityName.show().focus();
      $charity.val('');
      $charityMeta.val('');

      event.stopPropagation();
      return false;
    });

    $select
      .bind('change', function() {
        if ($single.is(':checked')) {
          $charitySelect.show();
        }
        else {
          $charityClear.trigger('click');
          $charitySelect.hide();
        }
      })
      .trigger('change');

    $charityClear.bind('click', function() {
      $charityActive.trigger('click');
      $charityName.val('').blur();
    });
  }
});

});
