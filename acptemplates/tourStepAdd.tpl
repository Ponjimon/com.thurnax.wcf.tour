{include file='header' pageTitle='wcf.acp.tour.step.'|concat:$action}
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		WCF.TabMenu.init();
		$('.tabMenuContainer').on('wcftabsbeforeactivate', function () {
			setTimeout(function() {
				$('#onPrev')[0].codemirror.refresh();
				$('#onNext')[0].codemirror.refresh();
				$('#onShow')[0].codemirror.refresh();
				$('#onCTA')[0].codemirror.refresh();
			}, 100);
		});
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.acp.tour.step.{$action}{/lang}</h1>
</header>

{include file='formError'}
{if $success|isset}<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{if $tourID}{link controller='TourStepList' object=$tours[$tourID]}{/link}{else}{link controller='TourStepList' object=$tours|current}{/link}{/if}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}wcf.acp.tour.step.list{/lang}</span></a></li>
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='TourStepAdd'}{/link}{else}{link controller='TourStepEdit' id=$tourStepID}{/link}{/if}">
	<div class="tabMenuContainer" data-active="{$activeTabMenuItem}" data-store="activeTabMenuItem">
		<nav class="tabMenu">
			<ul>
				<li><a href="{@$__wcf->getAnchor('general')}">{lang}wcf.acp.tour.step.general{/lang}</a></li>
				<li><a href="{@$__wcf->getAnchor('callbacks')}">{lang}wcf.acp.tour.step.callbacks{/lang}</a></li>

				{event name='tabMenuTabs'}
			</ul>
		</nav>
		
		<div id="general" class="container containerPadding tabMenuContent">
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
				
				{event name='generalFields'}
			</fieldset>
			
			<fieldset>
				<legend>{lang}wcf.acp.tour.step.advanced{/lang}</legend>
				<small>{lang}wcf.acp.tour.step.advanced.description{/lang}</small>
				
				<dl>
					<dt class="reversed"><label for="showPrevButton">{lang}wcf.acp.tour.step.showPrevButton{/lang}</label></dt>
					<dd><input type="checkbox" id="showPrevButton" name="showPrevButton"{if $showPrevButton} checked="checked"{/if} /></dd>
				</dl>
				
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
				
				<dl{if $errorField == 'eitherUrlOrOnNext'} class="formError"{/if}>
					<dt><label for="yOffset">{lang}wcf.acp.tour.step.url{/lang}</label></dt>
					<dd>
						<input type="url" id="url" name="url" value="{$url}" class="long" />
						{if $errorField == 'eitherUrlOrOnNext'}<small class="innerError">{lang}wcf.acp.tour.step.eitherUrlOrOnNext{/lang}</small>{/if}
						<small>{lang}wcf.acp.tour.step.url.description{/lang}</small>
					</dd>
				</dl>
				
				<dl{if $errorField == 'ctaLabel'} class="formError"{/if}>
					<dt><label for="ctaLabel">{lang}wcf.acp.tour.step.ctaLabel{/lang}</label></dt>
					<dd>
						<input type="text" id="ctaLabel" name="ctaLabel" value="{$i18nPlainValues['ctaLabel']}" />
						{if $errorField == 'ctaLabel'}<small class="innerError">{lang}wcf.global.form.error.{@$errorType}{/lang}</small>{/if}
						<small>{lang}wcf.acp.tour.step.ctaLabel.description{/lang}</small>
					</dd>
				</dl>
				{include file='multipleLanguageInputJavascript' elementIdentifier='ctaLabel' forceSelection=false}
				
				{event name='advancedFields'}
			</fieldset>
		</div>
		
		<div id="callbacks" class="container containerPadding tabMenuContent">
			<fieldset>
				<legend>{lang}wcf.acp.tour.step.callbacks{/lang}</legend>
				<small>{lang}wcf.acp.tour.step.callbacks.description{/lang}</small>
				
				<dl>
					<dt><label for="onPrev">{lang}wcf.acp.tour.step.onPrev{/lang}</label></dt>
					<dd>
						<textarea id="onPrev" name="onPrev" rows="10" cols="40">{$onPrev}</textarea>
						<small>{lang}wcf.acp.tour.step.onPrev.description{/lang}</small>
					</dd>
				</dl>
				
				<dl>
					<dt><label for="onNext">{lang}wcf.acp.tour.step.onNext{/lang}</label></dt>
					<dd>
						<textarea id="onNext" name="onNext" rows="10" cols="40">{$onNext}</textarea>
						{if $errorField == 'eitherUrlOrOnNext'}<small class="innerError">{lang}wcf.acp.tour.step.eitherUrlOrOnNext{/lang}</small>{/if}
						<small>{lang}wcf.acp.tour.step.onNext.description{/lang}</small>
					</dd>
				</dl>

				<dl{if $errorField == 'eitherUrlOrOnNext'} class="formError"{/if}>
					<dt><label for="onShow">{lang}wcf.acp.tour.step.onShow{/lang}</label></dt>
					<dd>
						<textarea id="onShow" name="onShow" rows="10" cols="40">{$onShow}</textarea>
						<small>{lang}wcf.acp.tour.step.onShow.description{/lang}</small>
					</dd>
				</dl>
				
				<dl>
					<dt><label for="onCTA">{lang}wcf.acp.tour.step.onCTA{/lang}</label></dt>
					<dd>
						<textarea id="onCTA" name="onCTA" rows="10" cols="40">{$onCTA}</textarea>
						<small>{lang}wcf.acp.tour.step.onCTA.description{/lang}</small>
					</dd>
				</dl>
				
				{include file='codemirror' codemirrorMode='javascript' codemirrorSelector='#onPrev,#onNext,#onShow,#onCTA'}
				{event name='callbackFields'}
			</fieldset>
			
			{event name='fieldsets'}
		</div>
	</div>
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}
