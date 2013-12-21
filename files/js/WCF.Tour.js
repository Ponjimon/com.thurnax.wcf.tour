/**
 * JS-API for starting hopscotch tours
 * 
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @package	com.thurnax.wcf.tour
 */
WCF.Tour = {
	/**
	 * dependencies to load
	 * @var	array<string>
	 */
	_dependencies: [ WCF_PATH + 'js/3rdParty/tour/hopscotch-0.1.2.min.js', WCF_PATH + 'js/3rdParty/tour/hopscotch-0.1.2.min.css' ],
	
	/**
	 * list of all active tours
	 * @var	array<string>
	 */
	activeTours: [],
	
	/**
	 * ID of the currently active tour
	 * @var	integer
	 */
	_activeTourID: null,
	
	/**
	 * Starts the tours
	 */
	startNextTour: function() {
		if (this.activeTours.length) {
			this.loadTour(this.activeTours.shift());
		}
	},
	
	/**
	 * Loads a tour
	 * 
	 * @param	string	tourName
	 */
	loadTour: function(tourName) {
		// setup environment
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
		
		// load tour
		this._proxy.setOption('data', {
			className: 'wcf\\data\\tour\\TourAction',
			actionName: 'loadTour',
			parameters: {
				tourName: tourName
			}
		});
		this._proxy.sendRequest();
	},
	
	/**
	 * Initializes hopscotch
	 */
	_initHopscotch: function() {
		// register helpers
		hopscotch.registerHelper('redirect', function(url) {
			location.href = url;
		});
		
		WCF.System.Dependency.Manager.invoke('hopscotch');
	},
	
	/**
	 * Handles AJAX responses.
	 * 
	 * @param	object		data
	 */
	_success: function(data) {
		switch (data.actionName) {
			case 'loadTour':
				if (this._activeTourID === null) {
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
						steps: data.returnValues.steps,
						onEnd: $.proxy(this._end, this),
						onClose: $.proxy(this._end, this),
						onError: $.proxy(this._error, this)
					};
					
					// start tour after hopscotch is loaded
					WCF.System.Dependency.Manager.register('hopscotch', function () {
						hopscotch.startTour($tour);
					});
				} else {
					this.activeTours.push(data.actionName.tourName);
				}
				break;
			case 'endTour':
				this._activeTourID = undefined;
				this.startNextTour();
				break;
		}
	},
	
	/**
	 * Handles AJAX errors.
	 *
	 * @param	object		jqXHR
	 * @param	string		textStatus
	 * @param	string		errorThrown
	 */
	_failure: function(jqXHR, textStatus, errorThrown) {
		return false; // ignore errors
	},
	
	/**
	 * Invoked when the tour ends or the user closes the tour.
	 */
	_end: function() {
		this._proxy.setOption('data', {
			className: 'wcf\\data\\tour\\TourAction',
			actionName: 'endTour',
			objectIDs: [ this._activeTourID ]
		});
		
		this._activeTourID = null;
		this._proxy.sendRequest();
	},
	
	/**
	 * Invoked when the specified target element doesn't exist on the page.
	 * @todo Should the error be logged?
	 */
	_error: function() {
		hopscotch.nextStep();
	}
};
