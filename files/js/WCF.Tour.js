/**
 * JS-API for starting hopscotch tours
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
WCF.Tour = {
	/**
	 * list of available tours
	 * @var	array<string>
	 */
	availableTours: [],
	
	/**
	 * dependencies to load
	 * @var	array<string>
	 */
	_dependencies: [ WCF_PATH + 'js/3rdParty/hopscotch/hopscotch-0.1.2.min.js', WCF_PATH + 'js/3rdParty/hopscotch/hopscotch-0.1.2.min.css' ],
	
	/**
	 * ID of the currently active tour
	 * @var	integer
	 */
	_activeTourID: null,
	
	/**
	 * Loads a tour.
	 * 
	 * @param	string	tourName
	 */
	loadTour: function(tourName) {
		if (this._activeTourID || !WCF.inArray(tourName, this.availableTours)) { // tour is already taken
			return;
		}
		
		// load tour
		this._setup();
		this._proxy.setOption('data', {
			className: 'wcf\\data\\tour\\TourAction',
			actionName: 'loadTourByName',
			parameters: {
				tourName: tourName
			}
		});
		this._proxy.sendRequest();
	},

	/**
	 * Loads a tour by the id. Do *not* use this method.
	 * 
	 * @param	integer	tourID
	 */
	loadTourByID: function(tourID) {
		this._setup();
		this._proxy.setOption('data', {
			className: 'wcf\\data\\tour\\TourAction',
			actionName: 'loadTour',
			objectIDs: [ tourID ]
		});
		this._proxy.sendRequest();
	},

	/**
	 * Sets up the environment.
	 */
	_setup: function() {
		if (this._dependencies !== null) {
			// init proxy
			this._proxy = new WCF.Action.Proxy({
				success: $.proxy(this._success, this),
				failure: $.proxy(this._failure, this),
				showLoadingOverlay: false
			});

			// load hopscotch
			head.load(this._dependencies, $.proxy(this._initHopscotch, this));
			this._dependencies = null;
		}
	},
	
	/**
	 * Callback after loading hopscotch.
	 */
	_initHopscotch: function() {
		// register helpers
		hopscotch.registerHelper('redirect_forward', function(url) { location.href = url; });
		hopscotch.registerHelper('redirect_back', function() { history.back(); });
		hopscotch.registerHelper('custom_callback', function(callback) { eval(callback); });
		
		WCF.System.Dependency.Manager.invoke('hopscotch');
	},
	
	/**
	 * Handles AJAX responses.
	 * 
	 * @param	object		data
	 */
	_success: function(data) {
		if (data.actionName == 'loadTour' && this._activeTourID === null) {
			this._activeTourID = data.objectIDs.pop();
			var $tour = {
				id: 'com.thurnax.wcf.tour' + this._activeTourID,
				i18n: {
					nextBtn: WCF.Language.get('wcf.tour.step.locales.nextBtn'),
					prevBtn: WCF.Language.get('wcf.tour.step.locales.prevBtn'),
					doneBtn: WCF.Language.get('wcf.tour.step.locales.doneBtn'),
					skipBtn: WCF.Language.get('wcf.tour.step.locales.skipBtn'),
					closeTooltip: WCF.Language.get('wcf.tour.step.locales.closeTooltip')
				},
				steps: this._fixSteps(data.returnValues),
				onEnd: $.proxy(this._end, this),
				onClose: $.proxy(this._end, this),
				onError: $.proxy(this._error, this)
			};
			
			// start tour after hopscotch is loaded
			WCF.System.Dependency.Manager.register('hopscotch', function () {
				hopscotch.startTour($tour);
			});
		}
	},
	
	/**
	 * Fixes the step array.
	 * Callbacks for onCTA must be converted into a javascript function.
	 * 
	 * @param	array<object>	steps
	 * @return	array<object>
	 */
	_fixSteps: function(steps) {
		for (var i in steps) {
			if (steps[i].onCTA !== undefined) {
				steps[i].onCTA = new Function(steps[i].onCTA);
			}
		}
		
		return steps;
	},
	
	/**
	 * Handles AJAX errors. Ignores errors when not in debug mode (stacktrace is not sent)
	 * 
	 * @param	object	data
	 * @param	object	jqXHR
	 * @param	string	textStatus
	 * @param	string	errorThrown
	 */
	_failure: function(data, jqXHR, textStatus, errorThrown) {
		return (data && data.stacktrace ? true : false);
	},
	
	/**
	 * Invoked when the tour ends or the user closes the tour.
	 */
	_end: function() {
		this._activeTourID = null;
		this._proxy.setOption('data', {
			className: 'wcf\\data\\tour\\TourAction',
			actionName: 'endTour'
		});
		this._proxy.sendRequest();
	},

	/**
	 * Invoked when the specified target element doesn't exist on the page.
	 */
	_error: function() {
		console.log('[WCF.Tour]: An error occurred while showing the tour with ID '+this._activeTourID+'.');
		
		// wait for hopscotch to end the tour
		setTimeout($.proxy(function() {
			if (hopscotch.getCurrStepNum() === 0) { // this was the last step
				this._end();
			}
		}, this), 100);
	}
};
