{include file='header' pageTitle='wcf.acp.tour.point.'|concat:$action}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.tour.point.{$action}{/lang}</h1>
</header>

{include file='formError'}
{if $success|isset}<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='TourPointList'}{/link}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}wcf.acp.menu.link.tour.point.list{/lang}</span></a></li>
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='TourPointAdd'}{/link}{else}{link controller='TourPointEdit' id=$tourPointID}{/link}{/if}">
	<div class="container containerPadding sortableListContainer marginTop">
		<fieldset>
			<legend>{lang}wcf.acp.tour.point.data{/lang}</legend>
			
			<dl{if $errorField == 'step'} class="formError"{/if}>
				<dt><label for="step">{lang}wcf.acp.tour.point.step{/lang}</label></dt>
				<dd>
					<input type="number" id="step" name="step" value="{$step}" min="0" max="8388607" required="required" class="tiny" />
					{if $errorField == 'step'}<small class="innerError">{if $errorType == 'notUnique'}{lang}wcf.acp.tour.point.step.notUnique{/lang}{else}{lang}wcf.global.form.error.{@$errorType}{/lang}{/if}</small>{/if}
					<small>{lang}wcf.acp.tour.point.step.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorField == 'elementName'} class="formError"{/if}>
				<dt><label for="elementName">{lang}wcf.acp.tour.point.elementName{/lang}</label></dt>
				<dd>
					<input type="text" id="elementName" name="elementName" value="{$elementName}" autofocus="autofocus" required="required" class="long" />
					{if $errorField == 'elementName'}<small class="innerError">{lang}wcf.global.form.error.empty{/lang}</small>{/if}
				</dd>
			</dl>
			
			<dl{if $errorField == 'position'} class="formError"{/if}>
				<dt><label for="position">{lang}wcf.acp.tour.point.position{/lang}</label></dt>
				<dd>
					<select id="position" name="position">
						{foreach from=$validPositions item=validPosition}
							<option value="{$validPosition}"{if $position == $validPosition} selected="selected"{/if}>{lang}wcf.acp.tour.point.position.{$validPosition}{/lang}</option>
						{/foreach}
					</select>
					{if $errorField == 'position'}<small class="innerError">{lang}wcf.global.form.error.empty{/lang}</small>{/if}
				</dd>
			</dl>
			
			<dl{if $errorField == 'pointText'} class="formError"{/if}>
				<dt><label for="pointText">{lang}wcf.acp.tour.point.pointText{/lang}</label></dt>
				<dd>
					<textarea id="pointText" name="pointText" required="required" cols="40" rows="10">{$i18nPlainValues['pointText']}</textarea>
					{if $errorField == 'pointText'}<small class="innerError">{lang}wcf.global.form.error.{@$errorType}{/lang}</small>{/if}
				</dd>
			</dl>
			{include file='multipleLanguageInputJavascript' elementIdentifier='pointText' forceSelection=false}
			
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
