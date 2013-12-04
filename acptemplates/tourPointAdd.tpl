{include file='header' pageTitle='wcf.acp.tour.tourPoint.add'|concat:$action}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.tour.tourPoint.{$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='TourPointList'}{/link}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}wcf.acp.menu.link.tour.tourPoint.list{/lang}</span></a></li>
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='TourPointAdd'}{/link}{else}{link controller='TourPointEdit' id=$tourPointID}{/link}{/if}">
	<div class="container containerPadding sortableListContainer marginTop">
		<fieldset>
			<legend>{lang}wcf.tour.tourPoint.data{/lang}</legend>
			
			<dl{if $errorField == 'step'} class="formError"{/if}>
				<dt><label for="step">{lang}wcf.tour.tourPoint.step{/lang}</label></dt>
				<dd>
					<input type="number" id="step" name="step" value="{$step}" min="0" max="8388607" required="required" class="tiny" />
					{if $errorField == 'step'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{elseif $errorType == 'greaterThan'}
								{lang greaterThan=0}wcf.global.form.error.greaterThan{/lang}
							{elseif $errorType == 'lessThan'}
								{lang lessThan=8388607}wcf.global.form.error.lessThan{/lang}
							{else}
								{lang}wcf.tour.tourPoint.step.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
					<small>{lang}wcf.tour.tourPoint.step.description{/lang}</small>
				</dd>
			</dl>
			<dl{if $errorField == 'elementName'} class="formError"{/if}>
				<dt><label for="elementName">{lang}wcf.tour.tourPoint.elementName{/lang}</label></dt>
				<dd>
					<input type="text" id="elementName" name="elementName" value="{$i18nPlainValues['elementName']}" autofocus="autofocus" required="required" class="long" />
					{if $errorField == 'elementName'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.tour.tourPoint.title.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			{include file='multipleLanguageInputJavascript' elementIdentifier='elementName' forceSelection=false}
			
			<dl{if $errorField == 'pointText'} class="formError"{/if}>
				<dt><label for="pointText">{lang}wcf.tour.tourPoint.pointText{/lang}</label></dt>
				<dd>
					<input type="text" id="pointText" name="pointText" value="{$i18nPlainValues['pointText']}" autofocus="autofocus" required="required" class="long" />
					{if $errorField == 'pointText'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.tour.tourPoint.title.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			{include file='multipleLanguageInputJavascript' elementIdentifier='pointText' forceSelection=false}

			<dl{if $errorField == 'position'} class="formError"{/if}>
				<dt><label for="position">{lang}wcf.tour.tourPoint.position{/lang}</label></dt>
				<dd>
					<input type="text" id="position" name="position" value="{$i18nPlainValues['position']}" autofocus="autofocus" required="required" class="long" />
					{if $errorField == 'position'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.tour.tourPoint.title.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			{include file='multipleLanguageInputJavascript' elementIdentifier='position' forceSelection=false}
			
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
