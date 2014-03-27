{include file='header' pageTitle='wcf.acp.tour.'|concat:$action}

<script data-relocate="true" src="{@$__wcf->getPath('wcf')}acp/js/WCF.ACP.Tour{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		new WCF.ACP.Tour.TourAdd();
		{if $action == 'edit'}
			new WCF.ACP.Tour.RestartTour();
		{/if}
	});
	//]]>
</script>

{include file='aclPermissions'}
{if $action == 'add'}
	{include file='aclPermissionJavaScript' containerID='groupPermissions'}
{else}
	{include file='aclPermissionJavaScript' containerID='groupPermissions' objectID=$tourID}
{/if}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.tour.{$action}{/lang}</h1>
</header>

{include file='formError'}
{if $success|isset}<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			{if $action == 'edit'}
				{if $identifier}
					<li><a class="button" href="{link controller=TourExportImport object=$tour}{/link}"><span class="icon icon16 icon-download-alt"></span> <span>{lang}wcf.acp.tour.export.this{/lang}</span></a></li>
				{/if}
				<li class="button jsTourRestart" data-object-id="{$tour->tourID}"><span class="icon icon16 icon-play"></span> <span>{lang}wcf.acp.tour.restartTour{/lang}</span></li>
				<li><a href="{link controller='TourStepList' object=$tour}{/link}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}wcf.acp.menu.link.user.tour.step.list{/lang}</span></a></li>
			{/if}
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

			<dl id="groupPermissions">
				<dt>{lang}wcf.acl.permissions{/lang}</dt>
				<dd></dd>
			</dl>

			<dl{if $errorField == 'identifier'} class="formError"{/if}>
				<dt><label for="identifier">{lang}wcf.acp.tour.identifier{/lang}</label></dt>
				<dd>
					<input type="text" id="identifier" name="identifier" value="{$identifier}" class="long" />
					{if $errorField == 'identifier'}<small class="innerError">{if $errorType == 'empty'}{lang}wcf.global.form.error.empty{/lang}{else}{lang}wcf.acp.tour.identifier.error.{@$errorType}{/lang}{/if}</small>{/if}
					<small>{lang}wcf.acp.tour.identifier.description{/lang}</small>
				</dd>
			</dl>
			
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
			<dl id="manualCodeContainer" class="disabled">
				<dt><label for="manualCode">{lang}wcf.acp.tour.manualCode{/lang}</label></dt>
				<dd><input type="text" value="WCF.Tour.loadTour('{$identifier}');" id="manualCode" disabled="disabled" class="long" /></dd>
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
