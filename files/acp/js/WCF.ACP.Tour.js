/**
 * ACP tour related classes.
 *
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
WCF.ACP.Tour = { };

/**
 * Handles the clipboard action 'move'.
 * 
 * @param	string		objectType
 */
WCF.ACP.Tour.ClipboardMove = Class.extend({
	/**
	 * object type used in the clipboard
	 * @var	string
	 */
	_objectType: undefined,
	
	/**
	 * Initializes the clipboard handler
	 * 
	 * @param	string		objectType
	 */
	init: function(objectType) {
		this._objectType = objectType;
		
		// bind listener
		$('.jsClipboardEditor').each($.proxy(function(index, container) {
			var $container = $(container);
			var $types = eval($container.data('types'));
			if (WCF.inArray(this._objectType, $types)) {
				$container.on('clipboardAction', $.proxy(this._execute, this));
				return false;
			}
		}, this));
	},
	
	/**
	 * Handles clipboard actions.
	 * 
	 * @param	object		event
	 * @param	string		type
	 * @param	string		actionName
	 * @param	object		parameters
	 */
	_execute: function(event, type, actionName, parameters) {
		if (actionName == this._objectType + '.move') {
			if (this._didInit === undefined) {
				$('#tourStepMoveDialog').wcfDialog({
					title: $('#tourStepMove').text()
				});
				
				// bind events
				$('#tourStepMove').click($.proxy(this._move, this));
				$('#tourStepMoveCancel').click(function() {
					$('#tourStepMoveDialog').wcfDialog('close');
				});
			} else {
				$('#tourStepMoveDialog').wcfDialog('show');
			}
		}
	},
	
	/**
	 * Moves the marked tour steps to another tour
	 */
	_move: function() {
		WCF.LoadingOverlayHandler.show();
		new WCF.Action.Proxy({
			data: {
				className: 'wcf\\data\\tour\\TourAction',
				actionName: 'move',
				objectIDs: [ $('#tourStepMoveTarget').val() ]
			},
			autoSend: true,
			success: function(data) {
				location.href = data.returnValues;
			}
		});
	}
});

/**
 * Implementation for AJAXProxy-based toggle actions.
 * Handles the clipboard actions 'enable' and 'disable'.
 * 
 * @see	https://github.com/WoltLab/WCF/pull/1615
 * @param	string		className
 * @param	jQuery		containerList
 * @param	string		buttonSelector
 * @param	string		objectType
 */
WCF.ACP.Tour.ClipboardToggle = WCF.Action.Toggle.extend({
	/**
	 * object type used in the clipboard
	 * @var	string
	 */
	_objectType: undefined,
	
	/**
	 * @see		WCF.Action.Toggle.init()
	 * @param	string		objectType
	 */
	init: function(className, containerSelector, buttonSelector, objectType) {
		this._super(className, containerSelector, buttonSelector);
		this._objectType = objectType;
		
		// bind listener
		$('.jsClipboardEditor').each($.proxy(function(index, container) {
			var $container = $(container);
			var $types = eval($container.data('types'));
			if (WCF.inArray(this._objectType, $types)) {
				$container.on('clipboardAction', $.proxy(this._execute, this));
				return false;
			}
		}, this));
	},
	
	/**
	 * Handles clipboard actions.
	 * 
	 * @param	object		event
	 * @param	string		type
	 * @param	string		actionName
	 * @param	object		parameters
	 */
	_execute: function(event, type, actionName, parameters) {
		if (actionName == this._objectType + '.enable' || actionName == this._objectType + '.disable') {
			this.proxy.setOption('data', {
				actionName: 'toggle',
				className: this._className,
				interfaceName: 'wcf\\data\\IToggleAction',
				objectIDs: parameters.objectIDs
			});
			this.proxy.sendRequest();
		}
	},
	
	/**
	 * @see	WCF.Action.Toggle._success()
	 */
	_success: function(data, textStatus, jqXHR) {
		this._super(data, textStatus, jqXHR);
		WCF.Clipboard.reload();
	}
});

/**
 * Handler for the tour add and edit form
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
WCF.ACP.Tour.TourAdd = Class.extend({
	/**
	 * Initializes the form
	 */
	init: function() {
		this._radioChanged();
		
		// bind events
		$('input[name="tourTrigger"]').change($.proxy(this._radioChanged, this));
		$('#identifier').keyup($.proxy(this._identifierChanged, this));
	},
	
	/**
	 * Change listener on the tour trigger radios
	 * 
	 * @param	jQuery.Event	event
	 */
	_radioChanged: function(event) {
		if ($('input[name="tourTrigger"]:checked').val() == 'specificSite') {
			$('#classNameContainer').removeClass('disabled');
			$('#className').enable().focus();
		} else {
			$('#classNameContainer, #manualCodeContainer').addClass('disabled');
			$('#className').disable();
		}
	},
	
	/**
	 * Change listener on the identifier
	 */
	_identifierChanged: function() {
		$('#manualCode').val("WCF.Tour.loadTourByIdentifier('"+$('#identifier').val()+"');");
	}
});

/**
 * Implementation for restarting a tour AJAXProxy-based
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
WCF.ACP.Tour.RestartTour = Class.extend({
	/**
	 * Initializes the action
	 */
	init: function() {
		$('.jsTourRestart').click($.proxy(this._click, this));
	},
	
	/**
	 * Handel button clicks
	 * 
	 * @param	jQuery.Event	event
	 */
	_click: function(event) {
		new WCF.Action.Proxy({
			data: {
				className: 'wcf\\data\\tour\\TourAction',
				actionName: 'restartTour',
				objectIDs: [ $(event.currentTarget).data('objectID') ]
			},
			autoSend: true,
			success: $.proxy(this._success, this)
		});
	},
	
	/**
	 * Handles successful AJAX requests.
	 */
	_success: function() {
		new WCF.System.Notification().show();
	}
});
