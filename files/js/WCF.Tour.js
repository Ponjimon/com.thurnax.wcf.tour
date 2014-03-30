/**
 * JS-API for starting hopscotch tours
 * The positioning code is heavily based on or taken from WCF.Popover.
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
WCF.Tour = {
	/**
	 * list of manual tours
	 * @var	array<integer>
	 */
	manualTours: [],
	
	/**
	 * list of available manual tours
	 * @var	array<integer>
	 */
	availableManualTours: [],
	
	/**
	 * action proxy
	 * @var	WCF.Action.Proxy
	 */
	_proxy: null,
	
	/**
	 * ID of the currently active tour
	 * @var	integer
	 */
	_activeTourID: null,
	
	/**
	 * active tour
	 * @var	array<mixed>
	 */
	_activeTour: null,
	
	/**
	 * tour offset
	 * @var	integer
	 */
	_tourOffset: 10,
	
	/**
	 * minimum margin (all directions) for tour
	 * @var	integer
	 */
	_margin: 20,
	
	/**
	 * Loads a tour by the id.
	 * 
	 * @param	integer	tourID
	 * @param	boolean	forceStop
	 */
	loadTour: function(tourID, forceStop) {
		// a tour is already running
		if (this._activeTourID) {
			if (forceStop) { // stop tour
				hopscotch.endTour();
			} else { // cancel request
				return;
			}
		}
		
		// setup
		if (this._proxy === null) {
			// init proxy
			this._proxy = new WCF.Action.Proxy({
				success: $.proxy(this._success, this),
				failure: $.proxy(this._failure, this),
				showLoadingOverlay: false
			});
			
			// create dom nodes
			this._tour = $('<div class="tour"><div class="tourContainer"><span class="icon icon16 icon-remove jsTooltip pointer" title="'+
				WCF.Language.get('wcf.tour.step.locales.closeTooltip')+'"></span><div class="tourContent" /></div></div>').hide().appendTo(document.body);
			this._tourContent = this._tour.find('.tourContent:eq(0)');
			
			// bind events
			this._tour.find('.icon').click($.proxy(this._close, this));
		}
		
		// send request
		this._proxy.setOption('data', {
			className: 'wcf\\data\\tour\\TourAction',
			actionName: 'loadTour',
			objectIDs: [ tourID ]
		});
		this._proxy.sendRequest();
	},

	/**
	 * Loads a tour by the identifier
	 * 
	 * @param	string	identifier
	 * @param	boolean	force
	 * @param	boolean	forceLoading
	 */
	loadTourByIdentifier: function(identifier, force, forceStop) {
		var $tourID = this.manualTours[identifier];
		if ($tourID !== undefined && (force || WCF.inArray($tourID, this.availableManualTours))) {
			this.loadTour($tourID, forceStop);
		}
	},
	
	/**
	 * Handles AJAX responses.
	 * 
	 * @param	object		data
	 */
	_success: function(data) {
		if (data.actionName == 'loadTour' && this._activeTourID === null) {
			// start tour
			this._activeTourID = data.objectIDs.pop();
			this._activeTour = data.returnValues;
			this._showTourStep(0);
		}
	},
	
	/**
	 * Shows a tour step
	 *  
	 * @param	integer	index
	 * @return	boolean
	 */
	_showTourStep: function(index) {
		var $element = $(this._activeTour[index].target + ':eq(0)');
		if (!$element.length) {
			return false;
		}
		
		// insert content
		this._tourContent.html(this._activeTour[index].template);
		
		// get dimensions
		var $dimensions = this._fixElementDimensions(this._tour, this._tour.show().getDimensions());
		this._tour.hide();
		
		// position tour
		var $orientation = this._getOrientation($element, $dimensions.height, $dimensions.width, this._activeTour[index].orientation.x, this._activeTour[index].orientation.y);
		this._tour.css(this.getCSS($element, $orientation.x, $orientation.y));
		this._tour.removeClass('bottom left right top').addClass($orientation.x).addClass($orientation.y);
		
		// show tour
		this._tour.stop().wcfFadeIn();
		this._tour.children('span').hide();
		return true;
	},
	
	/**
	 * Resolves tour orientation, tries to use default orientation first.
	 * 
	 * @param	jQuery	element
	 * @param	integer	height
	 * @param	integer	width
	 * @param	string	defaultX
	 * @param	string	defaultY
	 * @return	object
	 */
	_getOrientation: function(element, height, width, defaultX, defaultY) {
		// get offsets and dimensions
		var $offsets = element.getOffsets('offset');
		var $elementDimensions = element.getDimensions();
		var $documentDimensions = $(document).getDimensions();
		
		// try default orientation first
		var $orientationX = (defaultX === 'left') ? 'left' : 'right';
		var $orientationY = (defaultY === 'bottom') ? 'bottom' : 'top';
		var $result = this._evaluateOrientation($orientationX, $orientationY, $offsets, $elementDimensions, $documentDimensions, height, width);
		
		if ($result.flawed) {
			// try flipping orientationX
			$orientationX = ($orientationX === 'left') ? 'right' : 'left';
			$result = this._evaluateOrientation($orientationX, $orientationY, $offsets, $elementDimensions, $documentDimensions, height, width);
			
			if ($result.flawed) {
				// try flipping orientationY while maintaing original orientationX
				$orientationX = ($orientationX === 'right') ? 'left' : 'right';
				$orientationY = ($orientationY === 'bottom') ? 'top' : 'bottom';
				$result = this._evaluateOrientation($orientationX, $orientationY, $offsets, $elementDimensions, $documentDimensions, height, width);
				
				if ($result.flawed) {
					// try flipping both orientationX and orientationY compared to default values
					$orientationX = ($orientationX === 'left') ? 'right' : 'left';
					$result = this._evaluateOrientation($orientationX, $orientationY, $offsets, $elementDimensions, $documentDimensions, height, width);
					
					if ($result.flawed) {
						// fuck this shit, we will use the default orientation
						$orientationX = (defaultX === 'left') ? 'left' : 'right';
						$orientationY = (defaultY === 'bottom') ? 'bottom' : 'top';
					}
				}
			}
		}
		
		return {
			x: $orientationX,
			y: $orientationY
		};
	},
	
	/**
	 * Evaluates if tour fits into given orientation.
	 * 
	 * @param	string		orientationX
	 * @param	string		orientationY
	 * @param	object		offsets
	 * @param	object		elementDimensions
	 * @param	object		documentDimensions
	 * @param	integer		height
	 * @param	integer		width
	 * @return	object
	 */
	_evaluateOrientation: function(orientationX, orientationY, offsets, elementDimensions, documentDimensions, height, width) {
		if (orientationX === 'left') {
			var $widthDifference = offsets.left - width;
		} else {
			var $widthDifference = documentDimensions.width - (offsets.left + width);
		}
		
		if (orientationY === 'bottom') {
			var $heightDifference = documentDimensions.height - (offsets.top + elementDimensions.height + this._tourOffset + height);
		} else {
			var $heightDifference = offsets.top - (height - this._tourOffset);
		}
		
		// check if both difference are above margin
		var $flawed = false;
		if ($heightDifference < this._margin || $widthDifference < this._margin) {
			$flawed = true;
		}
		
		return {
			flawed: $flawed,
			x: $widthDifference,
			y: $heightDifference
		};
	},
	
	/**
	 * Computes CSS for tour.
	 * 
	 * @param	jQuery	element
	 * @param	string	orientationX
	 * @param	string	orientationY
	 * @return	object
	 */
	getCSS: function(element, orientationX, orientationY) {
		var $offsets = element.getOffsets('offset');
		var $elementDimensions = this._fixElementDimensions(element, element.getDimensions());
		
		if (orientationX === 'left') {
			var $left = $offsets.left + $elementDimensions.width - this._tour.outerWidth() - this._tourOffset;
		} else if (orientationX === 'right') {
			var $left = $offsets.left + this._tourOffset;
		}
		
		if (orientationY === 'top') {
			var $top = $offsets.top - this._tour.outerHeight() - this._tourOffset;
		} else if (orientationY === 'bottom') {
			var $top = $offsets.top + element.outerHeight() + this._tourOffset;
		}
		
		return {
			left: $left,
			top: $top
		};
	},
	
	/**
	 * Tries to fix dimensions if element is partially hidden (overflow: hidden).
	 *  
	 * @param	jQuery		element
	 * @param	object		dimensions
	 * @return	dimensions
	 */
	_fixElementDimensions: function(element, dimensions) {
		var $parentDimensions = element.parent().getDimensions();
		
		if ($parentDimensions.height < dimensions.height) {
			dimensions.height = $parentDimensions.height;
		}
		
		if ($parentDimensions.width < dimensions.width) {
			dimensions.width = $parentDimensions.width;
		}
		
		return dimensions;
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
		// remove action tour from available tours
		if (WCF.inArray(this._activeTourID, this.availableManualTours)) {
			this.availableManualTours.splice(this.availableManualTours.indexOf(this._activeTourID), 1);
		}
		
		// send request
		this._activeTourID = null;
		this._proxy.setOption('data', {
			className: 'wcf\\data\\tour\\TourAction',
			actionName: 'endTour'
		});
		this._proxy.sendRequest();
	}
};
