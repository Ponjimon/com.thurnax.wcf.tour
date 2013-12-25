{include file='header' pageTitle='wcf.acp.tour.step.'|concat:$action}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.tour.step.{$action}{/lang}</h1>
</header>

{include file='formError'}
{if $success|isset}<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			{if $action == 'add'}
				<li><a href="{link controller='TourStepList'}{/link}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}wcf.acp.tour.step.list{/lang}</span></a></li>
			{else}
				<li id="jumpToTourStep" class="button dropdown">
					<div class="dropdownToggle" data-toggle="jumpToTourStep"><span class="icon icon16 icon-sort"></span> <span>{lang}wcf.acp.tour.step.jumpToTourStep{/lang}</span></div>
					<ol class="dropdownMenu">
						{foreach from=$availableTourSteps item=availableTourStep}
							<li><a href="{link controller='TourStepEdit' id=$availableTourStep->tourStepID}{/link}">{$availableTourStep->target|tableWordwrap}: {$availableTourStep->content|tableWordwrap|language}</a></li>
						{/foreach}
					</ol>
				</li>				
				<li><a href="{link controller='TourStepList' object=$tours[$tourID]}{/link}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}wcf.acp.tour.step.list{/lang}</span></a></li>
			{/if}
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='TourStepAdd'}{/link}{else}{link controller='TourStepEdit' id=$tourStepID}{/link}{/if}">
	<div class="container containerPadding sortableListContainer marginTop">
		<fieldset>
			<legend>{lang}wcf.acp.tour.step.data{/lang}</legend>
			
			<dl{if $errorField == 'tourID'} class="formError"{/if}>
				<dt><label for="tourID">{lang}wcf.acp.tour.step.tour{/lang}</label></dt>
				<dd>
					<select id="tourID" name="tourID">
						{foreach from=$tours item=tour}
							<option value="{$tour->tourID}"{if $tourID == $tour->tourID} selected="selected"{/if}>{$tour->visibleName|language}</option>
						{/foreach}
					</select>
					<small>{lang}wcf.acp.tour.step.tour.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorField == 'target'} class="formError"{/if}>
				<dt><label for="target">{lang}wcf.acp.tour.step.target{/lang}</label></dt>
				<dd>
					<input type="text" id="target" name="target" value="{$target}" autofocus="autofocus" required="required" class="long" />
					{if $errorField == 'target'}<small class="innerError">{lang}wcf.global.form.error.empty{/lang}</small>{/if}
					<small>{lang}wcf.acp.tour.step.target.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorField == 'placement'} class="formError"{/if}>
				<dt><label for="placement">{lang}wcf.acp.tour.step.placement{/lang}</label></dt>
				<dd>
					<select id="placement" name="placement">
						{foreach from=$validPlacements item=validPlacement}
							<option value="{$validPlacement}"{if $placement == $validPlacement} selected="selected"{/if}>{lang}wcf.acp.tour.step.placement.{$validPlacement}{/lang}</option>
						{/foreach}
					</select>
					{if $errorField == 'placement'}<small class="innerError">{lang}wcf.global.form.error.empty{/lang}</small>{/if}
				</dd>
			</dl>
			
			<dl{if $errorField == 'title'} class="formError"{/if}>
				<dt><label for="title">{lang}wcf.acp.tour.step.title{/lang}</label></dt>
				<dd>
					<input type="text" id="title" name="title" value="{$i18nPlainValues['title']}" />
					{if $errorField == 'title'}<small class="innerError">{lang}wcf.global.form.error.{@$errorType}{/lang}</small>{/if}
					<small>{lang}wcf.acp.tour.step.title.description{/lang}</small>
				</dd>
			</dl>
			{include file='multipleLanguageInputJavascript' elementIdentifier='title' forceSelection=false}

			<dl{if $errorField == 'stepContent'} class="formError"{/if}>
				<dt><label for="stepContent">{lang}wcf.acp.tour.step.content{/lang}</label></dt>
				<dd>
					<textarea id="stepContent" name="stepContent" required="required" cols="40" rows="10">{$i18nPlainValues['stepContent']}</textarea>
					{if $errorField == 'stepContent'}<small class="innerError">{lang}wcf.global.form.error.{@$errorType}{/lang}</small>{/if}
				</dd>
			</dl>
			{include file='multipleLanguageInputJavascript' elementIdentifier='stepContent' forceSelection=false}
			
			{event name='dataFields'}
		</fieldset>
		
		<fieldset>
			<legend>{lang}wcf.acp.tour.step.advancedOptions{/lang}</legend>
			<small>{lang}wcf.acp.tour.step.advancedOptions.description{/lang}</small>
			
			{*<dl{if $errorField == 'scrollDuration'} class="formError"{/if}>*}
				{*<dt><label for="scrollDuration">{lang}wcf.acp.tour.step.scrollDuration{/lang}</label></dt>*}
				{*<dd>*}
					{*<input type="number" id="scrollduration" name="scrollDuration" value="{$scrollDuration}" min="0" max="8388607" required="required" class="tiny" />*}
					{*{if $errorField == 'scrollDuration'}<small class="innerError">{lang}wcf.global.form.error.{@$errorType}{/lang}</small>{/if}*}
					{*<small>{lang}wcf.acp.tour.step.scrollDuration.description{/lang}</small>*}
				{*</dd>*}
			{*</dl>*}
			
			<dl{if $errorField == 'xOffset'} class="formError"{/if}>
				<dt><label for="xOffset">{lang}wcf.acp.tour.step.xOffset{/lang}</label></dt>
				<dd>
					<input type="number" id="xOffset" name="xOffset" value="{$xOffset}" min="0" max="8388607" class="tiny" />
					{if $errorField == 'xOffset'}<small class="innerError">{lang}wcf.global.form.error.{@$errorType}{/lang}</small>{/if}
					<small>{lang}wcf.acp.tour.step.xOffset.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorField == 'yOffset'} class="formError"{/if}>
				<dt><label for="yOffset">{lang}wcf.acp.tour.step.yOffset{/lang}</label></dt>
				<dd>
					<input type="number" id="yOffset" name="yOffset" value="{$yOffset}" min="0" max="8388607" class="tiny" />
					{if $errorField == 'yOffset'}<small class="innerError">{lang}wcf.global.form.error.{@$errorType}{/lang}</small>{/if}
					<small>{lang}wcf.acp.tour.step.yOffset.description{/lang}</small>
				</dd>
			</dl>
			
			<dl>
				<dt class="reversed"><label for="showPrevButton">{lang}wcf.acp.tour.step.showPrevButton{/lang}</label></dt>
				<dd><input type="checkbox" id="showPrevButton" name="showPrevButton"{if $showPrevButton} checked="checked"{/if} /></dd>
			</dl>
			
			<dl{if $errorField == 'url'} class="formError"{/if}>
				<dt><label for="yOffset">{lang}wcf.acp.tour.step.url{/lang}</label></dt>
				<dd>
					<input type="url" id="url" name="url" value="{$url}" class="long" />
					<small>{lang}wcf.acp.tour.step.url.description{/lang}</small>
				</dd>
			</dl>
			
			{event name='advancedFields'}
		</fieldset>
		
		{event name='fieldsets'}
	</div>
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}
