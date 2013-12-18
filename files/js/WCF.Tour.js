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
	_dependencies: false,
	
	init: function(basePath) {
		this._dependencies = [ basePath + 'js/3rdParty/tour/hopscotch-0.1.2.min.js', basePath + 'js/3rdParty/tour/hopscotch-0.1.2.min.css'];
	},
	
	/**
	 * Loads a tour
	 * 
	 * @param	string	tourName
	 */
	loadTour: function(tourName) {
		if (this._dependencies === false) { // tours are disabled
			return;
		} else if (this._dependencies !== null) { // load dependencies
			this._loadDependencies();
		}
		
		// load tour
		this._proxy.setOption('data', {
			className: 'wcf\\data\\tour\\TourAction',
			actionName: 'loadSteps',
			parameters: {
				tourName: tourName
			}
		});
		this._proxy.sendRequest();
	},

	/**
	 * Loads the dependencies
	 */
	_loadDependencies: function() {
		// init proxy
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});
		
		// load hopscotch
		head.load(this._dependencies, $.proxy(this._initHopscotch, this));
		this._dependencies = null;
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
		WCF.System.Dependency.Manager.register('hopscotch', function() {
			hopscotch.startTour({
				id: data.objectIDs.pop(),
				i18n: {
					nextBtn: WCF.Language.get('wcf.tour.step.locales.nextBtn'),
					prevBtn: WCF.Language.get('wcf.tour.step.locales.prevBtn'),
					doneBtn: WCF.Language.get('wcf.tour.step.locales.doneBtn'),
					skipBtn: WCF.Language.get('wcf.tour.step.locales.skipBtn'),
					closeTooltip: WCF.Language.get('wcf.tour.step.locales.closeTooltip')
				},
				steps: data.returnValues
			});
		});
	}
};
