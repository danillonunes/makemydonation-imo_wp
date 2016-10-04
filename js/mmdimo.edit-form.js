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
  var $charities = $('#mmdimo-charities', metabox);
  var $select = $('#mmdimo-charity-select input', metabox);
  var $all = $('#mmdimo-charity-select-all', metabox);
  var $single = $('#mmdimo-charity-select-select', metabox);
  var $charitySelect = $('#mmdimo-charity-selection', metabox);
  var $charityMeta = $('#mmdimo-charity-metadata', metabox);
  var $charityName = $charities.after('<input type="text" id="mmdimo-charity-name" size="40">').next('#mmdimo-charity-name');
  var $charityActive = $state.before('<ul id="mmdimo-charity-active" class="mmdimo-empty"></ul>').prev('#mmdimo-charity-active');
  var metadata = {};
  var options, engine, dataset;

  if ($charities.length && $charityName.length) {
    $charities
      .hide()
      .on('mmdimo-add', function(e, charity) {
        metadata['charity-ein-' + charity.ein] = {
          charity: charity
        };
        $charities.trigger('mmdimo-reval');

        $charityActive
          .removeClass('mmdimo-empty')
          .append('<li id="mmdimo-charity-active-' + charity.ein + '"><strong>' + charity.name + '</strong><br><span>EIN: ' + charity.ein + ' — ' + charity.city + ', ' + charity.state + '</span> <a class="mmdimo-charity-clear" title="Clear selected charity">Remove</a></li>');
      })
      .on('mmdimo-remove', function(e, ein) {
        delete metadata['charity-ein-' + ein];
        $charities.trigger('mmdimo-reval');

        $charityActive.find('#mmdimo-charity-active-' + ein)
          .remove();

        if (!$charityActive.children('li').length) {
          $charityActive.addClass('mmdimo-empty');
        }
      })
      .on('mmdimo-reval', function() {
        $charities.val(Object.keys(metadata).join(',').replace(/charity-ein-/g, ''));
        $charityMeta.val(JSON.stringify(metadata));
      });

    $charityName
      .attr('autocomplete', 'OFF')
      .attr('aria-autocomplete', 'list');

    $state
      .bind('change', function() {
        var st = $(this).val();
        var state = '';

        if (st) {
          state = $state.find('option[value="' + $(this).val() + '"]').text() + ' ';
        }

        $charityName.attr('placeholder', 'Search all ' + state + 'charities');
      })
      .trigger('change');

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
        $charities.trigger('mmdimo-add', suggestion);

        $charityName
          .typeahead('close')
          .typeahead('val', '');
      });

    if ($charityMeta.val()) {
      var defaultMetadata = $.parseJSON($charityMeta.val());
      if (typeof defaultMetadata.charity != 'undefined') {
        metadata['charity-ein-' + defaultMetadata.charity.ein] = defaultMetadata;
      }
      else {
        $.each(defaultMetadata, function(ein, charity) {
          metadata[ein] = charity;
        });
      }

      $.each(metadata, function(ein, option) {
        $charities.trigger('mmdimo-add', option.charity);
      });
    }

    $select
      .bind('change', function() {
        if ($single.is(':checked')) {
          $charitySelect.show();
        }
        else {
         $charitySelect.hide();
        }
      })
      .trigger('change');

    $charityActive
      .on('click', '.mmdimo-charity-clear', function() {
        var ein = $(this).parent('li').attr('id').match(/\d+/)[0];

        $charities.trigger('mmdimo-remove', ein);
      });
  }
});

});
