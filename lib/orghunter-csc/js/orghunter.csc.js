(function(w) {

/**
 * Libraries data.
 */
var libs = {
	jquery: {
		js: ['lib/jquery/jquery.min.js'],
		exists: function() {
			return typeof jQuery !== 'undefined';
		}
	},
	chosen: {
		css: ['lib/chosen/chosen.min.css'],
		js: ['lib/chosen/chosen.jquery.min.js'],
		exists: function() {
			return libs.jquery.exists() && typeof jQuery.fn.chosen !== 'undefined';
		}
	},
	typeahead: {
		js: ['lib/corejs-typeahead/typeahead.bundle.min.js'],
		exists: function() {
			return libs.jquery.exists() && typeof jQuery.fn.typeahead !== 'undefined';
		}
	},
	csc: {
		css: ['css/orghunter.csc.min.css'],
		exists: function() {
			return libs.jquery.exists() && libs.chosen.exists() && libs.typeahead.exists() && typeof jQuery.fn.orghunterCSC !== 'undefined';
		}
	}
};

/**
 * Default settings.
 */
var defaultSettings = {
	apiURL: '',
	searchURL: '{apiURL}/charitysearch?eligible={eligible}&state={state}&city={city}&searchTerm={term}',
	searchAliasURL: '{apiURL}/charitysearchalias?eligible={eligible}&state={state}&city={city}&searchTerm={term}',
	citiesURL: '{apiURL}/cities/{state}',
	searchPlaceholder: 'Search all {location} charities',
	searchAllCityPlaceholder: 'Select a state to filter by city',
	searchStateCityPlaceholder: 'All {state} cities',
	searchAlias: false,
	searchEligible: true,
	stateChosen: true,
	resultsHeight: 'auto',
	resultsLimit: 5,
	submitButton: false,
	onSubmit: null,
	apiData: {},
	size: 'medium',
	width: 'normal',
	onReady: null,
	onCharitySelect: null,
	cscPath: null
};

/**
 * States from United States.
 */
var statesUS = {
	'AL': 'Alabama',
	'AK': 'Alaska',
	'AZ': 'Arizona',
	'AR': 'Arkansas',
	'CA': 'California',
	'CO': 'Colorado',
	'CT': 'Connecticut',
	'DE': 'Delaware',
	'DC': 'District of Columbia',
	'FL': 'Florida',
	'GA': 'Georgia',
	'HI': 'Hawaii',
	'ID': 'Idaho',
	'IL': 'Illinois',
	'IN': 'Indiana',
	'IA': 'Iowa',
	'KS': 'Kansas',
	'KY': 'Kentucky',
	'LA': 'Louisiana',
	'ME': 'Maine',
	'MD': 'Maryland',
	'MA': 'Massachusetts',
	'MI': 'Michigan',
	'MN': 'Minnesota',
	'MS': 'Mississippi',
	'MO': 'Missouri',
	'MT': 'Montana',
	'NE': 'Nebraska',
	'NV': 'Nevada',
	'NH': 'New Hampshire',
	'NJ': 'New Jersey',
	'NM': 'New Mexico',
	'NY': 'New York',
	'NC': 'North Carolina',
	'ND': 'North Dakota',
	'OH': 'Ohio',
	'OK': 'Oklahoma',
	'OR': 'Oregon',
	'PA': 'Pennsylvania',
	'RI': 'Rhode Island',
	'SC': 'South Carolina',
	'SD': 'South Dakota',
	'TN': 'Tennessee',
	'TX': 'Texas',
	'UT': 'Utah',
	'VT': 'Vermont',
	'VA': 'Virginia',
	'WA': 'Washington',
	'WV': 'West Virginia',
	'WI': 'Wisconsin',
	'WY': 'Wyoming'
};

/*
 * States from US Unincomporated Territory.
 */
var statesUT = {
	'AS': 'American Samoa',
	'GU': 'Guam',
	'MP': 'Northern Mariana Islands',
	'PR': 'Puerto Rico',
	'VI': 'U.S. Virgin Islands'
};

/**
 * List of options for state field.
 */
var stateOptions = {'': '- All States -'};
for (var stateUS in statesUS) {
	stateOptions[stateUS] = statesUS[stateUS];
}
stateOptions['-'] = '--------------------';
for (var stateUT in statesUT) {
	stateOptions[stateUT] = statesUT[stateUT];
}

/**
 * Current script directory.
 */
var cwd = null;
var scriptName = /js\/orghunter\.csc(\.min)?\.js(.*)?/;
var getcwd = function() {
	if (cwd) {
		return cwd;
	}
	if (document.currentScript && document.currentScript.src) {
		cwd = document.currentScript.src.replace(scriptName, '');
		return cwd;
	}
	var scripts = document.getElementsByTagName('script');
	for (var i = 0; i < scripts.length; i++) {
		if (scripts[i].src.match(scriptName)) {
			cwd = scripts[i].src.replace(scriptName, '');
			return cwd;
		}
	}
	if (typeof console !== 'undefined' && typeof console.warn !== 'undefined') {
		console.warn('Could not determine orghunter.csc.js path.');
	}
};

/**
 * Add library assets.
 */
var addLibrary = function(library, path) {
	if (!library.exists()) {
		path = path || getcwd();
		path = path.slice(-1) == '/' ? path : path + '/';

		if (typeof library.css !== 'undefined') {
			for (i in library.css) {
				addStyle(path + library.css[i]);
			}
		}
		if (typeof library.js !== 'undefined') {
			for (i in library.js) {
				addScript(path + library.js[i]);
			}
		}
	}
};

/**
 * Add style to main document.
 */
var addStyle = function(path) {
	var style = document.createElement('link');
	style.rel = 'stylesheet';
	style.href = path;
	document.getElementsByTagName('head')[0].appendChild(style);
};

/**
 * Add script to main document.
 */
var addScript = function(path) {
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = path;
	document.getElementsByTagName('head')[0].appendChild(script);
};

/**
 * Check if a condition is met and then call a callback function.
 */
var callWhen = function(callback, condition, wait) {
	var max = 30000;
	var init = 10;
	var step = 2;
	wait = (typeof wait !== 'undefined') ? wait : init;
	if (condition()) {
		callback();
	}
	else if (wait < max) {
		window.setTimeout(function() {
			callWhen(callback, condition, wait * step);
		}, wait);
	}
};

/**
 * Generate a pseudo-random string to use as unique attributes, events, etc.
 */
var randomStr = function() {
	return 'ohcsc-' + ('' + Math.random()).replace(/\D/g, '');
};

/**
 * jQuery plugin function.
 */
var jQueryFn = function(settings) {
	/**
	 * Alias jQuery locally for non-conflict.
	 */
	var $ = jQuery;

	/**
	 * Set user-defined settings.
	 */
	settings = $.extend({}, defaultSettings, settings);

	/**
	 * Unique event names.
	 */
	var evInit = randomStr();
	var evReset = randomStr();
	var evRefresh = randomStr();
	var evFieldInit = randomStr();
	var evFieldReset = randomStr();
	var evFieldRefresh = randomStr();
	var evFieldSet = randomStr();

	/**
	 * Set globally user-defined API data.
	 */
	var apiData = $.extend({}, settings.apiData);

	/**
	 * Set search url depending if alias is or is not being used.
	 */
	var charitySearchURL = settings.searchAlias ? settings.searchAliasURL : settings.searchURL;

	/**
	 * Replace tokens in strings for provided parameters.
	 */
	var parseStr = function(str, params) {
		return str.replace(/{([a-zA-Z0-9]+)}/g, function(m, token) {
			return params[token] || '';
		});
	};

	/**
	 * List of fetched cities to be cached.
	 */
	var cities = {};

	/**
	 * Fetch cities from a given state.
	 */
	var citiesFetch = function(state, callback) {
		if (typeof cities[state] == 'undefined') {
			var urlParams = $.extend({}, settings, {state: state});
			var url = parseStr(settings.citiesURL, urlParams);
			$.getJSON(url, apiData, function(json) {
				cities[state] = new Bloodhound({
					datumTokenizer: function(str) { return [str] },
					queryTokenizer: function(str) { return [str] },
					local: json
				});

				callback();
			});
		}
		else {
			callback();
		}
	};

	/**
	 * State for search values.
	 */
	var searchEligible = settings.searchEligible ? '1' : '0';
	var searchState = '';
	var searchCity = '';
	var searchTerm = '';

	/**
	 * This is used to prevent submitting the form when enter is pressed on typeahead suggestion.
	 */
	var typeaheadSelected = false;

	/**
	 * Full name of selected state.
	 */
	searchStateName = function() {
		if (searchState) {
			return stateOptions[searchState];
		}
	};

	/**
	 * Define main search element and sub fields elements.
	 */
	var $search = this;
	var $state = $('<select id="ohcsc-state" class="ohcsc-state ohcsc-s ohcsc-f"></select>');
	var $city = $('<input id="ohcsc-city" type="text" class="ohcsc-city ohcsc-tf ohcsc-f"/>');
	var $term = $('<input id="ohcsc-term" type="text" class="ohcsc-term ohcsc-tf ohcsc-f"/>');
	var $submit = $('<button id="ohcsc-submit" class="ohcsc-submit">' + settings.submitButton + '</button>');

	/**
	 * Define main search and sub fields wrappers.
	 */
	var fieldSizes = {
		small: 'sm',
		medium: 'md',
		large: 'lg'
	};
	var $searchWrapper = $('<div id="ohcsc-w" class="ohcsc-w ohcsc-w-' + fieldSizes[settings.size] + ' ohcsc-w-width-' + settings.width + '"></div>').appendTo($search);
	var $stateWrapper = $('<div id="ohcsc-state-w" class="ohcsc-f-w"></div>').append($state);
	var $cityWrapper = $('<div id="ohcsc-city-w" class="ohcsc-f-w"></div>').append($city);
	var $termWrapper = $('<div id="ohcsc-term-w" class="ohcsc-f-w"></div>').append($term);

	/**
	 * Define the main search bloodhound query.
	 */
	var searchQuery = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: parseStr(charitySearchURL, {}),
			prepare: function(q, s) {
				var search = {
					eligible: searchEligible,
					state: searchState,
					city: searchCity,
					term: encodeURIComponent(q)
				};
				var params = $.extend({}, settings, search);
				s.url = parseStr(charitySearchURL, params);
				s.data = apiData;
				return s;
			},
			transform: function(response) {
				return response.data;
			}
		}
	});

	/**
	 * Bind event listeners to main element.
	 */
	$search
		.bind(evInit, function() {
			$state.trigger(evFieldInit);
			$city.trigger(evFieldInit);
			$term.trigger(evFieldInit);
			$submit.trigger(evFieldInit);
			$search.trigger(evRefresh);
		})
		.bind(evReset, function() {
			$state.trigger(evFieldReset);
			$city.trigger(evFieldReset);
			$term.trigger(evFieldReset);
		})
		.bind('ohcsc-refresh ' + evRefresh, function() {
			$state.trigger(evFieldRefresh);
			$city.trigger(evFieldRefresh);
			$term.trigger(evFieldRefresh);
			$submit.trigger(evFieldRefresh);
		})
		.bind('ohcsc-reset', function(e, options) {
			if (!options.keepState) {
				$state.trigger(evFieldReset);
			}
			if (!options.keepCity) {
				$city.trigger(evFieldReset);
			}
			if (!options.keepTerm) {
				$term.trigger(evFieldReset);
			}
		});

	/**
	 * Bind event listeners to state element.
	 */
	$state
		.bind(evFieldInit, function() {
			$searchWrapper.append($stateWrapper);
			$.each(stateOptions, function(k, v) {
				$state.append('<option value="' + k +'">' + v + '</option>');
			});
			if (settings.defaultState && stateOptions[settings.defaultState]) {
				$state.trigger(evFieldSet, [settings.defaultState]);
			}
			if (settings.stateChosen) {
				$state.chosen();
			}
			$state.trigger('change');
		})
		.bind(evFieldRefresh, function() {
			if (settings.stateChosen) {
				$state.chosen('destroy');
				$state.chosen();
			}
		})
		.bind(evFieldSet, function(e, value) {
			$state
				.val(value)
				.trigger('change');
		})
		.bind(evFieldRefresh, function() {
			$stateWrapper.removeClass('selected');
			if (searchState) {
				$stateWrapper.addClass('selected');
			}
		})
		.bind('change', function() {
			searchState = $state.val();
		})
		.bind('change', function() {
			if (searchState) {
				window.setTimeout(function() {
					$city.trigger('focus');
				}, 10);
			}
		})
		.bind('change', function() {
			$search.trigger(evReset);
			$search.trigger(evRefresh);
		})
		.bind('change', function() {
			if ($state.val() == '-') {
				$state.trigger(evFieldSet, ['']);
			}
		});

	/**
	 * Bind event listeners to city element.
	 */
	$city
		.bind('keyup change', function() {
			searchCity = $city.val();
			$search.trigger(evRefresh);
		})
		.bind('change', function() {
			if (searchCity) {
				$term.trigger('focus');
			}
		})
		.bind(evFieldInit, function() {
			$searchWrapper.append($cityWrapper);
			if (settings.defaultCity) {
				$city.trigger(evFieldSet, [settings.defaultCity]);
			}
		})
		.bind(evFieldReset, function() {
			$city
				.typeahead('val', '')
				.val('')
				.trigger('change');
		})
		.bind(evFieldSet, function(e, value) {
			$city
				.val(value)
				.trigger('change');
		})
		.bind(evFieldRefresh, function() {
			$cityWrapper.removeClass('selected');
			if (searchCity) {
				$cityWrapper.addClass('selected');
			}
		})
		.bind(evFieldRefresh, function() {
			if (!searchState) {
				$city.attr('disabled', 'disabled');
			}
			else {
				$city.removeAttr('disabled');
			}
		})
		.bind(evFieldRefresh, function() {
			var placeholder = settings.searchAllCityPlaceholder;
			var params = {};

			if (searchState) {
				params.state = searchStateName();
				placeholder = parseStr(settings.searchStateCityPlaceholder, params);
			}

			$city.attr('placeholder', placeholder);
		})
		.bind(evFieldRefresh, function() {
			if (searchState && typeof cities[searchState] == 'undefined') {
				$city.typeahead('destroy');
				citiesFetch(searchState, function(cities) {
					var currentFocus = $city.is(':focus');
					$search.trigger(evRefresh);
					if (currentFocus) {
						$city.trigger('focus');
					}
				});
			}
		})
		.bind(evFieldRefresh, function() {
			if (searchState && typeof cities[searchState] !== 'undefined') {
				if (!$city.is('.tt-input')) {
					$city
						.typeahead({
							hint: true
						},
						{
							name: 'cities',
							source: cities[searchState],
							limit: 1
						});
				}
			}
			else {
				$city.typeahead('destroy');
			}
		})
		.bind('typeahead:autocomplete typeahead:select', function() {
			$city.trigger('change');
		})
		.bind('keydown', function(e) {
			if (e.which == 13) {
				$city.trigger($.Event('keydown', {
					which: 39
				}));
			}
		});

	/**
	 * Bind event listeners to term element.
	 */
	$term
		.bind('keyup change', function() {
			searchTerm = $term.val();
			$search.trigger(evRefresh);
		})
		.bind(evFieldInit, function() {
			$searchWrapper.append($termWrapper);
			$term.val('');
		})
		.bind(evFieldReset, function() {
			$term
				.typeahead('val', '')
				.val('')
				.trigger('change');
		})
		.bind(evFieldRefresh, function() {
			var params = {
				location: 'US'
			};

			if (searchState) {
				params.location = searchStateName();
				if (searchCity) {
					params.location = searchCity + ', ' + searchState;
				}
			}

			$term.attr('placeholder', parseStr(settings.searchPlaceholder, params));
		})
		.bind(evFieldRefresh, function() {
			if (settings.resultsHeight == 'auto') {
				$termWrapper.removeClass('fixed-height');
			}
			else {
				$termWrapper.addClass('fixed-height');
			}
		})
		.typeahead({
			highlight: true
		}, {
			display: function(suggestion) {
				return suggestion.charityName;
			},
			templates: {
				suggestion: function(suggestion) {
					s = '<div><em>' + suggestion.charityName + '</em><small>' + suggestion.city + ', ' + suggestion.state + '</small></div>';
					return s;
				}
			},
			source: searchQuery,
			limit: settings.resultsLimit
		})
		.bind('typeahead:asyncrequest', function() {
			$termWrapper.addClass('load');
		})
		.bind('typeahead:asynccancel typeahead:asyncreceive', function() {
			$termWrapper.removeClass('load');
		})
		.bind('typeahead:select', function(event, suggestion) {
			var data = [suggestion.ein, suggestion];
			typeaheadSelected = true;
			if (typeof settings.onCharitySelect == 'function') {
				settings.onCharitySelect.apply(this, data);
			}
		})
		.bind('typeahead:active', function() {
			if (settings.submitButton && $termWrapper.children('.twitter-typeahead').length && !$submit.parent('.twitter-typeahead').length) {
				$submit.appendTo($termWrapper.children('.twitter-typeahead'));
			}
		})
		.bind('typeahead:active', function() {
			$termWrapper.find('.tt-menu').css('height', settings.resultsHeight);
		});

	/**
	 * Bind event listeners to submit element.
	 */
	if (settings.submitButton) {
		$submit
			.bind(evFieldInit, function() {
				$submit
					.appendTo($termWrapper)
					.hide();
			})
			.bind(evFieldRefresh, function() {
				if (searchState || searchCity || $term.val()) {
					$submit.show();
				}
				else {
					$submit.hide();
				}
			})
			.bind('click', function() {
				var data = [{
					state: searchState,
					city: searchCity,
					term: searchTerm
				}];
				if (typeof settings.onSubmit == 'function') {
					settings.onSubmit.apply(this, data);
				}
			});

		$term
			.bind('keydown', function(e) {
				if (e.which == 13 && !typeaheadSelected) {
					$submit.trigger('click');
				}
			});
	}
	else {
		$searchWrapper.addClass('has-load');
	}

	/**
	 * Trigger init.
	 */
	$search.trigger(evInit);

	if (typeof settings.onReady == 'function') {
		settings.onReady();
	}

	return this;
};

/**
 * Wrap around jQuery orghunterCSC plugin function to load additional dependencies if necessary.
 */
var jQueryFnWrapper = function(settings) {
	var selector = this;
	dependenciesInit(settings);

	callWhen(function() {
		jQueryFn.call(selector, settings);
	}, function() {
		return libs.chosen.exists() && libs.typeahead.exists() && libs.csc.exists();
	});
};

/**
 * Set jQuery orghunterCSC plugin.
 */
var init = function() {
	if (libs.jquery.exists() && !libs.csc.exists()) {
		jQuery.fn.orghunterCSC = jQueryFnWrapper;
	}
};

/**
 * Prepare script that will be called initially.
 *
 * Add jQuery and dependencies if not available and set orghunterCSC plugin.
 */
var dependenciesInit = function(settings) {
	var path = (settings && settings.cscPath) ? settings.cscPath.replace(scriptName, '') : null;

	addLibrary(libs.jquery, path);

	callWhen(function() {
		addLibrary(libs.chosen, path);
		addLibrary(libs.typeahead, path);
		addLibrary(libs.csc, path);

		callWhen(function() {
			init();
		}, function() {
			return libs.chosen.exists() && libs.typeahead.exists();
		});
	}, function() {
		return libs.jquery.exists();
	});
};

/**
 * Translate direct call to orghunterCSC() to jQuery plugin syntax.
 */
w.orghunterCSC = function(selector, settings) {
	dependenciesInit(settings);

	callWhen(function() {
		jQuery(selector).orghunterCSC(settings);
	}, function() {
		return libs.csc.exists();
	});
};

/**
 * Trigger initial script.
 */
init();

})(window);
