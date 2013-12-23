{include file='header' pageTitle='wcf.acp.tour.'|concat:$action}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.tour.{$action}{/lang}</h1>
	
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			/**
			 * Handler for the tour add and edit form
			 * 
			 * @author	Magnus Kühn
			 * @copyright	2013 Thurnax.com
			 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
			 * @package	com.thurnax.wcf.tour
			 * @todo Create file WCF.ACP.Tour?
			 */
			WCF.ACP.TourAdd = Class.extend({
				/**
				 * Initializes the form
				 */
				init: function() {
					this._radioChanged();
					
					// bind events
					$('input[name="tourTrigger"]').change($.proxy(this._radioChanged, this));
					$('#tourName').keyup($.proxy(this._tourNameChanged, this));
				},
				
				/**
				 * Event listener for the tour trigger radios
				 *
				 * @param	jQuery.Event	event
				 */
				_radioChanged: function(event) {
					$('#classNameContainer, #tourNameContainer, #manualCodeContainer').addClass('disabled');
					switch ($('input[name="tourTrigger"]:checked').val()) {
						case 'firstSite':
							$('#className, #tourName').disable();
							break;
						case 'specificSite':
							$('#classNameContainer').removeClass('disabled');
							$('#className').enable().focus();
							break;
						case 'manual':
							$('#tourNameContainer, #manualCodeContainer').removeClass('disabled');
							$('#tourName').enable().focus();
					}
				},
				
				/**
				 * Event listener for the tour name input
				 */
				_tourNameChanged: function() {
					$('#manualCode').val("WCF.Tour.loadTour('"+$('#tourName').val()+"');");
				}
			});
			
			new WCF.ACP.TourAdd();
		});
		//]]>
	</script>
</header>

{include file='formError'}
{if $success|isset}<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='TourList'}{/link}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}wcf.acp.menu.link.user.tour.list{/lang}</span></a></li>
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='TourAdd'}{/link}{else}{link controller='TourEdit' object=$tour}{/link}{/if}">
	<div class="container containerPadding sortableListContainer marginTop">
		<fieldset>
			<legend>{lang}wcf.acp.tour.data{/lang}</legend>
			
			<dl{if $errorField == 'visibleName'} class="formError"{/if}>
				<dt><label for="visibleName">{lang}wcf.acp.tour.visibleName{/lang}</label></dt>
				<dd>
					<input type="text" id="visibleName" name="visibleName" value="{$i18nPlainValues['visibleName']}" required="required" class="long" />
					{if $errorField == 'visibleName'}<small class="innerError">{lang}wcf.global.form.error.{@$errorType}{/lang}</small>{/if}
				</dd>
			</dl>
			{include file='multipleLanguageInputJavascript' elementIdentifier='visibleName' forceSelection=false}
			
			{event name='dataFields'}
		</fieldset>
		
		<fieldset>
			<legend>{lang}wcf.acp.tour.tourTrigger{/lang}</legend>
			
			<dl>
				<dt class="reversed"><label for="triggerFirstSite">{lang}wcf.acp.tour.tourTrigger.firstSite{/lang}</label></dt>
				<dd>
					<input type="radio" name="tourTrigger" id="triggerFirstSite" value="firstSite"{if $tourTrigger == 'firstSite'} checked="checked"{/if} />
					<small>{lang}wcf.acp.tour.tourTrigger.firstSite.description{/lang}</small>
				</dd>
			</dl>
			
			<dl>
				<dt class="reversed"><label for="triggerSpecificSite">{lang}wcf.acp.tour.tourTrigger.specificSite{/lang}</label></dt>
				<dd>
					<input type="radio" name="tourTrigger" id="triggerSpecificSite" value="specificSite"{if $tourTrigger == 'specificSite'} checked="checked"{/if} />
					<small>{lang}wcf.acp.tour.tourTrigger.specificSite.description{/lang}</small>
				</dd>
			</dl>
			<dl id="classNameContainer" class="disabled{if $errorField == 'className'} formError{/if}">
				<dt><label for="className">{lang}wcf.acp.tour.className{/lang}</label></dt>
				<dd>
					<input type="text" id="className" name="className" value="{$className}" required="required" disabled="disabled" pattern="^\\?([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\)*[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$" class="long" />
					<small>{lang}wcf.acp.tour.className.description{/lang}</small>
					{if $errorField == 'className'}<small class="innerError">{if $errorType == 'invalid'}{lang}wcf.acp.tour.className.error.invalid{/lang}{else}{lang}wcf.global.form.error.empty{/lang}{/if}</small>{/if}
				</dd>
			</dl>
			
			<dl>
				<dt class="reversed"><label for="triggerManual">{lang}wcf.acp.tour.tourTrigger.manual{/lang}</label></dt>
				<dd>
					<input type="radio" name="tourTrigger" id="triggerManual" value="manual"{if $tourTrigger == 'manual'} checked="checked"{/if} />
					<small>{lang}wcf.acp.tour.tourTrigger.manual.description{/lang}</small>
				</dd>
			</dl>
			<dl id="tourNameContainer" class="disabled{if $errorField == 'tourName'} formError{/if}">
				<dt><label for="tourName">{lang}wcf.acp.tour.tourName{/lang}</label></dt>
				<dd>
					<input type="text" id="tourName" name="tourName" value="{$tourName}" required="required" disabled="disabled" class="long" />
					{if $errorField == 'tourName'}<small class="innerError">{if $errorType == 'notUnique'}{lang}wcf.acp.tour.tourName.error.notUnique{/lang}{else}{lang}wcf.global.form.error.empty{/lang}{/if}</small>{/if}
					<small>{lang}wcf.acp.tour.tourName.description{/lang}</small>
				</dd>
			</dl>
			<dl id="manualCodeContainer" class="disabled">
				<dt><label for="manualCode">{lang}wcf.acp.tour.manualCode{/lang}</label></dt>
				<dd><input type="text" value="WCF.Tour.loadTour('{$tourName}');" id="manualCode" disabled="disabled" class="long" /></dd>
			</dl>
			
			{event name='triggerFields'}
		</fieldset>
		
		{event name='fieldsets'}
	</div>

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}
