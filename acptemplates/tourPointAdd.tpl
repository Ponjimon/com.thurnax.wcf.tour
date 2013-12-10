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
			<dl{if $errorField == 'title'} class="formError"{/if}>
				<dt><label for="title">{lang}wcf.acp.tour.point.title{/lang}</label></dt>
				<dd>
					<input type="text" id="title" name="title" cols="40" rows="10">{$i18nPlainValues['title']}</textarea>
					{if $errorField == 'title'}<small class="innerError">{lang}wcf.global.form.error.{@$errorType}{/lang}</small>{/if}
				</dd>
			</dl>
			{include file='multipleLanguageInputJavascript' elementIdentifier='pointText' forceSelection=false}
			
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
		
		<fieldset>
			<legend>{lang}wcf.acp.tour.point.advancedOptions{/lang}</legend>
			<small>{lang}wcf.acp.tour.point.advancedOptions.description{/lang}</small>
			
			<dl{if $errorField == 'scrollDuration'} class="formError"{/if}>
				<dt><label for="scrollDuration">{lang}wcf.acp.tour.point.scrollDuration{/lang}</label></dt>
				<dd>
					<input type="number" id="scrollduration" name="scrollDuration" value="{$scrollDuration}" min="0" max="8388607" required="required" class="tiny" />
					{if $errorField == 'scrollDuration'}<small class="innerError">{lang}wcf.global.form.error.{@$errorType}{/lang}</small>{/if}
					<small>{lang}wcf.acp.tour.point.scrollDuration.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorField == 'xOffset'} class="formError"{/if}>
				<dt><label for="xOffset">{lang}wcf.acp.tour.point.xOffset{/lang}</label></dt>
				<dd>
					<input type="number" id="xOffset" name="xOffset" value="{$xOffset}" min="0" max="8388607" class="tiny" />
					{if $errorField == 'xOffset'}<small class="innerError">{lang}wcf.global.form.error.{@$errorType}{/lang}</small>{/if}
					<small>{lang}wcf.acp.tour.point.xOffset.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorField == 'yOffset'} class="formError"{/if}>
				<dt><label for="yOffset">{lang}wcf.acp.tour.point.yOffset{/lang}</label></dt>
				<dd>
					<input type="number" id="yOffset" name="yOffset" value="{$yOffset}" min="0" max="8388607" class="tiny" />
					{if $errorField == 'yOffset'}<small class="innerError">{lang}wcf.global.form.error.{@$errorType}{/lang}</small>{/if}
					<small>{lang}wcf.acp.tour.point.yOffset.description{/lang}</small>
				</dd>
			</dl>
			<dl{if $errorField == 'multipage'} class="formError"{/if}>
				<dt><label for="multipage">{lang}wcf.acp.tour.point.multipage{/lang}</label></dt>
				<dd>
					<input type="text" id="multipage" name="multipage" value="{$multipage}" autofocus="autofocus" class="long" />
					{if $errorField == 'multipage'}<small class="innerError">{lang}wcf.global.form.error.empty{/lang}</small>{/if}
				</dd>
			</dl>
			
			<dl>
				<dt></dt>
				<dd>
					<label><input type="checkbox" name="showPrevButton" id="showPrevButton" value="1"{if $showPrevButton} checked="checked"{/if} /> <span>{lang}wcf.acp.tour.point.showPrevButton{/lang}</span></label>
					<small>{lang}wcf.acp.tour.point.showPrevButton.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorField == 'onPrev'} class="formError"{/if}>
				<dt><label for="onPrev">{lang}wcf.acp.tour.point.onPrev{/lang}</label></dt>
				<dd>
					<textarea id="onPrev" name="onPrev" cols="40" rows="10">{$onPrev}</textarea>
					{if $errorField == 'onPrev'}<small class="innerError">{lang}wcf.global.form.error.{@$errorType}{/lang}</small>{/if}
				</dd>
			</dl>
			
			<dl>
				<dt></dt>
				<dd>
					<label><input type="checkbox" name="showNextButton" id="showNextButton" value="1"{if $showNextButton} checked="checked"{/if} /> <span>{lang}wcf.acp.tour.point.showPrevButton{/lang}</span></label>
					<small>{lang}wcf.acp.tour.point.showNextButton.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorField == 'onNext'} class="formError"{/if}>
				<dt><label for="onNext">{lang}wcf.acp.tour.point.onNext{/lang}</label></dt>
				<dd>
					<textarea id="onNext" name="onNext" cols="40" rows="10">{$onNext}</textarea>
					{if $errorField == 'onNext'}<small class="innerError">{lang}wcf.global.form.error.{@$errorType}{/lang}</small>{/if}
				</dd>
			</dl>
			
			<dl>
				<dt></dt>
				<dd>
					<label><input type="checkbox" name="showCTAButton" id="showCTAButton" value="1"{if $showCTAButton} checked="checked"{/if} /> <span>{lang}wcf.acp.tour.point.showCTAButton{/lang}</span></label>
					<small>{lang}wcf.acp.tour.point.showCTAButton.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorField == 'onCTA'} class="formError"{/if}>
				<dt><label for="onCTA">{lang}wcf.acp.tour.point.onCTA{/lang}</label></dt>
				<dd>
					<textarea id="onCTA" name="onCTA" cols="40" rows="10">{$onCTA}</textarea>
					{if $errorField == 'onCTA'}<small class="innerError">{lang}wcf.global.form.error.{@$errorType}{/lang}</small>{/if}
				</dd>
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
