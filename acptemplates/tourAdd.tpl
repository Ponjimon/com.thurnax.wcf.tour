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
			
			<dl{if $errorField == 'tourName'} class="formError"{/if}>
				<dt><label for="tourName">{lang}wcf.acp.tour.tourName{/lang}</label></dt>
				<dd>
					<input type="text" id="tourName" name="tourName" value="{$tourName}" autofocus="autofocus" required="required" class="long" />
					{if $errorField == 'tourName'}<small class="innerError">{if $errorType == 'notUnique'}{lang}wcf.acp.tour.tourName.error.notUnique{/lang}{else}{lang}wcf.global.form.error.{@$errorType}{/lang}{/if}</small>{/if}
					<small>{lang}wcf.acp.tour.tourName.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorField == 'description'} class="formError"{/if}>
				<dt><label for="description">{lang}wcf.acp.tour.description{/lang}</label></dt>
				<dd>
					<input type="text" id="description" name="description" value="{$i18nPlainValues['description']}" required="required" class="long" />
					{if $errorField == 'description'}<small class="innerError">{lang}wcf.global.form.error.{@$errorType}{/lang}</small>{/if}
				</dd>
			</dl>
			{include file='multipleLanguageInputJavascript' elementIdentifier='description' forceSelection=false}
			
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
