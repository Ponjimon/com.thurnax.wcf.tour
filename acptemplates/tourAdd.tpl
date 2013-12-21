{include file='header' pageTitle='wcf.acp.tour.'|concat:$action}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.tour.{$action}{/lang}</h1>
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
			
			<dl{if $errorField == 'tourName'} class="formError"{/if}>
				<dt><label for="tourName">{lang}wcf.acp.tour.tourName{/lang}</label></dt>
				<dd>
					<input type="text" id="tourName" name="tourName" value="{$tourName}" required="required" class="long" />
					{if $errorField == 'tourName'}<small class="innerError">{if $errorType == 'notUnique'}{lang}wcf.acp.tour.tourName.error.notUnique{/lang}{else}{lang}wcf.global.form.error.{@$errorType}{/lang}{/if}</small>{/if}
					<small>{lang}wcf.acp.tour.tourName.description{/lang}</small>
				</dd>
			</dl>
			
			<dl>
				<dt class="reversed"><label for="showPrevButton">{lang}wcf.acp.tour.showPrevButton{/lang}</label></dt>
				<dd><input type="checkbox" id="showPrevButton" name="showPrevButton"{if $showPrevButton} checked="checked"{/if} /></dd>
			</dl>
			
			{event name='dataFields'}
		</fieldset>
		
		{event name='fieldsets'}
	</div>

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}
